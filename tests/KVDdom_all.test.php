<?php
Error_Reporting (  E_ALL & ~E_STRICT);

define ( 'KVD_CLASSES_DIR' , '/data/projects/kvd/kvd/classes/');
define ( 'OEI_CLASSES_DIR' , '/data/projects/kvd/oei/trunk/classes/');
define ( 'AGAVI_DIR' , '/opt/csw/php5/lib/php/agavi/');

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');

require_once( AGAVI_DIR . 'core/AgaviObject.class.php' );
require_once( AGAVI_DIR . 'database/DatabaseManager.class.php' );

require_once( KVD_CLASSES_DIR . 'domain/KVDdom_IdentityMap.class.php');
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_MapperFactory.class.php');
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_MapperRegistry.class.php');
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_DataMapper.class.php');
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_ChangeableDataMapper.class.php');
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_LogableDataMapper.class.php');
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_Sessie.class.php');
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_SystemFields.class.php');
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_DomainObjectCollection.class.php');
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_PDODataMapper.class.php' );
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_PDOChunkyQuery.class.php');
require_once( KVD_CLASSES_DIR . 'domain/KVDdom_SqlLogger.class.php' );


require_once('domain/KVDdom_MapperFactory.test.php');
require_once('domain/KVDdom_MapperRegistry.test.php');
require_once('domain/KVDdom_IdentityMap.test.php');
require_once( 'domain/KVDdom_DomainObjectCollection.test.php');
require_once( 'domain/KVDdom_SqlLogger.test.php');
require_once( 'domain/KVDdom_PDOChunkyQuery.test.php');
require_once( 'domain/KVDdom_Sessie.test.php');

$test = new GroupTest('KVDdom_AllTests');
$test->addTestCase( new TestOfMapperFactory());
$test->addTestCase( new TestOfMapperRegistry());
$test->addTestCase( new TestOfGenericIdentityMap());
$test->addTestCase( new TestOfDomainObjectCollection( ));
$test->addTestCase( new TestOfSessie());
$test->addTestCase ( new TestOfSqlLogger( ) );
$test->addTestCase( new TestOfPDOChunkyQuery());

$test->run(new TextReporter());

?>
