<?php
/**
 * @package KVD.dom.exception
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Exception wordt aangemaakt wanneer een DomainObject niet gevonden kan worden.
 *
 * @package KVD.dom.exception
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdom_DomainObjectNotFoundException extends Exception
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $id;
    
    /**
     * @param string $msg
     * @param string $type
     * @param integer $id
     */
    public function __construct( $msg , $type , $id )
    {
        parent::__construct ( $msg );
        $this->type = $type;
        $this->id = $id ;
        $this->generateMessage();
    }
    
    private function generateMessage ()
    {
        $this->message .= " [DONotFound Error: Het record van het type {$this->getType()} met sleutel {$this->getId()}dat u probeert te openen kon niet gevonden worden.]";    
    }

     /**
      * Het type van het object dat niet gevonden kon worden.
      * @return string
      */
     public function getType()
     {
         return $this->type;
     }

     /**
      * De sleutel van het object dat niet gevonden kon worden. Meestal is dit een integer.
      * @return mixed
      */
     public function getId()
     {
         return $this->id;
     }

}
?>
