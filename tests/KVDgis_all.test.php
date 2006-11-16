<?php
error_reporting(E_ALL & ~E_STRICT);
    
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');

define ('GISMAP' , '/data/projects/kvd/kvd/classes/gis/');
define ('GISTESTMAP' , '/data/projects/kvd/kvd/tests/gis/');
define ('UTILMAP' , '/data/projects/kvd/kvd/classes/util/');

define ( 'CRABUSER' , 'VIOE' );
define ( 'CRABPWD' , 'GISTLIBE' );
//define ('KVDGISMAPFILE' , 'c:/opt/kvd/tests/gis/KVDgis.map');
//define ('KVDGISTEMPDIR' , 'c:/opt/tmp/');

//require_once('gis/KVDgis_MsMapController.test.php');
//require_once(GISTESTMAP.'KVDgis_MsMapActionZoomIn.test.php');
//require_once(GISTESTMAP.'KVDgis_MsMapState.test.php');
//require_once(GISTESTMAP.'KVDgis_MsMapAjaxHandler.test.php');
//require_once(GISTESTMAP.'exception/KVDgis_MsMapActionBestaatNietException.test.php');
require_once ( UTILMAP . 'Gateway/KVDutil_Gateway.interface.php' );
require_once ( UTILMAP . 'Gateway/KVDutil_GatewayUnavailableException.class.php' );
require_once ( GISMAP . 'crab/KVDgis_Crab1Gateway.class.php');
require_once ( GISMAP . 'crab/KVDgis_Crab2Gateway.class.php');
require_once ( GISMAP . 'geometry/KVDgis_GeomGeometry.class.php');
require_once ( GISMAP . 'geometry/KVDgis_GeomPoint.class.php');
require_once ( GISMAP . 'geometry/KVDgis_GeomMultiPoint.class.php');

require_once ( GISTESTMAP.'geometry/KVDgis_GeomPoint.test.php');
require_once ( GISTESTMAP.'geometry/KVDgis_GeomMultiPoint.test.php');
require_once ( GISTESTMAP.'crab/KVDgis_Crab1Gateway.test.php');
require_once ( GISTESTMAP.'crab/KVDgis_Crab2Gateway.test.php');
require_once ( GISTESTMAP.'crab/KVDgis_CrabCache.test.php');
require_once ( GISTESTMAP.'crab/KVDgis_NullCrabCache.test.php');

$test = new GroupTest('KVDgis_AllTests');
//$test->addTestCase( new TestOfMsMapActionBestaatNietException());
//$test->addTestCase( new TestOfMsMapState());
//$test->addTestCase( new TestOfMsMapAjaxHandler());
$test->addTestCase( new TestOfGeomPoint());
$test->addTestCase( new TestOfGeomMultiPoint());
//$test->addTestCase( new TestOfCrab1Gateway( ) );
$test->addTestCase( new TestOfCrab2Gateway( ) );
$test->addTestCase( new TestofCrabCache( ) );
$test->addTestCase( new TestofNullCrabCache( ) );
//$test->addTestCase( new TestOfMsMapActionZoomIn());
//$test->addTestCase( new TestOfSessie());

$test->run( new TextReporter() );

?>
