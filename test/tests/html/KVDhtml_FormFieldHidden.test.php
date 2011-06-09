<?php

class TestOfFormFieldHidden extends UnitTestCase
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

    function testFormFieldHidden( )
    {
        $formField = new KVDhtml_FormFieldHidden( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '<input*>', $html);
        $this->assertWantedPattern( '/ type="hidden"/' ,$html);
        $this->assertWantedPattern( '/ name="testField"/', $html);
        $this->assertNoUnwantedPattern( '/ class="testClass"/', $html);
        $this->assertWantedPattern( '/ value="testValue"/', $html);
        $this->assertWantedPattern( '/ id="testId"/', $html);
    }

    function testFormFieldHiddenNotReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldHidden( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertNoUnwantedPattern( '/ readonly="readonly"/', $html);
    }

    function testFormFieldHiddenDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldHidden( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertNoUnwantedPattern( '/ disabled="disabled"/', $html);
    }

}
?>
