<?php
/**
 * @package KVD.dom
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_PDOLogableDataMapper
 *
 * De abstracte mapper die alle Logable Datamappers gemeen hebben. Zij verzorgen de communicatie met de databank voor LogableDomainObjects.
 * @package KVD.dom
 * @since 5 okt 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
abstract class KVDdom_PDOLogableDataMapper extends KVDdom_PDOChangeableDataMapper
{

    /**
     * De naam van de table waarnaar de log-informatie geschreven wordt.
     */
    const LOGTABEL = "";

    /**
     * Constant om aan te geven dat de laatst gelogde versie van een bepaald record gezocht wordt.
     */
    const MAXVERSIE = "-1";

    /**
     * De velden die nodig zijn voor het SystemFields object.
     *
     * @var string
     */
    protected $sfvelden = "versie";

    /**
     * logtabel
     *
     * @var string
     */
    protected $logtabel;

    /**
     * getDeleteStatement
     *
     * @return string SQL Statement
     */
    protected function getDeleteStatement( )
    {
        return  "DELETE FROM " . $this->tabel .
                " WHERE " . $this->id . " = ? AND versie = ?";
    }

    protected function getUpdateStatement()
    {
        return  "UPDATE " . $this->tabel . " SET " .
                $this->getUpdateFieldsStatement( ) . ( $this->systemFieldsMapper->getUpdateSystemFieldsString() <> "" ? ', ' . $this->systemFieldsMapper->getUpdateSystemFieldsString( ) : '').
                " WHERE " . $this->id . " = ? AND versie = ?";
    }

    /**
     * getLogSelectStatement
     *
     * @return string SQL Statement
     */
    abstract protected function getLogSelectStatement( );

    /**
     * Zoek een object in de log-tabellen op basis van een Id.
     * @return string Een SQL statement om een bepaalde versie van een object te vinden. De parameters id en versie moeten ingevuld worden.
     */
    protected function getFindByLogIdStatement()
    {
        return  $this->getLogSelectStatement( ) .
                " WHERE log_" . $this->id . " = ? AND versie = ?";
    }

    /**
     * @return string Een SQL statement om het meest recent gelogde object te vinden. De parameter id moet ingevuld worden.
     */
    protected function getFindByLogIdMaxVersieStatement()
    {
        return  $this->getLogSelectStatement( ) .
                " WHERE log_" . $this->id . " = ? AND versie = ( SELECT MAX( versie ) FROM " . $this->logtabel . " WHERE id = ? )";
    }

    /**
     * @return string Een SQL statement om een object te loggen. Zou normaal enkel nog een id nodig hebben.
     */
    protected function getLogInsertStatement()
    {
        return  "INSERT INTO " . $this->logtabel .
                " ( id, " . $this->velden . ", " . $this->systemFieldsMapper->getSystemFields( ) . ")" .
                " SELECT id , " . $this->velden . ", " . $this->systemFieldsMapper->getSystemFields( ) .
                " FROM " . $this->tabel . " WHERE " . $this->id . " = ?";
    }

    /**
     * @return string Een SQL statement om alle gelogde versies van een object uit de log-tabellen te halen. De parameter id moet nog ingevuld worden.
     */
    protected function getLogFindAllStatement()
    {
        return  $this->getLogSelectStatement( ) .
                " WHERE log_" . $this->id . " = ?" .
                $this->getLogOrderStatement( );
    }

    /**
     * @return string Een ORDER BY SQL statement dat de sorteervolgorde voor gelogde records aangeeft.
     */
    protected function getLogOrderStatement()
    {
        return " ORDER BY versie DESC";
    }

    /**
     * @param array $velden Alle velden die ook in de logtabel moeten gezocht worden
     * @param string $logtabel Naam van de tabel die de gelogde data bevat.
     * @return string Lijst van de velden met een prefix toegepast.
     */
    protected function getLogVelden( $velden, $logtabel )
    {
        $fields = explode ( ', ' , $velden );
        foreach ( $fields as &$field ) {
            $field = "$logtabel.$field AS $field";
        }
        return implode ( ', ', $fields );
    }

    /**
     * Voeg een nieuw DomainObject toe aan de databank
     *
     * @param KVDdom_LogableDomainObject $domainObject Het DomainObject dat moet toegevoegd worden aan de databank.
     * @throws PDOException Indien er een databank probleem optreed.
     */
    public function insert ($domainObject)
    {
        try {
            $stmt = $this->_conn->prepare ($this->getInsertStatement() );
            $this->doInsert( $stmt , $domainObject );
            $stmt->execute();
            return $domainObject;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Stel de waarden voor het selecteren van de te updaten record in
     * @param PDOStatement $stmt
     * @param integer $id
     * @param integer $versie
     * @param integer $firstParam De numerieke index in de PDO Statement van de id parameter.
     */
    public function doSetUpdateWhere($stmt, $id, $versie, $startIndex)
    {
        $stmt->bindValue($startIndex++, $id , PDO::PARAM_INT );
        $stmt->bindValue($startIndex++, $versie , PDO::PARAM_INT );
    }

    /**
     * update
     *
     * @param KVDdom_DomainObject $domainObject
     * @return KVDdom_DomainObject
     */
    public function update ( $domainObject )
    {
        $currentVersie = $domainObject->getSystemFields( )->getVersie( );

        $this->systemFieldsMapper->updateSystemFields( $domainObject , $this->_sessie->getGebruiker( )->getGebruikersNaam( ) );

        $this->logInsert( $domainObject->getId( ) );

        $stmt = $this->_conn->prepare( $this->getUpdateStatement( ));
        $lastIndex = $this->bindValues( $stmt , 1 , $domainObject );
        $lastIndex = $this->doSetSystemFields( $stmt , $domainObject , $lastIndex );
        $this->doSetUpdateWhere ( $stmt , $domainObject->getId( ) , $currentVersie , $lastIndex );

        $stmt->execute( );

        if ( $stmt->rowCount( ) == 0 ) {
            $msg = 'Het record werd gewijzigd sinds u het geopend hebt.';
            throw new KVDdom_ConcurrencyException ( $msg , $domainObject );
        }
        return $domainObject;
    }

    /**
     * Verwijder een bepaald DomainObject uit de databank.
     *
     * Implementeert Optimist Offline Locking, indien het object gewijzigd is door iemand anders sinds het geladen werd wordt er een Exception gegenereerd.
     * @param KVDdom_DomainObject $domainObject Het DomainObject dat moet verwijderd worden.
     * @throws KVDdom_ConcurrencyException
     */
    public function delete ( $domainObject )
    {
        try {
            $currentVersie = $domainObject->getSystemFields()->getVersie();
            $this->LogInsert( $domainObject->getId() );
            $stmt = $this->_conn->prepare($this->getDeleteStatement());
            $this->doSetUpdateWhere( $stmt , $domainObject->getId( ) , $currentVersie , 1 );
            $stmt->execute( );
        } catch ( PDOException $e ) {
            throw KVDdom_ExceptionConvertor::convert( $e , $domainObject );
        }
        if ( $stmt->rowCount( )  === 0 ) {
            $message = 'Het object dat u probeert te verwijderen is gewijzigd sinds u het geopend hebt. U hebt geprobeerd om versie ' . $currentVersie . ' te verwijderen.';
            throw new KVDdom_ConcurrencyException ($message,$domainObject);
        }
    }

    /**
     * Een abstracte methode om een gelogde versie van een domainObject aan te maken.
     *
     * Grote verschil is dat deze versie niet meer gewijzigd kan worden door een gebruiker. Ze moet ook niet worden opgenomen door de UOW.
     * @param integer $id Uniek nummer van het te laden object
     * @param integer $versie Versie van het te laden object. Indien versie gelijk is aan(@link KVDdom_LogableDataMapper::MAXVERSIE) wordt de recentste versie geladen.
     * @return KVDdom_DomainObject
     * @throws KVDdom_LogDomainObjectNotFoundException
     * */
    protected function abstractFindByLogId ( $returnType , $id , $versie )
    {
        $id = ( int ) $id;
        if ( $versie == self::MAXVERSIE ) {
            $stmt = $this->_conn->prepare ( $this->getFindByLogIdMaxVersieStatement() );
            $stmt->bindValue ( 2 , $id , PDO::PARAM_INT );
        } else {
            $stmt = $this->_conn->prepare ( $this->getFindByLogIdStatement() );
            $stmt->bindValue ( 2 , $versie , PDO::PARAM_INT );
        }
        $stmt->bindValue (1 , $id , PDO::PARAM_INT );
        $stmt->execute( );
        if ( !$row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
            throw new KVDdom_LogDomainObjectNotFoundException ( '', $returnType, $id, $versie);
        }
        return $this->doLogLoad ( $id , $row );
    }

    /**
     * Een abstract methode die een collectie met alle vorige versies van een bepaald domainObject teruggeeft.
     * @param integer $id
     * @return KVDdom_DomainObjectCollection
     */
    protected function abstractFindLogAll ( $id )
    {
        $stmt = $this->_conn->prepare ( $this->getLogFindAllStatement() );
        $stmt->bindValue ( 1 , $id , PDO::PARAM_INT );
        return $this->executeLogFindMany ( $stmt );
    }

    /**
     * @param integer $id
     * @throws Exception - Indien het record niet gelogd kon worden.
     */
    protected function LogInsert( $id )
    {
        $stmt = $this->_conn->prepare ( $this->getLogInsertStatement() );
        $stmt->bindValue ( 1 , $id , PDO::PARAM_INT );
        $stmt->execute();
    }

    /**
     * @param integer $id
     * @param StdClass $row
     * @return KVDdom_DomainObject
     */
    abstract public function doLogLoad ( $id , $row );

    /**
     * findByLogId
     *
     * @param integer $id
     * @param integer $versie
     * @return KVDdom_DomainObject
     * @throws KVDdom_LogDomainObjectNotFoundException
     */
    abstract public function findByLogId( $id , $versie = self::MAXVERSIE );

    /**
     * @param integer $id
     * @return KVDdom_DomainObjectCollection
     */
    public function findLogAll( $id )
    {
        return $this->abstractFindLogAll( $id );
    }

    /**
     * executeLogFindMany
     *
     * Voer een zoekacties op meerdere records uit in de log-tabellen.
     * @param Statement $stmt
     * @return KVDdom_DomainObjectLogCollection
     */
    protected function executeLogFindMany( $stmt )
    {
        $stmt->execute( );
        $domainObjects = array ( );
        while ( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
            $logObject = $this->doLogLoad( $row->id, $row );
            $domainObjects[] = $logObject;
        }
        return new KVDdom_DomainObjectLogCollection ( $domainObjects );
    }

    /**
     * isVerwijderd
     *
     * Gaat na of een bepaald object nog voorkomt in de hoofdtabellen of niet.
     * @since 31 okt 2006
     * @param KVDdom_LogableDomainObject $domainObject
     * @return boolean True indien het object enkel voorkomt in de logtabellen, false indien het object nog een bestaande hoofdversie heeft.
     * @deprecated Nagaan of dit nog zin heeft.
     */
    protected function isVerwijderd( $domainObject )
    {
        try {
            $obj = $this->findById( $domainObject->getId( ) );
            return $obj->isVerwijderd( );
        } catch ( KVDdom_LogDomainObjectNotFoundException $e ) {
            return true;
        }
    }

    /**
     * getSystemFieldsString
     *
     * @deprecated              Beter om rechtstreeks naar de systemFieldsMapper te gaan.
     * @param string $tabelNaam
     * @param boolean $logTabel
     * @param string $systemFields
     * @return string
     */
    protected function getSystemFieldsString (  $tabelNaam , $logTabel = false , $systemFields = null )
    {
        return $this->systemFieldsMapper->getSystemFieldsString( $tabelNaam, $logTabel , $systemFields );
    }

    /*
     * Stel de waarden van het SystemFields object in in de SQL statement
     *
     * @deprecated              Beter om rechtstreeks naar de systemFieldsMapper te gaan.
     * @param PDOStatement $stmt
     * @param KVDdom_DomainObject $domainObject
     * @param integer $startIndex De numerieke index in de PDO Statement van de eerste parameter (  de gebruikersnaam ).
     * @return integer Nummer van de volgende te gebruiken index in het sql statement.
     */
    protected function doSetSystemFields( $stmt, $domainObject, $startIndex )
    {
        return $this->systemFieldsMapper->doSetSystemFields( $stmt, $domainObject, $startIndex );
    }

    /**
     * doLoadSystemFields
     *
     * @deprecated              Beter om rechtstreeks naar de systemFieldsMapper te gaan.
     * @param stdClass $row
     * @param string $prefix
     * @return KVDdom_LegacySystemFields
     */
    protected function doLoadSystemFields( $row , $prefix = null)
    {
        return $this->systemFieldsMapper->doLoadSystemFields( $row , $prefix );
    }
}
