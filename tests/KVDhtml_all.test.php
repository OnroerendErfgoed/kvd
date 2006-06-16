<?php
Error_Reporting (  E_ALL & ~E_STRICT);

define ( 'KVD_CLASSES_DIR' , '/data/projects/kvd/kvd/classes/');
define ( 'OEI_CLASSES_DIR' , '/data/projects/kvd/oei/classes/');

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');


require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormField.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldFile.class.php');

require_once('html/KVDhtml_FormFieldFile.test.php');

$test = new GroupTest('KVDhtml_AllTests');
$test->addTestCase( new TestOfFormFieldFile());
//$test->addTestCase( new TestOfMapperRegistry());
//$test->addTestCase( new TestOfGenericIdentityMap());
//$test->addTestCase( new TestOfDomainObjectCollection( ));
//$test->addTestCase( new TestOfSessie());
$test->run(new TextReporter());

?>
