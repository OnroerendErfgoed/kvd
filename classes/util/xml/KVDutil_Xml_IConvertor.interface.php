<?php
/**
 * @package     KVD.util
 * @subpackage  xml
 * @version     $Id$
 * @copyright   2008-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author      Bram Goessens <bram.goessens@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_Xml_IConvertor 
 *
 * Deze interface kan gebruikt worden door andere classes die het gebruik van 
 * @link{KVDutil_Xml_DomainObjectProcessor} classes regelen.
 *
 * @package     DIBE.util
 * @subpackage  lijsten
 * @since       15 feb 2008
 * @copyright   2008-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author      Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDutil_Xml_IConvertor
{
    const STATUS_NONE = 0;
    const STATUS_INSERT_CREATED = 1;
    const STATUS_INSERT_COMMITED = 2;
    const STATUS_UPDATE_CREATED = 3;
    const STATUS_UPDATE_COMMITED = 4;
    
    /**
     * commit 
     *
     * Verwerk de aangemaakte of aangepaste objecten
     *
     * @throws <b>KVDutil_Xml_Exception</b> - Indien er niets is om te verwerken
     * @return void
     */
    public function commit( );

    /**
     * getXml 
     * 
     * @return SimpleXMLElement
     */
    public function getXml( );

    /**
     * getWarnings 
     * 
     * @return array
     */
    public function getWarnings();

}
?>
