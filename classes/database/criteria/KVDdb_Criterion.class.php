<?php
/**
 * @package     KVD.database
 * @subpackage  criteria
 * @version     $Id$
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @copyright   2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * @package     KVD.database
 * @subpackage  criteria
 * @since       24 aug 2006
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @copyright   2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
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
    const NOT_EQUAL = '<>';

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
    const EXISTS = 'EXISTS';

    /**
     * @var string 
     */
    const NOT_EXISTS = 'NOT EXISTS';

    /**
     * @var string
     */
    protected $sqlOperator;

    /**
     * @var string
     */
    protected $field = null;

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
     * generateValue 
     * 
     * @param integer $mode 
     * @return string
     */
    protected function generateValue( $mode = KVDdb_Criteria::MODE_FILLED , $value)
    {
        return $mode == KVDdb_Criteria::MODE_FILLED ? $this->sanitize( $value ) : '?';
    }

    
    /**
     * @return string
     */
    public function generateSql( $mode = KVDdb_Criteria::MODE_FILLED , $dbType = KVDdb_Criteria::DB_MYSQL )
    {
        $sql = "( " . $this->field . ' ' . $this->sqlOperator . ' ' . $this->generateValue( $mode , $this->value );
        $sql .= $this->generateSqlChildren( $mode , $dbType );
        return $sql .= ' )';
    }

    /**
     * @return string
     */
    protected function generateSqlChildren( $mode , $dbType )
    {
        $sql = '';
        foreach ( $this->children as $child) {
            $sql .= $child['combinatie'] . $child['criterion']->generateSql( $mode , $dbType);
        }
        return $sql;
    }

    /**
     * getField 
     * 
     * Naam van de velden waarvoor dit criterion en zijn children dienen.
     * @since   9 mei 2009
     * @return  array
     */
    public function getFields( )
    {
        $ret = array( $this->field );
        return array_unique( array_merge( $ret, $this->getFieldsChildren( ) ) );
    }

    /**
     * getFieldsChildren 
     * 
     * @return array
     */
    protected function getFieldsChildren( )
    {
        $ret = array( );
        foreach ( $this->children as $child ){
            $ret = array_merge( $ret , $child['criterion']->getFields( ) );
        }
        return $ret;
    }

    /**
     * getValues 
     * 
     * @return array
     */
    public function getValues( )
    {
        $ret = array ( $this->value );
        return array_merge( $ret , $this->getValuesChildren( ) );
    }


    /**
     * getValuesChildren 
     * 
     * @return array
     */
    protected function getValuesChildren( )
    {
        $ret = array( );
        foreach ( $this->children as $child ){
            $ret = array_merge( $ret , $child['criterion']->getValues( ) );
        }
        return $ret;
        
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
     * notEquals 
     * 
     * @param string $field 
     * @param mixed $value 
     * @return KVDdb_Criterion
     */
    public static function notEquals( $field , $value )
    {
        return new KVDdb_Criterion( self::NOT_EQUAL , $field, $value );
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
     * @param   KVDdb_IQuery        $value
     * @return  KVDdb_ExistsCriterion
     */
    public static function exists( $value )
    {
        return new KVDdb_ExistsCriterion( self::EXISTS, $value );
    }

    /**
     * notExists 
     * 
     * @param   KVDdb_IQuery    $value 
     * @return  KVDdb_ExistsCriterion
     */
    public static function notExists( $value )
    {
        return new KVDdb_ExistsCriterion( self::NOT_EXISTS, $value );
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

    /**
     * year 
     * 
     * @param string $field 
     * @param integer $value 
     * @return KVDdb_YearEqualsCriterion 
     */
    public static function year( $field, $value )
    {
        return new KVDdb_YearEqualsCriterion( $field, $value);
    }
}

/**
 * @package     KVD.database
 * @subpackage  criteria
 * @since       24 aug 2006
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @copyright   2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdb_MatchCriterion extends KVDdb_Criterion
{
    public function __construct ( $field, $value )
    {
        parent::__construct( null, $field, $value );
    }
    
    public function generateSql( $mode = KVDdb_Criteria::MODE_FILLED , $dbType = KVDdb_Criteria::DB_MYSQL )
    {
        $sql = "( UPPER( " . $this->field . ' ) LIKE UPPER( ' . $this->generateValue( $mode , $this->value ) . ' )';
        $sql .= $this->generateSqlChildren( $mode , $dbType );
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
    public function generateSql( $mode = KVDdb_Criteria::MODE_FILLED , $dbType = KVDdb_Criteria::DB_MYSQL )
    {
        $values = $this->value;
        foreach ( $values as &$value) {
            $value = $this->generateValue( $mode , $value);
        }
        $values = implode ( $values , ', ' );
        $sql = "( " . $this->field . " " . $this->sqlOperator . " ( ". $values . " )";
        $sql .= $this->generateSqlChildren( $mode , $dbType);
        return $sql .= ' )';
    }

    /**
     * getValues 
     *
     * @since 15 jul 2008
     * @return array
     */
    public function getValues( )
    {
        return $this->value;
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
    public function generateSql( $mode = KVDdb_Criteria::MODE_FILLED, $dbType = KVDdb_Criteria::DB_MYSQL )
    {
        $sql = "( " . $this->field . " " . $this->sqlOperator . " ( " . $this->value->generateSql( ) . " )";
        $sql .= $this->generateSqlChildren( $mode , $dbType);
        return $sql .= ' )';
    }

    /**
     * getValues 
     *
     * Een Subselect mag enkel de values voor zijn kinderen teruggeven.
     * @since 16 april 2008
     * @return array
     */
    public function getValues( )
    {
        return $this->getValuesChildren( );
    }
}

/**
 * KVDdb_ExistsCriterion 
 * 
 * @package     KVD.database
 * @subpackage  criteria
 * @since       27 mrt 2009
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdb_ExistsCriterion extends KVDdb_Criterion
{
    public function __construct ( $operator, $value )
    {
        parent::__construct( $operator, null, $value );
    }
    
    /**
     * @return string
     */
    public function generateSql( $mode = KVDdb_Criteria::MODE_FILLED, $dbType = KVDdb_Criteria::DB_MYSQL )
    {
        $sql = "( " . $this->sqlOperator . " ( " . $this->value->generateSql( ) . " )";
        $sql .= $this->generateSqlChildren( $mode , $dbType);
        return $sql .= ' )';
    }

    /**
     * getValues 
     *
     * Een Exists mag enkel de values voor zijn kinderen teruggeven.
     * @since 16 april 2008
     * @return array
     */
    public function getValues( )
    {
        return $this->getValuesChildren( );
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
     * generateSql 
     * 
     * @param integer $mode 
     * @param integer $dbType 
     * @return string
     */
    public function generateSql( $mode = KVDdb_Criteria::MODE_FILLED , $dbType = KVDdb_Criteria::DB_MYSQL )
    {
        $sql = "( " . $this->field . " IS NULL";
        $sql .= $this->generateSqlChildren( $mode , $dbType);
        return $sql .= ' )';
    }

    /**
     * getValues 
     * 
     * Een IsNull heeft geen waarden die achteraf gebonden moeten worden, enkel een veld.
     * @since   9 mei 2009
     * @return  array
     */
    public function getValues( )
    {
        return array( );
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
    public function generateSql( $mode = KVDdb_Criteria::MODE_FILLED , $dbType = KVDdb_Criteria::DB_MYSQL )
    {
        $sql = "( " . $this->field . " IS NOT NULL";
        $sql .= $this->generateSqlChildren( $mode , $dbType );
        return $sql .= ' )';
    }

    /**
     * getValues 
     * 
     * Een IsNotNull heeft geen waarden die achteraf gebonden moeten worden, enkel een veld.
     * @since   9 mei 2009
     * @return  array
     */
    public function getValues( )
    {
        return array( );
    }
}

/**
 * KVDdb_YearEqualsCriterion 
 * 
 * @package KVD.database
 * @subpackage criteria
 * @since 22 aug 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdb_YearEqualsCriterion extends KVDdb_Criterion
{
    /**
     * __construct 
     * 
     * @param string $field 
     * @param integer $value 
     * @return void
     */
    public function __construct( $field, $value)
    {
        parent::__construct( null, $field, $value);
    }

    /**
     * generateSql 
     * 
     * @param integer $mode 
     * @return string
     */
    public function generateSql( $mode = KVDdb_Criteria::MODE_FILLED , $dbType = KVDdb_Criteria::DB_MYSQL )
    {
        if ( $dbType == KVDdb_Criteria::DB_MYSQL ) {
            $sql = '( YEAR(' . $this->field . ') = ' . $this->generateValue( $mode, $this->value );
        } else {
            throw new Exception ( 'This Criterion is only supported for MySQL.' );
        }
        $sql .= $this->generateSqlChildren( $mode , $dbType );
        return $sql .= ')';
    }
}
?>
