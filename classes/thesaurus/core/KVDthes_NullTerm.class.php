<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @version    $Id$
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_NullTerm 
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      19 maart 2007
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_NullTerm extends KVDthes_Term
{
    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct( )
    {
        $this->id = 0;
        $this->term = 'Onbepaald';
        $this->relations = new KVDthes_Relations();
    }

    /**
     * setTerm 
     * 
     * @param string $term 
     * @return void
     */
    public function setTerm( $term )
    {
        return;
    }


    /**
     * addRelation 
     * 
     * @param KVDthes_Relation $relation 
     * @return void
     */
    public function addRelation ( KVDthes_Relation $relation )
    {
        return;
    }


    /**
     * accept 
     * 
     * @param KVDthes_TreeVisitor $visitor 
     * @return void
     */
    public function accept( KVDthes_AbstractTreeVisitor $visitor )
    {
        return true;
    }

    /**
     * isPreferredTerm 
     * 
     * @return boolean
     */
    public function isPreferredTerm( )
    {
        return false;
    }

    /**
     * getPreferredTerm 
     * 
     * @return KVDthes_NullTerm
     */
    public function getPreferredTerm( )
    {
        return new KVDthes_NullTerm( );
    }

    /**
     * isNull 
     * 
     * @return boolean
     */
    public function isNull()
    {
        return true;
    }
}
?>
