<?php

class TestOfWachtwoordGenerator extends UnitTestCase
{
    private $generator;
    
    public function setUp( )
    {
        $generator = new KVDutil_WachtwoordGenerator( );
    }

    public function tearDown( )
    {
        $this->generator = null;
    }

    public function testDefault( )
    {
        $generator = new KVDutil_WachtwoordGenerator( );
        $wachtwoord = $generator->generate( );
        $this->assertEquals( strlen( $wachtwoord ) , 8 );
        $this->assertRegExp( '/[0-9]+/' , $wachtwoord );
        $this->assertRegExp( '/[a-z]+/', $wachtwoord );
        $this->assertNotRegExp( '/[A-Z]/' , $wachtwoord );
    }

    public function testLengte( )
    {
        $lengtes = array ( 8 , 15 , 6 );
        foreach ( $lengtes as $lengte ) {
            $generator = new KVDutil_WachtwoordGenerator( $lengte );
            $wachtwoord = $generator->generate( );
            $this->assertEquals( strlen( $wachtwoord ) , $lengte );
        }
    }

    public function testTeKort( )
    {
        try {
            $generator = new KVDutil_WachtwoordGenerator( 3 );
        } catch ( InvalidArgumentException $e ) {
            return;
        }
        $this->fail( 'InvalidArgumentException', 'Wacthwoorden genereren met een te korte lengte moet een exception geven.');
    }
    public function testWithHoofdletters( )
    {
        $generator = new KVDutil_WachtwoordGenerator( 8 , true );
        $wachtwoord = $generator->generate( );
        $this->assertRegExp( '/[A-Z]+/' , $wachtwoord );
        $this->assertRegExp( '/[0-9]+/' , $wachtwoord );
        $this->assertRegExp( '/[a-z]+/' , $wachtwoord );
    }

    public function testMultipleAreDifferent( )
    {
        $generator = new KVDutil_WachtwoordGenerator( );
        $wachtwoorden = array( );
        for ( $i = 0 ; $i<5; $i++) {
            $wachtwoorden[$i] = $generator->generate( );
            if ( $i > 0 ) {
                $this->assertNotEquals( $wachtwoorden[$i] , $wachtwoorden[$i-1] );
            }
        }
    }
}
?>
