<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Een Unit of Work.
 *
 * Is ook het centrale aanspreekpunt voor de Identity Map en het MapperRegistry.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 2005
 * @todo Uitzoeken of deze class nog nood heeft aan een ref naar de gebruiker aangezien deze info wschl in het User object zal zitten.
 */
class KVDdom_Sessie {

    /**
     * @var array
     */
    private $dataMappersConnections;
    
    /**
     * @var array
     */
    private $commitVolgorde;

    /**
     * Naam van de class die gebruikt worden om gebruikers te beheren.
     * @var string
     */
    private $gebruikerClass;
    /**
     * @var integer
     */
    private $gebruikerId;

    /**
     * @var KVDdom_Gebruiker
     */
    private $_gebruiker;
    
    /**
     * @var KVDdom_MapperRegistry
     */
    private $_mapperRegistry;
    
    /**#@+
     * @var KVDdom_GenericIdentityMap
     */
    private $_newObjects;
    private $_dirtyObjects;
    private $_removedObjects;
    private $_approvedObjects;
    private $_historyClearedObjects;
    
    private $_identityMap;
    /**#@-*/

    /**
     * @var DatabaseManager
     */
    private $_databaseManager;

    /**
     * @var KVDutil_GatewayRegistry
     */
    private $_gatewayRegistry;

    /**
     * @var KVDdom_SqlLogger
     */
    private $sqlLogger;

    /**
     * Maak een nieuwe KVDdom_Sessie aan.
     *
     * De parameter $config is vrij uitgebreid, maar is nodig voor een aantal zaken.
     * Ze moet 3 keys bevatten:
     *      - 'dataMapperDirs' Een array van directories waar de datamappers kunnen gevonden worden.
     *      - 'commitVolgorde' Een array waaruit kan afgeleid worden in welke volgorde DomainObjects naar de databank geschreven moeten worden.
     *      - 'dataMappersConnections' Een array met namen van DataMappers als key en de naam van een connectie als value. Alle DataMappers die niet in deze array voorkomen zullen met de default connectie werken.
     *      - 'gebruikerClass' Naam van de class die gebruikt moet worden voor gebruiker-objecten.
     *      - 'gatewayFactoryConfig'    Configuratie-instellingen voor de gatewayFactory. Zie {@link KVDutil_GatewayFactory} voor meer info. 
     *                                  Indien dit weggelaten wordt dan wordt er geen gatewayRegistry aangelegd.
     * Voorbeeld:
     * <code>
     *      $config = array (
     *              'dataMapperDirs'     => array ( '/opt/datamappers/',
     *                                              '/usr/datamappers/'),
     *              'commitVolgorde'    => array (  1 => 'Adres',
     *                                              2 => 'Persoon',
     *                                              3 => 'Organisatie',
     *                                              4 => 'PersoonNaarOrganisatie',
     *                                              5 => 'Vondstmelding'),
     *              'dataMappersConnections'    => array (  'Vondstmelding' => 'CAI'),
     *              'gebruikerClass'            => 'OEIdo_GebGebruiker',
     *              'logSql'                    => true
     *                 );
     *      $dbm = new DatabaseManager ();
     *      $sqlLogger = new KVDdom_SqlLogger();
     *      $gatewayConfig = array ( .... );
     *      $gatewayRegistry = new KVDutil_GatewayRegistry( new KVDutil_GatewayFactory( $gatewayConfig ) );
     *      $sessie = new KVDdom_Sessie ( 25 , $dbm, $config, $sqlLogger);                     
     * </code>
     * @param integer $gebruikerId
     * @param DatabaseManager $databaseManager Object uit het Agavi/Mojavi Framework. Een ander object met dezelfde interface kan ook werken.
     * @param array $config Array van configuratie-opties.
     * @param KVDdom_SqlLogger Een class om sql statementes te loggen ter controle.
     * @param KVDutil_GatewayRegistry Een class die Gateways kan aanleveren. Indien leeg kunnen er geen datamappers zijn die met een gateway werken ipv een databank verbinding.
     */
    public function __construct( $gebruikerId , $databaseManager, $config , $sqlLogger = null, $gatewayRegistry = null)
    {
        $this->_gebruiker = null;
        $this->gebruikerId = $gebruikerId;
        if ( !array_key_exists( 'gebruikerClass', $config)) {
            throw new InvalidArgumentException ( 'De parameter config moet een array-sleutel gebruikerClass hebben.');
        }
        if ( !array_key_exists( 'dataMapperDirs', $config)) {
            throw new InvalidArgumentException ( 'De parameter config moet een array-sleutel dataMapperDirs hebben.');
        }
        $this->gebruikerClass = $config['gebruikerClass'];
        $this->_mapperRegistry = new KVDdom_MapperRegistry( new KVDdom_MapperFactory( $this , $config['dataMapperDirs'] ) );
        
        $this->_newObjects = new KVDdom_GenericIdentityMap();
        $this->_dirtyObjects = new KVDdom_GenericIdentityMap();
        $this->_removedObjects = new KVDdom_GenericIdentityMap();
        $this->_approvedObjects = new KVDdom_GenericIdentityMap();
        $this->_historyClearedObjects = new KVDdom_GenericIdentityMap();
        
        $this->_identityMap = new KVDdom_GenericIdentityMap();

        $this->_databaseManager = $databaseManager;

        $this->_gatewayRegistry = $gatewayRegistry;
        
        $this->commitVolgorde = array_key_exists( 'commitVolgorde' , $config ) ? $config['commitVolgorde'] : array( );

        $this->dataMappersConnections = array_key_exists( 'dataMappersConnections' , $config ) ? $config['dataMappersConnections'] : array( );

        if ( $sqlLogger != null && array_key_exists( 'logSql' , $config) && $config['logSql'] == true ) {
            $this->sqlLogger = $sqlLogger;
        } else {
            $this->sqlLogger = new KVDdom_SqlLogger( );
        }
    }

