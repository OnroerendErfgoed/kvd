<?php
/**
 * @package    KVD.util
 * @subpackage auth
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 */

/**
 * Editeerbare collectie van rollen
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      30 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 */
class KVDutil_Auth_RolCollectie extends KVDdom_DomainObjectCollection
{
    /**
     * __construct
     *
     * @param array     $collection
     * @return void
     */
    public function __construct($collection)
    {
        parent::__construct($collection, "KVDutil_Auth_Rol" );
    }
}
?>
