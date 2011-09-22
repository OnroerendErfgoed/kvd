<?php
class KVDhtml_FormFieldFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * factory 
     * 
     * @var KVdhtml_FormFieldFactory
     */
    private $factory;

    private $fieldOptions;
    
    public function setUp( )
    {
        $this->fieldOptions = array(    array ( 'type' => 'text' ),
                                        array ( 'type' => 'hidden' ),
                                        array ( 'type' => 'password' ),
                                        array ( 'type' => 'select' ),
                                        array ( 'type' => 'textarea' ),
                                        array ( 'type' => 'radio' ),
                                        array ( 'type' => 'checkbox' ),
                                        array ( 'type' => 'file' ),
                                        array ( 'type' => 'reset' ),
                                        array ( 'type' => 'submit' ),
                                        array ( 'type' => 'date'),
                                        array ( 'type' => 'combobox' , 'dataUrl' => 'persoon.php' ),
                                        array ( )
                                        );
        $this->factory = new KVDhtml_FormFieldFactory();
    }

    public function tearDown( )
    {
        $this->factory = null;
        $this->fieldOptions = null;
    }

    public function testIsAFactory( )
    {
        $this->assertInstanceOf( 'KVDhtml_FormFieldFactory', $this->factory );
    }

    public function testAllTypes( )
    {
        foreach ( $this->fieldOptions as $fieldOption) {
            $field = $this->factory->getFormField( $fieldOption );
            $this->assertInstanceOf( 'KVDhtml_FormField', $field );
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIllegalType( )
    {
            $field = $this->factory->getFormField ( array ( 'type' => 'ongeldigType' ) );
    }
}
?>
