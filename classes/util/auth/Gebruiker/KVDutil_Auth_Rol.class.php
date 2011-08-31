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
 * DomainObject voor Rollen
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      30 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_Rol extends KVDdom_DomainObject
{
    /**
     * naam van de rol
     *
     * @var string
     */
    protected $naam;

    /**
     * beshrijving van de rol.
     *
     * @var string
     */
    protected $beshrijving;

    /**
     * __construct
     * ID en naam kunnen dezelfde zijn. Toch is dit niet altijd zo.
     * In LDAP zal het ID attribuut gebruikt worden om de DN van de rol bij te houden.
     * Volledige DN is nodig om transacties met de ldap datasource mogelijk te maken
     *
     * @param   string          $id: dn of naam van de rol
     * @param   string          $naam
     * @param   string          $beschrijving
     * @return  void
     */
    public function __construct($id, $naam ='', $beschrijving = '')
    {
        parent::__construct($id);
        $this->naam = $naam;
        $this->beschrijving = $beschrijving;
    }

    /**
     * getNaam
     *
     * @return string
     */
    public function getNaam()
    {
        return $this->naam;
    }

    /**
     * getBeschrijving
     *
     * @return string
     */
    public function getBeschrijving()
    {
        return $this->beschrijving;
    }

    /**
     * geeft een omschrijving van dit object terug.
     *
     * @return string de omschrijving.
     */
    public function getOmschrijving()
    {
        return $this->getNaam().' ('.$this->getBeschrijving.')';
    }
}
?>