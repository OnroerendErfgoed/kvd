<?php
/**
 * @package KVD.thes
 * @subpackage visitor
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_TreeVisitor 
 * 
 * @package KVD.thes
 * @subpackage visitor
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_AbstractTreeVisitor
{
    /**
     * relations_sort_order 
     * 
     * @var integer
     */
    protected $relationsSortOrder = KVDthes_TermSorter::SORT_UNSORTED;

    /**
     * setRelationsSortOrder 
     * 
     * @param   integer     $order  Een van de constanten uit KVDthes_Relations. 
     * @return void
     */
    public function setRelationsSortOrder( $order )
    {
        $this->relationsSortOrder = $order;
    }

    /**
     * sortRelations 
     * 
     * @return void
     */
    protected function sortRelations( KVDthes_Term $node )
    {
        if ( $this->relationsSortOrder > KVDthes_TermSorter::SORT_UNSORTED ) {
            $node->sortRelations( $this->relationsSortOrder );
        }
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
     * visitRelation 
     * 
     * @param KVDthes_Relation $rel 
     * @return boolean
     */
    public function visitRelation( KVDthes_Relation $rel )
    {
        return true;
    }

    /**
     * enterRelations 
     * 
     * @param KVDthes_Term $node 
     * @return boolean
     */
    public function enterRelations( KVDthes_Term $node )
    {
        return false;
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
        return $relations->getIterator( );
    }
}
?>
