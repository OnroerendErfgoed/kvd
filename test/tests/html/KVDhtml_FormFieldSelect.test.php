<?php

class TestOfFormFieldSelect extends UnitTestCase
{
    private $fieldOptions;
    
    function setUp( )
    {
        $this->fieldOptions = array (  'name' => 'testField' ,
                                       'class' => 'testClass',
                                       'id' =>  'testId',
                                       'options' => array ( '1' => 'Eerste optie',
                                                            '2' => 'Tweede optie'
                                                            )
                                    );
    }

    function tearDown( )
    {
        $this->fieldOptions = array( );
    }

    function testFormFieldSelect( )
    {
        $formField = new KVDhtml_FormFieldSelect( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '<select[\w]*>', $html);
        $this->assertWantedPattern( '</select>', $html);
        $this->assertNoUnwantedPattern( '/ type="*"/' ,$html);
        $this->assertWantedPattern( '/ name="testField"/', $html);
        $this->assertWantedPattern( '/ class="testClass"/', $html);
        $this->assertWantedPattern( '/ id="testId"/', $html);
    }

    function testFormFieldOptions( )
    {
        $formField = new KVDhtml_FormFieldSelect( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/<option value="1">Eerste optie<\/option>/' , $html );
        $this->assertWantedPattern( '/<option value="2">Tweede optie<\/option>/' , $html );
    }

    function testFormFieldSelectedOption( )
    {
        $this->fieldOptions['value'] = '1';
        $formField = new KVDhtml_FormFieldSelect( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/<option value="1" selected="selected">Eerste optie<\/option>/' , $html );
        $this->assertWantedPattern( '/<option value="2">Tweede optie<\/option>/' , $html );
    }


    function testFormFieldSelectNoReadonly( )
    {
        $this->fieldOptions['readonly'] = true;
        $formField = new KVDhtml_FormFieldSelect( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertNoUnwantedPattern( '/ readonly="readonly"/', $html);
    }

    function testFormFieldSelectDisabled( )
    {
        $this->fieldOptions['disabled'] = true;
        $formField = new KVDhtml_FormFieldSelect( $this->fieldOptions );
        $html = $formField->toHtml( );
        $this->assertWantedPattern( '/ disabled="disabled"/', $html);
    }

}
?>
