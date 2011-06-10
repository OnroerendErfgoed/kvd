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
 * Kan uris genereren voor object op basis van simpele sting vervanging.
 * 
 * @package    KVD.thes
 * @subpackage serialiser
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Serialiser_Rdf_ConfigUriGenerator implements KVDthes_Serialiser_Rdf_IUriGenerator
{
    /**
     * parameters 
     * 
     * @var array
     */
    protected $parameters;

    /**
     * __construct 
     * 
     * @param array $parameters 
     * @return void
     */
    public function __construct( array $parameters )
    {
        $this->parameters = $parameters;
    }

    /**
     * Maak een uri voor een thesaurus term 
     * 
     * @param KVDthes_term $term 
     * @return string
     */
    public function generateTermUri( KVDthes_term $term )
    {
        $thes_id = $term->getThesaurus( )->getId( );
        if ( isset( $this->parameters['uri_templates'][$thes_id]['term'] ) ) {
            $tpl = $this->parameters['uri_templates'][$thes_id]['term'];
            return sprintf( $tpl, $term->getId( ) );
        } else {
            throw new InvalidArgumentException( 
                sprintf( 'Kan geen Uri genereren voor termen uit thesaurus %d.', 
                         $thes_id )
            );
        }

    }

    /**
     * Maak een uri voor een thesaurus 
     * 
     * @param KVDthes_thesaurus $thes 
     * @return string
     */
    public function generateThesaurusUri( KVDthes_thesaurus $thes )
    {
        $thes_id = $thes->getId( );
        if ( isset( $this->parameters['uri_templates'][$thes_id]['thesaurus'] ) ) {
            $tpl = $this->parameters['uri_templates'][$thes_id]['thesaurus'];
            return $tpl;
        } else {
            throw new InvalidArgumentException( 
                sprintf( 'Kan geen Uri genereren voor thesaurus %d.', $thes->getId( ) )
            );
        }
    }
}
?>
