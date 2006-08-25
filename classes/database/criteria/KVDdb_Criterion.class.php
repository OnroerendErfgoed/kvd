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
class KVDdb_Criterion
{
    /**
     * @var string
     */
    const EN = ' AND ';
    
    /**
     * @var string
     */
    const OF = ' OR ';
    
    /**
     * @var string
     */
    const EQUAL = '=';

    /**
     * @var string
     */
    const GREATER_THAN = '>';

    /**
     * @var string
     */
    const LESS_THAN = '<';
    
    /**
     * @var string
     */
    const LIKE = 'LIKE';
    
    /**
     * @var string
     */
    protected $sqlOperator;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var array
     */
    protected $children;
   
    /**
     * @param string $sqlOperator
     * @param string $field
     * @param mixed $value
     */
    protected function __construct ( $sqlOperator, $field, $value )
    {   
        $this->sqlOperator = $sqlOperator;
        $this->field = $field;
        $this->value = $value;
        $this->sanitize( );
        $this->children = array( );
    }

    protected function sanitize( )
    {
        if ( is_string( $this->value ) ) {
            $this->value = "'" . $this->value . "'";
        }
    }
    
    /**
     * @return string
     */
    public function generateSql( )
    {
        $sql = "( " . $this->field . ' ' . $this->sqlOperator . ' ' . $this->value;
        foreach ( $this->children as $child) {
            $sql .= $child['combinatie'] . $child['criterion']->generateSql( );
        }
        return $sql .= ' )';
    }

    /**
     * @param KVDdb_Criterion
     */
    public function addOr( $criterion )
    {
        $this->children[] = array ( 'combinatie' => self::OF , 'criterion' => $criterion);
    }

    /**
     * @param KVDdb_Criterion
     */
    public function addAnd ( $criterion )
    {
        $this->children[] = array( 'combinatie' => self::EN , 'criterion' => $criterion);
    }

    public static function equals ( $field, $value )
    {
        return new KVDdb_Criterion ( self::EQUAL, $field, $value );
    }

    public static function matches ( $field, $value )
    {
        return new KVDdb_MatchCriterion ( $field , $value );
    }

    public static function greaterThan ( $field , $value )
    {
        return new KVDdb_Criterion ( self::GREATER_THAN , $field , $value );
    }

    public static function lessThan ( $field , $value )
    {
        return new KVDdb_Criterion ( self::LESS_THAN , $field , $value );
    }
}

class KVDdb_MatchCriterion extends KVDdb_Criterion
{
    public function __construct ( $field, $value )
    {
        parent::__construct( null, $field, $value );
    }
    
    public function generateSql( )
    {
        $sql = "( UPPER( " . $this->field . ' ) LIKE UPPER( ' . $this->value . ' )';
        foreach ( $this->children as $child) {
            $sql .= $child['combinatie'] . $child['criterion']->generateSql( );
        }
        return $sql .= ' )';
    }
}
?>
