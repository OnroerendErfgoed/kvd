<?php
class KVDutil_Auth_AangemeldStatusTest extends PHPUnit_Framework_TestCase {

    /**
     * @var KVDutil_Auth_AangemeldStatus
     */
    protected $object;

    /**
     * @var MockAuthenticatie
     */
    protected $authenticatie;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        // Get a Mock authenticatie object to work with.
        $classToMock = 'KVDutil_Auth_Authenticatie';
        $methodsToMock = array('setStatus', 'getAfgemeldStatus');
        $mockConstructorParams = array();
        $mockClassName = '';
        $callMockConstructor = false;

        $this->authenticatie = $this->getMock($classToMock,
                                     $methodsToMock,
                                     $mockConstructorParams,
                                     $mockClassName,
                                     $callMockConstructor);

        $this->object = new KVDutil_Auth_AangemeldStatus( $this->authenticatie );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->authenticatie = null;
        $this->object = null;
    }

    public function testAfmelden() {
        $this->authenticatie->expects($this->once())
                 ->method('getAfgemeldStatus')
                 ->will( $this->returnValue(new KVDutil_Auth_AfgemeldStatus(
                            $this->authenticatie, $this->getMock('KVDutil_Auth_IProvider'))));
        $this->assertTrue($this->object->afmelden());
    }
    
    public function testGetGebruiker() {
        $this->assertInstanceOf('KVDutil_Auth_Gebruiker', $this->object->getGebruiker());
    }

    public function testSetGebruiker() {
        $gebruiker = new KVDutil_Auth_Gebruiker($this->getMock('KVDutil_Auth_IProvider'),'id','goessebr');
        $this->object->setGebruiker($gebruiker);
        $gebruikerNaSetter = $this->object->getGebruiker();
        $this->assertInstanceOf('KVDutil_Auth_Gebruiker', $gebruikerNaSetter);
        $this->assertEquals('goessebr', $gebruiker->getGebruikersnaam());
    }

    public function testIsAangemeld() {
        $this->assertTrue($this->object->isAangemeld());
    }

}

?>
