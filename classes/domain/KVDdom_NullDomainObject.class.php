<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Special Case voor een DomainObject dat niet bestaat.)
 *
 * Voor meer informatie zie Patterns of Enterprise Application Architecture p. 496.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDdom_NullDomainObject implements KVDdom_DomainObject
{
    
    /**
     * Geeft het Id nummer van dit object terug.
     * @return integer
     */
    public function getId()
    {
        return 0;
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
     * Geef een omschrijving voor het object.
     * @return string
     */
    public function getOmschrijving()
    {
        return '';
    }
    
}
?>
