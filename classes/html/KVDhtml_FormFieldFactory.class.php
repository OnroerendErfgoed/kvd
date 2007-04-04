<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since maart 2006
 */
class KVDhtml_FormFieldFactory
{
    /**
     * @param array $fieldOptions Elk item in de array moet overeenkomen met de config-string voor een bepaald type. Zie de types zelf voor meer info.
     * @return KVDhtml_FormField
     * @throws <b>InvalidArgumentException</b> - Indien er om een ongeldig type gevraagd wordt.
     */
    public function getFormField( $fieldOptions )
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
            case 'file':;
                $field = new KVDhtml_FormFieldFile( $fieldOptions );
                break;
            case 'date':;
                $field = new KVDhtml_FormFieldDate( $fieldOptions );
                break;
            default:
                throw new InvalidArgumentException ( "U hebt een ongeldig veldtype opgegeven: {$fieldOptions['type']}");
        }
        return $field;
    }
}
?>
