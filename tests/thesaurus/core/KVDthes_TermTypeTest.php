<?php

require_once ( 'PHPUnit/Framework.php' );

class KVDthes_TermTypeTest extends PHPUnit_Framework_TestCase
{
    protected $tt;

    public function setUp( )
    {
        $this->tt = new KVDthes_TermType( 'PT', 'Voorkeursterm' );
    }

    public function tearDown( )
    {
        $this->tt = null;
    }

    public function testAll( )
    {
        $this->assertEquals( 'PT', $this->tt->getId( ) );
        $this->assertEquals( 'Voorkeursterm', $this->tt->getType( ) );
        $this->assertEquals( 'Voorkeursterm', $this->tt->getOmschrijving( ) );
    }
}
?>
