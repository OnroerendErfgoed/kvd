<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDhtml_FormFieldFactory.class.php,v 1.1 2006/01/12 12:30:14 Koen Exp $
 */

/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDhtml_FormFieldFactory
{
    /**
     * @param array $fieldOptions
     * @return KVDhtml_FormField
     * @throws <b>InvalidArgumentException</b> - Indien er om een ongeldig type gevraagd wordt.
     */
    public function getFormField( &$fieldOptions )
    {
        if (!isset($fieldOptions['type'])) {
            $fieldOptions['type'] = 'text';
        }
        switch ( $fieldOptions['type'] )
        {
            case 'text':
                $field = new KVDhtml_FormFieldText($fieldOptions);
                break;
            case 'password':
                $field = new KVDhtml_FormFieldPassword($fieldOptions);
                break;
            case 'checkbox':
                $field = new KVDhtml_FormFieldCheckbox($fieldOptions);
                break;
            case 'textarea':
                $field = new KVDhtml_FormFieldTextarea($fieldOptions);
                break;
            case 'select':
                $field = new KVDhtml_FormFieldSelect($fieldOptions);
                break;
            case 'hidden':
                $field = new KVDhtml_FormFieldHidden($fieldOptions);
                break;
            case 'submit':
                $field = new KVDhtml_FormFieldSubmit( $fieldOptions );
                break;
            case 'reset':
                $field = new KVDhtml_FormFieldReset( $fieldOptions );
                break;
            case 'radio':
                $field = new KVDhtml_FormFieldRadio( $fieldOptions );
                break;
            case 'file':
                $field = new KVDhtml_FormFieldFile( $fieldOptions );
                break;
            default:
                throw new InvalidArgumentException ( "U hebt een ongeldig veldtype opgegeven: {$fieldOptions['type']}");
        }
        return $field;
    }
}
?>
