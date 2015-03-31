<?php
/**
 * @package    KVD.thes
 * @subpackage visitor
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_TreeVisitorGraphviz
 *
 * @package    KVD.thes
 * @subpackage visitor
 * @since      12 sep 2007
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_TreeVisitorGraphviz extends KVDthes_AbstractTreeVisitor
{
    /**
     * result
     *
     * @var string
     */
    private $result;

    /**
     * currItem
     *
     * @var KVDthes_Term
     */
    private $currItem;

    /**
     * __construct
     *
     * @param integer $sortOrder    Zie de constanten in KVDthes_Relations
     * @return void
     */
    public function __construct( $sortOrder = KVDthes_TermSorter::SORT_UNSORTED )
    {
        $this->relationsSortOrder = $sortOrder;
        $this->result = "digraph thesaurus {\n";
    }

    /**
     * enterRelations
     *
     * @param KVDthes_Term $node
     * @return boolean
     */
    public function enterRelations( KVDthes_Term $node )
    {
        $this->sortRelations( $node );
        return true;
    }

    /**
     * visit
     *
     * @param KVDthes_Term $node
     * @return void
     */
    public function visit(KVDthes_Term $node)
    {
        $this->currItem = $node;
        $this->result .= $this->currItem->getId( ) . ' [label = "'. $this->currItem ."\"];\n";
        return true;
    }

    /**
     * visitRelation
     *
     * @param KVDthes_Relation $rel
     * @return boolean
     */
    public function visitRelation( KVDthes_Relation $rel )
    {
        $this->result .= $this->currItem->getId( ) . ' -> ' . $rel->getTerm( )->getId( ) . ";\n";
        return true;
    }

    /**
     * enterComposite
     *
     * @param KVDthes_Term $term
     * @return boolean
     */
    public function enterComposite(KVDthes_Term $term)
    {
        $this->depth++;
        return true;
    }

    /**
     * leaveComposite
     *
     * @param KVDthes_Term $term
     * @return boolean
     */
    public function leaveComposite(KVDthes_Term $term)
    {
        $this->depth--;
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
        return $this->result . '}';
    }
}

?>
