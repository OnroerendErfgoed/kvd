<?php
/**
 * @package   KVD.agavi
 * @subpackagedatabase
 * @version   $Id$
 * @copyright 2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDag_LdapDatabase provides connectivity for the LDAP database API layer.
 *
 * @package   KVD.agavi
 * @subpackagedatabase
 * @since     2011
 * @copyright 2004-2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_LdapDatabase extends AgaviDatabase
{
    /**
     * Connect to the database.
     *
     * @throws     <b>AgaviDatabaseException</b> If a connection could not be
     *                                           created.
     *
     * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
     */
    protected function connect()
    {
        // determine how to get our parameters
        $method = $this->getParameter('method', 'normal');

        // get parameters
        switch($method) {
            case 'normal':
                // get parameters normally
                $host 	  = $this->getParameter('host');
                $port     = $this->getParameter('port', 389);
                $version  = $this->getParameter('version', 3);
                $basedn   = $this->getParameter('basedn');
                $binddn   = $this->getParameter('binddn', null);
                $bindpw   = $this->getParameter('bindpw', null);

                if($host == null || $port == null || $version == null || $basedn == null ) {
                    // missing required dsn parameter
                    $error = 'Database configuration specifies method "normal", but is missing 1 or more parameters.
                        Required parameters are host, port, version, basedn';
                    throw new AgaviDatabaseException($error);
                }
                break;
            default:
                // who knows what the user wants...
                $error = 'Invalid KVDag_LdapDatabase parameter retrieval method "%s"';
                $error = sprintf($error, $method);
                throw new AgaviDatabaseException($error);
        }

        // The configuration array:
        $config = array (
            'host'    		=> $host,
            'port'    		=> $port,
            'version'  		=> $version,
            'basedn'      	=> $basedn
        );

        // Connecting using the configuration:
        $this->connection = Net_LDAP2::connect($config);

        // Testing for connection error
        if (Net_LDAP2::isError($this->connection)) {
            // the connection's foobar'd
            $error = 'Failed to create a KVDag_LdapDatabase connection';

            throw new AgaviDatabaseException($error);
        }

        //ldapbeheerconnection
        if( ($binddn != null && $bindpw != null ) ) {
            $res = $this->connection->bind( $binddn, $bindpw );
            if (Net_LDAP2::isError($res)) {
                // the authentication's foobar'd
                $error = 'Failed to authenticate to the KVDag_LdapDatabase connection';

                throw new AgaviDatabaseException($error);
            }
        }

        // make sure the connection went through
        if($this->connection === false) {
            // the connection's foobar'd
            $error = 'Failed to create a KVDag_LdapDatabase connection';

            throw new AgaviDatabaseException($error);
        }

        // since we're not an abstraction layer, we copy the connection
        // to the resource
        $this->resource =& $this->connection;
    }

    /**
    * Execute the shutdown procedure.
    *
    * @throws     <b>AgaviDatabaseException</b> If an error occurs while shutting
    *                                           down this database.
    *
    * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
    */
    public function shutdown()
    {
        if($this->connection != null) {
            @$result = $this->connection->done();
            $this->connection = null;
            if (Net_LDAP2::isError($result)) {
                $error = 'Could not close KVDag_LdapDatabase connection';
                throw new AgaviDatabaseException($error);
            }
        }
    }
}
?>