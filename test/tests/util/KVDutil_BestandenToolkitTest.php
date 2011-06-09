<?php
require_once 'PHPUnit/Framework.php';

class KVDutil_BestandenToolkitTest extends PHPUnit_Framework_TestCase
{

    /**
     * testFormatBestandsGrootte 
     *
     * @dataProvider provider 
     * @return void
     */
    public function testFormatBestandsGrootte( $bytes, $format )
    {
        $this->assertEquals( KVDutil_BestandenToolkit::formatBestandsGrootte( $bytes ), $format );
    }

    public function provider(  )
    {
        return array( 
            array ( 0, '0B'),
            array ( 720, '720B'),
            array ( 1024, '1KB' ),
            array ( 1026, '1KB' ),
            array ( 1030, '1.01KB' ),
            array ( 1048578, '1MB' ),
            array ( 1073741824, '1GB' )
                    );
    }
}

?>
