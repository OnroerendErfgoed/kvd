<?php
/**
 * @package     KVD.database
 * @version     $Id$
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

require_once( 'PHPUnit/Framework.php' );

/**
 * KVDdb_SqlTest 
 * 
 * @package     KVD.database
 * @since       jan 2010
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdb_SqlTest extends PHPUnit_Framework_TestCase
{
    public function testExists( )
    {
        $query = new KVDdb_SqlQuery( 'SELECT * FROM provincie');
        $this->assertType( 'KVDdb_SqlQuery', $query );
    }

    public function testGenerateSql(  )
    {
        $query = new KVDdb_SqlQuery( 'SELECT * FROM provincie');
        $this->assertEquals( 'SELECT * FROM provincie', $query->generateSql() );
    }
}
?>
