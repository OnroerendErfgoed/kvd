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
 * Een class die door alle value domein-objecten van een applicatie ge�rfd wordt. Meestal zijn dit de waarden die in de keuzelijsten zitten. DataMappers voor deze objecten zouden in DataMappers van de objecten die verwijzen naar de keuzelijst moeten zitten, tenzij het om een keuzelijst gaat die door veel verschillende tabellen geraadpleegd wordt. Dan wordt er een aparte DM aangemaakt.
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
     * @param $id Id nummer van het object.
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
    
}
?>
