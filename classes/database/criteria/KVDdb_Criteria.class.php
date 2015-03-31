<?php
/**
 * @package    KVD.database
 * @subpackage criteria
 * @copyright  2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Dit object stelt een verzameling van sql voorwaarden voor.
 *
 * @package    KVD.database
 * @subpackage criteria
 * @since      24 aug 2006
 * @copyright  2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
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
     * @var integer
     */
    const MODE_FILLED = 1;

    /**
     * @var integer
     */
    const MODE_PARAMETERIZED = 2;

    /**
     * @var integer
     */
    const DB_MYSQL = 1;

    /**
     * @var integer
     */
    const DB_PGSQL = 2;


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
    public function add ( KVDdb_Criterion $criterion )
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
    public function generateSql( $mode = self::MODE_FILLED , $dbType = self::DB_MYSQL )
    {
        $tmp = array( );
        if ( $this->count( ) > 0 ) {
            $tmp[] = $this->generateWhereClause( $mode, $dbType );
        }
        if ( count( $this->orderFields ) > 0 ) {
            $tmp[] = $this->generateOrderClause( );
        }
        return implode ( $tmp, " " );
    }

    /**
     * generateWhereClause
     *
     * @param   integer     $mode   Zie de MODE_ constanten
     * @param   integer     $dbType Zie de DB_ constanten.
     * @return  string
     */
    private function generateWhereClause( $mode, $dbType )
    {
        if ( $this->count( ) == 0 ) {
            return '';
        }
        $tmp = array( );
        foreach ( $this->criteria as $criteria ) {
            $tmp[] = $criteria->generateSql( $mode, $dbType );
        }
        return 'WHERE ' . implode ( $tmp, ' AND ' );
    }

    /**
     * generateOrderClause
     *
     * @return string
     */
    private function generateOrderClause( )
    {
        if ( count( $this->orderFields ) == 0 ) {
            return '';
        }
        return 'ORDER BY ' . implode ( $this->orderFields, ' , ' );
    }

    /**
     * Wis de bestaande volgorde.
     */
    public function clearOrder( )
    {
        $this->orderFields = array( );
    }

    /**
     * hasOrder
     *
     * Ga na of er een sorteervolgorde werd ingesteld.
     * @return  boolean
     */
    public function hasOrder( )
    {
        return count( $this->orderFields ) > 0;
    }

    /**
     * hasCriteria
     *
     * Ga na of er zoekcriteria werden ingesteld.
     *
     * @param   string  $field  Indien aanwezig zal er gekeken worden
     *                          of er zoekcriteria op dit veld aanwezig zijn.
     * @return  boolean
     */
    public function hasCriteria( $field = null)
    {
        if ( $field !== null ) {
            $fields = $this->getFields( );
            return in_array( $field, $fields);
        }
        return count( $this->criteria ) > 0;
    }

    /**
     * Telt het aantal criterion objecten.
     * @return integer
     */
    public function count( )
    {
        return count( $this->criteria );
    }

    /**
     * getFields
     *
     * Lijst met alle velden waarop criteria zitten.
     * @return array
     */
    public function getFields( )
    {
        $ret = array( );
        foreach ( $this->criteria as $criterion ) {
            $ret = array_merge( $ret, $criterion->getFields( ) );
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
        $ret = array( );
        foreach ( $this->criteria as $criterion ) {
            $ret = array_merge( $ret, $criterion->getValues( ) );
        }
        return $ret;
    }

}
?>
