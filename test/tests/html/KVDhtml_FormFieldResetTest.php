<?php

class KVDhtml_FormFieldResetTest extends PHPUnit_Framework_TestCase
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
        $this->assertRegExp( '<input*>', $html);
        $this->assertRegExp( '/ type="reset"/' ,$html);
        $this->assertRegExp( '/ name="testField"/', $html);
        $this->assertRegExp( '/ class="testClass"/', $html);
        $this->assertRegExp( '/ value="testValue"/', $html);
        $this->assertRegExp( '/ id="testId"/', $html);
    }

    function testFormFieldResetNoReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldReset( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertNotRegExp( '/ readonly="readonly"/', $html);
    }

    function testFormFieldResetDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldReset( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ disabled="disabled"/', $html);
    }

}
?>
