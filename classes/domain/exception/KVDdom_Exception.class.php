<?php
/**
 * @package KVD.dom.exception
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Afhandelen van Concurrency problemen bij het opslaan van DomainObjects.
 *
 * @package KVD.dom.exception
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 21 april 2006
 */
class KVDdom_ConcurrencyException extends Exception
{
    /**
     * @var KVDdom_DomainObject
     */
    protected $domainObject;
    
    /**
     * @param string $msg
     * @param KVDdom_DomainObject $domainObject
     */
    public function __construct( $msg , $domainObject = null )
    {
        parent::__construct ( $msg );
        if ( $domainObject !== null ) {
           $this->setDomainObject ( $domainObject );
        }
    }

    /**
     * Geef het DomainObject dat niet kon worden opgeslagen in zodat er later iets mee gedaan kan worden.
     * @param $domainObject
     */
    public function setDomainObject ( $domainObject )
    {
        $this->domainObject = $domainObject;
        $this->message .= " [Concurreny Error: {$domainObject->getClass()} {$domainObject->getId()} werd gewijzigd sinds u het geopend hebt om te bewerken.]";
    }

    /**
     * Krijg het DomainObject waarbij de Concurrency Exception zich voordeed.
     * @return KVDdom_DomainObject
     */
    public function getDomainObject ()
    {
        return $this->domainObject;
    }

}

/**
 * Bij het committen van een sessie werd niet alle data verwerkt
 * @package KVD.dom.exception
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 28 jun 2006
 */
class KVDdom_IncompleteSessieCommitException extends Exception
{
    /**
     * @param string $msg
     * @param string $identityMap Naam van de Identity Map die niet volledig verwerkt werd.
     * @param integer $mapCount Aantal overblijvende objecten.
     */
    public function __construct ( $msg, $identityMap, $mapCount)
    {
        parent::__construct( $msg );
        $this->message .= " [Sessie Error: {$identityMap} werd niet volledig verwerkt en bevat nog {$mapCount} objecten.]";
    }
}

?>
