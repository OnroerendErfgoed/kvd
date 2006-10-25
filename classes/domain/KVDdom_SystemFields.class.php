<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
    
/**
 * KVDdom_SystemFields
 *
 * Een class die de status van DomainObjects bijhoudt. Heeft geen eigen DataMapper, deze taak wordt afgehandeld door de Abstracte DataMappers.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdom_SystemFields {
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
     * Datum waarop de aanpassing werd gedaan.
     * @var date
     */
    private $bewerktOp;
    
    /**
     * Werd het domainObject al nagezien door de redactie?
     * @var boolean
     */
    private $gecontroleerd;

    /**
     * Is dit de meest recente versie van die object?
     * @var boolean
     */
    private $currentRecord;

    /**
     * Maak het object aan. Enkel het gebruikersobject is verreist. De andere velden kunnen worden opgevuld met standaardwaarden.
     * @param string $gebruikersNaam Naam van de gebruiker.
     * @param boolean $currentRecord Gaat het om de meest recente versie van een record of niet?
     * @param integer $versie Huidige versie van het record.
     * @param integer $bewerktOp Wanneer werd deze versie van het record aangemaakt ( een timestamp dus) ?
     * @param boolean $gecontroleerd Werd het record al gecontroleerd?
     */ 
    public function __construct ( $gebruikersNaam, $currentRecord = true, $versie = 0, $bewerktOp = null, $gecontroleerd = false)
    {
        if ($bewerktOp == null) {
            $bewerktOp = date(KVDdom_DomainObject::DATETIME_FORMAT , time());
        }
        $this->gebruikersNaam = $gebruikersNaam;
        $this->currentRecord = $currentRecord;
        $this->versie = $versie;
        $this->bewerktOp = $bewerktOp;
        $this->gecontroleerd = $gecontroleerd;
    }

    /**
     * Verhoog de versie-informatie in het object naar de volgende versie. 
     *
     * @param string $gebruikersNaam Naam van de gebruiker die de update uitvoerde. Indien afwezig wordt de huidige gebruiker behouden.
     */
    public function updateSystemFields ($gebruikersNaam=null)
    {
        if ( $gebruikersNaam instanceof KVDdom_Gebruiker) {
            throw new IllegalArgumentException ( 'Gebruikersnaam moet een string zijn!');
        }
        if ($gebruikersNaam != null) {
            $this->gebruikersNaam = $gebruikersNaam;
        }
        $this->versie++;
        $this->bewerktOp = date(KVDdom_DomainObject::DATETIME_FORMAT , time());
        $this->gecontroleerd = false;
        $this->currentRecord=true;
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
     * @return date
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
     * @return boolean
     */
    public function isCurrentRecord()
    {
        return $this->currentRecord; 
    }

    public function setApproved( )
    {
        $this->gecontroleerd = true;
    }
}
?>
