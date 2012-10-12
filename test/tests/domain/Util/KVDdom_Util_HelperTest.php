<?php
/**
 * @package   OEPS.util
 * @version   $Id: KVDdom_Util_HelperTest.php 4301 2011-12-09 16:21:24Z verbisph $
 * @copyright 2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Philip Verbist <philip.verbist@hp.com> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * @package   OEPS.util
 * @since     1.3.3
 * @copyright 2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Philip Verbist <philip.verbist@hp.com> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_Util_HelperTest extends PHPUnit_Framework_TestCase
{

    public $fieldobject;

    public function setUp()
    {
        $this->fieldobject = new KVDdom_SimpleTestDomainObject( 1, 'Van Daele, Koen' );
    }

    public function getFieldObject()
    {
        return $this->fieldobject;
    }

    public function testGetDataForFieldString( )
    {
        $this->assertEquals("Van Daele, Koen", KVDdom_Util_Helper::getDataForFieldString(new KVDdom_SimpleTestDomainObject( 1, 'Van Daele, Koen' ), "getTitel"));
        $this->assertEquals("Ja", KVDdom_Util_Helper::getDataForFieldString(new KVDdom_SimpleTestDomainObject( 1, true ), "getTitel"));
        $this->assertEquals("Nee", KVDdom_Util_Helper::getDataForFieldString(new KVDdom_SimpleTestDomainObject( 1, false ), "getTitel"));
        $this->assertEquals("1, 2", KVDdom_Util_Helper::getDataForFieldString(new KVDdom_SimpleTestDomainObject( 1, array(1, 2) ), "getTitel"));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetDataForFieldStringException( )
    {
        $titel = KVDdom_Util_Helper::getDataForFieldString(new KVDdom_SimpleTestDomainObject( 1, 'Van Daele, Koen' ), "getTitel.getData");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetDataForInvalidFieldStringException( )
    {
        $titel = KVDdom_Util_Helper::getDataForFieldString(new KVDdom_SimpleTestDomainObject( 1, 'Van Daele, Koen' ), "getTi tel.getD ta");
    }
}
?>
