<?php
error_reporting(E_ALL & ~E_STRICT);
    
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');

define ('GISMAP' , 'c:\opt\kvd\classes\gis\\');
define ('DATABASEMAP' , 'c:\opt\kvd\classes\database\\');
define ('GISTESTMAP' , 'c:\opt\kvd\tests\gis\\');
define ('KVDGISMAPFILE' , 'c:/opt/kvd/tests/gis/KVDgis.map');
define ('KVDGISTEMPDIR' , 'c:/opt/tmp/');

//require_once('gis/KVDgis_MsMapController.test.php');
//require_once(GISTESTMAP.'KVDgis_MsMapActionZoomIn.test.php');
require_once(GISTESTMAP.'KVDgis_MsMapState.test.php');
//require_once(GISTESTMAP.'KVDgis_MsMapAjaxHandler.test.php');
require_once(GISTESTMAP.'exception/KVDgis_MsMapActionBestaatNietException.test.php');
require_once(GISTESTMAP.'geometry/KVDgis_GeomPoint.test.php');

$test = new GroupTest('KVDgis_AllTests');
$test->addTestCase( new TestOfMsMapActionBestaatNietException());
$test->addTestCase( new TestOfMsMapState());
//$test->addTestCase( new TestOfMsMapAjaxHandler());
$test->addTestCase( new TestOfGeomPoint());
//$test->addTestCase( new TestOfMsMapActionZoomIn());
//$test->addTestCase( new TestOfSessie());

$test->run( new TextReporter() );

?>
