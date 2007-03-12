<?php
class TestOfBestandenToolkit extends UnitTestCase
{
    public function testFormatBestandsGrootte( )
    {
        $this->assertEqual ( KVDutil_BestandenToolkit::formatBestandsGrootte( 0 ) , '0B' );
        $this->assertEqual ( KVDutil_BestandenToolkit::formatBestandsGrootte( 720 ) , '720B' );
        $this->assertEqual ( KVDutil_BestandenToolkit::formatBestandsGrootte( 1024 ) , '1KB' );
        $this->assertEqual ( KVDutil_BestandenToolkit::formatBestandsGrootte( 1026 ) , '1KB' );
        $this->assertEqual ( KVDutil_BestandenToolkit::formatBestandsGrootte( 1030 ) , '1.01KB' );
        $this->assertEqual ( KVDutil_BestandenToolkit::formatBestandsGrootte( 1048576 ) , '1MB' );
        $this->assertEqual ( KVDutil_BestandenToolkit::formatBestandsGrootte( 1073741824 ) , '1GB' );
    }
}
?>
