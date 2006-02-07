<?php
require_once GISMAP . 'KVDgis_MsMapState.class.php';

class TestOfMsMapState extends UnitTestCase
{
    private $_testMsMapState;
    private $testExtent;
    private $testMapStateArray;

    function setUp()
    {
        $this->_testMsMapState = new KVDgis_MsMapState ();
        $this->testExtent = array ( 'minx'  =>  25000,
                                    'miny'  =>  100000,
                                    'maxx'  =>  25000,
                                    'maxy'  =>  100000
                                    );
        $layerOne = new stdClass();
        $layerOne->on = true;
        $layerOne->name = 'testLayer';
        $layerTwo = new stcClass();
        $layerTwo->on = false;
        $layerTwo->name = 'testLayerTwo';
        $this->testInputLayers = array ( $layerOne , $layerTwo );
        $this->testOutputLayers = array ( 0 =>  array ( 'on' => true, 'name' => 'testLayer'),
                                          1 =>  array ( 'on' => false, 'name' => 'testLayerTwo'));
        $this->testMapStateArray = array (  'legendHtml'    =>  '<p>legend</p>',
                                            'mapImageUrl'       =>  'testM.jpg',
                                            'scale'             =>  25000,
                                            'mapImageHeight'    =>  600,
                                            'mapImageWidth'     =>  800,
                                            'currentExtent'     =>  $this->testExtent,
                                            'layers'            =>  $this->testLayers
                                            'queryFile'         =>  'test.qry'
                                            );
    }

    function tearDown()
    {
        $this->_testMsMapState = null;
    }
    
    function testSetMapImageUrl()
    {
        $this->_testMsMapState->setMapImageUrl('testM.jpg');
        $this->assertEqual ( $this->_testMsMapState->getMapImageUrl(),'testM.jpg');
    }

    function testConstructWithArray()
    {
        $this->_testMsMapState = new KVDgis_MsMapState ( $this->testMapStateArray );
        $this->assertEqual ( $this->_testMsMapState->getLegendHtml(),'<p>legend</p>');
        $this->assertEqual ( $this->_testMsMapState->getMapImageUrl(),'testM.jpg');
        $this->assertEqual ( $this->_testMsMapState->getScale(),25000);
        $this->assertEqual ( $this->_testMsMapState->getMapImageWidth(),800);
        $this->assertEqual ( $this->_testMsMapState->getMapImageHeight(),600);
        $this->assertEqual ( $this->_testMsMapState->getCurrentExtent(),$this->testExtent);
        $this->assertEqual ( $this->_testMsMapState->getQueryFile(),'test.qry');
        $this->assertEqual ( $this->_testMsMapState->getCurrentExtent(),$this->testLayers);
    }

    function testConvertToArray()
    {
        $this->_testMsMapState->setLegendHtml('<p>legend</p>');
        $this->_testMsMapState->setMapImageUrl('testM.jpg');
        $this->_testMsMapState->setScale(25000);
        $this->_testMsMapState->setMapImageHeight(600);
        $this->_testMsMapState->setMapImageWidth(800);
        $this->_testMsMapState->setCurrentExtent($this->testExtent);
        $this->_testMsMapState->setQueryFile('test.qry');
        $this->assertEqual ( $this->_testMsMapState->convertToArray() , $this->testMapStateArray);
    }
}
?>
