<?php    
/**
 * @package KVD.dom
 * @version $Id$
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_PDOLogableDataMapper 
 * 
 * De abstracte mapper die alle Logable Datamappers gemeen hebben. Zij verzorgen de communicatie met de databank voor LogableDomainObjects.
 * @package KVD.dom
 * @since 5 okt 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDdom_PDOLogableDataMapper extends KVDdom_PDODataMapper
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
     */
    const SFVELDEN = "versie";

    /**
     * logtabel 
     * 
     * @var string
     */
    protected $logtabel;

    /**
     * getInsertStatement 
     * 
     * @return string SQL Statement
     */
    abstract protected function getInsertStatement( );

    /**
     * getDeleteStatement 
     * 
     * @return string SQL Statement
     */
    abstract protected function getDeleteStatement( );

    /**
     * getUpdateStatement 
     * 
     * @return string SQL Statement
     */
    abstract protected function getUpdateStatement( );

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
                " ( id, " . $this->velden . ", " . self::SFVELDEN . ")" .
                " SELECT id , " . $this->velden . ", " . self::SFVELDEN .
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
     * @return string Een SQL statement om alle gelogde versies van een record te verwijderen. Heeft de parameter id nodig.
     */
    protected function getClearLogStatement( )
    {
        return  "DELETE FROM " . $this->logtabel . 
                " WHERE " . self::ID . " = ? ";
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
     * getLogSystemFields 
     * 
     * @param string $tabelNaam Naam van de tabel waarvoor geprefixt moet worden.
     * @param boolean $logTabel Wordt de data geladen uit de gelogde tabel of niet?
     * @param string $systemFields String met alle systeemvelden
     * @return string Lijst van de systeemVelden, maar met een prefix en 
     */
    protected function getSystemFieldsString ( $tabelNaam , $logTabel = false , $systemFields = self::SFVELDEN )
    {
        $fields = explode ( ', ' , $systemFields );
        $tabel = ( $logTabel == false ) ? $tabelNaam : 'log_' . $tabelNaam;
        foreach ( $fields as &$field ) {
            $field = "$tabel.$field AS " . $tabelNaam . "_" . $field;
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
     * doInsert 
     *
     * Dit is een stub methode die de standaard handelingen uitvoert maar verder kan overschreven worden.
     * @since 30 okt 2006
     * @param PDOStatement $stmt 
     * @param KVDdom_LogableDomainObject $domainObject 
     * @return integer Nummer van de volgende te gebruiken index in het sql statement.
     */
    protected function doInsert( $stmt , $domainObject )
    {
            $stmt->bindValue ( 1 , $domainObject->getId( ) , PDO::PARAM_INT );
            $lastIndex = $this->bindValues ( $stmt , 2 , $domainObject );    
            return $this->doSetSystemFields( $stmt , $domainObject , $lastIndex );
    }
    
    /**
     * Stel de waarden van het SystemFields object in in de SQL statement
     *
     * @param PDOStatement $stmt
     * @param KVDdom_DomainObject $domainObject
     * @param integer $startIndex De numerieke index in de PDO Statement van de eerste parameter ( de gebruikersnaam ).
     * @return integer Nummer van de volgende te gebruiken index in het sql statement.
     */
    public function doSetSystemFields($stmt, $domainObject, $startIndex )
    {
        $systemFields = $domainObject->getSystemFields();
        $stmt->bindValue( $startIndex++ , $systemFields->getVersie( ) , PDO::PARAM_INT );
        return $startIndex;
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
            $this->_conn->beginTransaction( );
            $this->LogInsert( $domainObject->getId() );
            $stmt = $this->_conn->prepare($this->getDeleteStatement());
            $stmt->bindValue (1, $domainObject->getId() , PDO::PARAM_INT );
            $stmt->execute( );
            if ( $stmt->rowCount( )  === 0 ) {
                $message = 'Het object dat u probeert te verwijderen is gewijzigd sinds u het geopend hebt.';
                throw new KVDdom_ConcurrencyException ($message,$domainObject);
            }
            $this->_conn->commit( );
        } catch ( KVDdom_ConcurrencyException $e ) {
            $this->_conn->rollBack( );
            throw $e;
        } catch ( PDOException $e) {
            $this->_conn->rollBack( );
            throw $e;
        }
    }

    /**
     * update 
     * 
     * @since 30 okt 2006
     * @param KVDdom_LogableDomainObject $domainObject 
     * @return KVDdom_LogableDomainObject Het object dat werd geupdate, maar met aangepaste systeemvelden.
     */
    abstract protected function update( $domainObject );

    /**
     * bindValues 
     *
     * Methode waarin alle inhouds-velden in het sql-statement een waarde moeten toegewezen krijgen. Dus niet de id of systeemvelden, maar wel de echte data.
     * @since 30 okt 2006
     * @param PDOStatement $stmt 
     * @param integer $startIndex 
     * @param KVDdom_LogableDomainObject $domainObject 
     * @return integer Volgende te gebruiken index in het statement.
     */
    abstract protected function bindValues ( $stmt , $startIndex , $domainObject );

    /**
     * Een abstract functie die het grootste deel van het opzoekwerk naar een DomainObject met een specifieke Id uitvoert
     *
     * @param string $returnType Het soort DomainObject dat gevraagd wordt, nodig om de IdentityMap te controleren.
     * @param integer $id Het id nummer van het gevraagd DomainObject.
     * @return KVDdom_DomainObject Een DomainObject van het type dat gevraagd werd met de parameter $returnType.
     *          Indien het DomainObject zelf niet gevonden werd, maar er is wel een gelogde versie van beschikbaar dan wordt er een Special Case object geretourneerd.
     *          Dit deleted object bevat geen data maar kan wel bewerkt worden en zo terug naar de vorige versie gezet worden.
     * @throws <b>KVDdom_DomainObjectNotFoundException</b> Indien het gevraagde DomainObject niet werd gevonden en er ook geen gelogde versie van bestaat.
     */
    protected function abstractFindById ( $returnType , $id )
    {
        $id = ( int ) $id;
        try {
            $domainObject = parent::abstractFindById( $returnType, $id );
        } catch ( KVDdom_DomainObjectNotFoundException $e ) {
            try {
                $laatsteVersie = $this->findByLogId( $id );
            } catch ( KVDdom_LogDomainObjectNotFoundException $le ) {
                // Er is helemaal niets van dit type met deze id in de databank.
                throw $e;
            }
            $systemFields = new KVDdom_SystemFields( 'ongekend', false, $laatsteVersie->getSystemFields( )->getVersie( ) );
            $domainObject = $this->createDeleted( $id, $systemFields );
        }
        return $domainObject;
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
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception ( 'Het record kon niet gelogd worden omwille van een SQL probleem: ' . $e->getMessage() );
        }
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
     * restoreDeleted
     *
     * Een oudere versie van een DomainObject wordt terug geplaatst als het actuele record.
     * @param KVDdom_LogableDomainObject Een oudere versie van een record.
     * @return KVDdom_LogableDomainObject De nieuwe, recente versie van het record met aangepaste {@link KVDdom_SystemFields}.
     * @throws <b>InvalidArgumentException</b> Indien het domainObject geen oude versie van een record is maar al de meest recente versie is.
     */
    protected function restoreDeleted( $domainObject )
    {
        if ( $domainObject->getSystemFields( )->isCurrentRecord( ) ) {
            throw new InvalidArgumentException ( 'Enkel een oude versie van een record kan hersteld worden tot meest recente versie.' );
        }
        $domainObject->getSystemFields( )->updateSystemFields( $this->_sessie->getGebruiker( )->getGebruikersNaam( ) );
        $this->insert( $domainObject );
        return $domainObject;
    }

    /**
     * Maak een Special Case aan van een domainObject dat null is maar wel kan geupdate worden.
     * @param integer $id
     * @param KVDdom_SystemFields $systemFields
     */
    abstract protected function createDeleted( $id, $systemFields );

    /**
     * clearHistory
     *
     * Verwijder de geschiedenis van een object uit de databank ( komt neer op het wissen van alle data in de log-tabellen voor een bepaald object).
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> Indien een record zijn geschiedenis niet gewist kan worden.
     */
    public function clearHistory( $domainObject )
    {
        $stmt = $this->_conn->prepare( $this->getClearLogStatement( ) );
        $stmt->bindValue( 1 , $domainObject->getId( ) , PDO::PARAM_INT );
        try {
            $stmt->execute( );
        } catch ( SQLException $e ) {
            throw new Exception ( 'De geschiedenis van het record kan niet verwijderd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
    }
    
    /**
     * executeLogFindMany
     *
     * Voer een zoekacties op meerdere records uit in de log-tabellen.
     * @param Statement $stmt
     * @return KVDdom_DomainObjectCollection
     */
    protected function executeLogFindMany( $stmt )
    {
        $stmt->execute( );
        $domainObjects = array ( );
        while ( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
            $logObject = $this->doLogLoad( $row->id, $row );
            $domainObjects[] = $logObject;
        }
        return new KVDdom_DomainObjectCollection ( $domainObjects );
    }

    /**
     * getIdFromSequence 
     * 
     * @since 30 okt 2006
     * @param string $sequenceName Naam van de sequentie waarvoor een nummer moet opgehaald worden.
     * @return integer Een id waarde
     */
    protected function getIdFromSequence( $sequenceName )
    {
        $stmt = $this->_conn->query( "SELECT nextval ( '$sequenceName' )" );
        return $stmt->fetchColumn( );
    }

    /**
     * @since 30 okt 2006
     */
    abstract public function create ();
}
?>
