<?php
/**
 * @package KVD.dom
 * @subpackage Systemfields
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_NullSystemFieldsMapper 
 * 
 * @package KVD.dom
 * @subpackage Systemfields
 * @since 1 jul 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_NullSystemFieldsMapper extends KVDdom_AbstractSystemFieldsMapper
{
    /**
     * doLoadSystemFields 
     * 
     * @param StdClass $row 
     * @param string $prefix 
     * @return KVDdom_NullSystemFields
     */
    public function doLoadSystemFields( $row , $prefix = null )
    {
        return KVDdom_ChangeableSystemFields::newNull( );
    }

    /**
     * newNull 
     * 
     * @param integer $versie 
     * @return KVDdom_NullSystemFields
     */
    public static function newNull( $versie )
    {
        return KVDdom_ChangeableSystemFields::newNull( );
    }

    /**
     * updateSystemFields 
     * 
     * @param KVdom_DomainObject $domainObject 
     * @param string $gebruiker 
     * @return void
     */
    public function updateSystemFields( KVdom_DomainObject $domainObject , $gebruiker=null)
    {
        return;
    }
}
?>
