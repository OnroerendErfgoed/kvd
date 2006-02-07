<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_DomainObject.interface.php,v 1.1 2006/01/12 14:46:02 Koen Exp $
 */

/**
 * De interface waaraan een DomainObject zich moet houden.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

interface KVDdom_DomainObject 
{
    /**
     * Geeft het Id nummer van dit object terug.
     * @return integer
     */
    public function getId();
    
    /**
     * Geef het type van een DomainObject terug. Onder andere nodig om de DataMapper te kunnen vinden.
     * @return string
     */
    public function getClass();

    /**
     * Geef een omschrijving van het betreffende object. Kan gebruikt worden voor oa. overzichten en keuzelijsten.
     * @return string
     */
    public function getOmschrijving();
}
?>
