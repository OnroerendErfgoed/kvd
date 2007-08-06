<?php
/**
 * @package KVD.dom
 * @subpackage systemfields
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_ChangeableSystemFieldsMapper 
 * 
 * @package KVD.dom
 * @subpackage systemfields
 * @since 27 jun 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_ChangeableSystemFieldsMapper extends KVDdom_AbstractSystemFieldsMapper
{
    /**
     * systemFields 
     * 
     * @var string
     */
    protected $systemFields = "aangemaakt_door, aangemaakt_op, versie, bewerkt_door, bewerkt_op";

    /**
     * doLoadSystemFields 
     * 
     * @param StdClass  $row 
     * @param string    $prefix 
     * @return KVDdom_ChangeableSystemFields
     */
    public function doLoadSystemFields( $row , $prefix = null )
    {
        if ( $prefix !== null ) {
            $prefix .= '_';
        }
        $aangemaaktOp = $prefix . 'aangemaakt_op';
        $aangemaaktDoor = $prefix . 'aangemaakt_door';
        $bewerktOp = $prefix . 'bewerkt_op';
        $bewerktDoor = $prefix . 'bewerkt_door';
        $versie = $prefix . 'versie';
        return new KVDdom_ChangeableSystemFields(   $row->$aangemaaktDoor,
                                                    new DateTime( $row->$aangemaaktOp),
                                                    $row->$versie,
                                                    $row->$bewerktDoor,
                                                    new DateTime( $row->$bewerktOp ) );
    }

    /**
     * doSetSystemFields 
     * 
     * @param PDOStatement                  $stmt 
     * @param KVDdom_DomainObject           $domaiObject
     * @param integer                       $startIndex 
     * @return integer      Volgende te gebruiken index.
     */
    public function doSetSystemFields( $stmt , $domainObject , $startIndex )
    {
        if ( !$domainObject->hasSystemFields( ) ) {
            throw new LogicException ( 'Kan de systemFields van een object dat geen systemFields heeft niet instellen op een statement.');
        }
        $stmt->bindValue( $startIndex++, $domainObject->getSystemFields( )->getAangemaaktDoor( ), PDO::PARAM_STR);
        $stmt->bindValue( $startIndex++, $domainObject->getsystemFields( )->getAangemaaktOp( )->format( DATE_ISO8601), PDO::PARAM_STR);
        $stmt->bindValue( $startIndex++, $domainObject->getSystemFields( )->getTargetVersie( ), PDO::PARAM_INT);
        $stmt->bindValue( $startIndex++, $domainObject->getSystemFields( )->getBewerktDoor( ), PDO::PARAM_STR);
        $stmt->bindValue( $startIndex++, $domainObject->getSystemFields( )->getBewerktOp( )->format( DATE_ISO8601), PDO::PARAM_STR);
        return $startIndex;
    }

    /**
     * newNull
     * 
     * @param integer $versie 
     * @return KVDdom_ChangeableSystemFields
     */
    public function newNull( $versie = 0 )
    {
        return new KVDdom_ChangeableSystemFields( 'anoniem' , null, $versie );
    }

    /**
     * updateSystemFields 
     * 
     * @param KVDdom_DomainObject $domainObject 
     * @param string $gebruiker 
     * @throws LogicException   Indien het domainObject geen systemFields heeft.
     * @return void
     */
    public function updateSystemFields( KVDdom_DomainObject $domainObject , $gebruiker=null)
    {
        if ( !$domainObject->hasSystemFields( ) ) {
            throw new LogicException ( 'Kan de systemFields van een object dat geen systemFields heeft niet updaten.');
        }
        $domainObject->getSystemFields()->setUpdated( $gebruiker );
    }
}
?>
