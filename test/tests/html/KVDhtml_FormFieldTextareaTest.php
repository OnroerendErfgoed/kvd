<?php
class KVDhtml_FormFieldTextareaTest extends PHPUnit_Framework_Testcase
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
        $this->assertRegExp( '<textarea[\w]*>', $html);
        $this->assertRegExp( '</textarea>', $html);
        $this->assertNotRegExp( '/ type="*"/' ,$html);
        $this->assertRegExp( '/ name="testField"/', $html);
        $this->assertRegExp( '/ class="testClass"/', $html);
        $this->assertRegExp( '/ id="testId"/', $html);
    }

    function testFormFieldTextareaReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldTextarea( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ readonly="readonly"/', $html);
    }

    function testFormFieldTextareaDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldTextarea( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ disabled="disabled"/', $html);
    }

}
?>
