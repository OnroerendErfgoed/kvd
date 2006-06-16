<?php
/**
 * @package KVD.dom.exception
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Exception wordt aangemaakt wanneer een gelogde versie van een DomainObject niet gevonden kan worden.
 *
 * @package KVD.dom.exception
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdom_LogDomainObjectNotFoundException extends Exception
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
     * @var integer
     */
    private $versie;
    
    /**
     * @param string $msg
     * @param string $type
     * @param integer $id
     * @param integer $versie;
     */
    public function __construct( $msg , $type , $id , $versie )
    {
        parent::__construct ( $msg );
        $this->type = $type;
        $this->id = $id ;
        $this->versie = $versie;
        $this->generateMessage();
    }
    
    private function generateMessage ()
    {
        $this->message .= " [LogDONotFound Error: Versie {$this->getVersie()} van het record van het type {$this->getType()} met sleutel {$this->getId()}dat u probeert te openen kon niet gevonden worden.]";    
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

    /**
     * De versie van het object die u gevraagd hebt bestaat niet ( of het ganse object bestaat niet).
     * @return integer
     */
     public function getVersie( )
     {
         return $this->versie;
     }

}
?>
