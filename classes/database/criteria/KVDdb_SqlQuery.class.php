<?php
/**
 * @package    KVD.database
 * @subpackage criteria
 * @copyright  2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Stelt een query voor die met ruwe SQL wordt geprogrammeerd.
 *
 * @package    KVD.database
 * @subpackage criteria
 * @since      27 mrt 2009
 * @copyright  2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdb_SqlQuery implements KVDdb_IQuery
{
    /**
     * sql
     *
     * @var string
     */
    private $sql;

    /**
     * __construct
     *
     * @param   string  $sql
     * @return  void
     */
    public function __construct( $sql )
    {
        $this->sql = $sql;
    }

    /**
     * generateSql
     *
     * @return string
     */
    public function generateSql(
                        $mode = KVDdb_Criteria::MODE_FILLED,
                        $dbType = KVDdb_Criteria::DB_PGSQL )
    {
        return $this->sql;
    }

    /**
     * getValues
     *
     * Altijd leeg voor een sql query aangezien die per definite al gevuld is.
     * @since   1.4
     * @return  array
     */
    public function getValues(  )
    {
        return array( );
    }
}
?>
