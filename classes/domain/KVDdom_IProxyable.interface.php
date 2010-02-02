<?php
/**
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_IProxyable 
 * 
 * Deze interface kan gebruikt worden om aan te geven of een object al dan niet volledig geladen is.
 * @package     KVD.dom
 * @since       okt 2008
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDdom_IProxyable
{
    /**
     * isProxy 
     * 
     * Is dit een echt domainobject of is dit gewoon een proxy object dat enkel beschikt over het type en het id van het domainobject?
     * @return  boolean     False indien het een echt domainobject is, true indien het een proxy object is.
     */
    public function isProxy( );

    /**
     * newProxy 
     * 
     * Maak een nieuw proxyobject voor dit id.
     * @param   integer             $id     Id nummer dat het proxy object moet hebben.
     * @return  KVDdom_IProxyable   Een object dat de proxyable interface implementeert.
     */
    public static function newProxy( $id );
}
?>
