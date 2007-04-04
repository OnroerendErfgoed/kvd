<?php

class TestOfFormFieldDate extends UnitTestCase
{
    private $fieldOptions;
    
    function setUp( )
    {
        $this->fieldOptions = array (  'name' => 'testField' ,
                                       'class' => 'testClass',
                                       'id' =>  'testId',
                                       'value' => '2005-12-01'
                                    );
    }

    function tearDown( )
    {
        $this->fieldOptions = array( );
    }

    function testFormFieldText( )
    {
        $formField = new KVDhtml_FormFieldDate( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '<input*>', $html);
        $this->assertWantedPattern( '/ dojoType="dropdowndatepicker"/' ,$html);
        $this->assertWantedPattern( '/ name="testField"/', $html);
        $this->assertWantedPattern( '/ class="testClass"/', $html);
        $this->assertWantedPattern( '/ value="2005-12-01"/', $html);
        $this->assertWantedPattern( '/ id="testId"/', $html);
    }

    function testFormFieldReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldDate( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ readonly="readonly"/', $html);
    }

    function testFormFieldDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldDate( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ disabled="disabled"/', $html);
    }

    function testFormFieldStartEndDate( )
    {
        $this->fieldOptions['startDate'] = '2006-01-01';
        $this->fieldOptions['endDate'] = '2006-12-31';
        $formField = new KVDhtml_FormFieldDate( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ startDate="2006-01-01"/', $html);
        $this->assertWantedPattern( '/ endDate="2006-12-31"/', $html);
    }

    function testFormFieldValueConversion( )
    {
        $this->fieldOptions['value'] = '15-02-2006';
        $formField = new KVDhtml_FormFieldDate( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ value="2006-02-15"/', $html);
    }
    
}
?>
