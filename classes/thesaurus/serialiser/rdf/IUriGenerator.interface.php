<?php
/**
 * @package    KVD.thes
 * @subpackage serialiser
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Interface die plugins moeten implementeren die kunnen gebruikt worden bij 
 * het serialiseren van thesauri naar rdf.
 * 
 * @package    KVD.thes
 * @subpackage serialiser
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDthes_Serialiser_Rdf_IUriGenerator
{
    /**
     * Maak een uri aan waaronder de term gekend mag zijn.
     * 
     * @param KVDthes_Term  $term
     * @return string
     */
    public function generateTermUri( KVDthes_Term $term );

    /**
     * Maak een uri aan waaronder de thesaurus gekend mag zijn.
     * 
     * @param KVDthes_Thesaurus  $thes
     * @return string
     */
    public function generateThesaurusUri( KVDthes_Thesaurus $thes );
}
?>
