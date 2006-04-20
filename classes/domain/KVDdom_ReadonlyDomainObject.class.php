<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
 
/**
 * DomainObjects die niet gewijzigd kunnen worden maar wel veel data bevatten.
 *
 * Indien het DomainObject onwijzigbaar is en de gelijkheid van 2 objecten niet afhangt van identiteit maar van waarde dan wordt er beter gebruik gemaakt van een KVDdom_ValueDomainObject.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
abstract class KVDdom_ReadonlyDomainObject implements KVDdom_DomainObject {

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
     * Maak het KVDdom_DomainObject
     * @param KVDdom_Sessie $sessie 
     * @param integer $id Id dat aan het nieuwe KVDdom_DomainObject moet gegeven worden.
     */
    public function __construct ( $id , $sessie ) 
    {
        $this->_sessie = $sessie;
        $this->id = $id;
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

}
?>
