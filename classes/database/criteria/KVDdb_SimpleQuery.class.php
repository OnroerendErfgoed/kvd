<?php
/**
 * @package KVD.database
 * @subpackage criteria
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.database
 * @subpackage criteria
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 28 aug 2006
 */
class KVDdb_SimpleQuery
{
    private $fields;

    private $table;

    private $criteria;

    public function __construct ( $fields , $table , $criteria = null )
    {
        $this->fields = $fields;
        $this->table = $table;
        $this->criteria = is_null( $criteria ) ? new KVDdb_Criteria( ) : $criteria;
    }
    public function generateSql( )
    {
        $sql =  'SELECT ' . implode ( $this->fields, ', ' ) . ' FROM ' . $this->table;
        $where = $this->criteria->generateSql( );
        if ( $where != '' ) {
            $sql .= ' ' . $where;
        }
        return $sql;
    }
}
?>
