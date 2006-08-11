<?php
/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 31 jul 2006
 */
class KVDag_KeuzelijstHelper
{
    private $domainObjects;
    
    public function __construct ( $mapper, $finderName = 'findAll', $orderField = null )
    {
        try {
            $this->domainObjects = $mapper->$finderName( $orderField )
        } catch ( Exception $e ) {
            $this->domainObjects = array( );
        }
    }

    public function getAsOptionList
    {
       $buffer = '';
       foreach ( $this->domainObjects as $domainObject ) {
           $buffer .= "<option value=\"{$domainObject->getId()}\">{$domainObject->getOmschrijving()}</option>\n";
       }
       return $buffer;
    }
}
?>
