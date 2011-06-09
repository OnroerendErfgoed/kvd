<?php

class TestOfFormFieldCheckbox extends UnitTestCase
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

    function testFormFieldCheckbox( )
    {
        $formField = new KVDhtml_FormFieldCheckbox( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '<input*>', $html);
        $this->assertWantedPattern( '/ type="checkbox"/' ,$html);
        $this->assertWantedPattern( '/ name="testField"/', $html);
        $this->assertWantedPattern( '/ class="testClass"/', $html);
        $this->assertWantedPattern( '/ value="testValue"/', $html);
        $this->assertWantedPattern( '/ id="testId"/', $html);
    }

    function testFormFieldCheckboxNotReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldCheckbox( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertNoUnwantedPattern( '/ readonly="readonly"/', $html);
    }

    function testFormFieldCheckboxDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldCheckbox( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ disabled="disabled"/', $html);
    }

    function testFormFieldCheckboxChecked( )
    {
        $this->fieldOptions['checked'] = true;
        $formField = new KVDhtml_FormFieldCheckbox( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ checked="checked"/', $html);
    }
    

    

}
?>
