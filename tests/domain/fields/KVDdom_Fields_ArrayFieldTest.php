<?php
/**
 * @package     KVD.dom
 * @subpackage  fields
 * @category    test
 * @version     $Id$
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

require_once( 'PHPUnit/Framework.php' );

/**
 * KVDdom_Fields_ArrayFieldTest 
 * 
 * @package     KVD.dom
 * @subpackage  fields
 * @category    test
 * @since       1 sep 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_Fields_ArrayFieldTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->dom = $this->getMock( 'KVDdom_SimpleTestDomainObject', array(), array(1, 'Een eerste object') );
        $this->field = new KVDdom_Fields_ArrayField( $this->dom, 'eigenaars' );
        $this->test_array = array( );
        $this->lena = 'Lena Van Daele';
        $this->mira = 'Mira Van Daele';

    }

    public function testField( )
    {
        $this->assertType( 'KVDdom_Fields_ArrayField', $this->field );
        $this->assertEquals( 'eigenaars', $this->field->getName( ) );
    }

    public function testInitializeValue( )
    {
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->never())->method( 'markFieldAsDirty' );
        $this->field->initializeValue( $this->test_array );
        $this->assertType( 'array', $this->field->getValue() );
        $this->assertEquals( 0, count($this->field->getValue( ) ) );
    }

    private function isLena( $lena )
    {
        $this->assertEquals( 'Lena Van Daele', $lena );
    }

    private function isMira( $mira )
    {
        $this->assertEquals( 'Mira Van Daele', $mira );
    }


    public function testAdd(  )
    {
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->exactly(2))->method( 'markFieldAsDirty' );
        $this->field->initializeValue( $this->test_array );
        $this->field->add( $this->lena );
        $arr = $this->field->getValue( );
        $this->assertEquals( 1, count($arr) );
        $this->field->add( $this->mira );
        $arr = $this->field->getValue( );
        $this->assertEquals( 2, count($arr) );
    }

    public function testAddExisting(  )
    {
        $arr = array( $this->mira );
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->never())->method( 'markFieldAsDirty' );
        $this->field->initializeValue( $arr );
        $this->assertEquals( 1, count($this->field->getValue( )) );
        $this->field->add( $this->mira );
        $this->assertEquals( 1, count($this->field->getValue( )) );
    }

    public function testRemove( )
    {
        $arr = array( $this->mira);
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->once())->method( 'markFieldAsDirty' );
        $this->field->initializeValue( $arr );
        $this->assertEquals( 1, count($this->field->getValue( )) );
        $this->field->remove( $this->mira );
        $this->assertEquals( 0, count($this->field->getValue( )) );
    }

    public function testClear(  )
    {
        $arr = array( $this->lena, $this->mira);
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->once())->method( 'markFieldAsDirty' );
        $this->field->initializeValue( $arr );
        $tarr = $this->field->getValue( );
        $this->assertEquals( 2, count($tarr) );
        $this->field->clear();
        $tarr = $this->field->getValue( );
        $this->assertEquals( 0, count($tarr) );
    }

}
?>
