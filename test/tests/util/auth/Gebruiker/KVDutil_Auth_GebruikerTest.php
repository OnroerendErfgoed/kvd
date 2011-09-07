<?php
/**
 * Test class for KVDutil_Auth_Gebruiker.
 * Generated by PHPUnit on 2011-09-05 at 14:11:41.
 */
class KVDutil_Auth_GebruikerTest extends PHPUnit_Framework_TestCase {

    /**
     * @var KVDutil_Auth_Gebruiker
     */
    protected $do;

    /**
     * @var KVDutil_Auth_IProvider
     */
    protected $provider;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $connectie['goessebr'] = array(
            'paswoord' => 'encryrpted_pas',
            'familienaam' => 'Goessens',
            'voornaam' => 'Bram',
            'mail' => 'bram.goessens@rwo.vlaanderen.be',
            'telefoon' => '02/5531868',
            'rollen'=> array(
                'oeps'=>array(
                    'oeps.beheerder'=>array(
                        'naam'=>'Beheerder oeps',
                        'beschrijving'=>'beschrijving beheerder oeps'
                    )
                ),
                'oar'=> array(
                    'oar.beheerder'=>array(
                        'naam'=>'Beheerder oar',
                        'beschrijving'=>'beschrijving beheerder oar'
                    )
                ),
                'cai'=> array(
                    'cai.beheerder'=>array(
                        'naam'=>'Beheerder cai',
                        'beschrijving'=>'beschrijving beheerder cai'
                    ),
                    'cai.invoerder'=>array(
                        'naam'=>'Invoerder cai',
                        'beschrijving'=>'beschrijving Invoerder cai'
                    )
                )
            )
        );
        $this->provider = new KVDutil_Auth_ArrayProvider( $connectie );
        $this->do = new KVDutil_Auth_Gebruiker( $this->provider, 'goessebr', 'goessebr',
                'wachtwoord', 'Bram', 'Goessens', 'bram.goessens@rwo.vlaanderen.be', '025531868');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->do = null;
        $this->provider = null;
    }

    public function testGetters( )
    {
        $this->assertEquals( 'goessebr', $this->do->getID() );
        $this->assertEquals( 'goessebr', $this->do->getGebruikersnaam() );
        $this->assertEquals( 'Bram', $this->do->getVoornaam() );
        $this->assertEquals( 'Goessens', $this->do->getFamilieNaam() );
        $this->assertEquals( 'wachtwoord', $this->do->getWachtwoord() );
        $this->assertEquals( 'bram.goessens@rwo.vlaanderen.be', $this->do->getEmail() );
        $this->assertEquals( '025531868', $this->do->getTelefoon() );
    }

    public function testGetOmschrijving() {
        $this->assertEquals( 'Bram Goessens', $this->do->getOmschrijving() );
    }

    public function testGetClass() {
        $this->assertEquals( 'KVDutil_Auth_Gebruiker', $this->do->getClass() );

    }

    public function test__toString() {
        $this->assertEquals( $this->do->getOmschrijving(), (string) $this->do );
    }

    public function testCheckRollenVoordatRollenGeladen() {
        $this->assertFalse( $this->do->checkRollen() );
    }

    public function testGetRollenVoorApplicatie() {
        //We hebben een dummy object nodig dat een methode getId() bevat.
        //We gebruiken hiervoor zonder noemenswaardige redenen de gebruikerclass met ID cai
        $applicatie = new KVDutil_Auth_Gebruiker( $this->provider, 'cai', '' );
        
        $rollen = $this->do->getRollenVoorApplicatie( $applicatie );
        $this->assertTrue( $this->do->checkRollen() );
        $this->assertInstanceOf( 'KVDutil_Auth_RolCollectie', $rollen);
        $this->assertEquals( 2, $rollen->count());
    }

    public function testGetRollenVoorApplicatieNaam() {
        $geb = new KVDutil_Auth_Gebruiker( $this->provider, 'goessebr', 'goessebr',
                'wachtwoord', 'Bram', 'Goessens', 'bram.goessens@rwo.vlaanderen.be', '025531868');
        $this->assertFalse( $geb->checkRollen() );
        $rollen = $geb->getRollenVoorApplicatieNaam( 'cai' );
        $this->assertTrue( $geb->checkRollen() );
        $this->assertInstanceOf( 'KVDutil_Auth_RolCollectie', $rollen);
        $this->assertEquals( 2, $rollen->count());
    }

    public function testIsNull() {
       $this->assertFalse( $this->do->isNull() );
    }

    public function testNewNull() {
        $geb = KVDutil_AUTH_Gebruiker::newNull();
        $this->assertEquals( 'KVDutil_Auth_Gebruiker', $geb->getClass() );
        $this->assertTrue( $geb->isNull() );
    }
}
?>