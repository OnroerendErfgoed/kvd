<?php

class TestOfPDOTransaction extends UnitTestCase
{
    public function setUp( )
    {
        Mock::generate( 'PDO' );
        $this->conn = new MockPDO( );
    }

    public function tearDown( )
    {
        $this->conn = null;
    }
    
    public function testConnectionIsInTransaction( )
    {
        KVDutil_PDOTransaction::beginTransaction( $this->conn );
        $this->assertTrue( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
        KVDutil_PDOTransaction::commit( $this->conn );
        $this->assertFalse( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
    }

    public function testCommit( )
    {
        $this->conn->expectOnce( 'beginTransaction' );
        $this->conn->expectOnce( 'commit' );
        $this->assertFalse( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
        KVDutil_PDOTransaction::beginTransaction( $this->conn );
        $this->assertTrue( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
        KVDutil_PDOTransaction::commit( $this->conn );
        $this->assertFalse( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
    }

    public function testRollBack( )
    {
        $this->assertFalse( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
        KVDutil_PDOTransaction::beginTransaction( $this->conn );
        $this->assertTrue( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
        KVDutil_PDOTransaction::rollBack( $this->conn );
        $this->assertFalse( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
    }

    public function testNestedConnection( )
    {
        $this->assertFalse( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
        KVDutil_PDOTransaction::beginTransaction( $this->conn );
        $this->assertTrue( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
        KVDutil_PDOTransaction::beginTransaction( $this->conn );
        $this->assertTrue( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
        KVDutil_PDOTransaction::commit( $this->conn );
        $this->assertTrue( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
        KVDutil_PDOTransaction::commit( $this->conn );
        $this->assertFalse( KVDutil_PDOTransaction::isInTransaction($this->conn ) );
    }

}
?>
