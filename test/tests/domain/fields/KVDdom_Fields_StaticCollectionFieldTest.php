<?php
/**
 * @package     KVD.dom
 * @subpackage  fields
 * @category    test
 * @version     $Id$
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_Fields_StaticCollectionFieldTest 
 * 
 * @package     KVD.dom
 * @subpackage  fields
 * @category    test
 * @since       1 maart 2010
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_Fields_StaticCollectionFieldTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->dom = $this->getMock( 'KVDdom_SimpleTestDomainObject', array(), array(1, 'Een eerste object') );
        $this->field = new KVDdom_Fields_StaticCollectionField( $this->dom, 'eigenaars', 'KVDdom_SimpleTestDomainObject' );
        $this->collection = new KVDdom_EditeerbareDomainObjectCollection( array( ), 'KVDdom_SimpleTestDomainObject' );
        $this->lena = new KVDdom_SimpleTestDomainObject(10, 'Lena Van Daele');
        $this->mira = new KVDdom_SimpleTestDomainObject(11, 'Mira Van Daele');

    }

    public function testField( )
    {
        $this->assertType( 'KVDdom_Fields_StaticCollectionField', $this->field );
        $this->assertEquals( 'eigenaars', $this->field->getName( ) );
    }

    public function testFieldType(  )
    {
        $field = new KVDdom_Fields_StaticCollectionField( $this->dom, 'eigenaars', 'KVDdom_SimpleTestDomainObject' );
        $this->assertEquals( 'KVDdom_SimpleTestDomainObject', $field->getType(  )  );
    }

    public function testInitializeValue( )
    {
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->never())->method( 'markFieldAsDirty' );
        $this->field->initializeValue( $this->collection );
        $this->assertType( 'KVDdom_DomainObjectCollection', $this->field->getValue() );
        $this->assertEquals( 0, $this->field->getValue( )->count( ) );
    }

    private function isLena( $lena )
    {
        $this->assertEquals( 10, $lena->getId( ) );
        $this->assertEquals( 'Lena Van Daele', $lena->getOmschrijving( ) );
    }

    private function isMira( $mira )
    {
        $this->assertEquals( 11, $mira->getId( ) );
        $this->assertEquals( 'Mira Van Daele', $mira->getOmschrijving( ) );
    }


    public function testAdd(  )
    {
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->exactly(2))->method( 'markFieldAsDirty' );
        $this->field->add( $this->lena );
        $coll = $this->field->getValue( );
        $this->assertEquals( 1, count($coll) );
        $first = $coll->getFirst( );
        $this->isLena( $first );
        $this->field->add( $this->mira );
        $coll = $this->field->getValue( );
        $this->assertEquals( 2, count($coll) );
    }

    public function testAddExisting(  )
    {
        $collection = new KVDdom_EditeerbareDomainObjectCollection( array( 11 => $this->mira), 'KVDdom_SimpleTestDomainObject' );
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->never())->method( 'markFieldAsDirty' );
        $this->field->initializeValue( $collection );
        $coll = $this->field->getValue( );
        $this->assertEquals( 1, count($coll) );
        $first = $coll->getFirst( );
        $this->isMira( $first );
        $this->field->add( $this->mira );
        $this->assertEquals( 1, count($coll) );
    }

    public function testRemove( )
    {
        $collection = new KVDdom_EditeerbareDomainObjectCollection( array( 11 => $this->mira), 'KVDdom_SimpleTestDomainObject' );
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->once())->method( 'markFieldAsDirty' );
        $this->field->initializeValue( $collection );
        $coll = $this->field->getValue( );
        $this->assertEquals( 1, count($coll) );
        $first = $coll->getFirst( );
        $this->isMira( $first );
        $this->field->remove( $this->mira );
        $coll = $this->field->getValue( );
        $this->assertEquals( 0, count($coll) );
    }

    public function testClear(  )
    {
        $collection = new KVDdom_EditeerbareDomainObjectCollection( array( 10 => $this->lena, 11 => $this->mira), 'KVDdom_SimpleTestDomainObject' );
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->once())->method( 'markFieldAsDirty' );
        $this->field->initializeValue( $collection );
        $coll = $this->field->getValue( );
        $this->assertEquals( 2, count($coll) );
        $this->field->clear();
        $coll = $this->field->getValue( );
        $this->assertEquals( 0, count($coll) );
    }

    public function testSetValue( )
    {
        $collection = new KVDdom_EditeerbareDomainObjectCollection( array( 11 => $this->mira), 'KVDdom_SimpleTestDomainObject' );
        $this->dom->expects($this->never())->method( 'markDirty' );
        $this->dom->expects($this->once())->method( 'markFieldAsDirty' );
        $this->field->setValue( $collection );
        $this->assertType( 'KVDdom_DomainObjectCollection', $this->field->getValue() );
        $this->assertEquals( 1, $this->field->getValue( )->count( ) );
    }

}
?>
