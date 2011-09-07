<?php
class KVDutil_Auth_RolCollectieTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->do = new KVDutil_Auth_Rol( 'dn=oeps_beheer', 'OEPS beheerder',
                'Beheerder Inventaris Onroerend Erfgoed');
        $this->do2 = new KVDutil_Auth_Rol( 'dn=oeps_invoerder', 'OEPS invoerder',
                'Invoerder Inventaris Onroerend Erfgoed');
        $this->do3 = new KVDutil_Auth_Rol( 'dn=oeps_lezer', 'OEPS lezer',
                'Lezer Inventaris Onroerend Erfgoed');
        $this->testarray = array ( $this->do, $this->do2, $this->do3 );
        $this->collectie = new KVDutil_Auth_RolCollectie( $this->testarray );
    }

    public function tearDown( )
    {
        $this->do = null;
        $this->do2 = null;
        $this->do3 = null;
        $this->testarray = null;
        $this->collectie = null;
    }

    public function testObjectsType( )
    {
        $this->assertInstanceOf('KVDutil_Auth_Rol', $this->do);
        $this->assertInstanceOf('KVDutil_Auth_Rol', $this->collectie->current());
        $this->collectie->next();
        $this->assertInstanceOf('KVDutil_Auth_Rol', $this->collectie->current());
        $this->collectie->next();
        $this->assertInstanceOf('KVDutil_Auth_Rol', $this->collectie->current());
    }

    /**
     * @expectedException KVDdom_OngeldigTypeException
     */
    public function testAddWrongType() {
        $this->collectie->add( new KVDutil_AUTH_NullGebruiker() );
    }

    /**
     * @expectedException KVDdom_OngeldigTypeException
     */
    public function testReplaceWrongType() {
        $this->collectie->replace( new KVDutil_AUTH_NullGebruiker() );
    }

    /**
     * @expectedException KVDdom_OngeldigTypeException
     */
    public function testRemoveWrongType() {
        $this->collectie->remove( new KVDutil_AUTH_NullGebruiker() );
    }
}
?>
