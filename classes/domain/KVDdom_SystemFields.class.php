<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_SystemFields.class.php,v 1.1 2006/01/12 14:46:03 Koen Exp $
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
     * @var KVDdom_Gebruiker
     */
    private $_gebruiker;

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
     * @param KVDdom_Gebruiker $gebruiker Object met gebruikerinfo.
     * @param integer $versie 
     * @param date $bewerktOp
     * @param boolean $gecontroleerd
     */ 
    public function __construct ( $gebruiker, $currentRecord = true, $versie = 0, $bewerktOp = null, $gecontroleerd = false)
    {
        if ($bewerktOp == null) {
            $bewerktOp = date('Y-m-d H:i:s', time());
        }
        $this->_gebruiker = $gebruiker;
        $this->currentRecord = $currentRecord;
        $this->versie = $versie;
        $this->bewerktOp = $bewerktOp;
        $this->gecontroleerd = $gecontroleerd;
    }

    /**
     * Verhoog de versie-informatie in het object naar de volgende versie. 
     *
     * @param KVDdom_Gebruiker $gebruiker Object met gebruikerinfo. Indien afwezig wordt de huidige gebruiker behouden.
     */
    public function updateSystemFields ($gebruiker = null)
    {
        if ($gebruiker != null) {
            $this->_gebruiker = $gebruiker;
        }
        $this->versie++;
        $this->bewerktOp = date('Y-m-d H:i:s', time());
        $this->gecontroleerd = false;
        $this->currentRecord=true;
    }

    /**
     * @return integer Geef het id van de huidige gebruiker weer.
     */
    public function getGebruikerId()
    {
        return $this->_gebruiker->getId();
    }

    /**
     * @return KVDdom_Gebruiker 
     */
    public function getGebruiker()
    {
        return $this->_gebruiker;
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
}
?>
