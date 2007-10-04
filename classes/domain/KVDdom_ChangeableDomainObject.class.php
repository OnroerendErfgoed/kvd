<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
 
/**
 * DomainObjects die gewijzigd kunnen worden.
 *
 * Het wijzigen van een DomainObject gaat altijd via de UnitOfWork die in het KVDdom_Sessie object zit.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
abstract class KVDdom_ChangeableDomainObject implements KVDdom_DomainObject, KVDdom_Nullable {

    /**
     * Een constante om aan te geven dat een bepaald veld nog geladen moet worden.
     */
    const PLACEHOLDER = "TE LADEN";

    /**
     * Id nummer van het domain-object
     * @var integer
     */
    protected $id;
    
    /**
     * Verwijzing naar het KVDdom_Sessie object
     * @var KVDdom_Sessie
     */
    protected $_sessie;

    /**
     * systemFields 
     * 
     * @var KVDdom_ChangeableSystemFields
     */
    protected $systemFields;
    
    /**
     * Maak het KVDdom_DomainObject
     * @param integer $id Id dat aan het nieuwe KVDdom_DomainObject moet gegeven worden.
     * @param KVDdom_Sessie $sessie 
     * @param KVDdom_ChangeableSystemFields $systemFields
     */
    public function __construct ( $id , $sessie , $systemFields = null) 
    {
        $this->_sessie = $sessie;
        $this->id = $id;
        $this->systemFields = $systemFields;
        $this->markClean();
    }

    /**
     * Geeft het Id nummer van dit object terug.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Markeert dit object als Clean
     *
     * Dit record zal door (@link KVDdom_Sessie) niet worden opgeslaan bij het verwerken van de UnitOfWork.
     */
    protected function markClean()
    {
       $this->_sessie->registerClean($this);
    }

    /**
     * Markeert dit object als New
     *
     * Dit record zal door KVDdom_Sessie worden opgeslaan bij het verwerken van de UnitOfWork. Dit komt neer op het uitvoeren van een SQL INSERT statement.
     */
    protected function markNew()
    {
        $this->_sessie->registerNew($this);
    }

    /**
     * Markeert dit object als Dirty
     *
     * Dit record zal door KVDdom_Sessie worden opgeslaan bij het verwerken van de UnitOfWork. Dit komt neer op het uitvoeren van een SQL UPDATE statement.
     */
    protected function markDirty()
    {
        $this->_sessie->registerDirty($this);
    }

    /**
     * Markeert dit object als Removed
     *
     * Dit record zal door KVDdom_Sessie worden verwijderd uit de databank bij het verwerken van de UnitOfWork. Dit komt neer op het uitvoeren van een SQL DELETE statement.
     */
    protected function markRemoved()
    {
        $this->_sessie->registerRemoved($this);
    }

    /**
     * Maakt een nieuw KVDdom_ChangeableDomainObject aan dat niet uit de databank wordt geladen
     * @param integer $id Het Id nummer voor het nieuwe object. Meestal wordt dit aangereikt door de DataMapper die er voor moet zorgen dat dit nummer uniek is binnen het type object.
     * @param KVDdom_Sessie $sessie Het sessie object. Of een ander object dat de Unit Of Work implementeert
     * @return KVDdom_ChangeableDomainObject
     */
    static function create( $id , $sessie )
    {
        throw new Exception ( 'This method should only be called on a concrete implementation.' );
    }

    /**
     * Verwijdert dit object uit het domein
     */
    abstract function remove();

    /**
     * @return boolean
     */
    public function isNull( )
    {
        return false;
    }

    /**
     * hasSystemFields 
     * 
     * @return boolean
     */
    public function hasSystemFields( )
    {
        return !is_null( $this->systemFields );
    }

    /**
     * getSystemFields 
     * 
     * Geeft het systemFields object terug of null indien er geen is.
     * @return KVDdom_ChangeableSystemFields
     */
    public function getSystemFields( )
    {
        return $this->systemFields;
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
