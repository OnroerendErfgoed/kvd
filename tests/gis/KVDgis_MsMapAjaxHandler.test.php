<?php
error_reporting(E_STRICT);
    
require_once GISMAP . 'KVDgis_MsMapAjaxHandler.class.php';
require_once GISMAP . 'KVDgis_MsMapController.class.php';
require_once GISMAP . 'KVDgis_MsMapState.class.php';
require_once GISMAP . 'mapaction\KVDgis_MsMapAction.class.php';
require_once GISMAP . 'mapaction\KVDgis_MsMapActionZoomInPoint.class.php';
require_once GISMAP . 'mapaction\KVDgis_MsMapActionZoomInRectangle.class.php';
require_once GISMAP . 'mapaction\KVDgis_MsMapActionZoomOut.class.php';
require_once GISMAP . 'mapaction\KVDgis_MsMapActionZoomExtent.class.php';
require_once GISMAP . 'mapaction\KVDgis_MsMapActionZoomScale.class.php';
require_once GISMAP . 'mapaction\KVDgis_MsMapActionPan.class.php';
require_once GISMAP . 'mapaction\KVDgis_MsMapActionZoomProvincie.class.php';
require_once GISMAP . 'mapaction\KVDgis_MsMapActionZoomGemeente.class.php';
require_once GISMAP . 'exception\KVDgis_MsMapActionBestaatNietException.class.php';


require_once (DATABASEMAP . 'KVDdb_ConnectionFactory.class.php');
require_once (DATABASEMAP . 'KVDdb_ConnectionFactoryPDO.class.php');

class TestOfMsMapAjaxHandler extends UnitTestCase
{
    private $testExtent;
    private $testMapStateArray;
    private $testMapStateObject;

    private $_testMsMapAjaxHandler;

    function setUp()
    {
        $this->testExtent = array ( 'minx'  =>  22500,
                                    'miny'  =>  110000,
                                    'maxx'  =>  260000,
                                    'maxy'  =>  288000
                                    );
        $this->testMapStateArray = array (  'legendImageUrl'    =>  'testL.jpg',
                                            'mapImageUrl'       =>  'testM.jpg',
                                            'scale'             =>  25000,
                                            'mapImageHeight'    =>  600,
                                            'mapImageWidth'     =>  800,
                                            'currentExtent'     =>  $this->testExtent,
                                            );
        $this->testMapStateObject = new stdClass();
        $this->testMapStateObject->legendImageUrl = 'testL.jpg';
        $this->testMapStateObject->mapImageUrl = 'testM.jpg';
        $this->testMapStateObject->scale = 25000;
        $this->testMapStateObject->mapImageHeight = 600;
        $this->testMapStateObject->mapImageWidth = 800;
        $this->testMapStateObject->currentExtent = $this->testExtent;
        $this->testMapStateObject->queryFile = null;
        $config = array ( 'alg_gis' => array (  'dsn' => 'pgsql:dbname=CAI;host=localhost',
                                                 'user' => 'postgres',
                                                 'password' => 'foefhal9'));                                    
        $dbConnFactory = new KVDdb_ConnectionFactoryPDO ( $config );                   
        $this->_testMsMapAjaxHandler = new KVDgis_MsMapAjaxHandler( KVDGISMAPFILE , KVDGISTEMPDIR , $dbConnFactory);
    }

    function tearDown()
    {
        $this->_testMsMapAjaxHandler = null;
    }
    
    function testDoMapActionZoomInPoint()
    {
        $actionParams = new stdClass();
        $actionParams->zmX = 162;
        $actionParams->zmY = 200;
        $response = $this->_testMsMapAjaxHandler->doMapAction('ZoomInPoint',$this->testMapStateArray,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
    }

    function testDoMapActionZoomInRectangle()
    {
        $actionParams = new stdClass();
        $actionParams->zmX1 = 162;
        $actionParams->zmY1 = 200;
        $actionParams->zmX2 = 200;
        $actionParams->zmY2 = 244;
        $response = $this->_testMsMapAjaxHandler->doMapAction('ZoomInRectangle',$this->testMapStateArray,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
    }

    function testDoMapActionZoomInPointWithMapStateObject()
    {
        $actionParams = new stdClass();
        $actionParams->zmX = 162;
        $actionParams->zmY = 200;
        $response = $this->_testMsMapAjaxHandler->doMapAction('ZoomInPoint',$this->testMapStateObject,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
    }

    function testDoMapActionZoomOut()
    {
        $actionParams = new stdClass();
        $actionParams->zmX = 162;
        $actionParams->zmY = 200;
        $testMapStateArray = $this->testMapStateArray;
        $testExtent = array ( 'minx'  =>  75000,
                              'miny'  =>  150000,
                              'maxx'  =>  98000,
                              'maxy'  =>  200000
                             );
        $testMapStateArray['currentExtent'] = $testExtent;
        $response = $this->_testMsMapAjaxHandler->doMapAction('ZoomOut',$testMapStateArray,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
    }

    function testDoMapActionZoomExtent()
    {
        $actionParams = new stdClass();
        $actionParams->zmX = 162;
        $actionParams->zmY = 200;
        $response = $this->_testMsMapAjaxHandler->doMapAction('ZoomExtent',$this->testMapStateArray,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
    }

    function testDoMapActionZoomScale()
    {
        $actionParams = new stdClass();
        $actionParams->zmScale = 25000;
        $response = $this->_testMsMapAjaxHandler->doMapAction('ZoomScale',$this->testMapStateArray,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
        $this->assertEqual ( $response->scale , 25000 );
    }

    function testDoMapActionPan()
    {
        $actionParams = new stdClass();
        $actionParams->panX = 162;
        $actionParams->panY = 200;
        $testMapStateArray = $this->testMapStateArray;
        $testExtent = array ( 'minx'  =>  75000,
                              'miny'  =>  150000,
                              'maxx'  =>  98000,
                              'maxy'  =>  200000
                             );
        $testMapStateArray['currentExtent'] = $testExtent;
        $response = $this->_testMsMapAjaxHandler->doMapAction('Pan',$testMapStateArray,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
    }

    function testDoMapActionZoomProvincie()
    {
        $actionParams = new stdClass();
        $actionParams->zmProvincie = 70000;
        $response = $this->_testMsMapAjaxHandler->doMapAction('ZoomProvincie',$this->testMapStateObject,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
        $this->assertTrue($response->scale > 100000);
        $this->assertNotNull($response->queryFile);
    }

    function testDoMapActionZoomProvincieTwice()
    {
        $actionParams = new stdClass();
        $actionParams->zmProvincie = 70000;
        $response = $this->_testMsMapAjaxHandler->doMapAction('ZoomProvincie',$this->testMapStateObject,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
        $this->assertTrue($response->scale > 100000);
        $this->assertNotNull($response->queryFile);
        $actionParams = new stdClass();
        $actionParams->zmProvincie = 10000;
        $response = $this->_testMsMapAjaxHandler->doMapAction('ZoomProvincie',$response,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
        $this->assertTrue($response->scale > 100000);
        $this->assertNotNull($response->queryFile);
    }

    function testDoMapActionZoomGemeente()
    {
        $actionParams = new stdClass();
        $actionParams->zmGemeente = 44001;
        $response = $this->_testMsMapAjaxHandler->doMapAction('ZoomGemeente',$this->testMapStateObject,$actionParams);
        print_r($response);
        $this->assertIsA($response,'stdClass');
        $this->assertTrue($response->scale > 10000);
        $this->assertNotNull($response->queryFile);
    }
}
?>
