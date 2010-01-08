<?php
Mock::generate( 'PDO' );
Mock::generate( 'PDOStatement' );
Mock::generate( 'KVDdom_PDODataMapper' );
class TestOfPDOChunkyQuery extends UnitTestCase
{
    private $pdo;
    
    public function setUp( )
    {
        $this->pdo = new MockPDO( $this );
        $this->mapper = new MockKVDdom_PDODataMapper( $this );
    }

    public function tearDown( )
    {
        $this->pdo->tally( );
        $this->mapper->tally( );
    }
    
    public function testExists( )
    {
        $rs = new MockPDOStatement( );
        $rs->expectOnce( 'fetchColumn' );
        $rs->setReturnValue ( 'fetchColumn' , 255 );
        $this->pdo->setReturnValue( 'query' , $rs );
        $this->pdo->expectOnce( 'query' , array( 'SELECT COUNT(id) FROM gemeenten'));
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $this->mapper, 'SELECT * FROM gemeenten' );
        $this->assertIsA( $query, 'KVDdom_PDOChunkyQuery' );
    }

    public function testGetPagingData( )
    {
        $this->pdo->expectOnce( 'query' );
        $rs = new MockPDOStatement( );
        $rs->expectOnce( 'fetchColumn' );
        $rs->setReturnValue ( 'fetchColumn' , 255 );
        $this->pdo->setReturnValue( 'query' , $rs );
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $this->mapper, 'SELECT * FROM gemeenten' );
        $this->assertIsA( $query, 'KVDdom_PDOChunkyQuery' );
        $this->assertEqual ( $query->getTotalRecordCount( ) , 255 );
        $this->assertEqual ( $query->getTotalChunksCount( ) , 3);
        $this->assertEqual ( $query->getChunk( ) , 1);
        $this->assertEqual ( $query->getRowsPerChunk( ) , 100);
    }

    public function testSetChunks( )
    {
        $this->pdo->expectOnce( 'query' );
        $rs = new MockPDOStatement( );
        $rs->expectOnce( 'fetchColumn' );
        $rs->setReturnValue ( 'fetchColumn' , 255 );
        $this->pdo->setReturnValue( 'query' , $rs );
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $this->mapper, 'SELECT * FROM gemeenten' );
        $this->assertIsA( $query, 'KVDdom_PDOChunkyQuery' );
        $this->assertEqual ( $query->getTotalRecordCount( ) , 255 );
        $query->setRowsPerChunk( 50 );
        $this->assertEqual ( $query->getTotalChunksCount( ) , 6);
        $query->setChunk( 2 );
        $this->assertEqual ( $query->getChunk( ) , 2);
        $this->assertEqual ( $query->getRowsPerChunk( ) , 50);
    }

    public function testComplexQuery( )
    {
        $rs = new MockPDOStatement( );
        $rs->expectOnce( 'fetchColumn' );
        $rs->setReturnValue ( 'fetchColumn' , 255 );
        $this->pdo->setReturnValue( 'query' , $rs );
        $this->pdo->expectOnce( 'query' , array( 'SELECT COUNT(id) FROM locatie WHERE gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001)' ) );
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $this->mapper, 'SELECT * FROM locatie WHERE gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001)' );
        $this->assertIsA( $query, 'KVDdom_PDOChunkyQuery' );
    }

    public function testCaseInsensitive( )
    {
        $rs = new MockPDOStatement( );
        $rs->expectOnce( 'fetchColumn' );
        $rs->setReturnValue ( 'fetchColumn' , 255 );
        $this->pdo->setReturnValue( 'query' , $rs );
        $this->pdo->expectOnce( 'query' , array( 'SELECT COUNT(id) FROM locatie where gemeente_id in ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001)' ) );
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $this->mapper, 'select * from locatie where gemeente_id in ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001)' );
        $this->assertIsA( $query, 'KVDdom_PDOChunkyQuery' );
    }

    public function testZoekenProvincie( )
    {
        
        $rs = new MockPDOStatement( );
        $rs->expectOnce( 'fetchColumn' );
        $rs->setReturnValue ( 'fetchColumn' , 255 );
        $this->pdo->setReturnValue( 'query' , $rs );
        $this->pdo->expectOnce( 'query' , array( 'SELECT COUNT(relict.id) FROM mel_master.relict LEFT JOIN mel_gis.locatie ON (  relict.id = mel_gis.locatie.relict_id ) WHERE (  locatie.id IN (  SELECT locatie_id FROM mel_gis.adres WHERE (  provincie_id = 30000 ) ) )' ) );
        $sql = 'SELECT relict.id AS id, naam, is_deel_van, toponiem, locatie.id AS locatie_id, opmerkingen, gevonden, provincie_naam, gemeente_naam, deelgemeente_naam, locatie.bewaard AS bewaard, in_crab  FROM mel_master.relict LEFT JOIN mel_gis.locatie ON (  relict.id = mel_gis.locatie.relict_id ) WHERE (  locatie.id IN (  SELECT locatie_id FROM mel_gis.adres WHERE (  provincie_id = 30000 ) ) )';
        $query = new KVDdom_PDOChunkyQuery( $this->pdo, $this->mapper, $sql , 'relict.id' );
        $this->assertIsA( $query, 'KVDdom_PDOChunkyQuery' );

    }
}
?>
