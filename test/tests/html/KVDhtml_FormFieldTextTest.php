<?php

class KVDhtml_FormFieldTextTest extends PHPUnit_Framework_TestCase
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
        $formField = new KVDhtml_FormFieldText( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '<input*>', $html);
        $this->assertRegExp( '/ type="text"/' ,$html);
        $this->assertRegExp( '/ name="testField"/', $html);
        $this->assertRegExp( '/ class="testClass"/', $html);
        $this->assertRegExp( '/ value="testValue"/', $html);
        $this->assertRegExp( '/ id="testId"/', $html);
    }

    function testFormFieldTextReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldText( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ readonly="readonly"/', $html);
    }

    function testFormFieldTextDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldText( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ disabled="disabled"/', $html);
    }

    function testFormFieldValue0( )
    {
        $this->fieldOptions['value'] = 0;
        $formField = new KVDhtml_FormFieldText( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ value="0"/', $html);
    }

    public function testFormFieldValueHtlmEntities( )
    {
        $this->fieldOptions['value'] = 'Lena > Mira';
        $formField = new KVDhtml_FormFieldText( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ value="Lena &gt; Mira"/', $html);
    }

}
?>
