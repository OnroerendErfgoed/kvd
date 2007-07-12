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
 * KVDdom_ChangeableSystemFields 
 * 
 * Een class die de status van DomainObjects bijhoudt. De datamapping wordt verzorgd door de datamapper van het object waar het toe hoort.
 * Hiervoor beschikt dit object over een KVDdom_ChangeableSystemFieldsMapper.
 * @package KVD.dom
 * @subpackage Systemfields
 * @since 12 jul 2007
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_ChangeableSystemFields {

    /**
     * aangemaaktDoor
     *
     * De gebruiker die het record voor het eerst heeft ingevoerd.
     * @var string Een gebruikersnaam zoals ze voorkomt in de databank.
     */
    protected $aangemaaktDoor;

    /**
     * aangemaaktOp 
     * 
     * Datum en tijd waarop het object werd aangemaakt
     * @var DateTime
     */
    protected $aangemaaktOp;

    /**
     * bewerktDoor 
     *
     * De gebruiker die het record voor het eerst heeft ingevoerd.
     * @var string Een gebruikersnaam zoals ze voorkomt in de databank.
     */
    protected $bewerktDoor;

    /**
     * bewerktOp 
     * 
     * Datum en tijd waarop het object werd bewerkt.
     * @var DateTime
     */
    protected $bewerktOp;

    /**
     * Een versienummer. Belangrijk voor de Concurrency Control. Implementatie van Optimistic Offline Concurrency (POEAA).
     * @var integer
     */
    protected $versie;

    /**
     * targetVersie 
     * 
     * Een versienummer. Belangrijk voor de Concurrency Control. Implementatie van Optimistic Offline Concurrency (POEAA).
     * Dit is het versienummer waarnaar geupdate wordt in geval van een update.
     * @var integer
     */
    protected $targetVersie;

    /**
     * locked 
     * 
     * @var boolean
     */
    protected $locked;

    /**
     * Maak het object aan. Enkel het gebruikersobject is verreist. De andere velden kunnen worden opgevuld met standaardwaarden.
     * @param string $aangemaaktDoor Naam van de gebruiker.
     * @param integer $aangemaaktOp Wanneer werd het record aangemaakt?
     * @param integer $versie Huidige versie van het record.
     * @param string $bewerktDoor Wie heeft het record bewerkt, null indien het nog niet bewerkt werd.
     * @param integer $bewerktOp Wanneer werd dit record het laatst bewerkt, null indien het nog nooit bewerkt werd.
     */ 
    public function __construct ( $aangemaaktDoor, $aangemaaktOp = null, $versie = 0, $bewerktDoor = null, $bewerktOp = null)
    {
        if ($aangemaaktOp == null) {
            $aangemaaktOp = time( );
        }
        $this->aangemaaktDoor = $aangemaaktDoor;
        $this->versie = $this->targetVersie = $versie;
        $this->aangemaaktOp = date(KVDdom_DomainObject::DATETIME_FORMAT , $aangemaaktOp );
        $this->bewerktDoor = $bewerktDoor;
        $this->bewerktOp = is_null( $gbewerktOp ) ? null : date( KVDdom_DomainObject::DATETIME_FORMAT , $bewerktOp );
        $this->locked = false;
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
            $this->bewerktOp = date(KVDdom_DomainObject::DATETIME_FORMAT , time());
            //Zeker zijn dat aangemaaktOp en bewerktOp gelijk zijn in het geval van een nieuw object.
            if ( $this->versie == 0 ) {
                $this->aangemaaktOp = $this->bewerktOp;
                $this->aangemaaktDoor = $this->bewerktDoor;
            }
            $this->locked = true;
        }
    }

    /**
     * @return string 
     */
    public function getAangemaaktDoor()
    {
        return $this->aangemaaktDoor;
    }

    /**
     * @return string Een datum string.
     */
    public function getAangemaaktOp()
    {
        return $this->aangemaaktOp;
    }

    /**
     * @return integer
     */
    public function getVersie()
    {
        return $this->versie;
    }

    /**
     * getTargetVersie 
     * 
     * Dit geeft het versie-nummer terug waarnaar moet geupdate worden. Indien er nog geen updateSystemFields heeft plaatsgevonden is dit gelijk aan het initiele versienummer.
     * Belangrijk voor mappers die met een shared lock werken.
     * @return integer
     */
    public function getTargetVersie( )
    {
        return $this->targetVersie;
    }

    /**
     * @return string Een datum string.
     */
    public function getBewerktOp()
    {
        return $this->bewerktOp;
    }

    /**
     * getBewerktDoor 
     * 
     * @return string
     */
    public function getBewerktDoor( )
    {
        return $this->bewerktDoor;
    }

    /**
     * isBewerkt 
     * 
     * @return boolean
     */
    public function isBewerkt( )
    {
        return !( $this->aangemaaktOp == $this->bewerktOp)
    }

    /**
     * newNull 
     * 
     * @return KVDdom_ChangeableSystemFields
     */
    public static function newNull( )
    {
        return new KVDdom_ChangeableSystemFields( 'anoniem' );
    }
}
?>
