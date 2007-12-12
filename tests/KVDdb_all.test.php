<?php
error_reporting(E_ALL & ~E_STRICT);
    
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');

define ('KVD_CLASSES_DIR' , '/data/projects/kvd/kvd/classes/');

require_once ( KVD_CLASSES_DIR . 'database/criteria/KVDdb_Criterion.class.php' );
require_once ( KVD_CLASSES_DIR . 'database/criteria/KVDdb_Criteria.class.php' );
require_once ( KVD_CLASSES_DIR . 'database/criteria/KVDdb_SimpleQuery.class.php' );
require_once ( KVD_CLASSES_DIR . 'database/criteria/KVDdb_Join.class.php' );
require_once ( 'database/criteria/KVDdb_Criterion.test.php');
require_once ( 'database/criteria/KVDdb_Criteria.test.php');
require_once ( 'database/criteria/KVDdb_SimpleQuery.test.php');
require_once ( 'database/criteria/KVDdb_Join.test.php');

$test = new GroupTest('KVDgis_AllTests');
$test->addTestCase( new TestOfCriterion());
$test->addTestCase( new TestOfCriteria());
$test->addTestCase( new TestOfJoin());
$test->addTestCase( new TestOfCriteriaWithCriterion());
$test->addTestCase( new TestOfSimpleQuery());
$test->addTestCase( new TestOfSimpleQueryWithCriteria());

$test->run( new TextReporter() );

?>
