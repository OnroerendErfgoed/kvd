<?php
/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 3 jul 2006
 */
class KVDag_SelectorOverzichtTableHelper extends KVDag_PagedOverzichtTableHelper
{
    /**
     * @var string
     */
    private $idField;
    
    /**
     * @var string
     */
    private $formAction;
    
    /**
     * @param Controller $ctrl
     * @param array $config
     * @param KVDdom_DomainObjectCollectionPager $pager
     */
    public function __construct( $ctrl, $config , $pager)
    {
        parent::__construct( $ctrl, $config , $pager);
        
        if ( !isset( $config['formAction'] ) ) {
            throw new InvalidArgumentException ( 'Er is geen formAction gespecifieerd!' );
        }
        $this->formAction = $config['formAction'];

        $this->idField = isset( $config['idField'] ) ? $config['idField'] : 'id';

        $this->idField .= '[]';
        
    }

    /**
     * @param KVDdom_DomainObjectCollection $collection
     * @param boolean $generateActions
     */
    public function genRowsForCollection( $collection , $generateActions = false )
    {
        $rows = array( );

        foreach ( $collection as $domainObject ) {
            $row = array( );
            $fieldOptions = array ( 'name' => "{$this->idField}",
                                    'value'=> $domainObject->getId( ) );
            $selector = new KVDhtml_FormFieldCheckbox( $fieldOptions );
            $row[] = $selector->toHtml( );
            foreach ( $this->fieldsPerRow as $field ) {
                $row[] = $this->getDataForFieldString( $domainObject, $field );
            }
            $rows[] = $row;
        }
        if ( count( $rows ) > 0 ) {
            $this->_htmlTableHelper->setRows( $rows );
            $this->_htmlTableHelper->setHeaders( $this->headers );
        }
    }

    /**
     * @param array
     * @return string
     */
    public function toHtml( $cssClasses = null ) 
    {
        $html = "<form method=\"post\" action=\"{$this->formAction}\">\n";
        $html .= parent::toHtml( $cssClasses );
        $submit = new KVDhtml_FormFieldSubmit ( array ( 'name' =>   'relictSelecteren',
                                                        'value' =>  'Selecteren'
                                                       )
                                                );
        $html .= $submit->toHtml( );
        $html .= "</form>";
        return $html;
    }
    
}
?>
