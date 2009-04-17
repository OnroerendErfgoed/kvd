<?php
/**
 * @package     KVD.thes
 * @subpackage  visitor
 * @version     $Id: KVDthes_TreeVisitorHtml.class.php 459 2008-07-15 08:42:44Z vandaeko $
 * @copyright   2004-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_RenderingTreeVisitor
 *
 * Visitor die een thesaurus-structuur bewandelt en deze omzet naar iets anders. Dit omzetten gaat via
 * een aparte class die de {@link KVDthes_ITermRenderer} interface moet implementeren.
 * @package     KVD.thes
 * @subpackage  visitor
 * @since       19 apr 2009
 * @copyright   2004-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_RenderingTreeVisitor extends KVDthes_AbstractTreeVisitor
{
    /**
     * renderer 
     * 
     * @var KVDthes_ITermRenderer
     */
    private $renderer;

    /**
     * depth 
     * 
     * @var integer
     */
	private $depth;

    /**
     * result 
     * 
     * @var string
     */
    private $result;

    /**
     * __construct 
     * 
     * @param KVDthes_ITermRenderer     $renderer   Instantie van een class die de termen kan renderen.  
     * @param integer                   $sortOrder  Zie de constanten in KVDthes_Relations. Vooraleer de relaties van een term
     *                                              zullen gevolgd worden zullen ze op deze wijze gesorteerd worden.
     * @return void
     */
    public function __construct( KVDthes_ITermRenderer $renderer, $sortOrder = KVDthes_Relations::SORT_UNSORTED )
    {
        $this->renderer = $renderer;
        $this->relationsSortOrder = $sortOrder;
        $this->clear( );
    }

    public function clear( )
    {
        $this->depth = 1;
        $this->result = $this->renderer->getResultStart( );
    }

    /**
     * pad 
     * 
     * @return string
     */
	private function pad()
	{
        return str_repeat( "\t" , $this->depth );
	}

    /**
     * visit 
     * 
     * @param KVDthes_Term $node 
     * @return void
     */
	public function visit(KVDthes_Term $node)
	{
		$this->result .= $this->pad() . $this->renderer->getVisitStart( ) . "\n";
        $this->depth++;
        $this->result .= $this->pad( ) . $this->renderer->renderTerm( $node ) . "\n";
        $this->depth--;
        return true;
    }

    /**
     * enterComposite 
     * 
     * @return boolean
     */
	public function enterComposite(KVDthes_Term $node)
	{
        if ( $node->hasNTRelations( ) ) {
            $this->sortRelations( $node );
		    $this->depth++;
            $this->result .= $this->pad( ) . $this->renderer->getCompositeStart( ) . "\n";
            $this->depth++;
        }
		return true;
	}

    /**
     * leaveComposite 
     * 
     * @return boolean
     */
	public function leaveComposite(KVDthes_Term $node)
	{
        if ( $node->hasNTRelations( ) ) {
            $this->depth--;
            $this->result .= $this->pad( ) . $this->renderer->getCompositeEnd( ) . "\n";
		    $this->depth--;
        }
        $this->result .= $this->pad( ) . $this->renderer->getVisitEnd( ) . "\n";
        return true;
	}

    /**
     * getIterator
     * 
     * @param   KVDthes_Relations $relations 
     * @return  KVDthes_RelationsIterator
     */
    public function getIterator( KVDthes_Relations $relations )
    {
        return $relations->getNTIterator( );
    }

    /**
     * getResult 
     * 
     * @return string
     */
    public function getResult( )
    {
        return $this->result . $this->renderer->getResultEnd( ) . "\n";
    }
}
?>
