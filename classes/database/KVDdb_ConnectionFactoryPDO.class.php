<?php
/**
 * @package KVD.database
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id
 */

/**
 * @package KVD.database
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */ 
class KVDdb_ConnectionFactoryPDO extends KVDdb_ConnectionFactory
{
    /**
     * @param string $connectionName Naam van een databank-connectie zoals aangegeven in de config file.
     */
    protected function createConnection ( $connectionName )
    {
        $this->checkConnectionConfig ( $connectionName );
        try {
            $db = new PDO ( $this->connectionConfig[$connectionName]['dsn'],
                            $this->connectionConfig[$connectionName]['user'],
                            $this->connectionConfig[$connectionName]['password']);
        } catch (PDOException $e) {
            throw new Exception ( "De database $connectionName kon niet geopend worden. Controleer de parameters." );    
        }
        $this->connections[$connectionName] = $db;
    }
}
?>
