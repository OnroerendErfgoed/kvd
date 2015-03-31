<?php
/**
 * @package KVD.thes
 * @subpackage mapper
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_IDataMapper
 *
 * @package KVD.thes
 * @subpackage mapper
 * @since 9 jan 2008
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
interface KVDthes_IDataMapper extends KVDdom_IDataMapper
{
    /**
     * findAll
     *
     * Zoek alle termen in een thesaurus, zonder dat ze in een boom- of grafe-structuur worden geladen.
     * @return KVDdom_DomainObjectCollection
     */
    public function findAll( );

    /**
     * findRoot
     *
     * Zoek de root noot van deze thesaurus. Dit is dus de node die zelf geen BT heeft.
     * @return KVDthes_Term
     */
    public function findRoot( );
}
?>
