<?php
error_reporting ( E_ALL );

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');

define ( 'KVD_CLASSES_DIR' , '/data/projects/kvd/mel/webapp/lib/kvd/classes/');
define ( 'KVD_TESTS_DIR' , '/data/projects/kvd/mel/webapp/lib/kvd/tests/');

require_once ( KVD_CLASSES_DIR . 'util/KVDutil_DimensieConvertor.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_Dimensie.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_DimensieNaamAfkorting.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_Dimensies.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_VoorwerpAfmeting.class.php');
require_once ( KVD_CLASSES_DIR . 'util/KVDutil_VoorwerpGewicht.class.php');

//require_once ( 'util/KVDutil_DimensieConvertor.test.php');

$test = new GroupTest('KVDutil_AllTests');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_DimensieConvertor.test.php');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_Dimensie.test.php');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_DimensieNaamAfkorting.test.php');
$test->addTestFile( KVD_TESTS_DIR . 'util/KVDutil_Dimensies.test.php');
$test->run(new TextReporter());

?>
