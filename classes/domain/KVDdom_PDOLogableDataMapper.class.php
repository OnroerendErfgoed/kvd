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
     */
    const SFVELDEN = "gebruiker, bewerkt_op, versie, gecontroleerd";

    /**
     * Zoek een object in de log-tabellen op basis van een Id.
     * @return string Een SQL statement om een bepaalde versie van een object te vinden. De parameters id en versie moeten ingevuld worden.
     */
    abstract protected function getFindByLogIdStatement();

    /**
     * @return string Een SQL statement om het meest recent gelogde object te vinden. De parameter id moet ingevuld worden.
     */
    abstract protected function getFindByLogIdMaxVersieStatement(); 

    /**
     * @return string Een SQL statement om een object te loggen. Zou normaal enkel nog een id nodig hebben.
     */
    abstract protected function getLogInsertStatement();

    /**
     * @return string Een SQL statement om alle gelogde versies van een object uit de log-tabellen te halen. De parameter id moet nog ingevuld worden.
     */
    abstract protected function getLogFindAllStatement();

    /**
     * @return string Een SQL statement om een record goed te keuren. De parameter id moet nog ingevuld worden.
     */
    abstract protected function getApproveRecordStatement( );

    /**
     * @return string Een SQL statement om alle gelogde versies van een record goed te keuren. Enkel de parameter id moet nog worden ingevuld.
     */
    abstract protected function getApproveLogRecordsStatement( );

    /**
     * @return string Een SQL statement om alle te redacteren records te vinden. Heeft geen parameters meer nodig.
     */
    abstract protected function getFindTeRedacterenStatement( );

    /**
     * @return string Een SQL statement om alle records te zoeken die wel nog in de log-tabellen zitten maar niet meer in de gewone tabellen. Heeft geen parameters nodig.
     */
    abstract protected function getFindVerwijderdeStatement( );

    /**
     * @return string Een SQL statement om alle gelogde versies van een record te verwijderen. Heeft de parameter id nodig.
     */
    abstract protected function getClearLogStatement( );
    

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
     * Laad een SystemFields object op basis van een ResultSet
     *
     * @param StdClass $row Een StdClass object dat door PDO wordt afgeleverd via fetchRow. Dit object moet de nodige velden bevatten om een Systemfields object mee samen te kunnen stellen.
     * @param boolean $currentRecord Moet het object geladen worden alsof het de meest recente versie van het record is?
     * @param string $prefix Moet er voor zorgen dat bij een join van 2+ tabellen er 2+ systemfields objecten geladen kunnen worden. Standaard wordt er van uitgegaan dat er geen prefix nodig is.
     * @return KVDdom_SystemFields
     */
    public function doLoadSystemFields( $row , $currentRecord = true , $prefix = null)
    {
        if ($prefix !== null) {
            $prefix = $prefix . '_';
        }
        $gebruiker = $prefix . 'gebruiker';
        $versie = $prefix . 'versie';
        $bewerktOp = $prefix . 'bewerkt_op';
        $gecontroleerd = $prefix . 'gecontroleerd';
        return new KVDdom_SystemFields (    $row->$gebruiker,
                                            $currentRecord,
                                            $row->$versie ,
                                            $row->$bewerktOp ,
                                            $row->$gecontroleerd
                                        );
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
        $stmt->bindValue( $startIndex++ , $systemFields->getGebruikersNaam( ) , PDO::PARAM_STR );
        $stmt->bindValue( $startIndex++ , $systemFields->getBewerktOp( ) , PDO::PARAM_STR );
        $stmt->bindValue( $startIndex++ , $systemFields->getVersie( ) , PDO::PARAM_INT );
        $stmt->bindValue( $startIndex++ , $systemFields->getGecontroleerd( ) , PDO::PARAM_BOOL );
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
     * doInsert 
     * 
     * Stub methode die standaard handelingen uitvoert maar makkelijk kan overschreven worden.
     * @since 25 okt 2006
     * @param PDOStatement $stmt 
     * @param KVDdom_LogableDomainObject $domainObject 
     * @return integer Nummer van de volgende te gebruiken index in het sql statement.
     */
    protected function doInsert( $stmt , $domainObject )
    {
        $lastIndex = parent::doInsert( $stmt , $domainObject );
        return $this->doSetSystemFields( $stmt , $domainObject , $lastIndex );
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
     * @param integer $id
     * @return KVDdom_DomainObjectCollection
     */
    abstract public function findLogAll( $id );

    /**
     * @param KVDdom_DomainObjectCollection
     */
    abstract public function findTeRedacteren( );

    /**
     * @param KVDdom_DomainObjectCollection
     */
    abstract public function findVerwijderde( );

    /**
     * Zoek alle records van deze datamapper die nog niet gecontroleerd zijn.
     * @return KVDdom_DomainObjectCollection
     */
    protected function abstractFindTeRedacteren ( )
    {
        $stmt = $this->_conn->prepare ( $this->getFindTeRedacterenStatement() );
        return $this->executeFindMany ( $stmt );
    }

    /**
     * abstractFindVerwijderde 
     * 
     * Zoek alle verwijderde records in de log tabellen.
     * @return KVDdom_DomainObjectCollection
     */
    protected function abstractFindVerwijderde( )
    {
        $stmt = $this->_conn->prepare ( $this->getFindVerwijderdeStatement( ) );
        return $this->executeLogFindMany( $stmt );
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
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> Indien een record niet goedgekeurd kan worden.
     */
    public function approve ( $domainObject )
    {
        $stmt = $this->_conn->prepare ( $this->getApproveRecordStatement( ) );
        $stmt->bindValue (1 , $domainObject->getId( ) , PDO::PARAM_INT );
        try {
            $stmt->execute( );
        } catch (PDOException $e) {
            throw new Exception ( 'Het record kan niet goedgekeurd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
        $stmt = $this->_conn->prepare (  $this->getApproveLogRecordsStatement( ) );
        $stmt->bindValue( 1, $domainObject->getId( ) , PDO::PARAM_INT );
        try {
            $stmt->execute( );
        } catch (PDOException $e) {
            throw new Exception ( 'Het record kan niet goedgekeurd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
    }

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
}
?>
