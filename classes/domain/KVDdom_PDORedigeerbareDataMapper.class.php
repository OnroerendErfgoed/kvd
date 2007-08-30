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
        $sql =  $this->getSelectStatement( ) .
                " WHERE " . $this->tabel . ".gecontroleerd = false ";
        $this->_sessie->getSqlLogger( )->log( $sql );
        return $sql;
    }

    /**
     * @return string Een SQL statement om alle records te zoeken die wel nog in de log-tabellen zitten maar niet meer in de gewone tabellen. Heeft geen parameters nodig.
     */
    protected function getFindVerwijderdeStatement( )
    {
        return  $this->getLogSelectStatement( ) . 
                " WHERE NOT EXISTS ( SELECT 1 FROM " . $this->tabel . " WHERE id = log_" . $this->id . ") " .
                " AND versie = ( SELECT max( versie ) FROM " . $this->logtabel . " AS log WHERE log.id = log_" . $this->id . ")";
    }

    /**
     * getRevertDeleteStatement 
     * 
     * @return string Een SQL statement om de laatste gelogde versie van een object dat verwijderd werd terug te plaatsen.
     */
    protected function getRevertDeleteStatement( )
    {
        return  "INSERT INTO " . $this->tabel . 
                " ( SELECT * FROM " . $this->logtabel . 
                " WHERE log_" . $this->id . " = ? AND" .
                " versie = ( SELECT MAX( versie ) FROM " . $this->logtabel . " WHERE id = ? ) )";
    }

    /**
     * getDeleteLastLogged 
     * 
     * @return string SQL Statement dat de laaste gelogde versie verwijdert.
     */
    protected function getDeleteLastLoggedStatement( )
    {
        return  "DELETE FROM " . $this->logtabel . 
                " WHERE id = ? AND versie = ( SELECT MAX( versie ) FROM " . $this->logtabel . " WHERE id = ? )";
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
     * getRedactieOrderStatement 
     * 
     * @return string SQL statement dat de redactie kan sorteren.
     */
    protected function getRedactieOrderStatement( )
    {
        return " ORDER BY bewerkt_op DESC";    
    }
    
    /**
     * Zoek alle records van deze datamapper die nog niet gecontroleerd zijn.
     * @param string $orderField
     * @param string $orderDirection 
     * @return KVDdom_DomainObjectCollection
     */
    public function findTeRedacteren( $orderField = null , $orderDirection = null )
    {
        $orderStmt = is_null( $orderField ) ? '' : $this->getOrderClause( $orderField , $orderDirection );
        $stmt = $this->_conn->prepare ( $this->getFindTeRedacterenStatement() . $orderStmt );
        return $this->executeFindMany ( $stmt );
    }

    /**
     * abstractFindVerwijderde 
     * 
     * Zoek alle verwijderde records in de log tabellen.
     * @return KVDdom_DomainObjectCollection
     */
    public function findVerwijderde()
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
        try {
            $stmt = $this->_conn->prepare( $this->getRevertDeleteStatement( ) );
            $stmt->bindValue( 1 , $domainObject->getId( ) , PDO::PARAM_INT );
            $stmt->bindValue( 2 , $domainObject->getId( ) , PDO::PARAM_INT );
            $stmt->execute( );
            $stmt2 = $this->_conn->prepare( $this->getDeleteLastLoggedStatement( ) );
            $stmt2->bindValue( 1 , $domainObject->getId( ) , PDO::PARAM_INT );
            $stmt2->bindValue( 2 , $domainObject->getId( ) , PDO::PARAM_INT );
            $stmt2->execute( );
        } catch ( PDOException $e ) {
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
            $systemFields = $this->systemFieldsMapper->newNull( $laatsteVersie->getSystemFields( )->getVersie( ) );
            $domainObject = $this->createDeleted( $id, $systemFields );
        }
        return $domainObject;
    }

}
?>
