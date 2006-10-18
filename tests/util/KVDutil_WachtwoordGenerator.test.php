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
        $this->assertEqual( strlen( $wachtwoord ) , 8 );
        $this->assertWantedPattern( '/[0-9]+/' , $wachtwoord );
        $this->assertWantedPattern( '/[a-z]+/', $wachtwoord );
        $this->assertNoUnwantedPattern( '/[A-Z]/' , $wachtwoord );
    }

    public function testLengte( )
    {
        $lengtes = array ( 8 , 15 , 6 );
        foreach ( $lengtes as $lengte ) {
            $generator = new KVDutil_WachtwoordGenerator( $lengte );
            $wachtwoord = $generator->generate( );
            $this->assertEqual( strlen( $wachtwoord ) , $lengte );
        }
    }

    public function testTeKort( )
    {
        try {
            $generator = new KVDutil_WachtwoordGenerator( 3 );
            $this->fail( 'Wachtwoorden genereren met een te korte lengte moet een exception geven.' );
        } catch ( InvalidArgumentException $e ){
            $this->pass( );
        } catch ( Exception $e ) {
            $this->fail ( 'Ik had een InvalidArgumentException verwacht.' );
        }
    }
    public function testWithHoofdletters( )
    {
        $generator = new KVDutil_WachtwoordGenerator( 8 , true );
        $wachtwoord = $generator->generate( );
        $this->assertWantedPattern( '/[A-Z]+/' , $wachtwoord );
        $this->assertWantedPattern( '/[0-9]+/' , $wachtwoord );
        $this->assertWantedPattern( '/[a-z]+/' , $wachtwoord );
    }

    public function testMultipleAreDifferent( )
    {
        $generator = new KVDutil_WachtwoordGenerator( );
        $wachtwoorden = array( );
        for ( $i = 0 ; $i<5; $i++) {
            $wachtwoorden[$i] = $generator->generate( );
            if ( $i > 0 ) {
                $this->assertNotEqual( $wachtwoorden[$i] , $wachtwoorden[$i-1] );
            }
        }
    }
}
?>
