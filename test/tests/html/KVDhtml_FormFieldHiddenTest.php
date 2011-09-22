<?php

class KVDhtml_FormFieldHiddenTest extends PHPUnit_Framework_TestCase
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
        $this->assertRegExp( '<input*>', $html);
        $this->assertRegExp( '/ type="hidden"/' ,$html);
        $this->assertRegExp( '/ name="testField"/', $html);
        $this->assertNotRegExp( '/ class="testClass"/', $html);
        $this->assertRegExp( '/ value="testValue"/', $html);
        $this->assertRegExp( '/ id="testId"/', $html);
    }

    function testFormFieldHiddenNotReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldHidden( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertNotRegExp( '/ readonly="readonly"/', $html);
    }

    function testFormFieldHiddenDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldHidden( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertNotRegExp( '/ disabled="disabled"/', $html);
    }

}
?>