    /**
     * De eigenaar van de sessie.
     *
     * Is belangrijk omdat de DataMappers dit nodig hebben om vast te stellen wie de wijzigingen doorvoert zodat ze kunnen gelogd worden.
     * @return KVDdom_Gebruiker
     */
    public function getGebruiker()
    {
        if ( $this->_gebruiker === null ) {
            $gebruikerMapper = $this->getGebruikerMapper();
            $this->_gebruiker = $gebruikerMapper->findById ( $this->gebruikerId );
        }
        return $this->_gebruiker;
    }

    /**
     * @return KVDdom_GenericIdentityMap
     */
    public function getIdentityMap()
    {
        return $this->_identityMap;
    }

    /**
     * @param mixed $domainObject Ofwel een class-naam van een KVDdom_DomainObject, ofwel een KVDdom_DomainObject.
     * @return KVDdom_Datamapper Een datamapper voor het desbetreffende DomainObject.
     * @throws <b>InvalidArgumentException</b> - Indien de parameter $domainObject geen string of DomainObject is.
	 * @throws <b>KVDdom_DatabaseUnavailableException</b> - Indien de voor de dataMapper gespecifieerde connectie niet bestaat.
     */
    public function getMapper( $domainObject )
    {
        if ( is_string ( $domainObject ) ) {
            $teMappenClass = $domainObject;
        } else if ( $domainObject instanceof  KVDdom_DomainObject ) {
            $teMappenClass = $domainObject->getClass();
        } else {
            throw new InvalidArgumentException ( 'De parameter domainObject is geen DomainObject of geen naam van een DomainObject.' );
        }
        return $this->_mapperRegistry->getMapper( $teMappenClass );
    }

