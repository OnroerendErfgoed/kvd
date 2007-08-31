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
 * KVDthes_TreeVisitor 
 * 
 * @package KVD.thes
 * @subpackage Visitor
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_AbstractTreeVisitor
{
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

    public function visitRelation( KVDthes_Relation $rel )
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
