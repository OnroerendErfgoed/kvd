<?php
/**
 * @package    KVD.dm
 * @subpackage Thes
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Mapper voor het lokaal opslaan van COL (Catalogue of Life) concepten
 *
 * @package    KVD.dm
 * @subpackage Thes
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdm_ThesCOL extends KVDthes_DbConceptMapper
{
    const RETURNTYPE = 'KVDdo_ThesCOL';

    /**
     * getReturnType
     *
     * @return string
     */
    protected function getReturnType(  )
    {
        return self::RETURNTYPE;
    }
}
?>
