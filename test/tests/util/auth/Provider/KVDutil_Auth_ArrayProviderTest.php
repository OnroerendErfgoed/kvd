<?php

class KVDutil_Auth_ArrayProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var KVDutil_Auth_ArrayProvider
     */
    protected $object;

    public function setUp( )
    {
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
        $this->object = new KVDutil_Auth_ArrayProvider( $connectie );
    }

    public function tearDown( )
    {
        $this->object = null;
    }

    public function testExists( )
    {
        $this->assertInstanceOf('KVDutil_Auth_ArrayProvider', $this->object);
    }

    public function testAanmeldenFoutWachtwoord( )
    {
        $auth = $this->object->aanmelden( 'goessebr', 'foutepas' );
        $this->assertFalse($auth);
    }

    public function testAanmeldenFouteGebruikersnaam( )
    {
        $auth = $this->object->aanmelden( 'nietindatabank', 'pas' );
        $this->assertFalse($auth);
    }

    public function testAanmelden() {
        $gebruiker = $this->object->aanmelden( 'goessebr', 'encryrpted_pas' );
        $this->assertInstanceOf('KVDutil_Auth_Gebruiker', $gebruiker);
        $this->assertEquals( 'Goessens', $gebruiker->getFamilienaam());
        $this->assertEquals( 'Bram', $gebruiker->getVoornaam());
        $this->assertEquals( 'bram.goessens@rwo.vlaanderen.be', $gebruiker->getEmail());
        $this->assertEquals( 'encryrpted_pas', $gebruiker->getWachtwoord());
        $this->assertEquals( '02/5531868', $gebruiker->getTelefoon());
        $this->assertEquals( 'goessebr', $gebruiker->getGebruikersnaam());
    }

    public function testGetRollenVoorApplicatie() {
        $applicatie = $this->getMock('FictieveApplicatieClass', array('getId'));
        $applicatie->expects($this->once())
                    ->method('getId')
                    ->will($this->returnValue('cai'));
        $gebruiker = $this->object->aanmelden( 'goessebr', 'encryrpted_pas' );

        $rollen = $this->object->getRollenVoorApplicatie( $gebruiker, $applicatie );
        $this->assertInstanceOf( 'KVDutil_Auth_RolCollectie', $rollen);
        $this->assertEquals( 2, $rollen->count());
        $rol = $rollen->getFirst();
        $this->assertEquals( 'cai.beheerder', $rol->getId());
        $this->assertEquals( 'Beheerder cai', $rol->getNaam());
        $this->assertEquals( 'beschrijving beheerder cai', $rol->getBeschrijving());
    }

    public function testGetRollenVoorApplicatieNaam() {
        $gebruiker = $this->object->aanmelden( 'goessebr', 'encryrpted_pas' );
        
        $rollen = $this->object->getRollenVoorApplicatieNaam( $gebruiker, 'cai' );
        $this->assertInstanceOf( 'KVDutil_Auth_RolCollectie', $rollen);
        $this->assertEquals( 2, $rollen->count());
        $rol = $rollen->getFirst();
        $this->assertEquals( 'cai.beheerder', $rol->getId());
        $this->assertEquals( 'Beheerder cai', $rol->getNaam());
        $this->assertEquals( 'beschrijving beheerder cai', $rol->getBeschrijving());
    }
}
?>