    /**
     * @return KVDdom_Datamapper Datamapper voor het DomainObject dat als gebruikersobject telt.
     * @throws <b>Exception</b> - Indien de mapper voor het gebruikersobject niet gevonden wordt.
     */
    public function getGebruikerMapper ( )
    {
        return $this->getMapper( $this->gebruikerClass );
    }
        

    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     */
    public function registerNew ( $domainObject )
    {
        $id = $domainObject->getId();
        $type = $domainObject->getClass();
        if ($id === null) {
            throw new Exception ('Een object moet een id hebben');
        }
        if ($this->_newObjects->getDomainObject($type, $id) != null) {
            throw new Exception ('Een object kan slechts eenmaal geregistreerd worden als nieuw.');
        }
        if ($this->_dirtyObjects->getDomainObject($type, $id) != null) {
            throw new Exception ('Een reeds gewijzigd object kan niet als nieuw gemarkeerd worden.');
        }
        if ($this->_removedObjects->getDomainObject($type, $id) != null) {
            throw new Exception ('Een reeds verwijderd object kan niet als nieuw geregistreerd worden.');
        }
        $this->_newObjects->addDomainObject ( $domainObject );
        $this->_identityMap->addDomainObject ( $domainObject );
    }
    
    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     */
    public function registerDirty ( $domainObject )
    {
        $id = $domainObject->getId();
        $type = $domainObject->getClass();
        if ($id === null) {
            throw new Exception ('Een object moet een id hebben');
        }
        if ($this->_removedObjects->getDomainObject($type, $id) != null) {
            throw new Exception ('Een reeds verwijderd object kan niet als dirty geregistreerd worden.');
        }
        if ($this->_identityMap->getDomainObject($type, $id) == null && $domainObject->isCurrentRecord( ) ) {
            throw new Exception ('Een object dat niet in de IdentityMap zit kan niet als dirty gemarkeerd worden tenzij het om een verwijderd object zou gaan dat terug geplaatst moet worden.');
        }
        if ( ($this->_newObjects->getDomainObject($type, $id) == null) && ($this->_dirtyObjects->getDomainObject($type, $id) == null) ) {
            $this->_dirtyObjects->addDomainObject ( $domainObject );
        }
    }
    
    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     */
    public function registerRemoved ( $domainObject )
    {
        $id = $domainObject->getId();
        $type = $domainObject->getClass();
        if ($id === null) {
            throw new Exception ('Een object moet een id hebben');
        }
        if ($this->_removedObjects->getDomainObject($type, $id) != null) {
            throw new Exception ('Een reeds verwijderd object kan niet als nieuw geregistreerd worden.');
        }
        if ($this->_identityMap->getDomainObject($type, $id) == null) {
            throw new Exception ('Een object dat niet in de IdentityMap zit kan niet als te verwijderen gemarkeerd worden.');
        }
        if ($this->_newObjects->removeDomainObject($type, $id) == true) {
            $this->_identityMap->removeDomainObject($type, $id);
            return;
        }
        $this->_dirtyObjects->removeDomainObject($type, $id);
        $this->_identityMap->removeDomainObject($type, $id);
        $this->_removedObjects->addDomainObject ( $domainObject );
    }
    
    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     */
    public function registerClean ( $domainObject )
    {
        $id = $domainObject->getId();
        $type = $domainObject->getClass();
        if ($id === null) {
            throw new Exception ('Een object moet een id hebben');
        }
        $this->_identityMap->addDomainObject ( $domainObject );
    }
    
    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     * @throws <b>LogicException</b> - Indien er geprobeerd wordt een object goed te keuren dat niet goedgekeurd mag worden.
     */
    public function registerApproved ( $domainObject )
    {
        if ( $domainObject->getId( ) == null ) {
            throw new Exception ( 'Een object moet een id hebben' );
        }
        if ( $this->_identityMap->getDomainObject( $domainObject->getClass( ), $domainObject->getId( ) ) == null ) {
            throw new LogicException ( 'Een object dat niet in de IdentityMap zit kan niet als goed te keuren gemarkeerd worden.');
        }
        if ( $this->_newObjects->getDomainObject( $domainObject->getClass( ) , $domainObject->getId( ) ) != null ) {
            throw new LogicException ( 'Een object dat pas als nieuw gemarkeerd werd kan niet goedgekeurd worden. Het object moet eerst opgeslagen worden.');
        }
        if ( $this->_dirtyObjects->getDomainObject( $domainObject->getClass( ) , $domainObject->getId( ) ) != null ) {
            throw new LogicException ( 'Een object dat gewijzigd werd kan niet goedgekeurd worden. Het object moet eerst opgeslagen worden.');
        }
        if ( $this->_removedObjects->getDomainObject( $domainObject->getClass( ) , $domainObject->getId( ) ) != null ) {
            throw new LogicException ( 'Een object dat verwijderd  werd kan niet goedgekeurd worden. Het object moet eerst opgeslagen worden.');
        }
        $this->_approvedObjects->addDomainObject( $domainObject );
    }

    public function registerHistoryCleared( $domainObject )
    {
        if ( $domainObject->getId( ) == null ) {
            throw new LogicException ( 'Een object waarvan de geschiedenis verwijderd moet worden moet een id hebben' );
        }
        $this->_historyClearedObjects->addDomainObject( $domainObject );
    }

    /**
     * @return array Een array met 5 keys ( 'insert' , 'update' , 'delete' , 'approved', 'historyCleared' )die het aantal affected records bevatten.
     */
    public function commit()
    {
        $affected = array();
        $affected['insert'] = $this->insertNew();
        $affected['update'] = $this->updateDirty();
        $affected['delete'] = $this->deleteRemoved();
        $affected['approved'] = $this->updateApproved( );
        $affected['historyCleared'] = $this->deleteHistoryCleared( );
        return $affected;
    }

