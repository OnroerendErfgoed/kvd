<?php
error_reporting ( E_ALL & ~E_STRICT );

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');

define ( 'KVD_CLASSES_DIR' , '/data/projects/ds/oeps/libs/kvd/classes/');
define ( 'KVD_TESTS_DIR' , '/data/projects/ds/oeps/libs/kvd/tests/');

define ( 'CRABUSER' , 'VIOE' );
define ( 'CRABPWD' , 'GISTLIBE' );

require_once ( KVD_CLASSES_DIR . 'util/KVDutil_DimensieConvertor.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_Dimensie.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_DimensieNaamAfkorting.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_Dimensies.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_VoorwerpAfmeting.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_VoorwerpGewicht.class.php');
require_once ( KVD_CLASSES_DIR . 'util/Gateway/KVDutil_Gateway.interface.php');
require_once ( KVD_CLASSES_DIR . 'util/Gateway/KVDutil_GatewayFactory.class.php');
require_once ( KVD_CLASSES_DIR . 'util/Gateway/KVDutil_GatewayRegistry.class.php');
require_once ( KVD_CLASSES_DIR . 'util/Gateway/KVDutil_GatewayUnavailableException.class.php');
require_once ( KVD_CLASSES_DIR . 'gis/crab/KVDgis_Crab2Gateway.class.php');
require_once ( KVD_CLASSES_DIR . 'gis/crab/KVDgis_CrabCache.class.php');
require_once ( KVD_CLASSES_DIR . 'gis/crab/KVDgis_NullCrabCache.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_HuisnummerLabelSplitter.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_WachtwoordGenerator.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_PDOTransaction.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_DownloadModel.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_BestandenToolkit.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_DateRange.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_HuisnummerFacade.class.php');


//require_once ( 'util/KVDutil_DimensieConvertor.test.php');

$test = new GroupTest('KVDutil_AllTests');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_DimensieConvertor.test.php');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_Dimensie.test.php');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_DimensieNaamAfkorting.test.php');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_Dimensies.test.php');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_GatewayFactory.test.php');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_GatewayRegistry.test.php');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_HuisnummerLabelSplitter.test.php');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_WachtwoordGenerator.test.php');
//$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_PDOTransaction.test.php' );
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_DownloadModel.test.php' );
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_BestandenToolkit.test.php' );
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_DateRange.test.php' );
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_HuisnummerFacade.test.php');
$test->run(new TextReporter());

?>
