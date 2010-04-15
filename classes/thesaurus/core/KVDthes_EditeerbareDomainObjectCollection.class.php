<?php
/**
 * @package KVD.thes
 * @subpackage Core
 * @version     $Id$
 * @copyright 2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Dieter Standaert <dieter.standaert@hp.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_EditeerbareDomainObjectCollection 
 * 
 * Een KVDdom_EditeerbareDomainObjectCollection die een KVDthes_DomainObjectCollection als
 * immutable collection teruggeeft.
 * @package KVD.thes
 * @subpackage Core
 * @since 14 april 2010
 * @copyright 2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Dieter Standaert <dieter.standaert@hp.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_EditeerbareDomainObjectCollection extends KVDdom_EditeerbareDomainObjectCollection
{

    /**
     * getImmutableCollection 
     *
     * @return KVDthes_DomainObjectCollection
     */
    public function getImmutableCollection()
    {
        return new KVDthes_DomainObjectCollection( $this->collection );
    }


}



?>