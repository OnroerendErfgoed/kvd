<?php
/**
 * @package KVD.gis.exception
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Exception wordt aangemaakt wanneer er geprobeerd wordt een niet bestaande MsMapAction aan te maken.
 * @package KVD.gis.exception
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDgis_MsMapActionBestaatNietException extends Exception
{
    /**
     * @var string
     */
    private $actionName;
    
    /**
     * @param string $msg
     * @param string $actionName
     */
    public function __construct( $msg , $actionName )
    {
        parent::__construct ( $msg );
        $this->actionName = $actionName;
        $this->generateMessage();
    }
    
    private function generateMessage ()
    {
        $this->message .= " [MsMapActionBestaatNiet Error: De MsMapAction {$this->getActionName()} kon niet gevonden worden.]";    
    }

     /**
      * @return string
      */
     public function getActionName()
     {
         return $this->actionName;
     }

}

?>
