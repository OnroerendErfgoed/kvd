<?php
require_once GISMAP . 'exception/KVDgis_MsMapActionBestaatNietException.class.php';

class TestOfMsMapActionBestaatNietException extends UnitTestCase
{
    private $testException;

    function setUp()
    {
        $this->testException = new KVDgis_MsMapActionBestaatNietException ( '[Previous Message]','KVDgis_MsMapActionZoomIn');
    }

    function tearDown()
    {
        $this->testException = null;
    }
    
    function testMessage()
    {
        $this->assertEqual ( $this->testException->getMessage(),'[Previous Message] [MsMapActionBestaatNiet Error: De MsMapAction KVDgis_MsMapActionZoomIn kon niet gevonden worden.]');
    }

    function testActionName()
    {
        $this->assertEqual ( $this->testException->getActionName() , 'KVDgis_MsMapActionZoomIn');    
    }
}
?>
