<?php
/**
 * @package     KVD.html
 * @version     $Id$
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDhtml_OptionsHelperArrayTest 
 * 
 * @package     KVD.html
 * @sicne       2010
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDhtml_OptionsHelperArrayTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $test_array = array();
        $test_array[0] = 'Object 0';
        $test_array[100] = 'Object 100';
        $test_array[5000] = 'Object 5000';
        $this->oh = KVDhtml_AbstractOptionsHelper::arrayOptionsHelper( $test_array );
    }

    public function testEmpty( )
    {
        $oh = new KVDhtml_OptionsHelperArray( array( ) );
        $this->assertInstanceOf( 'KVDhtml_OptionsHelperArray', $oh );
        $this->assertEquals( '', $oh->toHtml(  ) );
    }

    public function testWithData( )
    {
        $html = "<option value=\"0\">Object 0</option>\n<option value=\"100\">Object 100</option>\n<option value=\"5000\">Object 5000</option>\n";
        $this->assertEquals( $html, $this->oh->toHtml() );
    }
}
?>
