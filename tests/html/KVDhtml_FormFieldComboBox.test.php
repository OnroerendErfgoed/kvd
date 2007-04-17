<?php

class TestOfFormFielComboBox extends UnitTestCase
{
    private $fieldOptions;
    
    function setUp( )
    {
        $this->fieldOptions = array (  'name' => 'testField' ,
                                       'class' => 'testClass',
                                       'id' =>  'testId',
                                       'value' => '1',
                                       'dataUrl' => 'persoon.php?filter=%{searchString}'
                                    );
    }

    function tearDown( )
    {
        $this->fieldOptions = array( );
    }

    function testFormFieldComboBox( )
    {
        $formField = new KVDhtml_FormFieldComboBox( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '<input*>', $html);
        $this->assertWantedPattern( '/ dojoType="ComboBox"/' ,$html);
        $this->assertWantedPattern( '/ name="testField"/', $html);
        $this->assertWantedPattern( '/ class="testClass"/', $html);
        $this->assertWantedPattern( '/ value="1"/', $html);
        $this->assertWantedPattern( '/ id="testId"/', $html);
        $this->assertWantedPattern( '/ dataUrl="persoon.php?filter=%{searchString}"/', $html);
        $this->assertWantedPattern( '/ autoComplete="true"/', $html);
        $this->assertWantedPattern( '/ mode="remote"/', $html);
        $this->assertWantedPattern( '/ maxListLength="20"/', $html);
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

}
?>
