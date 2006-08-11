<?php
/**
 * @package KVD.ag
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */


/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 19 jun 2006
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
     * @var string $module
     * @var string $subAction
     * @var KVDdom_LogableDataMapper
     */
    public function __construct ( $subAction , $mapper)
    {
        $this->subAction = $subAction;

        $this->mapper = $mapper;

    }

    /**
     * @var Request
     * @var KVDdom_LogableDomainObject
     */
    public function execute( $req, $domainObject)
    {
         if ( $req->getParameter( 'redactie' ) == 'goedkeuren' ) {
            $domainObject->approve( );
            $this->forwardAction = $this->subAction . '.Tonen';
         } else if (  $req->getParameter(  'redactie' ) == 'versieTerugzetten' ) {
            //Zet een versie terug.
            $versie = $this->mapper->findByLogId(  $domainObject->getId( ) , ( int) $req->getParameter(  'versie') );
            $domainObject->updateToPreviousVersion (  $versie );
            $this->forwardAction = $this->subAction . '.Tonen';
         } else if (  $req->getParameter(  'redactie' ) == 'verwijderenGeschiedenis' ) {
            $domainObject->verwijderGeschiedenis(  );
            if ( $domainObject->isNull(  ) ) {
                $this->forwardAction = 'RedactieOverzicht';
            } else {
                $this->forwardAction = $this->subaction . '.Tonen';
            }
         } else {
             throw new InvalidArgumentException ( 'De redactie die u probeert uit te voeren bestaat niet.');
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
