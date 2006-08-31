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
    const IN = 'IN';

    /**
     * @var string
     */
    const NOT_IN = 'NOT IN';
    
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
        $sql .= $this->generateSqlChildren( );
        return $sql .= ' )';
    }

    /**
     * @return string
     */
    protected function generateSqlChildren( )
    {
        $sql = '';
        foreach ( $this->children as $child) {
            $sql .= $child['combinatie'] . $child['criterion']->generateSql( );
        }
        return $sql;
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
        return new KVDdb_InCriterion( self::IN , $field, $value );
    }

    /**
     * @param string $field
     * @param array $value
     * @return KVDdb_InCriterion
     */
    public static function notIn ( $field, $value )
    {
        return new KVDdb_InCriterion( self::NOT_IN , $field, $value );
    }

    /**
     * @param string $field
     * @param KVDdb_SimpleQuery $value
     * @return KVDdb_InSubselectCriterion
     */
    public static function inSubselect ( $field , $value )
    {
        return new KVDdb_InSubselectCriterion ( self::IN , $field , $value );
    }

    /**
     * @param string $field
     * @param KVDdb_SimpleQuery $value
     * @return KVDdb_InSubselectCriterion
     */
    public static function notInSubselect ( $field , $value )
    {
        return new KVDdb_InSubselectCriterion ( self::NOT_IN , $field , $value );
    }

    /**
     * @param string $field
     * @return KVDdb_IsNullCriterion
     */
    public static function isNull ( $field )
    {
        return new KVDdb_IsNullCriterion( $field );
    }

    /**
     * @param string $field
     * @return KVDdb_IsNotNullCriterion
     */
    public static function isNotNull ( $field )
    {
        return new KVDdb_IsNotNullCriterion( $field );
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
        $sql .= $this->generateSqlChildren( );
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
     * @return string
     */
    public function generateSql( )
    {
        $values = $this->value;
        foreach ( $values as &$value) {
            $value = $this->sanitize( $value );
        }
        $values = implode ( $values , ', ' );
        $sql = "( " . $this->field . " " . $this->sqlOperator . " ( ". $values . " )";
        $sql .= $this->generateSqlChildren( );
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
     * @return string
     */
    public function generateSql( )
    {
        $sql = "( " . $this->field . " " . $this->sqlOperator . " ( " . $this->value->generateSql( ) . " )";
        $sql .= $this->generateSqlChildren( );
        return $sql .= ' )';
    }
}

/**
 * @package KVD.database
 * @subpackage criteria
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 30 aug 2006
 */
class KVDdb_IsNullCriterion extends KVDdb_Criterion
{
    
    /**
     * @param string $field
     */
    public function __construct ( $field )
    {
        parent::__construct( null , $field , null );
    }

    /**
     * @return string
     */
    public function generateSql( )
    {
        $sql = "( " . $this->field . " IS NULL";
        $sql .= $this->generateSqlChildren( );
        return $sql .= ' )';
    }
}


/**
 * @package KVD.database
 * @subpackage criteria
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 30 aug 2006
 */
class KVDdb_IsNotNullCriterion extends KVDdb_Criterion
{
    
    /**
     * @param string $field
     */
    public function __construct ( $field )
    {
        parent::__construct( null , $field , null );
    }

    /**
     * @return string
     */
    public function generateSql( )
    {
        $sql = "( " . $this->field . " IS NOT NULL";
        $sql .= $this->generateSqlChildren( );
        return $sql .= ' )';
    }
}
?>
