<?php
class KVDthes_RelationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    KVDthes_Relation
     * @access protected
     */
    protected $object;

    /**
     * term 
     * 
     * @var KVDthes_TestTerm
     */
    protected $term;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('KVDthes_RelationTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $this->term = new KVDthes_TestTerm( 0, $sessie, 'TestTerm');
        $this->object = new KVDthes_Relation( KVDthes_Relation::REL_RT , $this->term );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        $this->term = null;
        $this->object = null;
    }

    /**
     * testRelatieHeeftGeldigType 
     * 
     * @expectedException InvalidArgumentException
     */
    public function testRelatieHeeftGeldigType( )
    {
        $object = new KVDthes_Relation( 'REL_ONBESTAAND_TYPE', $this->term );
    }

    public function testGetType() {
        $this->assertEquals( $this->object->getType( ), KVDthes_Relation::REL_RT );
    }

    public function testGetTerm() {

        $this->assertEquals( $this->object->getTerm( ), $this->term );
    }

    public function testEquals() {
        $this->assertTrue( $this->object->equals( new KVDthes_Relation( KVDthes_Relation::REL_RT, $this->term ) ) );
    }

    public function testGetInverseRelation() {
        static $inverse = array (   KVDthes_Relation::REL_BT => KVDthes_Relation::REL_NT ,
                                    KVDthes_Relation::REL_NT => KVDthes_Relation::REL_BT ,
                                    KVDthes_Relation::REL_RT => KVDthes_Relation::REL_RT ,
                                    KVDthes_Relation::REL_USE => KVDthes_Relation::REL_UF ,
                                    KVDthes_Relation::REL_UF => KVDthes_Relation::REL_USE );
        foreach ( $inverse as $key => $value ) {
            $this->object = new KVDthes_Relation(  $key , $this->term );
            $this->assertEquals( $this->object->getInverseRelation( ), $value );
        }

    }

    public function test__toString() {
        $this->assertEquals( $this->object->__toString( ), 'Relation RT TestTerm' );
    }
}
?>
