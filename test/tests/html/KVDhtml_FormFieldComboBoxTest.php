<?php

class KVDhtml_FormFielComboBoxTest extends PHPUnit_Framework_TestCase
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
        $this->assertRegExp( '<input*>', $html);
        $this->assertRegExp( preg_quote('/ dojoType="ComboBox"/') ,$html);
        $this->assertRegExp( '/ name="testField"/', $html);
        $this->assertRegExp( '/ class="testClass"/', $html);
        $this->assertRegExp( '/ value="1"/', $html);
        $this->assertRegExp( '/ id="testId"/', $html);
        $this->assertRegExp( preg_quote('/ dataUrl="persoon.php?filter=%{searchString}"/'), $html);
        $this->assertRegExp( preg_quote('/ autoComplete="true"/'), $html);
        $this->assertRegExp( '/ mode="remote"/', $html);
        $this->assertRegExp( '/ maxListLength="20"/', $html);
    }

    function testFormFieldReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldDate( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ readonly="readonly"/', $html);
    }

    function testFormFieldDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldDate( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertRegExp( '/ disabled="disabled"/', $html);
    }

}
?>
