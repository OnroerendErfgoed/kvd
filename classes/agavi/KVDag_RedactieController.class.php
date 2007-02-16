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
    protected $forwardAction;

    /**
     * @var KVDdom_PDORedigeerbareDataMapper
     */
    private $mapper;

    /**
     * redacteur 
     * 
     * @var string
     */
    private $redacteur;

    /**
     * redirectParameters 
     * 
     * @var array
     */
    protected $redirectParameters = array( );

    /**
     * @param string $subAction
     * @param KVDdom_PDOLogableDataMapper $mapper
     * @param string $redacteur 
     */
    public function __construct ( $subAction , KVDdom_PDORedigeerbareDataMapper $mapper, $redacteur )
    {
        $this->subAction = $subAction;
        
        $this->mapper = $mapper;

        $this->redacteur = $redacteur;

    }

    /**
     * @param Request Het Request object in Agavi.
     * @param KVDdom_RedigeerbaarDomainObject Het object dat geredigeerd wordt.
     * @throws <b>InvalidArgumentException</b> Indien er om een niet bestaande redactie gevraagd wordt.
     * @throws <b>KVDag_RedactieException</b> Indien de redactie niet kan doorgevoerd worden.
     */
    public function execute( $req, $domainObject)
    {
        if ( !$req->hasParameter( 'redactie') ) {
            throw new InvalidArgumentException ( 'Er is geen redactie gespecifieerd' );
        }
        $this->forwardAction = $this->subAction . '.Tonen';
        switch ( $req->getParameter( 'redactie' ) ) {
            case 'approve':
                $this->approve( $domainObject );
                break;
            case 'updateToPrevious':
                $this->updateToPrevious( $domainObject , ( int ) $req->getParameter( 'versie' ) );
                break;
            case 'confirmDelete':
                $this->confirmDelete( $domainObject );
                break;
            case 'undoDelete':
                $this->undoDelete( $domainObject );
                break;
            default:
                throw new InvalidArgumentException ( 'U probeert een redactie actie ' . $req->getParameter( 'redactie') . ' uit te voeren die niet bestaat.' );
        }
    }

    /**
     * approve 
     * 
     * @param KVDdom_RedigeerbaarDomainObject $domainObject 
     * @return void
     */
    protected function approve( $domainObject )
    {
        $domainObject->approve( $this->redacteur );
        $this->redirectParameters = array( 'id' => $domainObject->getId( ) );
    }

    /**
     * versieTerugzetten 
     * 
     * @param KVDdom_RedigeerbaarDomainObject $domainObject 
     * @param integer $versie 
     * @return void
     */
    protected function updateToPrevious( $domainObject , $versie )
    {
        try {
            $previous = $this->mapper->findByLogId(  $domainObject->getId( ) , $versie );
        } catch ( KVDdom_DomainObjectNotFoundException $e ) {
            throw new KVDdom_RedactieException ( 'U probeert een niet bestaande versie(' . $versie . ') terug te zetten.');    
        }
        $domainObject->updateToPreviousVersion (  $previous );
        $this->redirectParameters = array( 'id' => $domainObject->getId( ) );
    }

    /**
     * verwijderGeschiedenis 
     * 
     * @param KVDdom_RedigeerbaarDomainObject $domainObject 
     * @return void
     */
    protected function confirmDelete( $domainObject )
    {
        $domainObject->confirmDelete( );
        $this->forwardAction = 'RedactieOverzicht';
    }

    /**
     * undoDelete 
     * 
     * @param KVDdom_RedigeerbaarDomainObject $domainObject 
     * @return void
     */
    protected function undoDelete ( $domainObject )
    {
        $domainObject->undoDelete( );
        $this->redirectParameters = array( 'id' => $domainObject->getId( ) );
    }

    /**
     * @return string De naam van de actie die uitgevoerd moeten worden.
     */
    public function getActionNaam( )
    {
        return $this->forwardAction;
    }

    /**
     * getRedirectParameters 
     * 
     * @return array
     */
    public function getRedirectParameters( )
    {
        return $this->redirectParameters;
    }
}

?>
