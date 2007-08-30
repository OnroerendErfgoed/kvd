<?php
/**
 * @package KVD.dom
 * @version $Id: KVDdom_SystemFields.class.php 278 2007-02-16 15:17:52Z vandaeko $
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
    
/**
 * KVDdom_LegacySystemFields 
 * 
 * Een class die de status van DomainObjects bijhoudt. Heeft geen eigen DataMapper, deze taak wordt afgehandeld door de Abstracte DataMappers.
 * @package KVD.dom
 * @since 2005
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_LegacySystemFields {
    /**
     * De gebruiker die iets met het record gedaan heeft. Meestal is dit de invoerder.
     * @var string Een gebruikersnaam zoals ze voorkomt in de databank.
     */
    private $gebruikersNaam;

    /**
     * Een versienummer. Belangrijk voor de Concurrency Control. Implementatie van Optimistic Offline Concurrency (POEAA).
     * @var integer
     */
    private $versie;

    /**
     * targetVersie 
     * 
     * Een versienummer. Belangrijk voor de Concurrency Control. Implementatie van Optimistic Offline Concurrency (POEAA).
     * Dit is het versienummer waarnaar geupdate wordt in geval van een update.
     * @var integer
     * @since 31 okt 2006
     */
    private $targetVersie;

    /**
     * Datum waarop de aanpassing werd gedaan.
     * @var string
     */
    private $bewerktOp;
    
    /**
     * Werd het domainObject al nagezien door de redactie?
     * @var boolean
     */
    private $gecontroleerd;

    /**
     * gecontroleerdDoor 
     * 
     * @var string Een gebruikersnaam zoals ze voorkomt in de databank.
     */
    private $gecontroleerdDoor;

    /**
     * gecontroleerdOp 
     * 
     * Datum waarop het record gecontroleerd werd.
     * @var string
     */
    private $gecontroleerdOp;

    /**
     * locked 
     * 
     * @since 31 okt 2006
     * @var boolean
     */
    private $locked;

    /**
     * Maak het object aan. Enkel het gebruikersobject is verreist. De andere velden kunnen worden opgevuld met standaardwaarden.
     * @param string $gebruikersNaam Naam van de gebruiker.
     * @param integer $versie Huidige versie van het record.
     * @param integer $bewerktOp Wanneer werd deze versie van het record aangemaakt ( een timestamp dus) ?
     * @param boolean $gecontroleerd Werd het record al gecontroleerd?
     * @param string $gecontroleerdDoor Door wie werd het record gecontroleerd?
     * @param string $gecontroleerdOp Wanneer werd het record gecontroleerd?
     */ 
    public function __construct ( $gebruikersNaam='anoniem', $versie = 0, $bewerktOp = null, $gecontroleerd = false, $gecontroleerdDoor = null, $gecontroleerdOp = null)
    {
        if ($bewerktOp == null) {
            $bewerktOp = time( );
        }
        $this->gebruikersNaam = $gebruikersNaam;
        $this->versie = $this->targetVersie = $versie;
        $this->bewerktOp = date(KVDdom_DomainObject::DATETIME_FORMAT , $bewerktOp );
        $this->gecontroleerd = $gecontroleerd;
        $this->gecontroleerdDoor = $gecontroleerdDoor;
        $this->gecontroleerdOp = is_null( $gecontroleerdOp ) ? null : date( KVDdom_DomainObject::DATETIME_FORMAT , $gecontroleerdOp );
        $this->locked = false;
    }

    /**
     * Verhoog de versie-informatie in het object naar de volgende versie. 
     *
     * Indien een andere actor al de opdracht heeft gegeven, wordt deze update niet meer uitgevoerd. Dit maakt het mogelijk om SystemFields te delen tussen objecten.
     * @param string $gebruikersNaam Naam van de gebruiker die de update uitvoerde. Indien afwezig wordt de huidige gebruiker behouden.
     */
    public function updateSystemFields ($gebruikersNaam=null)
    {
        if ( $gebruikersNaam instanceof KVDdom_Gebruiker) {
            throw new IllegalArgumentException ( 'Gebruikersnaam moet een string zijn!');
        }
        if ( !$this->locked ) {
            if ($gebruikersNaam != null) {
                $this->gebruikersNaam = $gebruikersNaam;
            }
            $this->targetVersie++;
            $this->bewerktOp = date(KVDdom_DomainObject::DATETIME_FORMAT , time());
            $this->gecontroleerd = false;
            $this->gecontroleerdDoor = null;
            $this->gecontroleerdOp = null;
            $this->locked = true;
        }
    }

    /**
     * @return string 
     */
    public function getGebruikersNaam()
    {
        return $this->gebruikersNaam;
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
     * @since 31 okt 2006
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
     * @return boolean
     */
    public function getGecontroleerd()
    {
        return $this->gecontroleerd;
    }

    /**
     * getGecontroleerdDoor 
     * 
     * @return string
     */
    public function getGecontroleerdDoor( )
    {
        return $this->gecontroleerdDoor;
    }

    /**
     * getGecontroleerdOp 
     * 
     * @return string Een datum string
     */
    public function getGecontroleerdOp( )
    {
        return $this->gecontroleerdOp;
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
        if ( $this->gecontroleerd || !is_null( $this->gecontroleerdDoor ) ) {
            throw new KVDdom_RedactieException ( 'U probeert een record goed te keuren dat al goedgekeurd is!');
        }
        $this->gecontroleerd = true;
        $this->gecontroleerdDoor = $gebruikersNaam;
        $this->gecontroleerdOp = date(KVDdom_DomainObject::DATETIME_FORMAT , time());
    }

    public static function newNull( )
    {
        return new KVDdom_LegacySystemFields( 'anoniem' );
    }
}
?>
