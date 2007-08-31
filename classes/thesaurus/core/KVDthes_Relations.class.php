<?php
/**
 * @package KVD.thes
 * @subpackage Core
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_Relations 
 * 
 * @package KVD.thes
 * @subpackage Core
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Relations implements IteratorAggregate, Countable
{
    /**
     * relations 
     * 
     * @var array
     */
    protected $relations;

    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct()
    {
        $this->relations = array( );
    }

    /**
     * addRelation 
     * 
     * @param KVDthes_Relation $relation 
     * @return void
     */
    public function addRelation ( KVDthes_Relation $relation )
    {
        $this->relations[] = $relation;
    }

    /**
     * getIterator 
     * 
     * @return KVDthes_RelationsIterator
     */
    public function getIterator( )
    {
        return new KVDthes_RelationsIterator( $this->relations );
    }

    /**
     * getNTIterator 
     * 
     * @return KVDthes_RelationTypeIterator
     */
    public function getNTIterator()
    {
        return new KVDthes_RelationTypeIterator( $this->relations , KVDthes_Relation::REL_NT );
    }

    /**
     * getBTIterator 
     * 
     * @return KVDthes_RelationTypeIterator
     */
    public function getBTIterator()
    {
        return new KVDthes_RelationTypeIterator( $this->relations , KVDthes_Relation::REL_BT );
    }

    /**
     * getUSEIterator 
     * 
     * @return KVDthes_RelationTypeIterator
     */
    public function getUSEIterator()
    {
        return new KVDthes_RelationTypeIterator( $this->relations , KVDthes_Relation::REL_USE );
    }

    /**
     * getUFIterator 
     * 
     * @return KVDthes_RelationsTypeIterator
     */
    public function getUFIterator()
    {
        return new KVDthes_RelationTypeIterator( $this->relations , KVDthes_Relation::REL_UF );
    }

    /**
     * getRTIterator 
     * 
     * @return KVDthes_RelationsTypeIterator
     */
    public function getRTIterator()
    {
        return new KVDthes_RelationTypeIterator( $this->relations , KVDthes_Relation::REL_RT );
    }

    public function count( )
    {
        return count( $this->relations );
    }
}
?>
