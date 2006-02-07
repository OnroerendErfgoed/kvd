<?php
/**
 * @package KVD.gis
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapAjaxHandler.class.php,v 1.4 2005/12/07 16:02:08 Koen Exp $
 */

/**
 * @package KVD.gis
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */ 
class KVDgis_MsMapAjaxHandler
{

    /**
     * @var KVDgis_MsMapController
     */
    private $_mapController;
    
    /**
     * @param KVDgis_MsMapController $mapController
     */
    public function __construct ( $mapController )
    {
        $this->_mapController = $mapController;
    }

    /**
     * @param string $action
     * @param stdClass $mapStateObject
     * @param stdClass $actionParametersObject
     * @return stdClass Stand van de map in object notatie ( om compatibel te zijn met JSON )
     */
    public function doMapAction($action,$mapStateObject,$actionParametersObject)
    {
        try {
            $actionParameters = array();
            
            foreach ($actionParametersObject as $key => $value) {
                $actionParameters[$key] = $value;
            }

            $mapState = new KVDgis_MsMapState ( $mapStateObject );

            $mapAction = $this->_mapController->getAction( $action , $mapState );
            $newMapState = $mapAction->execute( $actionParameters );

            return $newMapState->convertToObject();
        } catch (Exception $e) {
            $message = $e->getMessage() . "\n Backtrace: \n" . $e->getTraceAsString() . "\n------\n"; 
            $handle = fopen ( $this->tempDir . 'KVDgis_Error.log', 'a');
            fwrite ( $handle , $message);
            fclose($handle);
            return $message;
        }
    }
}
?>
