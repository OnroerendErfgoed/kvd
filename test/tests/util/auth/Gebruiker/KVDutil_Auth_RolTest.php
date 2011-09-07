<?php

class KVDutil_Auth_RolTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->do = new KVDutil_Auth_Rol( 'dn=oeps_beheer', 'OEPS beheerder',
                'Beheerder Inventaris Onroerend Erfgoed');
    }

    public function tearDown( )
    {
        $this->do = null;
    }

    public function testExists( )
    {
        $this->assertInstanceOf('KVDutil_Auth_Rol', $this->do);
    }

    public function testGetters( )
    {
        $this->assertEquals( 'dn=oeps_beheer', $this->do->getID() );
        $this->assertEquals( 'OEPS beheerder', $this->do->getNaam() );
        $this->assertEquals( 'Beheerder Inventaris Onroerend Erfgoed',
                $this->do->getBeschrijving());
    }

    public function testGetClass()
    {
        $this->assertEquals( 'KVDutil_Auth_Rol', $this->do->getClass() );
    }

    public function testOmschrijving()
    {
        $this->assertEquals( 'OEPS beheerder (Beheerder Inventaris Onroerend Erfgoed)',
                $this->do->getOmschrijving());
    }

    /*
     * Omschrijving mag geen lege haakjes geven, als er geen rolbeschrijving is opgegeven
     */
    public function testLegeBeschrijving()
    {
        $rol = new KVDutil_Auth_Rol('oeps_tester', 'Tester', '');
        $this->assertStringEndsNotWith( '()', $rol->getOmschrijving() );
    }

    public function testToString( )
    {
        $this->assertEquals( $this->do->getOmschrijving(), (string) $this->do );
    }

    
}
?>