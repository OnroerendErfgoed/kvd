<?php
class TestOfDownloadModel extends UnitTestCase
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
            $this->assertEqual( 'test.txt' , $bestand->getFileName( ) );
            $this->assertEqual( 0 , $bestand->getSize( ) );
        }
    }

}
?>
