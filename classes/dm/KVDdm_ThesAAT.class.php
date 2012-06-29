<?php
/**
 * @package    KVD.dm
 * @subpackage Thes
 * @version    $Id$
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Mapper voor het lokaal opslaan van AAT concepten
 *
 * @package    KVD.dm
 * @subpackage Thes
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdm_ThesAAT extends KVDthes_DbConceptMapper
{
    const RETURNTYPE = 'KVDdo_ThesAAT';

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
