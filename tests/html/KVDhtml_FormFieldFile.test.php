<?php

class TestOfFormFieldFile extends UnitTestCase
{
    private $fieldOptions;
    
    function setUp( )
    {
        $this->fieldOptions = array (  'name' => 'testField' ,
                                       'class' => 'testClass'
                                    );
    }

    function tearDown( )
    {
        $this->fieldOptions = array( );
    }

    function testFormFieldFile( )
    {
        $formField = new KVDhtml_FormFieldFile( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/type="file"/' ,$html);
        $this->assertWantedPattern( '/name="testField"/', $html);
        $this->assertWantedPattern( '/class="testClass"/', $html);
    }

    function testFormFieldFileReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldFile( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/type="file"/' ,$html);
        $this->assertWantedPattern( '/name="testField"/', $html);
        $this->assertWantedPattern( '/class="testClass"/', $html);
        $this->assertWantedPattern( '/ readonly/', $html);
    }

    function testFormFieldFileDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldFile( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/type="file"/' ,$html);
        $this->assertWantedPattern( '/name="testField"/', $html);
        $this->assertWantedPattern( '/class="testClass"/', $html);
        $this->assertWantedPattern( '/ disabled/', $html);
    }

}
?>
