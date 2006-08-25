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
 * @since 24 aug 2006
 */
class KVDdb_Criteria implements Countable
{
    /**
     * @var string
     */
    const ASC = 'ASC';
    
    /**
     * @var string
     */
    const DESC = 'DESC';
    
    /**
     * @var array
     */
    private $criteria;

    /**
     * @var array
     */
    private $orderFields;

    public function __construct( )
    {
        $this->criteria = array( );
        $this->orderFields = array( );
    }
   
    /**
     * @param KVDdb_Criterion $criterion
     */
    public function add ( $criterion )
    {
        $this->criteria[] = $criterion;
    }

    /**
     * @param string $field
     */
    public function addAscendingOrder( $field )
    {
        $this->orderFields[] = $field . ' ' . self::ASC;
    }

    /**
     * @param string $field
     */
    public function addDescendingOrder( $field )
    {
        $this->orderFields[] = $field . ' ' . self::DESC;
    }

    /**
     * @return string Een geldig sql WHERE statement ( geen spatie aan het begin )
     */
    public function generateSql()
    {
        $tmp = array( );
        if ( $this->count( ) > 0 ) {
            $tmp[] = $this->generateWhereClause( );
        }
        if ( count( $this->orderFields ) > 0 ) {
            $tmp[] = $this->generateOrderClause( );
        }
        return implode ( $tmp , " " );
    }

    private function generateWhereClause( )
    {
        if ( $this->count( ) == 0 ) {
            return '';
        }
        $tmp = array( );
        foreach ( $this->criteria as $criteria ) {
            $tmp[] = $criteria->generateSql( );
        }
        return 'WHERE ' . implode ( $tmp , ' AND ' );
    }

    private function generateOrderClause( )
    {
        if ( count( $this->orderFields ) == 0 ) {
            return '';
        }
        return 'ORDER BY ' . implode ( $this->orderFields , ' , ' );
    }

    /**
     * Wis de bestaande volgorde.
     */
    public function clearOrder( )
    {
        $this->orderFields = array( );
    }
    
    /**
     * Telt het aantal criterion objecten.
     * @return integer
     */ 
    public function count( )
    {
        return count( $this->criteria );
    }
    
}
?>
