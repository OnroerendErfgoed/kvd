<?php
/**
 * @package     KVD.dom
 * @subpackage  exception
 * @version     $Id$
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_Exception 
 * 
 * @package     KVD.dom
 * @subpackage  exception
 * @since       23 mrt 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_Exception extends Exception
{

}

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
        $this->message .= " [Sessie Error: {$identityMap} werd niet volledig verwerkt en bevat nog {$mapCount} objecten. Controleer of de commitVolgorde correct is ingesteld.]";
    }
}

/**
 * KVDdom_ReferenceViolationException 
 * 
 * @package KVD.dom.exception
 * @since 27 april 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_ReferenceViolationException extends Exception
{
    protected $domainObject;
    
    /**
     * __construct 
     * 
     * @param KVDdom_DomainObject   $domainObject
     * @param PDOException          $exception
     * @return void
     */
    public function __construct( $domainObject, PDOException $exception )
    {
        $this->domainObject = $domainObject;
        $this->exception = $exception;
        $this->message = 'U hebt geprobeerd een ' . $domainObject->getClass() . ' met id ' . $domainObject->getId( ) .' te bewerken of verwijderen maar dit is niet mogelijk omdat uw bewerking tot ongeldige referenties met andere objecten zou leiden.';
    }

    /**
     * Krijg het DomainObject waarbij de Concurrency Exception zich voordeed.
     * @return KVDdom_DomainObject
     */
    public function getDomainObject ()
    {
        return $this->domainObject;
    }

    /**
     * getException 
     * 
     * De PDOException die de fout aangeeft.
     * @return PDOException
     */
    public function getException( )
    {
        return $this->exception;
    }
}

/**
 * KVDdom_MapperConfigurationException 
 * 
 * @package KVD.dom.exception
 * @since 24 mei 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_MapperConfigurationException extends Exception 
{
    public function __construct( $msg , $mapper ) 
    {
        $this->message .= " [Mapper Configuratie Error: " . get_class( $mapper ) . "] " . $msg;
    }
}

/**
 * KVDdom_OngeldigeTypeException 
 * 
 * @package KVD.dom.exception
 * @since 19 dec 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_OngeldigeTypeException extends Exception
{
    public function __construct( $gekregenType, $gevraagdType )
    {
        $this->message .= " [Ongeldig Type: U probeert een bewerking uit te voeren met een object 
        dat een ander type heeft dat de collection waarop u de bewerking uitvoert. U probeert een $gekregenType 
        toe te voegen aan een collectie van het type $gevraagdType]";
    }
    
}
?>
