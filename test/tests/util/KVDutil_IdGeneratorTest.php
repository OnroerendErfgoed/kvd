<?php
/**
 * @package    KVD.util
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Unit test voor KVDutil_IdGenerator 
 * 
 * @package   KVD.util
 * @since     1.5
 * @copyright 2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_IdGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testGetNext( )
    {
        $gen = new KVDutil_IdGenerator( );
        $this->assertEquals( 0, $gen->next( ) );
        $this->assertEquals( 1, $gen->next( ) );
    }

    public function testGetNextWithStart( )
    {
        $gen = new KVDutil_IdGenerator(100);
        $this->assertEquals( 100, $gen->next( ) );
        $this->assertEquals( 101, $gen->next( ) );
    }
}
?>
