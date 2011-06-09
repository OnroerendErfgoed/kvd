<?php

class TestOfAuthenticator extends UnitTestCase
{
    public function setUp( )
    {
        Mock::generate( 'PDO' );
        $this->conn = new MockPDO( );
        $this->auth = new KVDutil_Authenticator($this->conn);
    }

    public function tearDown( )
    {
        $this->conn = null;
        $this->auth = null;
    }
    
    public function testLogin( )
    {
			$response = $this->auth->logIn("standadi", "blabla");
			$this->assertTrue($response);
			$this->assertTrue($this->auth->isAuthenticated());
			$this->assertEqual($this->auth->getNaam(), "Standaert");
    }
    public function testLogout()
    {
			$response = $this->auth->logIn("standadi", "blabla");
			$this->assertTrue($response);
			$response = $this->auth-logOut();
			$this->assertTrue($response);
			$this->assertFalse($this->auth->isAuthenticated());
			$this->assertEqual($auth->getNaam(), null);
    }
    public function testLogoutLogout()
    {
 			$response = $this->auth->logOut();
			$this->assertTrue($response);
			$response = $this->auth-logOut();
			$this->assertTrue($response);
			$this->assertFalse($this->auth->isAuthenticated());
			$this->assertEqual($auth->getNaam(), null);   
    }

}
?>
