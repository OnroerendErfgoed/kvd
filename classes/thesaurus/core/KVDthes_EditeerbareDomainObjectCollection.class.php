<?php
/**
 * @package     Kvd.Thes
 * @subpackage  Core
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_EditeerbareDomainObjectCollection extends KVDdom_EditeerbareDomainObjectCollection
{

    public function getImmutableCollection()
    {
        return new KVDthes_DomainObjectCollection( $this->collection );
    }


}



?>