<?php

class TestOfFormFieldReset extends UnitTestCase
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

    function testFormFieldText( )
    {
        $formField = new KVDhtml_FormFieldReset( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '<input*>', $html);
        $this->assertWantedPattern( '/ type="reset"/' ,$html);
        $this->assertWantedPattern( '/ name="testField"/', $html);
        $this->assertWantedPattern( '/ class="testClass"/', $html);
        $this->assertWantedPattern( '/ value="testValue"/', $html);
        $this->assertWantedPattern( '/ id="testId"/', $html);
    }

    function testFormFieldResetNoReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldReset( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertNoUnwantedPattern( '/ readonly="readonly"/', $html);
    }

    function testFormFieldResetDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldReset( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ disabled="disabled"/', $html);
    }

}
?>
