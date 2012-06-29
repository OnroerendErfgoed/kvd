<?php
/**
 * @package    KVD.thes
 * @subpackage util
 * @version    $Id$
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Een renderer die een link naar een concept uit de AAT rendert.
 *
 * @package    KVD.thes
 * @subpackage util
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Util_Match_AatRenderer implements KVDthes_Util_Match_IMatchRenderer
{
    /**
     * parameters
     *
     * @var array
     */
    protected $parameters = array();

    /**
     * __construct
     *
     * @param array $parameters Optional. This renderer comes configured with 
     * default values for aat and aat-ned. If both are present two links will 
     * be generated. If only one is present, only one link will be generated.
     * @return void
     */
    public function __construct(array $parameters = array())
    {
        $parameters = array_merge( 
            array(  
                'url' => 'http://www.getty.edu/vow/AATFullDisplay?find=vioe&logic=AND&note=&english=N&subjectid=%d',
                'aatnedurl' => 'http://browser.aat-ned.nl/%d'
            ),
            $parameters);

        $this->parameters = $parameters;
    }

    /**
     * getHtml
     *
     * @param KVDthes_Match $match
     * @param boolean       $link
     *
     * @return string
     */
    public function getHtml(KVDthes_Match $match, $link = true )
    {
        $omschrijving = $match->getTypeOmschrijving();
        $omschrijving .= ' ' . $match->getMatchable()->getThesaurus()->getNaam();
        $omschrijving .= ':' . $match->getMatchable()->getTerm();
        $omschrijving = KVDhtml_Tools::out($omschrijving);
        if ( !$link || !(isset($this->parameters['url']) || isset($this->parameters['aatnedurl']))) {
            return $omschrijving;
        }
        if ( isset($this->parameters['url']) && isset($this->parameters['aatnedurl']) ) {
            $aat = sprintf( 
                $this->parameters['url'],
                $match->getMatchable()->getId()
            );
            $aatned = sprintf(
                $this->parameters['aatnedurl'],
                $match->getMatchable()->getId()
            );
            return sprintf( 
                '%s (<a href="%s">AAT</a>, <a href="%s">AAT-NED</a>)',
                $omschrijving,
                $aat,
                $aatned
            );
        }
        if ( isset( $this->parameters['url'] ) ) {
            $url = $this->parameters['url'];
        } else {
            $url = $this->parameters['aatnedurl'];
        }
        return sprintf(
            '<a href="%s">%s</a>',
            $url,
            $omschrijving
        );
    }
}
