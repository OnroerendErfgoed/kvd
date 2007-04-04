<?php
Error_Reporting (  E_ALL & ~E_STRICT);

define ( 'KVD_CLASSES_DIR' , '/data/projects/kvd/kvd/classes/');
define ( 'OEI_CLASSES_DIR' , '/data/projects/kvd/oei/classes/');

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');


require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormField.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldFile.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldCheckbox.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldHidden.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldPassword.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldText.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldRadio.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldReset.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldSubmit.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldTextarea.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldSelect.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldDate.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_FormFieldFactory.class.php');
require_once( KVD_CLASSES_DIR . 'html/KVDhtml_Tools.class.php');

//require_once( KVD_CLASSES_DIR . 'html/KVDhtml_OptionsHelper.class.php');

require_once('html/KVDhtml_FormFieldFile.test.php');
require_once('html/KVDhtml_FormFieldCheckbox.test.php');
require_once('html/KVDhtml_FormFieldHidden.test.php');
require_once('html/KVDhtml_FormFieldPassword.test.php');
require_once('html/KVDhtml_FormFieldText.test.php');
require_once('html/KVDhtml_FormFieldRadio.test.php');
require_once('html/KVDhtml_FormFieldReset.test.php');
require_once('html/KVDhtml_FormFieldSubmit.test.php');
require_once('html/KVDhtml_FormFieldTextarea.test.php');
require_once('html/KVDhtml_FormFieldSelect.test.php');
require_once('html/KVDhtml_FormFieldDate.test.php');
require_once('html/KVDhtml_FormFieldFactory.test.php');
require_once('html/KVDhtml_Tools.test.php');

//require_once('html/KVDhtml_OptionsHelper.test.php');


$test = new GroupTest('KVDhtml_AllTests');
$test->addTestCase( new TestOfFormFieldFile());
$test->addTestCase( new TestOfFormFieldCheckbox());
$test->addTestCase( new TestOfFormFieldHidden());
$test->addTestCase( new TestOfFormFieldPassword());
$test->addTestCase( new TestOfFormFieldText());
$test->addTestCase( new TestOfFormFieldRadio());
$test->addTestCase( new TestOfFormFieldReset());
$test->addTestCase( new TestOfFormFieldSubmit());
$test->addTestCase( new TestOfFormFieldTextarea());
$test->addTestCase( new TestOfFormFieldSelect());
$test->addTestCase( new TestOfFormFieldDate());
$test->addTestCase( new TestOfFormFieldFactory());
$test->addTestCase( new TestOfTools());

//$test->addTestCase( new TestOfOptionsHelper( ));
$test->run(new TextReporter());

?>
