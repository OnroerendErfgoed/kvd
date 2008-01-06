<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
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
     * Een constante waar alle domainObjects mee werken zodat ze hetzelfde datum/tijd formaat gebruiken. Deze geeft enkel de datum weer.
     * @var string
     */
    const DATE_FORMAT = 'd-m-Y';

    /**
     * Een constante waar alle domainObjects mee werken zodat ze hetzelfde datum/tijd formaat gebruiken. Deze geeft de datum en de tijd weer.
     * @var string
     */
    const DATETIME_FORMAT = 'd-m-Y H:i';

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
