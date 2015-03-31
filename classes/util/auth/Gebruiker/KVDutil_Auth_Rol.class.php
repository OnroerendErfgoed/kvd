<?php
/**
 * @package    KVD.util
 * @subpackage auth
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 */

/**
 * DomainObject voor Rollen
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      30 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 */
class KVDutil_Auth_Rol implements KVDdom_DomainObject
{
    /**
     * Id nummer of unieke string id van het domain-object
     * @var string
     */
    protected $id;

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
        $this->id = $id;
        $this->naam = $naam;
        $this->beschrijving = $beschrijving;
    }

    /**
     * Geeft het Id nummer of Id string van dit object terug.
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
        if( !$this->getBeschrijving() ){
            return $this->getNaam();
        }
        return $this->getNaam().' ('.$this->getBeschrijving().')';
    }

    /**
     * Geef het type van een DomainObject terug. Onder andere nodig om de (@link KVDdom_DataMapper) te kunnen vinden.
     * @return string
     */
    public function getClass()
    {
        return get_class( $this );
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString( )
    {
        return $this->getOmschrijving( );
    }
}
?>
