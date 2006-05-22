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
 * @since 1.0.0
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
     * $config = array (
     *              'dataMapperDirs'     => array ( '/opt/datamappers/',
     *                                              '/usr/datamappers/'),
     *              'commitVolgorde'    => array (  1 => 'Adres',
     *                                              2 => 'Persoon',
     *                                              3 => 'Organisatie',
     *                                              4 => 'PersoonNaarOrganisatie',
     *                                              5 => 'Vondstmelding'),
     *              'dataMappersConnections'    => array (  'Vondstmelding' => 'CAI'),
     *              'gebruikerClass'            => 'OEIdo_GebGebruiker',
     *              'gatewayFactoryConfig'      => array ( 
     *                                              'KVDgis_Crab1Gateway' => array ( 'wsdl' => 'http://webservices.gisvlaanderen.be/wsdl',
     *                                                                               'username' => 'testUser',
     *                                                                               'password' => 'testpassword'),
     *                                              'KVDgis_Crab2Gateway' => array ( 'wsdl' => 'http://test.gisvlaanderen.be/wsdl',
     *                                                                               'username' => 'testUser',
     *                                                                               'password' => 'testPassword')
     *                                              )
     *                 );
     *      $dbm = new DatabaseManager ();                    
     *      $sessie = new KVDdom_Sessie ( 25 , $dbm, $config);                    
     * </code>
     * @param integer $gebruikerId
     * @param DatabaseManager $databaseManager Object uit het Agavi/Mojavi Framework. Een ander object met dezelfde interface kan ook werken.
     * @param array $config Array van configuratie-opties.
     */
    public function __construct( $gebruikerId , $databaseManager, $config )
    {
        $this->_gebruiker = null;
        $this->gebruikerId = $gebruikerId;
        if ( !array_key_exists( 'gebruikerClass', $config)) {
            throw new InvalidArgumentException ( 'De parameter config moet een array-sleutel gebruikerClass hebben.');
        }
        $this->gebruikerClass = $config['gebruikerClass'];
        $this->_mapperRegistry = new KVDdom_MapperRegistry( new KVDdom_MapperFactory( $this , $config['dataMapperDirs'] ) );
        
        $this->_newObjects = new KVDdom_GenericIdentityMap();
        $this->_dirtyObjects = new KVDdom_GenericIdentityMap();
        $this->_removedObjects = new KVDdom_GenericIdentityMap();
        
        $this->_identityMap = new KVDdom_GenericIdentityMap();

        $this->_databaseManager = $databaseManager;

        if ( !array_key_exists( 'gatewayFactoryConfig', $config)) {
            $this->_gatewayRegistry = null;
        } else {
            $factory = new KVDutil_GatewayFactory ( $config['gatewayFactoryConfig'] );
            $this->_gatewayRegistry = new KVDutil_GatewayRegistry ( $factory );
        }
        
        if (!array_key_exists('dataMappersConnections',$config)) {
            $config['dataMappersConnections'] = array();    
        }
        $this->initializeCommitVolgorde( $config['commitVolgorde'] );
        $this->initializeDataMappersConnections ( $config['dataMappersConnections']);
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
     * @throws <b>Exception</b> - Indien de parameter $domainObject geen string of DomainObject is.
     */
    public function getMapper( $domainObject )
    {
        if ( is_string ( $domainObject ) ) {
            $teMappenClass = $domainObject;
        } else if ( $domainObject instanceof  KVDdom_DomainObject ) {
            $teMappenClass = $domainObject->getClass();
        } else {
            throw new Exception ( 'Geen DomainObject of geen naam van een DomainObject.' );
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
            throw new Exception ('Een reeds verwijderd object kan niet als nieuw geregistreerd worden.');
        }
        if ($this->_identityMap->getDomainObject($type, $id) == null) {
            throw new Exception ('Een object dat niet in de IdentityMap zit kan niet als dirty gemarkeerd worden.');
        }
        if (($this->_newObjects->getDomainObject($type, $id) == null) && 
            ($this->_dirtyObjects->getDomainObject($type, $id) == null)) {
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
     * @return array Een array met 3 keys ( 'insert' , 'update' , 'delete' )die het aantal affected records bevatten.
     */
    public function commit()
    {
        $affected = array();
        $affected['insert'] = $this->insertNew();
        $affected['update'] = $this->updateDirty();
        $affected['delete'] = $this->deleteRemoved();
        return $affected;
    }

    /**
     * @return integer Het aantal nieuwe records dat werd toegevoegd.
     */
    private function insertNew()
    {
        $count = 0;
        foreach ($this->commitVolgorde as $type) {
            $newObjects = $this->_newObjects->getDomainObjects($type);
            if ( $newObjects !== null ) {
                $mapper = $this->_mapperRegistry->getMapper ( $type );
                foreach ( $newObjects as $new ) {
                    $mapper->insert($new);
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * @return integer Het aantal gewijzigde records.
     */
    private function updateDirty()
    {
        $count = 0;
        foreach ($this->commitVolgorde as $type) {
            $dirtyObjects = $this->_dirtyObjects->getDomainObjects($type);
            if ( $dirtyObjects !== null ) {
                $mapper = $this->_mapperRegistry->getMapper ( $type );
                foreach ( $dirtyObjects as $dirty ) {
                    $mapper->update($dirty);
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * @return integer Het aantal verwijderde records.
     */
    private function deleteRemoved()
    {
        $count=0;
        foreach ($this->commitVolgorde as $type) {
            $removedObjects = $this->_removedObjects->getDomainObjects($type);
            if ( $removedObjects !== null ) {
                $mapper = $this->_mapperRegistry->getMapper ( $type );
                foreach ( $removedObjects as $removed ) {
                    $mapper->delete($removed);
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Zoek de connectie die bij een bepaalde datamapper hoort.
     * 
     * @param string $dataMapper De naam van een dataMapper.
	 * @return Database Een database connectie.
	 * @throws <b>DatabaseException</b> - Indien de voor de dataMapper gespecifieerde connectie niet bestaat.
	 */
	public function getDatabaseConnection ( $dataMapper )
	{
        if (array_key_exists($dataMapper,$this->dataMappersConnections)) {
            return $this->_databaseManager->getDatabase($this->dataMappersConnections[$dataMapper])->getConnection();        
        } else {
            return $this->_databaseManager->getDatabase('default')->getConnection();            
        }
	}

    /**
     * Functie die er voor zorgt dat commits in de correcte volgorde gebeuren zodat er aan dependencies voldaan wordt.
     * @param array $commitVolgorde @see KVDdom_Sessie::__construct() voor een uitgebreide uitleg.
     */
    private function initializeCommitVolgorde ( $commitVolgorde )
    {
        $this->commitVolgorde = $commitVolgorde;
    }
    
    /**
     * Stel het array in dat bepaald met welke connectie een mapper werkt.
     * @param array $dataMappersConnections @see KVDdom_Sessie::__construct() voor een uitgebreide uitleg.
     */
    private function initializeDataMappersConnections ( $dataMappersConnections )
    {
        $this->dataMappersConnections = $dataMappersConnections;
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
}
?>
