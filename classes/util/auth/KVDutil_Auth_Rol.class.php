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
 * @since      16 aug 2011
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
     *
     * @param   string          $id: dn van de rol
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