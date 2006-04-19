<?php
/**
 * DomainObject voor KeuzeLijsten en andere Read-only objecten.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_ValueDomainObject.class.php,v 1.1 2006/01/12 14:46:03 Koen Exp $
 */

/**
 * DomainObject voor o.a. KeuzeLijsten.
 *
 * Een class die door alle value domein-objecten van een applicatie geërfd wordt. Meestal zijn dit de waarden die in de keuzelijsten zitten. DataMappers voor deze objecten zouden in DataMappers van de objecten die verwijzen naar de keuzelijst moeten zitten, tenzij het om een keuzelijst gaat die door veel verschillende tabellen geraadpleegd wordt. Dan wordt er een aparte DM aangemaakt.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

abstract class KVDdom_ValueDomainObject implements KVDdom_DomainObject
{
    /**
     * Constante om aan te geven dat bepaalde waarden pas later geladen moeten worden.
     * @var string
     */
    const PLACEHOLDER = "TE LADEN";
    
    /**
     * Id nummer van het domainObject
     * @var integer
     */
    protected $id;

    /**
     * Sessie object om aan mappers te kunnen.
     * @var KVDdom_Sessie
     */
    protected $_sessie;

    /**
     * @param integer $id Id nummer van het object.
     * @param KVDdom_Sessie $sessie Het sessie object. Is optioneel omdat vele ValueDomainObjecten nooit een sessie gaan nodig hebben.
     */
    public function __construct ( $id , $sessie = null)
    {
        $this->id = $id;
        $this->_sessie = $sessie;
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
     * Geef het type van een Domain Object terug. Onder andere nodig om de DataMapper te kunnen vinden.
     * @return string
     */
    public function getClass()
    {
        return get_class( $this );
    }
    
}
?>
