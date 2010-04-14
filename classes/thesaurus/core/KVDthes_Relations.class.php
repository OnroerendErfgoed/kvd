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
     * @param   KVDthes_Relation     $relation 
     * @return  boolean         True indien de relatie werd toegevoegd, false indien ze al aanwezig was.
     */
    public function addRelation ( KVDthes_Relation $relation )
    {
        if ( !$this->hasRelation( $relation ) ) {
            $this->relations[] = $relation;
            $this->currentSort = KVDthes_TermSorter::SORT_UNSORTED;
            return true;
        }
        return false;
    }

    /**
     * removeRelation 
     * 
     * @param   KVDthes_Relation     $relation 
     * @return  boolean             True indien de relatie werd verwijderd, false indien ze niet aanwezig was en dus niet verwijderd kon worden.
     */
    public function removeRelation ( KVDthes_Relation $relation )
    {
        if ( ( $key = array_search ( $relation, $this->relations ) ) !== false ) {
            unset( $this->relations[$key] );
            //array herindexeren zodat de iterator blijft werken.
            $this->relations = array_values( $this->relations );
            return true;
        }
        return false;
    }

    /**
     * hasRelation 
     * 
     * @param   KVDthes_Relation    $relation 
     * @return  boolean
     */
    public function hasRelation( KVDthes_Relation $relation ) 
    {
        return in_array( $relation, $this->relations );
    }

    /**
     * getImmutableCollection 
     * 
     * @return  KVDdom_DomainObjectCollection
     */
    public function getImmutableCollection( )
    {
        return new KVDdom_DomainObjectCollection( $this->relations );
    }

    /**
     * getIterator 
     * 
     * @param   $type   Type van relations of null om alle relations te krijgen.
     * @return  KVDthes_RelationsIterator
     */
    public function getIterator( $type = null)
    {
        if ( $type == null ) {
            return new KVDthes_RelationsIterator( $this->relations );
        } else {
            return new KVDthes_RelationTypeIterator( $this->relations, $type );
        }
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
     * @param   $type       Een type constante uit {@link KVDthes_Relation} of null.
     * @return  integer     Het totaal aantal relaties of indien er een type werd opgegeven, het aantal relaties van dit type.
     */
    public function count( $type = null )
    {
        if ( $type === null ) {
            return count( $this->relations );
        } else {
            $it = new KVDthes_RelationTypeIterator( $this->relations, $type );
            return count( $it );
        }
    }

    /**
     * sort 
     * 
     * Sorteer de relaties op een bepaalde manier, standaard wordt er alfabetisch gesorteerd op de term.
     * @param integer $sortMethod 
     * @return void
     */
    public function sort( $sortMethod = KVDthes_TermSorter::SORT_TERM )
    {
        if ( $sortMethod > KVDthes_TermSorter::SORT_UNSORTED && array_key_exists( $sortMethod, KVDthes_TermSorter::$methodMap) ) {
            usort( $this->relations, array ( new KVDthes_TermSorter($sortMethod), "compareRelations" ) );
        }
    }


}
?>
