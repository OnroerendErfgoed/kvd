<?php
/**
 * @package     KVD.html
 * @version     $Id$
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDhtml_OptionsHelperTest 
 * 
 * @package     KVD.html
 * @since       2010
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDhtml_OptionsHelperTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $test_array = array();
        $test_array[0] = new KVDdom_SimpleTestDomainObject( 0, 'Object 0' );
        $test_array[100] = new KVDdom_SimpleTestDomainObject( 100, 'Object 100' );
        $test_array[5000] = new KVDdom_SimpleTestDomainObject( 5000, 'Object 5000' );
        $coll = new KVDdom_DomainObjectCollection( $test_array );
        $this->oh = KVDhtml_AbstractOptionsHelper::objectOptionsHelper( $coll );
    }

    public function testEmpty( )
    {
        $oh = new KVDhtml_OptionsHelper( new KVDdom_DomainObjectCollection( array( ) ) );
        $this->assertInstanceOf( 'KVDhtml_OptionsHelper', $oh );
        $this->assertEquals( '', $oh->toHtml(  ) );
    }

    public function testAddEmptyLine( )
    {
        $oh = new KVDhtml_OptionsHelper( new KVDdom_DomainObjectCollection( array( ) ), true );
        $oh->setEmptyValues( 0, 'Maak uw keuze' );
        $this->assertEquals( "<option value=\"0\">Maak uw keuze</option>\n", $oh->toHtml(  ) );
    }

    public function testWithData( )
    {
        $html = "<option value=\"0\">Object 0</option>\n<option value=\"100\">Object 100</option>\n<option value=\"5000\">Object 5000</option>\n";
        $this->assertEquals( $html, $this->oh->toHtml() );
    }
}

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
