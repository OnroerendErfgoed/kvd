<?php
/**
 * @package KVD.database
 * @subpackage Criteria
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdb_Join 
 * 
 * @package KVD.database
 * @subpackage Criteria
 * @since 11 dec 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdb_Join
{
    /**
     * @var string 
     */
    const LEFT_JOIN = 'LEFT JOIN ';

    /**
     * @var array 
     */
    private static $joinTypes = array ( self::LEFT_JOIN );

    /**
     * table 
     * 
     * @var string
     */
    private $table;

    /**
     * fields 
     * 
     * @var array
     */
    private $fields;

    /**
     * type 
     * 
     * @var string
     */
    private $type;

    /**
     * __construct 
     * 
     * @param string    $table  Naam van de tabel waarnaar een join gelegd moet worden.
     * @param array     $fields Array van veldparen waarop gejoined moet worden.
     * @param string    $type   Zie de constantes.
     * @return void
     */
    public function __construct( $table, array $fields, $type )
    {
        $this->table = $table;
        if ( count( $fields ) < 1 ) {
            throw new InvalidArgumentException( 'Er moet minstens 1 veldpaar gedefinieerd zijn om een join te kunnen leggen.' );
        }
        foreach ( $fields as $fieldPair ) {
            if ( count( $fieldPair) != 2 ) {
                throw new InvalidArgumentException( 'Elk veldpaar kan maar uit 2 velden bestaan.' );
            }
        }
        if ( !in_array( $type , self::$joinTypes ) ) {
            throw new InvalidArgumentException ( 'Het jointype ' . $type . ' wordt niet ondersteund.' );
        }
    }

    /**
     * generateSql 
     * 
     * @return string
     */
    public function generateSql( )
    {
        $onArray = array( );
        foreach ( $this->fields as $fieldPair ) {
            $onArray[] = $fieldPair[0] . ' = ' . $fieldPair[1];
        }
        $onString = implode( ' AND ', $onArray );
        $sql = $this->type . $this->table . ' ON (' . $onString . ')';
        return $sql;
    }
}
?>      
