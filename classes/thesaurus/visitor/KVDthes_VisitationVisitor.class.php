<?php
/**
 * @package    KVD.thes
 * @subpackage visitor
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_VisitationVisitor
 *
 * @package    KVD.thes
 * @subpackage visitor
 * @since      23 aug 2007
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_VisitationVisitor extends KVDthes_AbstractTreeVisitor
{
    /**
     * count
     *
     * @var integer
     */
    private $count;

    /**
     * depth
     *
     * @var integer
     */
    private $depth;

    /**
     * visitation
     *
     * @var array
     */
    private $visitation;

    /**
     * __construct
     *
     * @param integer $sortOrder
     * @return void
     */
    public function __construct( $sortOrder = KVDthes_TermSorter::SORT_UNSORTED)
    {
        $this->count = 0;
        $this->depth = 0;
        $this->visitation = array( );
        $this->relationsSortOrder = $sortOrder;
    }

    /**
     * visit
     *
     * @param KVDthes_Term $node
     * @return void
     */
    public function visit(KVDthes_Term $node)
    {
        return true;
    }

    /**
     * enterComposite
     *
     * @param KVDthes_Term $node
     * @return boolean
     */
    public function enterComposite(KVDthes_Term $node)
    {
        $this->sortRelations( $node );
        $this->visitation[$node->getId( )]['id'] = $node->getId( );
        $this->visitation[$node->getId( )]['left'] = ++$this->count;
        $this->visitation[$node->getId( )]['depth'] = ++$this->depth;
        return true;
    }

    /**
     * leaveComposite
     *
     * @param KVDthes_Term $node
     * @return true
     */
    public function leaveComposite(KVDthes_Term $node)
    {
        $this->visitation[$node->getId( )]['right'] = ++$this->count;
        if ( $this->visitation[$node->getId( )]['depth'] != $this->depth-- ) {
            throw new Exception ( 'Depth should be equal' );
        };
        return true;
    }

    /**
     * getIterator
     *
     * @param KVDthes_Relations $relations
     * @return KVDthes_RelationsIterator
     */
    public function getIterator( KVDthes_Relations $relations )
    {
        return $relations->getNTIterator( );
    }

    /**
     * getResult
     *
     * @return array
     */
    public function getResult( )
    {
        return $this->visitation;
    }
}
?>
