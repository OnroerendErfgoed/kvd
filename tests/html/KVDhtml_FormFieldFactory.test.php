<?php
class TestOfFormFieldFactory extends UnitTestCase
{
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
        $this->assertIsA( $this->factory, 'KVDhtml_FormFieldFactory' );
    }

    public function testAllTypes( )
    {
        foreach ( $this->fieldOptions as $fieldOption) {
            $field = $this->factory->getFormField( $fieldOption );
            $this->assertIsA( $field, 'KVDhtml_FormField' );
        }
    }

    public function testIllegalType( )
    {
        try {
            $field = $this->factory->getFormField ( array ( 'type' => 'ongeldigType' ) );
            $this->fail( 'Het type ongeldigType zou een exception moeten genereren.');
        } catch ( InvalidArgumentException $e ) {
            $this->pass( );
        } catch ( Exception $e ) {
            $this->fail( 'Het type ongeldigType zou een exception van het type InvalidArgumentException moeten genereren.');
        }
    }
}
?>
