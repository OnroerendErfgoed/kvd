<?php
/**
 * @package   KVD.database
 * @version   $Id
 * @copyright 2004 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Abstract factory om database verbindingen te beheren.
 *
 * @package   KVD.database
 * @since     1.0.0
 * @copyright 2004 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */ 
abstract class KVDdb_ConnectionFactory
{
    /**
     * @var array Array met configuratieparameters
     */
    protected $connectionConfig;
    
    /**
     * @var array Een array van connectie-objecten.
     */
    protected $connections = array();
    
    /**
     * Maak een nieuwe connectionfactory aan op basis van een 
     * array waarin elke key een connection voorstelt.
     * 
     * @param array $connectionConfig Een array met configuratie-info.
     */
    public function __construct( $connectionConfig )
    {
        $this->connectionConfig = $connectionConfig;
    }

    /**
     * Verkrijg een bepaalde connectie.
     * 
     * @param string $connectionName
     */
    public function getConnection ( $connectionName )
    {
       if ( !array_key_exists ( $connectionName , $this->connections ) ) {
           $this->createConnection ( $connectionName );
       }
       return $this->connections[$connectionName];
    }

    protected function checkConnectionConfig ( $connectionName )
    {
        if ( !array_key_exists ( $connectionName, $this->connectionConfig ) ) {
            throw new Exception ( 
                "De database $connectionName kon niet geopend worden omdat er parameters ontbreken.");
        }
    }

    /**
     * @param string $connectionName Naam van een databank-connectie 
     *                               zoals aangegeven in de config file.
     */
    abstract protected function createConnection ( $connectionName );
}
?>
