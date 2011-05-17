<?php
/**
 * @package   KVD.database
 * @version   $Id
 * @copyright 2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Factory die meerdere PDO connecties kan beheren.
 *
 * @package   KVD.database
 * @since     1.0.0
 * @copyright 2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */ 
class KVDdb_ConnectionFactoryPDO extends KVDdb_ConnectionFactory
{
    /**
     * @param string $connectionName Naam van een databank-connectie 
     *                               zoals aangegeven in de config file.
     */
    protected function createConnection ( $connectionName )
    {
        $this->checkConnectionConfig ( $connectionName );
        try {
            $db = new PDO ( $this->connectionConfig[$connectionName]['dsn'],
                            $this->connectionConfig[$connectionName]['user'],
                            $this->connectionConfig[$connectionName]['password']);
        } catch (PDOException $e) {
            throw new Exception ( 
                "De database $connectionName kon niet geopend worden. 
                Controleer de parameters." );    
        }
        $this->connections[$connectionName] = $db;
    }
}
?>
