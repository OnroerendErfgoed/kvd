<?php
/**
 * @package    KVD.util
 * @subpackage auth
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Authenticator voor LDAP dbms
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      11 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_LDAPAuthenticator extends KVDutil_Auth_Authenticator
{
    /**
     * constructor
     * Strategy pattern: We maken gebruik van LDAP LoggedBehavior
     *
     * @param   $databaseConnection   een databank connectie.
     */
    public function __construct($databaseConnection)
    {
        parent::__construct($databaseConnection);
        $this->loggedInState = new KVDutil_Auth_LDAPLoggedIn($this);
        $this->loggedOutState = new KVDutil_Auth_LDAPLoggedOut($this);
    }
}
?>
