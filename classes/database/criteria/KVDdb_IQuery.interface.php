<?php
/**
 * @package    KVD.database
 * @subpackage criteria
 * @copyright  2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Interface die alle query objecten gemeenschappelijk hebben.
 *
 * @package    KVD.database
 * @subpackage criteria
 * @since      27 mrt 2009
 * @copyright  2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
interface KVDdb_IQuery
{
    /**
     * generateSql
     *
     * @return string
     */
    /**
     * generateSql
     *
     * @param mixed $mode Moet de query geparameteriseerd gebruikt worden of
     *                    moeten de waarden meteen worden ingevuld.
     * @param mixed $dbType Om welk soort databank gaat het?
     * @return void
     */
    public function generateSql(
                        $mode = KVDdb_Criteria::MODE_FILLED,
                        $dbType = KVDdb_Criteria::DB_MYSQL );

    /**
     * getValues
     *
     * Indien in de geparameteriseerde modus werken moet deze functie alle
     * waarden teruggeven die achteraf moeten ingevuld worden.
     * @since   1.4
     * @return  array
     */
    public function getValues(  );
}
?>
