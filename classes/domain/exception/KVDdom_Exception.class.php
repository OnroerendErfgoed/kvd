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
 * @since 1.0.0
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
?>
