<?php
require_once GISMAP . 'KVDgis_MsMapState.class.php';

class TestOfMsMapState extends UnitTestCase
{
    private $_testMsMapState;
    private $testExtent;
    private $testMapStateArray;
    private $testInputLayers;
    private $testOutputLayers;

    function setUp()
    {
        $this->_testMsMapState = new KVDgis_MsMapState ();
        $this->testExtent = array ( 'minx'  =>  22500,
                                    'miny'  =>  110000,
                                    'maxx'  =>  260000,
                                    'maxy'  =>  288000
                                    );
        $layerOne = new stdClass();
        $layerOne->on = true;
        $layerOne->name = 'testLayer';
        $layerTwo = new stdClass();
        $layerTwo->on = false;
        $layerTwo->name = 'testLayerTwo';
        $this->testInputLayers = array ( $layerOne , $layerTwo );
        $this->testOutputLayers = array ( array ( 'on' => true, 'name' => 'testLayer'),
                                          array ( 'on' => false, 'name' => 'testLayerTwo'));                            
        $this->testMapStateArray = array (  'legendHtml'        =>  '<p>test</p>',
                                            'mapImageUrl'       =>  'testM.jpg',
                                            'scale'             =>  25000,
                                            'mapImageHeight'    =>  600,
                                            'mapImageWidth'     =>  800,
                                            'currentExtent'     =>  $this->testExtent,
                                            'layers'            =>  $this->testInputLayers,
                                            'queryFile'         =>  null
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
        $this->assertEqual ( $this->_testMsMapState->getLegendHtml(),'<p>test</p>');
        $this->assertEqual ( $this->_testMsMapState->getMapImageUrl(),'testM.jpg');
        $this->assertEqual ( $this->_testMsMapState->getScale(),25000);
        $this->assertEqual ( $this->_testMsMapState->getMapImageWidth(),800);
        $this->assertEqual ( $this->_testMsMapState->getMapImageHeight(),600);
        $this->assertEqual ( $this->_testMsMapState->getCurrentExtent(),$this->testExtent);
        $this->assertEqual ( $this->_testMsMapState->getLayers(),$this->testOutputLayers);
        
    }

    function testConvertToArray()
    {
        $this->_testMsMapState->setLegendHtml('<p>test</p>');
        $this->_testMsMapState->setMapImageUrl('testM.jpg');
        $this->_testMsMapState->setScale(25000);
        $this->_testMsMapState->setMapImageHeight(600);
        $this->_testMsMapState->setMapImageWidth(800);
        $this->_testMsMapState->setCurrentExtent($this->testExtent);
        $this->_testMsMapState->setLayers($this->testInputLayers);
        $testMapStateArray = $this->testMapStateArray;
        $testMapStateArray['layers'] = $this->testOutputLayers;
        $this->assertEqual ( $this->_testMsMapState->convertToArray() , $testMapStateArray , 'convertToArray geeft niet de juiste output.');
    }
}
?>
