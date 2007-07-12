<?php    
/**
 * @package KVD.dom
 * @version $Id: KVDdom_PDORedigeerbareDataMapper.class.php 288 2007-03-19 10:44:22Z vandaeko $
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_RedigeerbareSystemFieldsMapper 
 * 
 * @package KVD.dom
 * @since 9 jul 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_LegacySystemFieldsMapper extends KVDdom_AbstractSystemFieldsMapper
{

    /**
     * De velden die nodig zijn voor het SystemFields object.
     * 
     * @var string
     */
    protected $systemFields = "gebruiker, bewerkt_op, versie, gecontroleerd, gecontroleerd_door, gecontroleerd_op";

    /**
     * Laad een SystemFields object op basis van een rij uit de databank
     *
     * @param StdClass $row Een StdClass object dat door PDO wordt afgeleverd via fetchRow. Dit object moet de nodige velden bevatten om een Systemfields object mee samen te kunnen stellen.
     * @param string $prefix Moet er voor zorgen dat bij een join van 2+ tabellen er 2+ systemfields objecten geladen kunnen worden. Standaard wordt er van uitgegaan dat er geen prefix nodig is.
     * @return KVDdom_RedigeerbareSystemFields
     */
    public function doLoadSystemFields( $row , $prefix = null)
    {
        if ($prefix !== null) {
            $prefix = $prefix . '_';
        }
        $gebruiker = $prefix . 'gebruiker';
        $bewerktOp = $prefix . 'bewerkt_op';
        $versie = $prefix . 'versie';
        $gecontroleerd = $prefix . 'gecontroleerd';
        $gecontroleerdDoor = $prefix . 'gecontroleerd_door';
        $gecontroleerdOp = $prefix . 'gecontroleerd_op';
        return new KVDdom_SystemFields( $row->$gebruiker,
                                        $row->$versie,
                                        strtotime( $row->$bewerktOp ),
                                        $row->$gecontroleerd,
                                        $row->$gecontroleerdDoor,
                                        strtotime( $row->$gecontroleerdOp ) );
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
        if ( !$domainObject->hasSystemFields( ) ) {
            throw new LogicException ( 'Kan de systemFields van een object dat geen systemFields heeft niet instellen op een statement.');
        }
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
     * newNull 
     * 
     * @param integer $versie 
     * @return KVDdom_RedigeerbareSystemFields
     */
    public function newNull( $versie = 0 )
    {
        return new KVDdom_SystemFields( 'ongekend', $versie );
    }

    /**
     * updateSystemFields 
     * 
     * @param KVDdom_DomainObject $domainObject 
     * @param string $gebruiker 
     * @return void
     */
    public function updateSystemFields( KVDdom_DomainObject $domainObject , $gebruiker = null )
    {
        if ( !$domainObject->hasSystemFields( ) ) {
            throw new LogicException ( 'Kan de systemFields van een object dat geen systemFields heeft niet updaten.');
        }
        $domainObject->getSystemFields( )->updateSystemFields( $gebruiker );
    }

}
?>
