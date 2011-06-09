<?php
//Mock::generate( 'PDO' );
//Mock::generate( 'PDOStatement' );
//Mock::generate( 'KVDdom_PDODataMapper' );

require_once( 'PHPUnit/Framework.php' );

class KVDdom_TestPDO {
    public function query(  ){

    }

}

class KVDdom_TestPDOStatement {

    public function fetchColumn(  )
    {

    }

}

class KVDdom_PDOChunkyQueryTest extends PHPUnit_Framework_TestCase
{
    private $pdo;
    
    public function setUp( )
    {
        $this->pdo = $this->getMock( 'KVDdom_TestPDO', array(), array () );
    }

    public function tearDown( )
    {
        $this->pdo = null;
        $this->mapper = null;
    }
    
    public function testExists( )
    {
        $rs = $this->getMock(  'KVDdom_TestPDOStatement' );
        $rs->expects( $this->once() )->method( 'fetchColumn' )->will( $this->returnValue( 255 ) );
        $this->pdo->expects( $this->once( ) )->method( 'query' )->with( $this->equalTo( 'SELECT COUNT(id) FROM gemeenten' ) )->will( $this->returnValue( $rs ) );
        $mapper = $this->getMock( 'KVDdom_PDODataMapper', array(), array(), 'KVDdo_GemeenteMapper1', false );
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $mapper, 'SELECT * FROM gemeenten' );
        $this->assertType('KVDdom_PDOChunkyQuery', $query );
    }

    public function testGetPagingData( )
    {
        $rs = $this->getMock(  'KVDdom_TestPDOStatement' );
        $rs->expects( $this->once() )->method( 'fetchColumn' )->will( $this->returnValue( 255 ) );
        $this->pdo->expects( $this->once( ) )->method( 'query' )->with( $this->equalTo( 'SELECT COUNT(id) FROM gemeenten' ) )->will( $this->returnValue( $rs ) );
        $mapper = $this->getMock( 'KVDdom_PDODataMapper', array(), array(), 'KVDdo_GemeenteMapper2', false );
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $mapper, 'SELECT * FROM gemeenten' );
        $this->assertType( 'KVDdom_PDOChunkyQuery', $query );
        $this->assertEquals ( 255, $query->getTotalRecordCount( ) );
        $this->assertEquals ( 3, $query->getTotalChunksCount( ) );
        $this->assertEquals ( 1, $query->getChunk( ) );
        $this->assertEquals ( 100, $query->getRowsPerChunk( ) );
    }

    public function testSetChunks( )
    {
        $rs = $this->getMock(  'KVDdom_TestPDOStatement' );
        $rs->expects( $this->once() )->method( 'fetchColumn' )->will( $this->returnValue( 255 ) );
        $this->pdo->expects( $this->once( ) )->method( 'query' )->with( $this->equalTo( 'SELECT COUNT(id) FROM gemeenten' ) )->will( $this->returnValue( $rs ) );
        $mapper = $this->getMock( 'KVDdom_PDODataMapper', array(), array(), 'KVDdo_GemeenteMapper3', false );
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $mapper, 'SELECT * FROM gemeenten' );
        $this->assertType( 'KVDdom_PDOChunkyQuery', $query );
        $this->assertEquals ( 255, $query->getTotalRecordCount( ) );
        $query->setRowsPerChunk( 50 );
        $this->assertEquals ( 6, $query->getTotalChunksCount( ) );
        $query->setChunk( 2 );
        $this->assertEquals ( 2, $query->getChunk( ) , 2);
        $this->assertEquals ( 50, $query->getRowsPerChunk( ) , 50);
    }

    public function testComplexQuery( )
    {
        $rs = $this->getMock(  'KVDdom_TestPDOStatement' );
        $rs->expects( $this->once() )->method( 'fetchColumn' )->will( $this->returnValue( 255 ) );
        $this->pdo->expects( $this->once( ) )->method( 'query' )->with( $this->equalTo( 'SELECT COUNT(id) FROM locatie WHERE gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001)' )  )->will( $this->returnValue( $rs ) );
        $mapper = $this->getMock( 'KVDdom_PDODataMapper', array(), array(), 'KVDdo_GemeenteMapper4', false );
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $mapper, 'SELECT * FROM locatie WHERE gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001)' );
        $this->assertType( 'KVDdom_PDOChunkyQuery', $query );
    }

    public function testCaseInsensitive( )
    {
        $rs = $this->getMock(  'KVDdom_TestPDOStatement' );
        $rs->expects( $this->once() )->method( 'fetchColumn' )->will( $this->returnValue( 255 ) );
        $this->pdo->expects( $this->once( ) )->method( 'query' )->with( $this->equalTo( 'SELECT COUNT(id) FROM locatie WHERE gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001)' )  )->will( $this->returnValue( $rs ) );
        $mapper = $this->getMock( 'KVDdom_PDODataMapper', array(), array(), 'KVDdo_GemeenteMapper5', false );
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $mapper, 'select * from locatie WHERE gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001)' );
        $this->assertType( 'KVDdom_PDOChunkyQuery', $query );
    }

    public function testZoekenProvincie( )
    {
        $rs = $this->getMock(  'KVDdom_TestPDOStatement' );
        $rs->expects( $this->once() )->method( 'fetchColumn' )->will( $this->returnValue( 255 ) );
        $this->pdo->expects( $this->once( ) )->method( 'query' )->with( $this->equalTo(  'SELECT COUNT(relict.id) FROM mel_master.relict LEFT JOIN mel_gis.locatie ON (  relict.id = mel_gis.locatie.relict_id ) WHERE (  locatie.id IN (  SELECT locatie_id FROM mel_gis.adres WHERE (  provincie_id = 30000 ) ) )' ) )->will( $this->returnValue( $rs ) );
        $mapper = $this->getMock( 'KVDdom_PDODataMapper', array(), array(), 'KVDdo_GemeenteMapper6', false );

        $sql = 'SELECT relict.id AS id, naam, is_deel_van, toponiem, locatie.id AS locatie_id, opmerkingen, gevonden, provincie_naam, gemeente_naam, deelgemeente_naam, locatie.bewaard AS bewaard, in_crab  FROM mel_master.relict LEFT JOIN mel_gis.locatie ON (  relict.id = mel_gis.locatie.relict_id ) WHERE (  locatie.id IN (  SELECT locatie_id FROM mel_gis.adres WHERE (  provincie_id = 30000 ) ) )';
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $mapper, $sql, 'relict.id' );
        $this->assertType( 'KVDdom_PDOChunkyQuery', $query );
    }
    
}
?>
