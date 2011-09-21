<?php
/**
 * @package     KVD.database
 * @version     $Id$
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdb_SimpleQueryTest 
 * 
 * @package     KVD.database
 * @since       jan 2010
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdb_SimpleQueryTest extends PHPUnit_Framework_TestCase
{
    public function testExists( )
    {
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id') , 'gemeente' );
        $this->assertInstanceOf( 'KVDdb_SimpleQuery', $query );
    }

    public function testWithoutCriteria( )
    {
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente' );
        $this->assertFalse( $query->hasJoins( ) );
        $this->assertEquals( $query->getValues( ), array(  ) );
    }

    public function testWithCriteria( )
    {
        $criteria = $this->getMock( 'KVDdb_Criteria' );
        $criteria->expects($this->once())->method( 'generateSql' )->will( $this->returnValue('WHERE ( provincie = 20001 )') );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' , $criteria );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente WHERE ( provincie = 20001 )' );
        $this->assertFalse( $query->hasJoins( ) );
    }

    public function testWithJoin( )
    {
        $join = $this->getMock( 'KVDdb_Join', array(), array( 'provincie', array(array( 'gemeente.provincie_id','provincie.id')),KVDdb_Join::LEFT_JOIN ) );
        $join->expects($this->once())->method( 'generateSql' )->will( $this->returnValue( 'LEFT JOIN provincie ON (gemeente.provincie_id = provincie.id)'));
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' );
        $this->assertFalse( $query->hasJoins( ) );
        $query->addJoin( $join );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente LEFT JOIN provincie ON (gemeente.provincie_id = provincie.id)' );
        $this->assertTrue( $query->hasJoins( ) );
    }
}

/**
 * KVDdb_SimpleQueryWithCriteriaTest 
 * 
 * @package     KVD.database
 * @since       jan 2010
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdb_SimpleQueryWithCriteriaTest extends PHPUnit_Framework_TestCase
{
    public function testSimple( )
    {
        $criteria = new KVDdb_Criteria( );
        $criteria->add( KVDdb_Criterion::equals( 'provincie_id' , 20001 ) );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' , 'gemeente' ) , 'gemeente' , $criteria );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id, gemeente FROM gemeente WHERE ( provincie_id = 20001 )' );
        $this->assertFalse( $query->hasJoins( ) );
    }
    
    public function testComplex( )
    {
        $criteria = new KVDdb_Criteria( );
        $criteria->add( KVDdb_Criterion::equals( 'provincie_id' , 20001 ) );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' , $criteria );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente WHERE ( provincie_id = 20001 )' );

        $criterion = KVDdb_Criterion::inSubselect( 'gemeente_id' , $query );
        $this->assertEquals ( $criterion->generateSql( ) , '( gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE ( provincie_id = 20001 ) ) )' );

        $criteria2 = new KVDdb_Criteria( );
        $criteria2->add( $criterion );
        $this->assertEquals ( $criteria2->generateSql( ) , 'WHERE ( gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE ( provincie_id = 20001 ) ) )' );
        $this->assertFalse( $query->hasJoins( ) );
    }

    public function testParameterizedCriteria( )
    {
        $criteria = new KVDdb_Criteria( );
        $criteria->add( KVDdb_Criterion::equals( 'provincie_id' , 20001 ) );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' , $criteria );
        $this->assertEquals ( $query->generateSql(KVDdb_Criteria::MODE_PARAMETERIZED) , 'SELECT gemeente_id FROM gemeente WHERE ( provincie_id = ? )' );
        $this->assertEquals ( $query->getValues(  ), array( '20001' ) );
    }
}


/**
 * KVDdb_SimpleQueryWithJoinTest 
 * 
 * @package     KVD.database
 * @since       jan 2010
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdb_SimpleQueryWithJoinTest extends PHPUnit_Framework_TestCase
{
    public function testSimple( )
    {
        $join = new KVDdb_Join( 'provincie', array ( array( 'gemeente.provincie_id', 'provincie.id') ), KVDdb_Join::LEFT_JOIN );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' );
        $this->assertInstanceOf( 'KVDdb_SimpleQuery', $query );
        $this->assertFalse( $query->hasJoins( ) );
        $query->addJoin( $join );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente LEFT JOIN provincie ON (gemeente.provincie_id = provincie.id)' );
        $this->assertTrue( $query->hasJoins( ) );
    }
}
?>
