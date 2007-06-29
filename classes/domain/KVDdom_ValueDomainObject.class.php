<?php
/**
 * DomainObject voor KeuzeLijsten en andere simpele objecten.
 *
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * DomainObject voor o.a. KeuzeLijsten.
 *
 * Een class die door alle value domein-objecten van een applicatie geerfd wordt. 
 * Meestal zijn dit de waarden die in de keuzelijsten zitten. 
 * DataMappers voor deze objecten zouden in DataMappers van de objecten die verwijzen naar de keuzelijst moeten zitten, 
 * tenzij het om een keuzelijst gaat die door veel verschillende tabellen geraadpleegd wordt. Dan wordt er een aparte DM aangemaakt.
 * Ingewikkelde keuzelijsten die ook nog veel andere data bevatten gebruiken best de KVDdom_ReadonlyDomainObject class als superclass.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

abstract class KVDdom_ValueDomainObject implements KVDdom_DomainObject
{
    /**
     * Id nummer van het domainObject
     * @var integer
     */
    protected $id;

    /**
     * @param integer $id Id nummer van het object.
     */
    public function __construct ( $id )
    {
        $this->id = $id;
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
