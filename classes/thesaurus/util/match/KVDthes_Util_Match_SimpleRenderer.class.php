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
 * Een renderer die een link naar een concept rendert.
 *
 * Deze renderer gaat er van uit dat er een enkele url is waarin 1 parameter id 
 * moet toegevoegd worden.
 *
 * @package    KVD.thes
 * @subpackage util
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Util_Match_SimpleRenderer implements KVDthes_Util_Match_IMatchRenderer
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
     * @param array $parameters Only parameter required is url. This parameter 
     * must be in sprintf syntax en contain a placeholder that can be replaced 
     * with an identifier in a thesaurus. eg. http://thesaurs/%s
     * @return void
     */
    public function __construct(array $parameters = array())
    {
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
        $omschrijving .= $match->getMatchable()->getThesaurus()->getNaam();
        $omschrijving .= ':' . $match->getMatchable()->getTerm();
        $omschrijving = KVDhtml_Tools::out($omschrijving);
        if ( !$link || !isset($this->parameters['url'] )) {
            return $match->getTypeOmschrijving() . ' ' . $omschrijving;
        }
        return sprintf( 
            '%s <a href="%s">%s</a>',
            $match->getTypeOmschrijving(),
            sprintf( $this->parameters['url'], $match->getMatchable()->getId()),
            $omschrijving
        );
    }
}
