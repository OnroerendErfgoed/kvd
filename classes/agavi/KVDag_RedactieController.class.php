<?php
/**
 * @package KVD.agavi
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id$
 */

/**
 * KVDag_RedactieController 
 * 
 * @since 19 jun 2006
 * @package KVD.agavi
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_RedactieController
{
    /**
     * @var string
     */
    private $subAction;
    
    /**
     * @var string
     */
    private $forwardAction;

    /**
     * @var KVDdom_LogableDataMapper
     */
    private $mapper;

    /**
     * @param string $subAction
     * @param KVDdom_LogableDataMapper
     * @throws <b>InvalidArgumentException</b> Indien de mapper geen object is.
     */
    public function __construct ( $subAction , $mapper)
    {
        $this->subAction = $subAction;
        
        if ( !is_object( $mapper ) ) {
            throw new InvalidArgumentException ( 'De mapper moet een object zijn.' );
        }
    
        $this->mapper = $mapper;

    }

    /**
     * @var Request Het Request object in Agavi.
     * @var KVDdom_LogableDomainObject Het object dat geredigeerd wordt.
     * @throws <b>InvalidArgumentException</b> Indien er om een niet bestaande redactie gevraagd wordt.
     */
    public function execute( $req, $domainObject)
    {
        if ( !$req->hasParameter( 'redactie') ) {
            throw new InvalidArgumentException ( 'Er is geen redactie gespecifieerd' );
        }
        switch ( $req->getParameter( 'redactie' ) ) {
            case 'goedkeuren':
                $domainObject->approve( );
                $this->forwardAction = $this->subAction . '.Tonen';
                break;
            case 'versieTerugzetten':
                $versie = $this->mapper->findByLogId(  $domainObject->getId( ) , ( int) $req->getParameter(  'versie') );
                $domainObject->updateToPreviousVersion (  $versie );
                $this->forwardAction = $this->subAction . '.Tonen';
                break;
            case 'verwijderenGeschiedenis':
                $domainObject->verwijderGeschiedenis(  );
                if ( $domainObject->isNull(  ) ) {
                    $this->forwardAction = 'RedactieOverzicht';
                } else {
                    $this->forwardAction = $this->subaction . '.Tonen';
                }
                break;
            default:
                throw new InvalidArgumentException ( 'U probeert een redactie actie ' . $req->getParameter( 'redactie') . ' uit te voeren die niet bestaat.' );
        }
    }

    /**
     * @return array De naam van de actie die uitgevoerd moeten worden.
     */
    public function getActionNaam( )
    {
        return $this->forwardAction;
    }
}
?>
