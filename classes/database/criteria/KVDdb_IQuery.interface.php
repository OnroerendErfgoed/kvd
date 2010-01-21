<?php
/**
 * @package     KVD.database
 * @subpackage  criteria
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdb_IQuery 
 * 
 * @package     KVD.database
 * @subpackage  criteria
 * @since       27 mrt 2009
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDdb_IQuery
{
    /**
     * generateSql 
     * 
     * @return string
     */
    public function generateSql( );
}
?>
