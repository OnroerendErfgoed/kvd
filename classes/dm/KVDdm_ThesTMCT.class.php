<?php
/**
 * @package    KVD.dm
 * @subpackage Thes
 * @copyright  2013 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Mapper voor het lokaal opslaan van TMCT concepten
 *
 * @package    KVD.dm
 * @subpackage Thes
 * @since      1.8
 * @copyright  2013 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdm_ThesTMCT extends KVDthes_DbConceptMapper
{
    const RETURNTYPE = 'KVDdo_ThesTMCT';

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
