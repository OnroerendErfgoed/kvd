<?php

class DownloadModelTest extends PHPUnit_Framework_TestCase
{
    private $map;
    
    public function setUp( )
    {
        $this->map = dirname ( __FILE__ ) . '/tmp';
        mkdir ( $this->map );
        touch ( $this->map . '/test.txt' );
    }

    public function tearDown( )
    {
        unlink ( $this->map . '/test.txt' );
        rmdir ( $this->map );
    }

    public function testCorrect( )
    {
        $model = new KVDutil_DownloadModel( $this->map);
        foreach ( $model as $bestand ) {
            $this->assertEquals( 'test.txt' , $bestand->getFileName( ) );
            $this->assertEquals( 0 , $bestand->getSize( ) );
        }
    }

    /**
     * testOngeldigeMapGeeftFout 
     * 
     * @expectedException   InvalidArgumentException
     * @return void
     */
    public function testOngeldigeMapGeeftFout( )
    {
        $model = new KVDutil_DownloadModel( '/niet_bestaande_map' );

    }

}
?>
