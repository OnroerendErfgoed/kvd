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
 * KVDthes_DojoTreeVisitorHtml 
 * 
 * @package KVD.thes
 * @subpackage Visitor
 * @since 5 sep 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_DojoTreeVisitorHtml extends KVDthes_AbstractTreeVisitor
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
        $this->result .= $this->pad( ) . '<p><a href="javascript:loadDetail(' . $node->getId( )  . ');">'. $node->getTerm() . "</a></p>\n";
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
        if ( $node->hasNTRelations( ) ) {
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
        return $relations->getNTIterator( );
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