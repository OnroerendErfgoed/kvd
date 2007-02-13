<?php    
/**
 * @package KVD.dom
 * @version $Id$
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_PDORedigeerbareDataMapper 
 * 
 * De abstracte mapper die alle Redigeerbare Datamappers gemeen hebben. Zij verzorgen de communicatie met de databank voor Domainobjects die de {@link KVDdom_Redigeerbaar} interface implementeren.
 * @package KVD.dom
 * @since 30 okt 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDdom_PDORedigeerbareDataMapper extends KVDdom_PDOLogableDataMapper
{

    /**
     * De velden die nodig zijn voor het SystemFields object.
     * 
     * @var string
     */
    protected $sfvelden = "gebruiker, bewerkt_op, versie, gecontroleerd, gecontroleerd_door, gecontroleerd_op";

    /**
     * @return string Een SQL statement om een record goed te keuren. De parameter id moet nog ingevuld worden.
     */
    protected function getApproveRecordStatement( )
    {
        return  "UPDATE " . $this->tabel . 
                " SET gecontroleerd = true, gecontroleerd_door = ?, gecontroleerd_op = ?" .
                " WHERE " . $this->id . " = ?";
    }

    /**
     * @return string Een SQL statement om alle gelogde versies van een record goed te keuren. Enkel de parameter id moet nog worden ingevuld.
     */
    protected function getApproveLogRecordsStatement( )
    {
        return  "UPDATE " . $this->logtabel . 
                " SET gecontroleerd = true, gecontroleerd_door = ?, gecontroleerd_op = ?" .
                " WHERE log_" . $this->id . " = ?";
    }

    /**
     * @return string Een SQL statement om alle te redacteren records te vinden. Heeft geen parameters meer nodig.
     */
    protected function getFindTeRedacterenStatement( )
    {
        return  $this->getSelectStatement( ) .
                " WHERE " . $this->tabel . ".gecontroleerd = false " . 
                $this->getOrderStatement( );
    }

    /**
     * @return string Een SQL statement om alle records te zoeken die wel nog in de log-tabellen zitten maar niet meer in de gewone tabellen. Heeft geen parameters nodig.
     */
    protected function getFindVerwijderdeStatement( )
    {
        return  $this->getLogSelectStatement( ) . 
                " WHERE NOT EXISTS ( SELECT 1 FROM " . $this->tabel . " WHERE id = log_" . $this->id . ") " .
                " AND versie = ( SELECT max( versie ) FROM " . $this->logtabel . " AS log WHERE log.id = log_" . $this->id . ")" .
                " ORDER BY log_" . $this->id . " ASC";
    }

    /**
     * getRevertDeleteStatement 
     * 
     * @return string Een SQL statement om de laatste gelogde versie van een object dat verwijderd werd terug te plaatsen.
     */
    protected function getRevertDeleteStatement( )
    {
        return  "INSERT INTO " . $this->tabel . 
                " SELECT * FROM " . $this->logtabel . 
                " WHERE " . self::ID " = ? AND" .
                " versie = ( SELECT MAX( versie ) FROM " . $this->logtabel . " WHERE id = ? )";
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
     * Laad een SystemFields object op basis van een ResultSet
     *
     * @param StdClass $row Een StdClass object dat door PDO wordt afgeleverd via fetchRow. Dit object moet de nodige velden bevatten om een Systemfields object mee samen te kunnen stellen.
     * @param string $prefix Moet er voor zorgen dat bij een join van 2+ tabellen er 2+ systemfields objecten geladen kunnen worden. Standaard wordt er van uitgegaan dat er geen prefix nodig is.
     * @return KVDdom_SystemFields
     */
    public function doLoadSystemFields( $row , $prefix = null)
    {
        if ($prefix !== null) {
            $prefix = $prefix . '_';
        }
        $gebruiker = $prefix . 'gebruiker';
        $versie = $prefix . 'versie';
        $bewerktOp = $prefix . 'bewerkt_op';
        $gecontroleerd = $prefix . 'gecontroleerd';
        $gecontroleerdDoor = $prefix . 'gecontroleerd_door';
        $gecontroleerdOp = $prefix . 'gecontroleerd_op';
        return new KVDdom_SystemFields (    $row->$gebruiker,
                                            $row->$versie ,
                                            strtotime( $row->$bewerktOp ),
                                            $row->$gecontroleerd,
                                            $row->$gecontroleerdDoor,
                                            strtotime( $row->$gecontroleerdOp )
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
    protected function doSetSystemFields($stmt, $domainObject, $startIndex )
    {
        $systemFields = $domainObject->getSystemFields();
        $stmt->bindValue( $startIndex++ , $systemFields->getGebruikersNaam( ) , PDO::PARAM_STR );
        $stmt->bindValue( $startIndex++ , $systemFields->getBewerktOp( ) , PDO::PARAM_STR );
        $stmt->bindValue( $startIndex++ , $systemFields->getTargetVersie( ) , PDO::PARAM_INT );
        $stmt->bindValue( $startIndex++ , $systemFields->getGecontroleerd( ) , PDO::PARAM_BOOL );
        $stmt->bindValue( $startIndex++ , $systemFields->getGecontroleerdDoor( ) , PDO::PARAM_STR );
        $stmt->bindValue( $startIndex++ , $systemFields->getGecontroleerdOp( ) , PDO::PARAM_STR );
        return $startIndex;
    }
        
    /**
     * Zoek alle records van deze datamapper die nog niet gecontroleerd zijn.
     * @param KVDdom_DomainObjectCollection
     */
    public function findTeRedacteren( )
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
    public function findVerwijderde( )
    {
        $stmt = $this->_conn->prepare ( $this->getFindVerwijderdeStatement( ) );
        return $this->executeLogFindMany( $stmt );
    }

    /**
     * @param KVDdom_LogableDomainObject $domainObject
     * @throws <b>Exception</b> Indien een record niet goedgekeurd kan worden.
     */
    public function approve ( $domainObject )
    {
        $stmt = $this->_conn->prepare ( $this->getApproveRecordStatement( ) );
        $stmt->bindValue(1, $domainObject->getSystemFields( )->getGecontroleerdDoor( ) , PDO::PARAM_STR );
        $stmt->bindValue(2, $domainObject->getSystemFields( )->getGecontroleerdOp( ) , PDO::PARAM_STR );
        $stmt->bindValue(3, $domainObject->getId( ) , PDO::PARAM_INT );
        try {
            $stmt->execute( );
        } catch ( PDOException $e) {
            throw new Exception ( 'Het record kan niet goedgekeurd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
        $stmt = $this->_conn->prepare (  $this->getApproveLogRecordsStatement( ) );
        $stmt->bindValue( 1, $domainObject->getSystemFields( )->getGecontroleerdDoor( ) , PDO::PARAM_STR );
        $stmt->bindValue( 2, $domainObject->getSystemFields( )->getGecontroleerdOp( ) , PDO::PARAM_STR );
        $stmt->bindValue( 3, $domainObject->getId( ), PDO::PARAM_INT );
        try {
            $stmt->execute( );
        } catch (PDOException $e) {
            throw new Exception ( 'Het record kan niet goedgekeurd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
    }

    /**
     * undoDelete 
     * 
     * Skelet-methode die voldoende is voor simpele objecten maar moet geoverriden worden voor objecten die een tros objecten controleren.
     * @param KVDdom_DomainObject $domainObject 
     */
    public function undoDelete( $domainObject )
    {
        $this->revertDelete( $domainObject );
    }

    /**
     * confirmDelete 
     * 
     * Skelet-methode die voldoende is voor simpele objecten maar moet geoverriden worden voor objecten die een tros objecten controleren.
     * @param KVDdom_DomainObject $domainObject 
     */
    public function confirmDelete( $domainObject )
    {
        $this->clearHistory( $domainObject );
    }

    /**
     * revertDelete 
     * 
     * @param KVDdom_DomainObject $domainObject 
     * @throw <b>Exception</b> Indien het verwijderde record niet kon teruggeplaatst worden.
     */
    public function revertDelete( $domainObject )
    {
        $stmt = $this->_conn->prepare( $this->getRevertDeleteStatement( ) );
        $stmt->bindValue( 1 , $domainObject->getId( ) , PDO::PARAM_INT );
        $stmt->bindValue( 2 , $domainObject->getId( ) , PDO::PARAM_INT );
        try {
            $stmt->execute( );
        } catch ( PDOException) {
            throw new Exception ( 'Het verwijderde record kan niet teruggeplaatst worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
    }

    /**
     * Maak een Special Case aan van een domainObject dat null is maar wel kan geupdate worden.
     * @param integer $id
     * @param KVDdom_SystemFields $systemFields
     */
    abstract protected function createDeleted( $id , $systemFields = null );

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
        } catch ( PDOException $e ) {
            throw new Exception ( 'De geschiedenis van het record kan niet verwijderd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
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
            $systemFields = new KVDdom_SystemFields( 'ongekend', $laatsteVersie->getSystemFields( )->getVersie( ) );
            $domainObject = $this->createDeleted( $id, $systemFields );
        }
        return $domainObject;
    }

}
?>
