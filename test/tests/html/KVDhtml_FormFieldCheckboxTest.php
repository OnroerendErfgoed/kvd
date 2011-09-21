<?php

class KVDhtml_FormFieldCheckboxTest extends PHPUnit_Framework_TestCase
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
        $this->assertRegExp( '<input*>', $html);
        $this->assertRegExp( '/ type="checkbox"/' ,$html);
        $this->assertRegExp( '/ name="testField"/', $html);
        $this->assertRegExp( '/ class="testClass"/', $html);
        $this->assertRegExp( '/ value="testValue"/', $html);
        $this->assertRegExp( '/ id="testId"/', $html);
    }

    function testFormFieldCheckboxNotReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldCheckbox( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertNotRegExp( '/ readonly="readonly"/', $html);
    }

    function testFormFieldCheckboxDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldCheckbox( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ disabled="disabled"/', $html);
    }

    function testFormFieldCheckboxChecked( )
    {
        $this->fieldOptions['checked'] = true;
        $formField = new KVDhtml_FormFieldCheckbox( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ checked="checked"/', $html);
    }
    

    

}
?>
