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
     * @var KVDdom_PDOLogableDataMapper
     */
    private $mapper;

    /**
     * @param string $subAction
     * @param KVDdom_PDOLogableDataMapper
     */
    public function __construct ( $subAction , KVDdom_PDOLogableDataMapper $mapper)
    {
        $this->subAction = $subAction;
        
        $this->mapper = $mapper;

    }

    /**
     * @param Request Het Request object in Agavi.
     * @param KVDdom_Redigeerbaar Het object dat geredigeerd wordt.
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
            case 'goedkeuren':
                $this->approve( $domainObject );
                break;
            case 'versieTerugzetten':
                $this->versieTerugzetten( $domainObject , ( int ) $req->getParameter( 'versie' ) );
                break;
            case 'verwijderenGeschiedenis':
                $this->verwijderGeschiedenis( $domainObject );
                break;
            default:
                throw new InvalidArgumentException ( 'U probeert een redactie actie ' . $req->getParameter( 'redactie') . ' uit te voeren die niet bestaat.' );
        }
    }

    /**
     * approve 
     * 
     * @param KVDdom_Redigeerbaar $domainObject 
     * @return void
     */
    protected function approve( $domainObject )
    {
        $domainObject->approve( );
    }

    /**
     * versieTerugzetten 
     * 
     * @param KVDdom_Redigeerbaar $domainObject 
     * @param integer $versie 
     * @return void
     */
    protected function versieTerugzetten( $domainObject , $versie )
    {
        try {
            $previous = $this->mapper->findByLogId(  $domainObject->getId( ) , $versie );
        } catch ( KVDdom_DomainObjectNotFoundException $e ) {
            throw new KVDag_RedactieException ( 'U probeert een niet bestaande versie(' . $versie . ') terug te zetten.');    
        }
        try {
            $domainObject->updateToPreviousVersion (  $previous );
        } catch ( Exception $e ) {
            throw new KVDag_RedactieException ( 'Onmogelijk om een domainObject te updaten naar de vorige versie (' . $versie . ').');    
        }
    }

    /**
     * verwijderGeschiedenis 
     * 
     * @param KVDdom_Redigeerbaar $domainObject 
     * @return void
     */
    protected function verwijderGeschiedenis( $domainObject )
    {
        $domainObject->verwijderGeschiedenis( );
        if ( $domainObject->isNull(  ) ) {
            $this->forwardAction = 'RedactieOverzicht';
        }
    }

    /**
     * @return string De naam van de actie die uitgevoerd moeten worden.
     */
    public function getActionNaam( )
    {
        return $this->forwardAction;
    }
}

/**
 * KVDag_RedactieException 
 * 
 * @package KVD.Ag
 * @since 10 nov 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_RedactieException extends Exception
{
    
}
?>