    /**
     * @return integer Het aantal nieuwe records dat werd toegevoegd.
     */
    private function insertNew()
    {
        return $this->processIdentityMap( $this->_newObjects, 'insert' );
    }

    /**
     * @return integer Het aantal gewijzigde records.
     */
    private function updateDirty()
    {
        return $this->processIdentityMap( $this->_dirtyObjects, 'update' );
    }

    /**
     * Verwijder alle domainObjects die al Te Verwijderen gemarkeerd zijn uit de databank. 
     * Het verwijderen zal in de omgekeerde volgorde van de commitVolgorde gebeuren, dit zal waarschijnlijk de regels voor referentiele integriteit beter respecteren.
     * @return integer Het aantal verwijderde records.
     */
    private function deleteRemoved()
    {
        $commitVolgorde = array_reverse( $this->commitVolgorde );
        return $this->processIdentityMap( $this->_removedObjects , 'delete', $commitVolgorde );
    }

    /**
     * @return integer Het aantal goedgekeurde records.
     */
    private function updateApproved( )
    {
        return $this->processIdentityMap( $this->_approvedObjects , 'approve');
    }

    /**
     * @return integer Het aantal records waarvan de geschiedenis gecleared werd.
     */
    private function deleteHistoryCleared( )
    {
        return $this->processIdentityMap( $this->_historyClearedObjects , 'clearHistory');
    }

    /**
     * @param KVDdom_GenericIdentityMap De te verwerken IdentityMap.
     * @param string $mapperFunction De naam van de functie op de bijhorende mapper die moet aangeroepen worden.
     * @param array $commitVolgorde Een array die aangeeft in welk volgorde de mappers moeten aangesproken worden.
     * @return integer Het aantal verwerkte objecten.
     */
    private function processIdentityMap ( $identityMap, $mapperFunction , $commitVolgorde = null )
    {
        if ( $commitVolgorde === null ) {
            $commitVolgorde = $this->commitVolgorde;
        }
        $count = 0;
        foreach ( $commitVolgorde as $type ) {
            $objects = $identityMap->getDomainObjects( $type );
            if ( $objects !== null ) {
                $mapper = $this->_mapperRegistry->getMapper ( $type );
                foreach ( $objects as $object ) {
                    $mapper->$mapperFunction( $object );
                    $count++;
                    $identityMap->removeDomainObject( $type, $object->getId( ) );
                    $this->_identityMap->removeDomainObject( $type, $object->getId( ) );
                }
            }
        }
        if ( count( $identityMap ) > 0 ) {
            throw new KVDdom_IncompleteSessieCommitException ( 'Het verwerken van de sessie is niet compleet.', $mapperFunction, count( $identityMap ) );
        }
        return $count;
    }

    /**
     * Zoek de connectie die bij een bepaalde datamapper hoort.
     * 
     * @param string $dataMapper De naam van een dataMapper.
	 * @return Database Een database connectie.
	 * @throws <b>KVDdom_DatabaseUnavailableException</b> - Indien de voor de dataMapper gespecifieerde connectie niet bestaat.
	 */
	public function getDatabaseConnection ( $dataMapper )
	{
        try {
            $databaseName = ( array_key_exists( $dataMapper , $this->dataMappersConnections ) ) ? $this->dataMappersConnections[$dataMapper] : 'default';
            return $this->_databaseManager->getDatabase($databaseName)->getConnection();            
        } catch ( DatabaseException $e ) {
            throw new KVDdom_DatabaseUnavailableException ( 'De gevraagde databank is niet beschikbaar.' , $databaseName , $e);
        }
	}

    /**
     * @param string $gateway Naam van de gevraagde gateway.
     * @return KVDutil_Gateway Een gateway naar een externe service.
     * @throws <b>LogicException</b> - Indien er geen gatewayRegistry aanwezig is.
     */
    public function getGateway ( $gateway )
    {
        if ( $this->_gatewayRegistry == null ) {
            throw new LogicException ( 'Er is geen gatewayRegistry beschikbaar, dus kan deze actie niet uitgevoerd worden. Controleer de configuratie.');
        }
        return $this->_gatewayRegistry->getGateway( $gateway );
    }

    /**
     * @return KVDdom_SqlLogger
     */
    public function getSqlLogger ()
    {
        return $this->sqlLogger;
    }
}
?>
