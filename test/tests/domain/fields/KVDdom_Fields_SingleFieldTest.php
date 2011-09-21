<?php
/**
 * @package     KVD.dom
 * @subpackage  fields
 * @category    test
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_Fields_SingleFieldTest 
 * 
 * @package     KVD.dom
 * @subpackage  fields
 * @category    test
 * @since       11 feb 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_Fields_SingleFieldTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->dom = $this->getMock( 'KVDdom_SimpleTestDomainObject', array(), array(1, 'Een eerste object') );
        $this->field = new KVDdom_Fields_SingleField( $this->dom, 'eigenaar', 'Koen Van Daele' );
    }

    public function testField( )
    {
        $this->assertInstanceOf( 'KVDdom_Fields_SingleField', $this->field );
        $this->assertEquals( 'eigenaar', $this->field->getName( ) );
    }

    public function testFieldType(  )
    {
        $field = new KVDdom_Fields_SingleField( $this->dom, 'eigenaar', 'Koen Van Daele', 'string' );
        $this->assertEquals( 'string', $field->getType(  )  );
    }

    public function testDefaultValue( )
    {
        $this->assertEquals( $this->field->getValue( ), $this->field->getDefaultValue() );
        $this->assertEquals( $this->field->getValue( ), 'Koen Van Daele' );
    }

    public function testInitializeValue( )
    {
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->never())->method( 'markFieldAsDirty' );
        $this->field->initializeValue( 'Koen Van Daele' );
        $this->assertEquals( 'Koen Van Daele', $this->field->getValue() );
    }

    public function testSetValue( )
    {
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->once())->method( 'markFieldAsDirty' );
        $this->field->setValue( 'Leen Meganck' );
        $this->assertEquals( $this->field->getValue(), 'Leen Meganck' );
    }

    public function testSetValueToTheSame( )
    {
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->never())->method( 'markFieldAsDirty' );
        $this->field->initializeValue( 'Koen Van Daele' );
        $this->field->setValue( 'Koen Van Daele' );
        $this->assertEquals( $this->field->getValue(), 'Koen Van Daele' );
    }
}
?>
