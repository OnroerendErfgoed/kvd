<?php

class TestOfFormFieldPassword extends UnitTestCase
{
    private $fieldOptions;
    
    function setUp( )
    {
        $this->fieldOptions = array (  'name' => 'testField' ,
                                       'class' => 'testClass',
                                       'id' =>  'testId',
                                       'value' => 'testValue'
                                    );
    }

    function tearDown( )
    {
        $this->fieldOptions = array( );
    }

    function testFormFieldPassword( )
    {
        $formField = new KVDhtml_FormFieldPassword( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '<input*>', $html);
        $this->assertWantedPattern( '/ type="password"/' ,$html);
        $this->assertWantedPattern( '/ name="testField"/', $html);
        $this->assertWantedPattern( '/ class="testClass"/', $html);
        $this->assertWantedPattern( '/ value="testValue"/', $html);
        $this->assertWantedPattern( '/ id="testId"/', $html);
    }

    function testFormFieldPasswordReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldPassword( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ readonly="readonly"/', $html);
    }

    function testFormFieldPasswordDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldPassword( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ disabled="disabled"/', $html);
    }

}
?>
