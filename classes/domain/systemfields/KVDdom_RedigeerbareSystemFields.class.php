<?php
/**
 * @package KVD.dom
 * @subpackage Systemfields
 * @version $Id: KVDdom_SystemFields.class.php 278 2007-02-16 15:17:52Z vandaeko $
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
    
/**
 * KVDdom_RedigeerbareSystemFields 
 * 
 * Een class die de status van DomainObjects bijhoudt. De datamapping wordt verzorgd door de datamapper van het object waar het toe hoort.
 * Hiervoor beschikt dit object over een KVDdom_RedigeerbareSystemFieldsMapper.
 * @package KVD.dom
 * @subpackage Systemfields
 * @since 12 jul 2007
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_RedigeerbareSystemFields extends KVDdom_ChangeableSystemFields {

    /**
     * gecontroleerdDoor
     *
     * De gebruiker die het record het gecontroleerd.
     * @var string Een gebruikersnaam zoals ze voorkomt in de databank.
     */
    protected $gecontroleerdDoor;

    /**
     * aangemaaktOp 
     * 
     * Datum en tijd waarop het object werd gecontroleerd
     * @var DateTime
     */
    protected $gecontroleerdOp;

    /**
     * Maak het object aan. Enkel het gebruikersobject is verreist. De andere velden kunnen worden opgevuld met standaardwaarden.
     * @param string $aangemaaktDoor Naam van de gebruiker.
     * @param DateTime $aangemaaktOp Wanneer werd het record aangemaakt?
     * @param integer $versie Huidige versie van het record.
     * @param string $bewerktDoor Wie heeft het record bewerkt, null indien het nog niet bewerkt werd.
     * @param DateTime $bewerktOp Wanneer werd dit record het laatst bewerkt, null indien het nog nooit bewerkt werd.
     * @param string $gecontroleerdDoor Wie heeft het record bewerkt, null indien het nog niet gecontroleerd werd.
     * @param DateTime $gecontroleerdOp Wanneer werd dit record het laatst bewerkt, null indien het nog nooit gecontroleerd werd.
     */ 
    public function __construct ( $aangemaaktDoor, DateTime $aangemaaktOp = null, $versie = 0, $bewerktDoor = null, DateTime $bewerktOp = null, $gecontroleerdDoor = null, DateTime $gecontroleerdOp = null)
    {
        parent::__construct( $aangemaaktDoor , $aangemaaktOp, $versie, $bewerktDoor, $bewerktOp, $gecontroleerdDoor, $gecontroleerdOp );

        $this->gecontroleerdDoor = $gecontroleerdDoor;
        $this->gecontroleerdOp = $gecontroleerdOp;
    }

    /**
     * Verhoog de versie-informatie in het object naar de volgende versie. 
     *
     * Indien een andere actor al de opdracht heeft gegeven, wordt deze update niet meer uitgevoerd. Dit maakt het mogelijk om SystemFields te delen tussen objecten.
     * @param string $gebruikersNaam Naam van de gebruiker die de update uitvoerde. Indien afwezig wordt de huidige gebruiker behouden.
     */
    public function setUpdated ($gebruikersNaam=null)
    {
        if ( $gebruikersNaam instanceof KVDdom_Gebruiker) {
            throw new IllegalArgumentException ( 'Gebruikersnaam moet een string zijn!');
        }
        if ( !$this->locked ) {
            $this->bewerktDoor = ( $gebruikersNaam == null ) ? $this->aangemaaktDoor : $gebruikersNaam;
            $this->targetVersie++;
            $this->bewerktOp = new DateTime( );
            //Zeker zijn dat aangemaaktOp en bewerktOp gelijk zijn in het geval van een nieuw object.
            if ( $this->versie == 0 ) {
                $this->aangemaaktOp = $this->bewerktOp;
                $this->aangemaaktDoor = $this->bewerktDoor;
            }
            $this->gecontroleerdOp = null;
            $this->gecontroleerdDoor = null;
            $this->locked = true;
        }
    }

    /**
     * @return string 
     */
    public function getGecontroleerdDoor()
    {
        return $this->aangemaaktDoor;
    }

    /**
     * @return DateTime
     */
    public function getGecontroleerdOp()
    {
        return $this->aangemaaktOp;
    }

    /**
     * isGecontroleerd 
     * 
     * @return boolean
     */
    public function isGecontroleerd( )
    {
        return !is_null($this->gecontroleerdDoor);
    }

    /**
     * setApproved 
     * 
     * @param string $gebruikersNaam Naam van de gebruiker die de controle uitvoerde
     * @throws <b>KVDdom_RedactieException</b> Indien u probeert een record goed te keuren dat al goedgekeurd is.
     * @return void
     */
    public function setApproved( $gebruikersNaam )
    {
        if ( $this->isGecontroleerd( ) ) {
            throw new KVDdom_RedactieException ( 'U probeert een record goed te keuren dat al goedgekeurd is!');
        }
        $this->gecontroleerdDoor = $gebruikersNaam;
        $this->gecontroleerdOp = date(KVDdom_DomainObject::DATETIME_FORMAT , time());
    }

    /**
     * newNull 
     * 
     * @return KVDdom_RedigeerbareSystemFields
     */
    public static function newNull( )
    {
        return new KVDdom_RedigeerbareSystemFields( 'anoniem' );
    }
}
?>
