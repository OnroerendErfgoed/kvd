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
     * Constante om aan te geven dat we willen sorteren op id. 
     */
    const SORT_ID = 1;

    /**
     * Constante om aan te geven dat we willen sorteren op de term. 
     */
    const SORT_TERM = 2;

    /**
     * Constant om aan te geven dat we willen sorteren op de Qualified Term. 
     */
    const SORT_QUALTERM = 3;

    /**
     * Constante om aan te geven dat we willen sorteren op de SortKey van een term. 
     */
    const SORT_SORTKEY = 4;

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

    /**
     * count 
     * 
     * Geeft het aantal relaties terug.
     * @return integer
     */
    public function count( )
    {
        return count( $this->relations );
    }

    /**
     * sort 
     * 
     * Sorteer de relaties op een bepaalde manier, standaard wordt er alfabetisch gesorteerd op de term.
     * @param integer $sortMethod 
     * @return void
     */
    public function sort( $sortMethod = self::SORT_TERM )
    {
        usort( $this->relations, array ( $this, 'compareTerm' ) );
    }

    /**
     * compareRelations 
     * 
     * @param   string              $comparedMethod 
     * @param   KVDthes_Relation    $a 
     * @param   KVDthes_Relation    $b 
     * @return  integer                                 -1, 0 of 1 indien de a kleiner dan, gelijk aan of groter dan b is.
     */
    private function compareRelations( $comparedMethod, KVDthes_Relation $a, KVDthes_Relation $b )
    {
        if ( $a->getTerm( )->$comparedMethod( ) < $b->getTerm( )->$comparedMethod( ) ) {
            return -1;
        }
        if ( $a->getTerm( )->$comparedMethod( ) > $b->getTerm( )->comparedMethod( ) ) {
            return 1;
        }
        return 0;
    }

    /**
     * compareId 
     * 
     * @param   KVDthes_Relation $a 
     * @param   KVDthes_Relation $b 
     * @return  integer
     */
    private function compareId( KVDthes_Relation $a, KVDthes_Relation $b )
    {
        return compareRelations( 'getId', $a, $b);
    }

    /**
     * compareTerm 
     * 
     * @param   KVDthes_Relation $a 
     * @param   KVDthes_Relation $b 
     * @return  integer
     */
    private function compareTerm( KVDthes_Relation $a, KVDthes_Relation $b )
    {
        return compareRelations( 'getTerm', $a, $b);
    }

    /**
     * compareQualTerm 
     * 
     * @param   KVDthes_Relation $a 
     * @param   KVDthes_Relation $b 
     * @return  integer
     */
    private function compareQualTerm( KVDthes_Relation $a, KVDthes_Relation $b )
    {
        return compareRelations( 'getQualifiedTerm', $a, $b);
    }

    /**
     * compareSortKey 
     * 
     * @param   KVDthes_Relation $a 
     * @param   KVDthes_Relation $b 
     * @return  integer
     */
    private function compareSortKey( KVDthes_Relation $a, KVDthes_Relation $b )
    {
        throw new LogicException ( 'Sorteren op SortKey werd nog niet geimplementeerd.' );
        //return compareRelations( 'getSortKey', $a, $b);
    }
}
?>
