<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @copyright  2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Dieter Standaert <dieter.standaert@hp.com>
 */

/**
 * KVDthes_EditeerbareDomainObjectCollection 
 * 
 * Een KVDdom_EditeerbareDomainObjectCollection die een KVDthes_DomainObjectCollection als
 * immutable collection teruggeeft.
 *
 * @package    KVD.thes
 * @subpackage core
 * @since      14 april 2010
 * @copyright  2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Dieter Standaert <dieter.standaert@hp.com>
 */
abstract class KVDthes_EditeerbareDomainObjectCollection 
                    extends KVDdom_EditeerbareDomainObjectCollection
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
