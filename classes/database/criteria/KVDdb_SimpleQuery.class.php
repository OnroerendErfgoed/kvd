<?php
/**
 * @package     KVD.database
 * @subpackage  criteria
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version     $Id$
 */

/**
 * @package     KVD.database
 * @subpackage  criteria
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since       28 aug 2006
 */
class KVDdb_SimpleQuery
{

    /**
     * fields 
     * 
     * @var array
     */
    private $fields;

    /**
     * table 
     * 
     * @var string
     */
    private $table;

    /**
     * joins 
     * 
     * @var array
     */
    private $joins;

    /**
     * criteria 
     * 
     * @var KVDdb_Criteria
     */
    private $criteria;

    /**
     * distinct 
     * 
     * @var boolean
     */
    private $distinct;

    /**
     * __construct 
     * 
     * @param array             $fields 
     * @param string            $table 
     * @param KVDdb_Criteria    $criteria 
     * @param boolean           $distinct   Of enkel de unieke waarden gezocht mogen worden, standaard wordt alles gezocht.
     * @return void
     */
    public function __construct ( $fields , $table , KVDdb_Criteria $criteria = null, $distinct = false )
    {
        $this->fields = $fields;
        $this->table = $table;
        $this->joins = array( );
        $this->criteria = is_null( $criteria ) ? new KVDdb_Criteria( ) : $criteria;
        $this->distinct = $distinct;
    }

    /**
     * generateSql 
     * 
     * @return string
     */
    public function generateSql( )
    {
        $sql =  'SELECT ' . ( $this->distinct ? 'DISTINCT ' : '' ) . implode ( $this->fields, ', ' ) . ' FROM ' . $this->table;
        if ( $this->hasJoins( ) ) {
            foreach( $this->joins as $join ) {
                $sql .= ' ' . $join->generateSql( );
            }
        }
        $where = $this->criteria->generateSql( );
        if ( $where != '' ) {
            $sql .= ' ' . $where;
        }
        return $sql;
    }

    /**
     * addJoin 
     * 
     * @param KVDdb_Join $join 
     * @return void
     */
    public function addJoin( KVDdb_Join $join )
    {
        $this->joins[] = $join;
    }

    /**
     * hasJoins 
     * 
     * @return boolean
     */
    public function hasJoins( )
    {
        return count( $this->joins ) > 0;
    }
}

?>
