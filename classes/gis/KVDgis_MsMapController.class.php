<?php
/**
 * @package KVD.gis
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapController.class.php,v 1.7 2006/01/10 16:27:59 Koen Exp $
 */

/**
 * @package KVD.gis
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */ 
class KVDgis_MsMapController
{
    /**
     * @var string Absoluut pad naar de mapfile
     */
    private $mapFile;

    /**
     * @var KVDdb_ConnectionFactory
     */
    private $_dbConnFactory;

    /**
     * @var string Map waarin tijdelijke bestanden kunnen opgeslagen worden.
     */
    private $tempDir;
    
    /**
     * @param string Absoluut pad naar de te gebruiken mapfile
     * @param string Map waarin tijdelijke bestanden kunnen opgeslagen worden.
     * @param array $dbConnFactoryConfig
     */
    public function __construct( $mapFile , $tempDir , $dbConnFactory)
    {
        $this->mapFile = $mapFile;
        $this->tempDir = $tempDir;
        $this->_dbConnFactory = $dbConnFactory;
    }

    /**
     * @return string Pad naar een dir waarin queries kunnen opgeslagen worden.
     */
    public function getQuerySaveDir()
    {
        return $this->tempDir . 'qry/';    
    }

    /**
     * @return string Pad naar een dir waarin tijdelijke images (gegenereerde kaarten, legendes, e.d.) kunnen opgeslagen worden.
     */
    public function getImageSaveDir()
    {
        return $this->tempDir . 'img/';    
    }

    /**
     * @param string $actionName Naam van de uit te voeren actie
     * @param KVDgis_MsMapState
     */
    public function getAction ( $actionName , $mapState )
    {
        $prefix = 'KVDgis_MsMapAction';
        $completeActionName = $prefix . ucfirst($actionName);
        try {
            $action = new $completeActionName ( $this , $mapState );
        } catch (KVDexc_AutoloadException $e) {
            $prefix = 'MELgis_MsMapAction';
            $completeActionName = $prefix . ucfirst($actionName);
            $action = new $completeActionName ( $this , $mapState );    
        } catch (Exception $e) {
                throw new KVDgis_MsMapActionBestaatNietException($e->getMessage(),$completeActionName);    
        }
        return $action;
    }

    /**
     * @param string $connectionName
     * @return PDO Een geldige pdo database
     */
    public function getConnection ( $connectionName )
    {
        return $this->_dbConnFactory->getConnection ( $connectionName ); 
    }

    /**
     * @return string $mapFile
     */
    public function getMapFile()
    {
        return $this->mapFile;    
    }
    
}
?>
