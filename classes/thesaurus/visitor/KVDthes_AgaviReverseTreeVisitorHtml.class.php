<?php
/**
 * @package KVD.thes
 * @subpackage Visitor
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_AgaviTreeVisitorHtml 
 *
 * @package KVD.thes
 * @subpackage Visitor
 * @since 19 maart 2007
 * @todo afwerken en zorgen dat het werkt als er meerdere paden naar de root node zijn.
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_AgaviReverseTreeVisitorHtml extends KVDthes_AbstractTreeVisitor
{
    /**
     * depth 
     * 
     * @var integer
     */
	private $depth = 1;

    /**
     * result 
     * 
     * @var string
     */
    private $result = "<ul>\n";

    /**
     * ro 
     * 
     * @var AgaviRouting
     */
    private $ro;

    /**
     * termRoute 
     * 
     * @var string
     */
    private $termRoute;

    /**
     * termIdParameter 
     * 
     * @var string
     */
    private $termIdParameter;

    public function __construct( $ro , $termRoute, $termIdParameter = 'id' )
    {
        $this->ro = $ro;
        $this->termRoute = $termRoute;
        $this->termIdParameter = $termIdParameter;
    }

    /**
     * pad 
     * 
     * @return string
     */
	private function pad()
	{
		$buf = '';
		for ($i=1;$i<=$this->depth;++$i) {
			$buf .= "\t";
		}
		return $buf;
	}

    /**
     * visit 
     * 
     * @param KVDthes_Term $node 
     * @return void
     */
	public function visit(KVDthes_Term $node)
	{
		$this->result .= $this->pad() ."<li>\n";
        $this->depth++;
        $this->result .= $this->pad( ) . '<p><a href="' . $this->ro->gen( $this->termRoute , array ( $this->termIdParameter => $node->getId( ) ) ) .'">'. $node->getTerm() . "</a></p>\n";
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
        if ( $node->hasBTRelations( ) ) {
		    $this->depth++;
            $this->result .= $this->pad( ) . "<ul>\n";
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
        if ( $node->hasBTRelations( ) ) {
            $this->depth--;
            $this->result .= $this->pad( ) . "</ul>\n";
		    $this->depth--;
        }
        $this->result .= $this->pad( ) . "</li>\n";
        return true;
	}

    /**
     * getIterator
     * 
     * @param KVDthes_Relations $relations 
     * @return void
     */
    public function getIterator( KVDthes_Relations $relations )
    {
        return $relations->getBTIterator( );
    }

    /**
     * getResult 
     * 
     * @return string
     */
    public function getResult( )
    {
        return $this->result . "</ul>\n";
    }
}
?>
