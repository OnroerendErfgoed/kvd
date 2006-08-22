<?php

class TestOfFormFieldTextarea extends UnitTestCase
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

    function testFormFieldTextarea( )
    {
        $formField = new KVDhtml_FormFieldTextarea( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '<textarea[\w]*>', $html);
        $this->assertWantedPattern( '</textarea>', $html);
        $this->assertNoUnwantedPattern( '/ type="*"/' ,$html);
        $this->assertWantedPattern( '/ name="testField"/', $html);
        $this->assertWantedPattern( '/ class="testClass"/', $html);
        $this->assertWantedPattern( '/ id="testId"/', $html);
    }

    function testFormFieldTextareaReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldTextarea( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ readonly="readonly"/', $html);
    }

    function testFormFieldTextareaDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldTextarea( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ disabled="disabled"/', $html);
    }

}
?>
