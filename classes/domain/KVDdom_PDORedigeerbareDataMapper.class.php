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
    protected $sfvelden = "gebruiker, bewerkt_op, versie, gecontroleerd";

    /**
     * @return string Een SQL statement om een record goed te keuren. De parameter id moet nog ingevuld worden.
     */
    protected function getApproveRecordStatement( )
    {
        return  "UPDATE " . $this->tabel . 
                " SET gecontroleerd = true" .
                " WHERE " . $this->id . " = ?";
    }

    /**
     * @return string Een SQL statement om alle gelogde versies van een record goed te keuren. Enkel de parameter id moet nog worden ingevuld.
     */
    protected function getApproveLogRecordsStatement( )
    {
        return  "UPDATE " . $this->logtabel . 
                " SET gecontroleerd = true" .
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
        return new KVDdom_SystemFields (    $row->$gebruiker,
                                            $row->$versie ,
                                            strtotime( $row->$bewerktOp ),
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
    protected function doSetSystemFields($stmt, $domainObject, $startIndex )
    {
        $systemFields = $domainObject->getSystemFields();
        $stmt->bindValue( $startIndex++ , $systemFields->getGebruikersNaam( ) , PDO::PARAM_STR );
        $stmt->bindValue( $startIndex++ , $systemFields->getBewerktOp( ) , PDO::PARAM_STR );
        $stmt->bindValue( $startIndex++ , $systemFields->getTargetVersie( ) , PDO::PARAM_INT );
        $stmt->bindValue( $startIndex++ , $systemFields->getGecontroleerd( ) , PDO::PARAM_BOOL );
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
        $stmt->bindValue(1, $domainObject->getId( ) , PDO::PARAM_INT );
        try {
            $stmt->execute( );
        } catch ( PDOException $e) {
            throw new Exception ( 'Het record kan niet goedgekeurd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
        $stmt = $this->_conn->prepare (  $this->getApproveLogRecordsStatement( ) );
        $stmt->bindValue( 1, $domainObject->getId( ), PDO::PARAM_INT );
        try {
            $stmt->execute( );
        } catch (PDOException $e) {
            throw new Exception ( 'Het record kan niet goedgekeurd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
    }

}
?>
