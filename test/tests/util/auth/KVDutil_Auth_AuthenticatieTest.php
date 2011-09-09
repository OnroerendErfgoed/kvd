<?php
class KVDutil_Auth_AuthenticatieTest extends PHPUnit_Framework_TestCase {

    /**
     * @var KVDutil_Auth_Authenticatie
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $connectie['goessebr'] = array(
            'paswoord' => 'encrypted_pas',
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
        $provider = new KVDutil_Auth_ArrayProvider( $connectie );
        $this->object = new KVDutil_Auth_Authenticatie($provider);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->object = null;
    }

    public function testAanmelden() {
        $this->assertTrue(
                $this->object->aanmelden( 'goessebr', 'encrypted_pas'));
        $this->assertFalse(
                $this->object->aanmelden( 'doesntexist', 'encrypted_pas' ));
        $this->assertFalse(
                $this->object->aanmelden( 'goessebr', 'foutpaswoord' ));
    }

    public function testAfmelden() {
        $this->assertTrue( $this->object->afmelden());

        $this->object->aanmelden( 'goessebr', 'encrypted_pas');
        $this->assertTrue( $this->object->afmelden());
    }

    public function testGetGebruiker() {
        $this->assertInstanceOf( 'KVDutil_Auth_Gebruiker', $this->object->getGebruiker() );
        $this->assertTrue( $this->object->getGebruiker()->isNull());

        $this->object->aanmelden( 'goessebr', 'encrypted_pas');
        $this->assertInstanceOf( 'KVDutil_Auth_Gebruiker', $this->object->getGebruiker() );
        $this->assertFalse( $this->object->getGebruiker()->isNull());
    }

    public function testIsAangemeld() {
        $this->assertFalse( $this->object->isAangemeld());

        $this->object->aanmelden( 'goessebr', 'encrypted_pas');
        $this->assertTrue( $this->object->isAangemeld());
    }

    public function testSetStatus() {
        $this->object->setStatus($this->object->getAangemeldStatus());
        //isAangemeld heeft true terug als het gedelegeerd wordt naar het AangemeldStatus
        $this->assertTrue( $this->object->isAangemeld());

        $this->object->setStatus($this->object->getAfgemeldStatus());
        //isAangemeld heeft false terug als het gedelegeerd wordt naar het AfgemeldStatus
        $this->assertFalse( $this->object->isAangemeld());
    }

    public function testGetAangemeldStatus() {
        $this->assertInstanceOf( 'KVDutil_Auth_AangemeldStatus', $this->object->getAangemeldStatus());
    }

    public function testGetAfgemeldStatus() {
        $this->assertInstanceOf( 'KVDutil_Auth_AfgemeldStatus', $this->object->getAfgemeldStatus());
    }

}

?>
