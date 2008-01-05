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
 * KVDthes_CountSiblingNodesVisitor 
 * 
 * Count the number of sibling nodes a node has. This does not only count the direct descendants but
 * all their descendants as well.
 * @package KVD.thes
 * @subpackage visitor
 * @since 23 aug 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_CountSiblingNodesVisitor extends KVDthes_AbstractTreeVisitor
{
    /**
     * count 
     * 
     * Count starts at -1 because a visitor always visits the root node.
     * @var integer
     */
    private $count = -1;

    /**
     * getCount 
     * 
     * @return integer
     */
    public function getCount( )
    {
        return $this->count;
    }

    /**
     * visit 
     * 
     * @param KVDthes_Term $node 
     * @return void
     */
	public function visit(KVDthes_Term $node)
    {
        $this->count++;
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
     * reset 
     * 
     * @return void
     */
    public function reset( )
    {
        $this->count = -1;
    }
}
?>
