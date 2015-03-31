<?php
/**
 * @package KVD.thes
 * @subpackage visitor
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_AgaviTreeVisitorHtml
 *
 * @package KVD.thes
 * @subpackage visitor
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_AgaviTreeVisitorHtml extends KVDthes_AbstractTreeVisitor
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

    /**
     * __construct
     *
     * @param AgaviRouting  $ro
     * @param string        $termRoute
     * @param string        $termIdParameter
     * @param integer       $relationsSortOrder
     * @return void
     */
    public function __construct( AgaviRouting $ro , $termRoute, $termIdParameter = 'id', $relationsSortOrder = KVDthes_TermSorter::SORT_UNSORTED )
    {
        $this->ro = $ro;
        $this->termRoute = $termRoute;
        $this->termIdParameter = $termIdParameter;
        $this->relationsSortOrder = $relationsSortOrder;
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
        $this->result .= $this->pad( ) . '<p><a href="' . $this->ro->gen( $this->termRoute , array ( $this->termIdParameter => $node->getId( ) ) ) .'">'. $node->getQualifiedTerm() . "</a></p>\n";
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
