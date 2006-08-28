<?php
/**
 * @package KVD.database
 * @subpackage criteria
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version $Id$
 */

/**²
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
        $this->children = array( );
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function sanitize( $value )
    {
        if ( is_string( $value ) ) {
            $value = "'" . $value . "'";
        }
        if ( is_bool( $this->value ) ) {
            $value = ( $value == true ) ? 'true' : 'false';
        }
        return $value;
    }
    
    /**
     * @return string
     */
    public function generateSql( )
    {
        $sql = "( " . $this->field . ' ' . $this->sqlOperator . ' ' . $this->sanitize( $this->value );
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

    /**
     * @param string $field
     * @param mixed $value
     * @return KVDdb_Criterion
     */
    public static function equals ( $field, $value )
    {
        return new KVDdb_Criterion ( self::EQUAL, $field, $value );
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return KVDdb_MatchCriterion
     */
    public static function matches ( $field, $value )
    {
        return new KVDdb_MatchCriterion ( $field , $value );
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return KVDdb_Criterion
     */
    public static function greaterThan ( $field , $value )
    {
        return new KVDdb_Criterion ( self::GREATER_THAN , $field , $value );
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return KVDdb_Criterion
     */
    public static function lessThan ( $field , $value )
    {
        return new KVDdb_Criterion ( self::LESS_THAN , $field , $value );
    }

    /**
     * @param string $field
     * @param array $value
     * @return KVDdb_InCriterion
     */
    public static function in ( $field, $value )
    {
        return new KVDdb_InCriterion( $field, $value );
    }

    /**
     * @param string $field
     * @param KVDdb_SimpleQuery $value
     * @return KVDdb_InSubselectCriterion
     */
    public static function inSubselect ( $field , $value )
    {
        return new KVDdb_InSubselectCriterion ( $field , $value );
    }
}


/**²
 * @package KVD.database
 * @subpackage criteria
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 24 aug 2006
 */
class KVDdb_MatchCriterion extends KVDdb_Criterion
{
    public function __construct ( $field, $value )
    {
        parent::__construct( null, $field, $value );
    }
    
    public function generateSql( )
    {
        $sql = "( UPPER( " . $this->field . ' ) LIKE UPPER( ' . $this->sanitize( $this->value ) . ' )';
        foreach ( $this->children as $child) {
            $sql .= $child['combinatie'] . $child['criterion']->generateSql( );
        }
        return $sql .= ' )';
    }
}

/**
 * @package KVD.database
 * @subpackage criteria
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 28 aug 2006
 */
class KVDdb_InCriterion extends KVDdb_Criterion
{
    /**
     * @param string $field
     * @param array $value
     */
    public function __construct ( $field , $value )
    {
        parent::__construct( null, $field, $value );
    }

    /**
     * @return string
     */
    public function generateSql( )
    {
        $values = $this->value;
        foreach ( $values as &$value) {
            $value = $this->sanitize( $value );
        }
        $values = implode ( $values , ', ' );
        $sql = "( " . $this->field . " IN ( ". $values . " )";
        foreach ( $this->children as $child) {
            $sql .= $child['combinatie'] . $child['criterion']->generateSql( );
        }
        return $sql .= ' )';
    }
}

/**
 * @package KVD.database
 * @subpackage criteria
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 28 aug 2006
 */
class KVDdb_InSubselectCriterion extends KVDdb_Criterion
{
    
    /**
     * @param string $sqlOperator
     * @param string $field
     * @param mixed $value
     */
    public function __construct ( $field , $value )
    {
        parent::__construct( null , $field , $value );
    }

    /**
     * @return string
     */
    public function generateSql( )
    {
        $sql = "( " . $this->field . " IN ( " . $this->value->generateSql( ) . " )";
        foreach ( $this->children as $child) {
            $sql .= $child['combinatie'] . $child['criterion']->generateSql( );
        }
        return $sql .= ' )';
    }
}
?>
