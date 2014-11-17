<?php
/**
 * @package KVD.gis
 * @subpackage crab
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version $Id$
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDgis_Crab2Gateway
 *
 * Een Gateway om te connecteren met crab2. Heeft bijna identiek dezelfde API als {@link KVDgis_Crab1Gateway}. De Crab1 gateway wordt echter niet meer onderhouden.
 * Gelieve er rekening mee te houden dat alle strings die door Crab2 worden teruggegeven in UTF-8 zijn. 
 * Zorg er dus voor dat html pagina's die deze data weergeven ook in UTF-8 zijn of converteer de strings eerst naar latin1 via utf8_decode. 
 * Alles weergeven in UTF-8 geniet de voorkeur omdat er ander onaangename neveneffecten kunnen ontstaan bij het heen-en-weer encoderen/decoderen.
 * @package KVD.gis
 * @subpackage crab
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 29 sep 2006
 * @uses KVDgis_CrabCache
 * @uses KVDgis_NullCrabCache
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDgis_Crab2Gateway implements KVDutil_Gateway
{
    /**
     * Code die CRAB gebruikt voor het Brussels Hoofdstedelijk Gewest
     * @var integer
     */
    const GEWEST_BRUSSEL = 1;
    /**
     * Code die CRAB gebruikt voor het Vlaams Gewest
     * @var integer 
     */
    const GEWEST_VLAANDEREN = 2;
    /**
     * Code die CRAB gebruikt voor het Waals Gewest
     * @var integer 
     */
    const GEWEST_WALLONIE = 3;


    /**
     * Code om lijsten van gemeenten te sorten op hun CRAB id. 
     * @var integer
     */
    const GEM_SORT_ID = 1;
    /**
     * Code om lijsten van gemeenten te sorteren op hun gemeentenaam.
     * @var integer
     */
    const GEM_SORT_NAAM = 2;
    /**
     * Code om lijsten van gemeenten te sorteren op de taalcode van de gemeentenaam.
     * @var integer
     */
    const GEM_SORT_TCGN = 3;
    /**
     * 
     * Code om lijsten van gemeenten te sorteren op de taalcode van het eerste taalstelsel van de gemeente.
     * @var integer
     */
    const GEM_SORT_TC = 4;
    /**
     *  
     * Code om lijsten van gemeenten te sorteren op de taalcode van het tweede taalstelsel van de gemeente.
     * @var integer
     */
    const GEM_SORT_TCTT = 5;
    /**
     *  
     * Code om lijsten van gemeenten te sorteren op hun NIScode.
     * @var integer
     */
    const GEM_SORT_NIS = 6;

    /**
     * Code om lijsten van straten te sorteren op het CRAB id.
     * @var integer
     */
    const STRAAT_SORT_ID = 1;
    /**
     * Code om lijsten van straten te sorteren op de straatnaam in de eerste taal.
     * @var integer
     */
    const STRAAT_SORT_NAAM = 2;
    /**
     * Code om lijsten van straten te sorteren op de straatnaam in de tweede taal.
     * @var integer
     */
    const STRAAT_SORT_NAAMTT = 3;
    /**
     * Code om lijsten van straten te sorteren op de taalcode van de straatnaam.
     * @var integer
     */
    const STRAAT_SORT_TC = 4;
    /**
     * Code om lijsten van straten te sorteren op de taalcode van de straatnaam in de tweede taal.
     * @var integer
     */
    const STRAAT_SORT_TCTT = 5;
    /**
     * Code om lijsten van straten te sorteren op het straatlabel ( de straatnaam in de eerste taal zonder eventuele suffixen )
     * @var integer
     */
    const STRAAT_SORT_LABEL = 6;

    /**
     * Code om de lijsten met huisnummers te sorteren op het CRAB id.
     * @var integer
     */
    const HUISNR_SORT_ID = 1;
    /**
     * Code om de lijsten met huisnummers te sorteren op het huisnummer zelf.
     * @var integer
     */
    const HUISNR_SORT_NAAM = 2;

    /**
     * Code om de lijsten met wegobjecten te sorteren op de identificator ( Multinet straatcode ).
     * @var integer
     */
    const WEG_SORT_ID = 1;
    /**
     * Code om de lijsten met wegobjecten te sorteren op de aard van het wegobject.
     * @var integer 
     */
    const WEG_SORT_AARD = 2;

    /**
     * Code om de lijsten met terreinobjecten te sorteren op de identificator ( Kadastraal perceel ).
     * @var integer
     */
    const TERREIN_SORT_ID = 1;
    /**
     *  Code om de lijsten met terreinobjecten te sorteren op hun aard.
     *  @var integer
     */
    const TERREIN_SORT_AARD = 2;
    
    /**
     * @var SoapClient
     * @access private
     */
    private $_client = false;

    /**
     * @var KVDgis_CrabCache
     * @access private
     */
    private $_cache;

    /**
     * Initializeer de Crab2Gateway voor gebruik.
     *
     * Parameters is een associatieve array met de volgende sleutels: 
     * - wsdl ( url naar de wsdl file)
     * - username
     * - password. 
     * Deze parameters zijn altijd vereist. Tevens zijn er ook nog de volgende 
     * optionele parameters:
     * - proxy_host: host die als proxy server dienst doet
     * - proxy_port: poort op de proxy server
     * - safe_mode: indien true dan zal er steeds getest worden of de service 
     * bereikbaar is voor de soapclient in gang wordt gezet. Werkt beter voor 
     * bv. unit testing maar zal zeker trager zijn. Default is false.
     * - cache: De parmeter cache is optioneel. Indien ze weggelaten wordt zal 
     * er geen data gecached worden. 
     * De parameter is eveneens een array met de volgende sleutels:
     * - active ( boolean ): indien false zal er geen data gecached worden
     * - cacheDir ( string ): dir waarin de caches worden aangemaakt
     * - expirationTimes ( array ): een array met verschillende sleutels per te cachen functie. Er moet minstens een sleutel default aanwezig zijn.
     *   ExpirationTimes is optioneel. Indien niet aanwezig worden er caches aangemaakt die nooit verlopen tenzij door manuele tussenkomst.
     * bv.:
     * <code>
     *  $parameters = array (   'wsdl'      => 'http://webservices.gisvlaanderen.be/crab_1_0/ws_crab_NDS.asmx?WSDL',
     *                          'username'  => 'USERNAME',
     *                          'password'  => 'PASSWORD',
     *                          'proxy_host'=> 'my.proxy.org',
     *                          'proxy_port'=> 10000
     *                          'cache'     => array (  'active'    => true,
     *                                                  'cacheDir'  => '/tmp/',
     *                                                  'expirationTimes' => array (    'default' => false,
     *                                                                                  'listStraatnamenByGemeenteId' => 3600
     *                                                                             )
     *                                                )
     *                      )
     * </code>
     * @param array $parameters
     * @throws <b>IllegalArgumentException</b> Indien er foute of ontbrekende parameters zijn.
     * @throws <b>KVDutil_GatewayUnavailableException</b> Indien de externe bron niet beschikbaar is.
     */
    public function __construct ( $parameters = array( ) )
    {
        if ( !isset( $parameters['wsdl'] ) ) {
            throw new InvalidArgumentException ( 'De array parameters moet een sleutel wsdl bevatten!' );
        }
    
        $soap_options = array ( 'exceptions'            => 1,
                                'features'              => SOAP_SINGLE_ELEMENT_ARRAYS,
                                'trace'                 => 0,
                                'connection_timeout'    => 5 );
        if ( isset( $parameters['proxy_host'] ) ) {
            $soap_options['proxy_host'] = $parameters['proxy_host'];
        }
        if ( isset( $parameters['proxy_port'] ) ) {
            $soap_options['proxy_port'] = $parameters['proxy_port'];
        }
        if ( isset( $parameters['safe_mode'] ) && $parameters['safe_mode'] === true ) {
            $cOps = array( 
                'http' => array ( 
                    'method' => 'GET'
                )
            );
            if ( isset( $parameters['proxy_host'] ) ) {
                $proxy = $parameters['proxy_host'];
                if ( isset ( $parameters['proxy_port'] ) ) {
                    $proxy .= ':' . $parameters['proxy_port'];
                }
                $cOps['http']['proxy'] = urlencode( $proxy );
                $cOps['http']['request_fulluri'] = true;
            }
            $streamContext = stream_context_create( $cOps);
            $res = @file_get_contents( $parameters['wsdl'], false, $streamContext ); 
            if ( !$res ) {
                throw new KVDutil_GatewayUnavailableException ( 
                    'De Crab2Gateway kan geen verbinding maken met de Crab webservice. De WSDL file is niet beschikbaar.' , 
                    __CLASS__ );
            }
        }

        $this->_client = new SoapClient ( $parameters['wsdl'] , $soap_options);

        if ( !isset( $parameters['cache'] ) ) {
            $this->_cache = new KVDgis_NullCrabCache( );
        } else {
            if ( !isset ( $parameters['cache']['active'] ) || $parameters['cache']['active'] == false ) {
                $this->_cache = new KVDgis_NullCrabCache( );
            } else {
                if ( !isset( $parameters['cache']['cacheDir'] ) ) {
                    throw new InvalidArgumentException ( 'Kan geen cache aanmaken omdat de parameter cacheDir niet aanwezig is!');
                }
                if ( !isset( $parameters['cache']['expirationTimes']) ) {
                    $parameters['cache']['expirationTimes']['default'] = false;
                }
                $this->_cache = new KVDgis_CrabCache( $parameters['cache']['cacheDir'], $parameters['cache']['expirationTimes']);
            }
        }
        
    }

    /**
     * getCacheName 
     * 
     * @param string $function Naam van de functie die moet gecached worden.
     * @return string 
     */
    private function getCacheName( $function )
    {
        return 'CRAB2_' . $function;
    }

    /**
     *
     * @param integer $gewestId Zie de KVDgis_Crab2Gateway::GEWEST constanten.
     * @param integer $sorteerVeld Zie de KVDgis_Crab2Gateway::GEM_SORT constanten.
     * @return array Associatieve array met de volgende sleutels:;
     * <ul>
     *  <li>gemeenteNaam: Naam van de gemeente.</li>
     *  <li>gemeenteId: Het Crab Id van de gemeente. Dit is niet gelijk aan de NIS-code.</li>
     *  <li>taalCode: De eerste taal in de gemeente.</li>
     *  <li>taalCodeGemeenteNaam: De taalvode van de gemeetenaam.</li>
     * </ul>
     * @throws IllegalArgumentException Indien er verkeerder parameters worden opgegeven.
     * @throws RuntimeException Indien de lijst niet geladen kan worden
     */
    public function listGemeentenByGewestId( $gewestId = self::GEWEST_VLAANDEREN,  $sorteerVeld = self::GEM_SORT_ID ) {
        $params = new stdClass( );
        if ( $gewestId < 1 || $gewestId > 3 ) {
            throw new IllegalArgumentException ( "De parameter gewestId van de functie listGemeentenByGewestId moet tussen 1 en 3 liggen.");
        }
        if ( $sorteerVeld < 1 || $sorteerVeld > 6 ) {
            throw new IllegalArgumentException ( "De parameter sorteerVeld van de functie listGemeentenByGewestId moet tussen 1 en 6 liggen.");
        }
        $functionParameters = func_get_args( );
        $result = $this->_cache->cacheGet ( $this->getCacheName( __FUNCTION__ ) , $functionParameters );
        if ( $result != false ) {
            return unserialize( $result );
        }
        $params->GewestId = $gewestId;
        $params->SorteerVeld = $sorteerVeld; 
        $paramsWrapper = new SoapParam ( $params , "ListGemeentenByGewestId" );
        try {
            $result = $this->_client->ListGemeentenByGewestId( $paramsWrapper );
            $gemeenteItems = $result->ListGemeentenByGewestIdResult->GemeenteItem;
        } catch ( SoapFault $e ) {
            throw new RuntimeException ( 'Kon de lijst met gemeentes niet laden.' );
        }
        
        $gemeenten = array( );
        foreach ( $gemeenteItems as $gemeente) {
            $gemeenteArray = array( );
            $gemeenteArray['gemeenteNaam'] = ( string ) $gemeente->GemeenteNaam;
            $gemeenteArray['gemeenteId'] = ( int ) $gemeente->GemeenteId;
            $gemeenteArray['taalCode'] = ( string ) $gemeente->TaalCode;
            $gemeenteArray['taalCodeGemeenteNaam'] = ( string ) $gemeente->TaalCodeGemeenteNaam;
            $gemeenten[] = $gemeenteArray;
        }
        $this->_cache->cachePut ( $this->getCacheName ( __FUNCTION__ ), $functionParameters , serialize( $gemeenten) );
        return $gemeenten;
    }

    /**
     * @param stdClass $gemeenteObject.
     * @return array Associatieve array met de volgende sleutels:;
     * <ul>
     *  <li>gewestId: Het nummer van het gewest waarin de straat ligt.</li>
     *  <li>gemeenteNaam: Naam van de gemeente.</li>
     *  <li>gemeenteId: Het Crab Id van de gemeente. Dit is niet gelijk aan de NIS-code.</li>
     *  <li>nisGemeenteCode: De NIS-code voor de gemeente.</li>
     *  <li>taalCode: De eerste taal in de gemeente.</li>
     *  <li>taalCodeGemeenteNaam: De taalvode van de gemeetenaam.</li>
     *  <li>centerX: De x-coordinaat van de centroide van de gemeente.</li>
     *  <li>centerY: De y-coordinaat van de centroide van de gemeente.</li>
     * </ul>
     * @throws InvalidArgumentException Indien er een verkeerde parameter wordt doorgegeven.
     */
    private function loadGemeenteArray ( $gemeenteObject )
    {
        if ( !( is_object ( $gemeenteObject ) ) ) {
            throw new InvalidArgumentException ( 'Kan enkel maar een gemeente laden op basis van een object.');
        }
        $gemeente = array( );
        $gemeente['gewestId'] = ( string ) $gemeenteObject->GewestId;
        $gemeente['gemeenteNaam'] = ( string ) $gemeenteObject->GemeenteNaam;
        $gemeente['gemeenteId'] = ( int ) $gemeenteObject->GemeenteId;
        $gemeente['nisGemeenteCode'] = ( int ) $gemeenteObject->NisGemeenteCode;
        $gemeente['taalCode'] = ( string ) $gemeenteObject->TaalCode;
        $gemeente['taalCodeGemeenteNaam'] = ( string ) $gemeenteObject->TaalCodeGemeenteNaam;
        $gemeente['centerX'] = ( float ) $gemeenteObject->CenterX;
        $gemeente['centerY'] = ( float ) $gemeenteObject->CenterY;
        return $gemeente;
    }
    
    /**
     * Vraag een gemeente op op basis van zijn crab Id.
     * 
     * @param integer $gemeenteId De crab Id van de gemeente.
     * @return array Associatieve array met de volgende sleutels:
     * <ul>
     *  <li>gewestId: Het nummer van het gewest waarin de straat ligt.</li>
     *  <li>gemeenteNaam: Naam van de gemeente.</li>
     *  <li>gemeenteId: Het Crab Id van de gemeente. Dit is niet gelijk aan de NIS-code.</li>
     *  <li>nisGemeenteCode: De NIS-code voor de gemeente.</li>
     *  <li>taalCode: De eerste taal in de gemeente.</li>
     *  <li>taalCodeGemeenteNaam: De taalvode van de gemeetenaam.</li>
     *  <li>centerX: De x-coordinaat van de centroide van de gemeente.</li>
     *  <li>centerY: De y-coordinaat van de centroide van de gemeente.</li>
     * </ul>
     * @throws RuntimeException Indien de gemeente niet kon geladen worden.
     */
    public function getGemeenteByGemeenteId( $gemeenteId )
    {
        $params = new stdClass( );
        $params->GemeenteId = $gemeenteId;
        $paramsWrapper = new SoapParam ( $params , "GetGemeenteByGemeenteId" );
        try {
            $result = $this->_client->GetGemeenteByGemeenteId( $paramsWrapper );
            return $this->loadGemeenteArray ( $result->GetGemeenteByGemeenteIdResult );
        } catch ( Exception $e ) {
            throw new RuntimeException ( "Kon de gemeente met gemeenteId $gemeenteId niet laden wegens: " . $e->getMessage( ) );
        }
    } 

    /**
     * Vraag een gemeente op op basis van zijn naam.
     * 
     * @param string $gemeenteNaam      
     * @param integer $gewestId Zie de KVDgis_Crab2Gateway::GEWEST_ constanten 
     * @return array Associatieve array met de volgende sleutels:
     * <ul>
     *  <li>gewestId: Het nummer van het gewest waarin de straat ligt.</li>
     *  <li>gemeenteNaam: Naam van de gemeente.</li>
     *  <li>gemeenteId: Het Crab Id van de gemeente. Dit is niet gelijk aan de NIS-code.</li>
     *  <li>nisGemeenteCode: De NIS-code voor de gemeente.</li>
     *  <li>taalCode: De eerste taal in de gemeente.</li>
     *  <li>taalCodeGemeenteNaam: De taalvode van de gemeetenaam.</li>
     *  <li>centerX: De x-coordinaat van de centroide van de gemeente.</li>
     *  <li>centerY: De y-coordinaat van de centroide van de gemeente.</li>
     * </ul>
     * @throws RuntimeException Indien de gemeente niet kon geladen worden.
     */
    public function getGemeenteByGemeenteNaam( $gemeenteNaam,  $gewestId = self::GEWEST_VLAANDEREN ) 
    {
        $params = new stdClass( );
        $params->GemeenteNaam = $gemeenteNaam;
        $params->GewestId = $gewestId;
        $paramsWrapper = new SoapParam ( $params , "GetGemeenteByGemeenteNaam" );
        try {
            $result = $this->_client->GetGemeenteByGemeenteNaam( $paramsWrapper );
            return $this->loadGemeenteArray ( $result->GetGemeenteByGemeenteNaamResult );
        } catch ( Exception $e ) {
            throw new RuntimeException ( "Kon de gemeente met gemeenteNaam $gemeenteNaam niet laden wegens: " . $e->getMessage( ) );
        }
    }

    /**
     * Vraag een gemeente op op basis van de NIS-code.
     * 
     * @param integer $nisGemeenteCode      
     * @return array Associatieve array met de volgende sleutels:
     * <ul>
     *  <li>gewestId: Het nummer van het gewest waarin de straat ligt.</li>
     *  <li>gemeenteNaam: Naam van de gemeente.</li>
     *  <li>gemeenteId: Het Crab Id van de gemeente. Dit is niet gelijk aan de NIS-code.</li>
     *  <li>nisGemeenteCode: De NIS-code voor de gemeente.</li>
     *  <li>taalCode: De eerste taal in de gemeente.</li>
     *  <li>taalCodeGemeenteNaam: De taalvode van de gemeetenaam.</li>
     *  <li>centerX: De x-coordinaat van de centroide van de gemeente.</li>
     *  <li>centerY: De y-coordinaat van de centroide van de gemeente.</li>
     * </ul>
     * @throws RuntimeException Indien de gemeente niet kon geladen worden.
     */
    public function getGemeenteByNISGemeenteCode( $nisGemeenteCode ) 
    {
        $params = new stdClass( );
        $params->NISGemeenteCode = $nisGemeenteCode;
        $paramsWrapper = new SoapParam ( $params , "GetGemeenteByNISGemeenteCode" );
        try {
            $result = $this->_client->GetGemeenteByNISGemeenteCode( $paramsWrapper );
            return $this->loadGemeenteArray ( $result->GetGemeenteByNISGemeenteCodeResult );
        } catch ( Exception $e ) {
            throw new RuntimeException ( "Kon de gemeente met nisGemeenteCode $nisGemeenteCode niet laden wegens: " . $e->getMessage( ) );
        }
    } 

    /**
     *
     * @param integer $gemeenteId      
     * @param integer $sorteerVeld Zie de KVDgis_Crab2Gateway::STRAAT_SORT_ constanten.
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li>straatnaam: Naam van de straat</li>
     *  <li>straatnaamId: Het Id van de straatnaam binnen crab.</li>
     *  <li>straatnaamLabel: Een label voor de straat ( straatnaam zonder eventuele suffixen ).</li>
     * </ul>
     * @throws InvalidArgumentException Indien er een ongeldig sorteerveld wordt opgegeven.
     * @throws RuntimeException Indien de straten niet geladen kunnen worden, meestal door een SOAP probleem.
     */
    public function listStraatnamenByGemeenteId( $gemeenteId,  $sorteerVeld = self::STRAAT_SORT_NAAM ) 
    {
        if ( $sorteerVeld < 1 || $sorteerVeld > 6  ) {
            throw new InvalidArgumentException ( "De parameter sorteerVeld van de functie listStraatnamenByGemeenteId moet tussen 1 en 6 liggen!");
        }
        $functionParameters = func_get_args( );
        $result = $this->_cache->cacheGet ( $this->getCacheName( __FUNCTION__ ), $functionParameters );
        if ( $result != false ) {
            return unserialize( $result );
        }
        $params = new StdClass( );
        $params->GemeenteId = (int) $gemeenteId;
        $params->SorteerVeld = ( int ) $sorteerVeld;
        $paramsWrapper = new SoapParam ( $params , "listStraatnamenByGemeenteId");
        try {
            $result = $this->_client->listStraatnamenByGemeenteId( $paramsWrapper );
            $straatItems = $result->ListStraatnamenByGemeenteIdResult->StraatnaamItem;
        } catch ( Exception $e ) {
            throw new RuntimeException ( 'Kon de lijst met straten niet laden wegens: ' . $e->getMessage( ) );
        }

        $straatnamen = array( );
        foreach ( $straatItems as $straatnaam ) {
            $straatnaamArray = array( );
            $straatnaamArray['straatnaam'] = $straatnaam->Straatnaam;
            $straatnaamArray['straatnaamId'] = ( int ) $straatnaam->StraatnaamId;
            $straatnaamArray['straatnaamLabel'] = $straatnaam->StraatnaamLabel;
            $straatnamen[] = $straatnaamArray;
        }
        $this->_cache->cachePut ( $this->getCacheName( __FUNCTION__ ), $functionParameters , serialize( $straatnamen ));
        return $straatnamen;
    }

    /**
     * @param stdClass $straatnaamObject
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li> straatnaam: Naam van de straat</li>
     *  <li> straatnaamId: Id van de straatnaam binnen Crab</li>
     *  <li> gemeenteId: Id (geen NIS-code) van de gemeente waarin de straat ligt</li>
     *  <li> taalCode: Taal waarin de straatnaam is opgesteld</li>
     *  <li> straatnaamLabel: Een label voor de straatnaam, te gebruiken in keuzelijsten</li>
     * </ul>
     * @throws InvalidArgumentException Indien er een verkeerde parameter wordt doorgegeven
     */
    private function loadStraatnaamArray ( $straatnaamObject )
    {
        
        if ( !( is_object( $straatnaamObject ) ) ) {
            throw new InvalidArgumentException ( 'Kan enkel maar een straat laden op basis van een object!');
        }
        $straatnaam = array( );
        $straatnaam['straatnaam'] = $straatnaamObject->Straatnaam;
        $straatnaam['straatnaamId'] = ( int ) $straatnaamObject->StraatnaamId;
        $straatnaam['gemeenteId'] = $straatnaamObject->GemeenteId;
        $straatnaam['taalCode'] = $straatnaamObject->TaalCode;
        $straatnaam['straatnaamLabel'] = $straatnaamObject->StraatnaamLabel;
        return $straatnaam;
    }

    /**
     *
     * @param integer $straatnaamId      
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li> straatnaam: Naam van de straat</li>
     *  <li> straatnaamId: Id van de straatnaam binnen Crab</li>
     *  <li> gemeenteId: Id (geen NIS-code) van de gemeente waarin de straat ligt</li>
     *  <li> taalCode: Taal waarin de straatnaam is opgesteld</li>
     *  <li> straatnaamLabel: Een label voor de straatnaam, te gebruiken in keuzelijsten</li>
     * </ul>
     * @access public
     * @throws RuntimeException Indien de straatnaam niet kon geladen worden
     */
    public function getStraatnaamByStraatnaamId( $straatnaamId ) 
    {
        $params = new stdClass( );
        $params->StraatnaamId = ( int ) $straatnaamId;
        $paramsWrapper = new SoapParam ( $params , 'GetStraatnaamByStraatnaamId' );
        try {
            $result = $this->_client->GetStraatnaamByStraatnaamId( $paramsWrapper );
            return $this->loadStraatnaamArray ( $result->GetStraatnaamByStraatnaamIdResult );
        } catch ( Exception $e ) {
            throw new RuntimeException ( "Kon de straatnaam met straatnaamId $straatnaamId niet laden wegens: " . $e->getMessage( ) );
        }
    }

    /**
     *
     * @param string $straatnaam      
     * @param integer $gemeenteId Het CRAB id van de gemeente ( niet de NIS code ).
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li> straatnaam: Naam van de straat</li>
     *  <li> straatnaamId: Id van de straatnaam binnen Crab</li>
     *  <li> gemeenteId: Id (geen NIS-code) van de gemeente waarin de straat ligt</li>
     *  <li> taalCode: Taal waarin de straatnaam is opgesteld</li>
     *  <li> straatnaamLabel: Een label voor de straatnaam, te gebruiken in keuzelijsten</li>
     * </ul>
     * @throws RuntimeException Indien de straatnaam niet kon geladen worden 
     * @access public
     */
    public function getStraatnaamByStraatnaam( $straatnaam,  $gemeenteId ) 
    {
        $params = new stdClass( );
        $params->Straatnaam = ( string ) $straatnaam;
        $params->GemeenteId = ( int ) $gemeenteId;
        $paramsWrapper = new SoapParam ( $params , 'GetStraatnaamByStraatnaam' );
        try {
            $result = $this->_client->GetStraatnaamByStraatnaam( $paramsWrapper );
            return $this->loadStraatnaamArray ( $result->GetStraatnaamByStraatnaamResult );
        } catch ( Exception $e ) {
            throw new RuntimeException ( "Kon de straatnaam met straatnaam $straatnaam in gemeente $gemeenteId niet wegens: " . $e->getMessage( ) );
        }
    } 

    /**
     *
     * @param integer straatnaamId      
     * @param integer sorteerVeld Zie de HUISNR_SORT_ constanten.
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li>huisnummer: Het huisnummer in tesktvorm.</li>
     *  <li>huisnummerId: Een uniek nummer voor het huisnummer.</li>
     * </ul>
     * @throws InvalidArgumentException Indien er een foute parameter wordt doorgegeven.
     * @throws RuntimeException Indien de lijst met huisnummers niet geladen kan worden.
     */
    public function listHuisnummersByStraatnaamId( $straatnaamId,  $sorteerVeld = self::HUISNR_SORT_NAAM ) {
        if ( $sorteerVeld < 1 ||$sorteerVeld >  2 ) {
            throw new InvalidArgumentException ( "De parameter sorteerVeld van de functie listHuisnummersByStraatnaamId moet tussen 1 en 2 liggen!");
        }
        $functionParameters = func_get_args( );
        $result = $this->_cache->cacheGet ( $this->getCacheName( __FUNCTION__ ) , $functionParameters );
        if ( $result != false ) {
            return unserialize( $result );
        }
        $params = new StdClass( );
        $params->StraatnaamId = ( int ) $straatnaamId;
        $params->SorteerVeld = ( int ) $sorteerVeld;
        $paramsWrapper = new SoapParam ( $params , "ListHuisnummersByStraatnaamId" );
        try {
            $result = $this->_client->ListHuisnummersByStraatnaamId( $paramsWrapper );
        } catch ( Exception $e ) {
            throw new RuntimeException ( 'Kon de lijst met huisnummers niet laden wegens: ' . $e->getMessage( ) );
        }

        $huisnummers = array( );
        if ( isset( $result->ListHuisnummersByStraatnaamIdResult->HuisnummerItem ) ) {
            foreach ( $result->ListHuisnummersByStraatnaamIdResult->HuisnummerItem as $huisnummer ) {
                $huisnummerArray = array( );
                $huisnummerArray['huisnummer'] = $huisnummer->Huisnummer;
                $huisnummerArray['huisnummerId'] = ( int ) $huisnummer->HuisnummerId;
                $huisnummers[] = $huisnummerArray;
            }
        }
        $this->_cache->cachePut ( $this->getCacheName( __FUNCTION__ ) , $functionParameters , serialize( $huisnummers ) );
        return $huisnummers;
    }

    /**
     * @param stdClass $huisnummerObject
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li>huisnummer: Een string voorstelling van het huisnummer ( kan bv. bis bevatten)</li>
     *  <li>huisnummerId: Het id van het huisnummer in Crab.</li>
     *  <li>straatnaamId: Het id van de straat waarin het huisnummer ligt.</li>
     * </ul>
     * @throws InvalidArgumentException Indien er een verkeerde parameter wordt doorgegeven
     */
    private function loadHuisnummerArray ( $huisnummerObject )
    {
        if ( !( is_object( $huisnummerObject ) ) ) {
            throw new InvalidArgumentException ( 'Kan alleen maar een huisnummer laden op basis van een object!');
        }
        $huisnummer = array( );
        $huisnummer['huisnummer'] = $huisnummerObject->Huisnummer;
        $huisnummer['huisnummerId'] = ( int ) $huisnummerObject->HuisnummerId;
        $huisnummer['straatnaamId'] = ( int ) $huisnummerObject->StraatnaamId;
        return $huisnummer;
    }

    /**
     *
     * @param integer huisnummerId
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li>huisnummer: Een string voorstelling van het huisnummer ( kan bv. bis bevatten)</li>
     *  <li>huisnummerId: Het id van het huisnummer in Crab.</li>
     *  <li>straatnaamId: Het id van de straat waarin het huisnummer ligt.</li>
     * </ul>
     * @throws RuntimeException Indien het huisnummer niet geladen kon worden.
     */
    public function getHuisnummerByHuisnummerId( $huisnummerId ) 
    {
        $params = new stdClass( );
        $params->HuisnummerId = $huisnummerId;
        $paramsWrapper = new SoapParam ( $params , "GetHuisnummerByHuisnummerId" );
        try {
            $result = $this->_client->GetHuisnummerByHuisnummerId( $paramsWrapper );
            return $this->loadHuisnummerArray( $result->GetHuisnummerByHuisnummerIdResult );
        } catch ( Exception $e ) {
            throw new RuntimeException ( "Kon het huisnummer met huisnummerId $huisnummerId niet laden wegens: " . $e->getMessage( ) );
        }
    } 
    
    /**
     *
     * @param string huisnummer
     * @param integer straatnaamId
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li>huisnummer: Een string voorstelling van het huisnummer ( kan bv. bis bevatten)</li>
     *  <li>huisnummerId: Het id van het huisnummer in Crab.</li>
     *  <li>straatnaamId: Het id van de straat waarin het huisnummer ligt.</li>
     * </ul>
     * @throws RuntimeException Indien het huisnummer niet geladen kon worden.
     */
    public function getHuisnummerByHuisnummer( $huisnummer,  $straatnaamId ) 
    {
        $params = new stdClass( );
        $params->Huisnummer = $huisnummer;
        $params->StraatnaamId = ( int ) $straatnaamId;
        $paramsWrapper = new SoapParam ( $params , "GetHuisnummerByHuisnummer" );
        try {
            $result = $this->_client->GetHuisnummerByHuisnummer( $paramsWrapper );
            return $this->loadHuisnummerArray( $result->GetHuisnummerByHuisnummerResult );
        } catch ( Exception $e ) {
            throw new RuntimeException ( "Kon het huisnummer met huisnummer $huisnummer in de straat $straatnaamId niet laden wegens: " . $e->getMessage( ) );
        }
    }

    /**
     * @param integer $huisnummerId
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li>postkantonCode: De postcode van dit huisnummer.</li>
     * </ul>
     * @throws RuntimeException Indien het postkanton niet geladen kon worden.
     */
    public function getPostkantonByHuisnummerId ( $huisnummerId )
    {
        $params = new stdClass( );
        $params->HuisnummerId = ( int ) $huisnummerId;
        $paramsWrapper = new SoapParam ( $params , "GetPostkantonByHuisnummerId");
        try {
            $result = $this->_client->GetPostkantonByHuisnummerId ( $paramsWrapper );
        } catch ( Exception $e ) {
            throw new RuntimeException ( "Kon het postkanton van het huisnummer met huisnummerId $huisnummerId niet laden wegens: " . $e->getMessage( ) );
        }
        return array ( 'postkantonCode' => $result->GetPostkantonByHuisnummerIdResult->PostkantonCode );
    }

    /**
     * @param integer straatnaamId
     * @param integer sorteerVeld      
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li>identificatorWegobject: Een code die het wegobject voorstelt, komt uit Multinet.</li>
     *  <li>aardWegobject: Het soort wegobject</li>
     * </ul>
     * @throws RuntimeException Indien de lijst met wegobjecten niet geladen kan worden.
     * @throws InvalidArgumentException Indien er op een ongeldig sorteerVeld gesorteerd wordt.
     */
    public function listWegobjectenByStraatnaamId( $straatnaamId,  $sorteerVeld = self::WEG_SORT_ID ) 
    {
        if ( $sorteerVeld < 1 || $sorteerVeld > 2 ) {
            throw new InvalidArgumentException ( "De parameter sorteerVeld van de functie listWegobjectenByStraatnaamId moet tussen 1 en 2 liggen!");
        }
        $params = new StdClass( );
        $params->StraatnaamId = $straatnaamId;
        $params->SorteerVeld = (int) $sorteerVeld;
        $paramsWrapper = new SoapParam ( $params , "ListWegobjectenByStraatnaamId");
        try {
            $result = $this->_client->ListWegobjectenByStraatnaamId ( $paramsWrapper );
        } catch ( Exception $e ) {
            throw new RuntimeException ( 'Kon de lijst met wegobjecten niet laden wegens: ' . $e->getMessage( ) );
        }

        $wegobjecten = array( );
        if ( isset( $result->ListWegobjectenByStraatnaamIdResult->WegobjectItem ) ) {
            foreach ( $result->ListWegobjectenByStraatnaamIdResult->WegobjectItem as $wegobject ) {
                $wegobjectArray = array( );
                $wegobjectArray['identificatorWegobject'] = $wegobject->IdentificatorWegobject;
                $wegobjectArray['aardWegobject'] = ( int ) $wegobject->AardWegobject;
                $wegobjecten[] = $wegobjectArray;
            }
        }
        return $wegobjecten;
    }

    /**
     * Geef alle terreinobjecten die horen tot een huisnummer.
     *
     * Opgelet! In tegenstelling tot de Crab1Gateway geeft deze functie alleen maar de identificatorTerreinobject en de aardTerreinobjectCode terug.
     * <code>
     *  //Crab1
     *  $terreinObjecten = $crab->listTerreinobjectenByHuisnummerId ( 1,1);
     *  foreach ( $terreinObjecten as $terreinObject) {
     *      echo "X: " . $terreinObject['centerX'] . " ,Y: " . $terreinObject['centerY'];
     *  }
     *  //Crab2
     *  $terreinObjecten = $crab->listTerreinobjectenByHuisnummerId ( 1,1 );
     *  foreach ( $terreinObjecten as $terreinObject) {
     *      $infoTerreinObject = $crab->getTerreinobjectByIdentificatorTerreinobject ( $terreinObject['identificatorTerreinobject']);
     *      echo "X: " . $infoTerreinObject['centerX'] . " ,Y: " . $infoTerreinObject['centerY'];
     *  }
     * </code>
     * @param integer huisnummerId      
     * @param integer sorteerVeld Zie de KVDgis_Crab2Gateway::TERREIN_SORT_ constanten
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li>identificatorTerreinobject: De identificator van het terreinobject.</li>
     *  <li>aardTerreinobjectCode: De aard van het terreinobject.</li>
     * </ul>
     * @throws InvalidArgumentException Indien er op een ongeldig sorteerVeld wordt gesorteerd.
     */
    public function listTerreinobjectenByHuisnummerId( $huisnummerId,  $sorteerVeld = self::TERREIN_SORT_ID ) 
    {
        if ( $sorteerVeld < 1 || $sorteerVeld > 2 ) {
            throw new InvalidArgumentException ( "De parameter sorteerVeld van de functie listTerreinobjectenByHuisnummerId moet tussen 1 en 2 liggen!");
        }
        $params = new StdClass( );
        $functionParameters = func_get_args( );
        $result = $this->_cache->cacheGet ( $this->getCacheName( __FUNCTION__ ) , $functionParameters );
        if ( $result != false ) {
            return unserialize( $result );
        }
        $params = new StdClass( );
        $params->HuisnummerId = (int) $huisnummerId;
        $params->SorteerVeld = ( int ) $sorteerVeld;
        $paramsWrapper = new SoapParam ( $params , "ListTerreinObjectenByHuisnummerId" );
        try {
            $result = $this->_client->ListTerreinobjectenByHuisnummerId( $paramsWrapper );
        } catch ( Exception $e ) {
            throw new RuntimeException ( 'Kan de lijst met terreinobjecten niet laden wegens: ' . $e->getMessage( ) );
        }
        
        $terreinobjecten = array( );
        if ( isset( $result->ListTerreinobjectenByHuisnummerIdResult->TerreinobjectItem ) ) {
           foreach ( $result->ListTerreinobjectenByHuisnummerIdResult->TerreinobjectItem as $terreinobject ) {
                $terreinobjectArray = array( );
                $terreinobjectArray['identificatorTerreinobject'] = $terreinobject->IdentificatorTerreinobject;
                $terreinobjectArray['aardTerreinobjectCode'] = ( int ) $terreinobject->AardTerreinobject;
                $terreinobjecten[] = $terreinobjectArray;
            }
        }
        $this->_cache->cachePut ( $this->getCacheName ( __FUNCTION__ ) , $functionParameters , serialize( $terreinobjecten ) );
        return $terreinobjecten;
    } 

    /**
     * @param string $identificatorTerreinobject Dit komt overeen met het kadastraal perceel.
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li>identificatorTerreinobject: De identificator van het terreinobject.</li>
     *  <li>aardTerreinobjectCode: De aard van het terreinobject.</li>
     *  <li>centerX: Het x-coordinaat van het centrum van het terreinbobject.</li>
     *  <li>centerY: Het y-coordinaat van het centrum van het terreinobject.</li>
     * </ul>
     * @since 02 okt 2006
     * @throws RuntimeException Indien het terreinobject niet geladen kan worden.
     * @throws InvalidArgumentException Indien er een ongeldige identificatorTerreinobject wordt opgegeven ( bv. null).
     */
    public function getTerreinobjectByIdentificatorTerreinobject( $identificatorTerreinobject )
    {
        if ( is_null( $identificatorTerreinobject ) ) {
            throw new InvalidArgumentException ( 'De identificatorTerreinobject mag niet null zijn.' );
        }
        $params = new stdClass( );
        $params->IdentificatorTerreinobject = ( string ) $identificatorTerreinobject;
        $paramsWrapper = new SoapParam ( $params , "GetTerreinobjectByIdentificatorTerreinobject" );
        try {
            $result = $this->_client->GetTerreinobjectByIdentificatorTerreinobject ( $paramsWrapper );
            return $this->loadTerreinArray( $result->GetTerreinobjectByIdentificatorTerreinobjectResult );
        } catch ( Exception $e ) {
            throw new RuntimeException ( "Kon het Terreinobject met id $identificatorTerreinobject niet laden wegens: " . $e->getMessage( ) );
        }
    }

    /**
     * @param stdClass $terreinObject Zoals teruggeven door de crab service als GetTerreinobjectByIdentificatorTerreinobjectResult.
     * @return array Een associatieve array met de volgende sleutels:
     * <ul>
     *  <li>identificatorTerreinobject: De identificator van het terreinobject.</li>
     *  <li>aardTerreinobjectCode: De aard van het terreinobject.</li>
     *  <li>centerX: Het x-coordinaat van het centrum van het terreinbobject.</li>
     *  <li>centerY: Het y-coordinaat van het centrum van het terreinobject.</li>
     * </ul>
     * @since 02 okt 2006
     * @throws InvalidArgumentException Indien er een verkeerde parameter wordt doorgegeven
     */
    private function loadTerreinArray ( $terreinObject )
    {
        if ( !( is_object( $terreinObject ) ) ) {
            throw new InvalidArgumentException ( 'Kan alleen maar een terrein laden op basis van een object!');
        }
        $terrein = array( );
        $terrein['identificatorTerreinobject'] = $terreinObject->IdentificatorTerreinobject;
        $terrein['aardTerreinobjectCode'] = ( int ) $terreinObject->AardTerreinobject;
        $terrein['centerX'] = ( float ) $terreinObject->CenterX;
        $terrein['centerY'] = ( float ) $terreinObject->CenterY;
        return $terrein;
    }

    public static function newNull( )
    {
        return new KVDgis_NullCrab2Gateway( );
    }

}

/**
 * KVDgis_NullCrab2Gateway 
 * 
 * @package KVD.gis
 * @subpackage crab
 * @since 5 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDgis_NullCrab2Gateway extends KVDgis_Crab2Gateway
{
    public function __construct( $parameters = array( ) )
    {

    }

    public function listStraatnamenByGemeenteId( $gemeenteId,  $sorteerVeld = self::STRAAT_SORT_NAAM ) 
    {
        return array ( array( 'straatnaam' => 'CRAB Onbereikbaar', 'straatnaamId' => 0, 'straatnaamLabel' => 'CRAB Onbereikbaar' ) );
    }

    public function getStraatnaamByStraatnaamId( $straatnaamId ) 
    {
        $straatnaam = array( );
        $straatnaam['straatnaam'] = 'CRAB Onbereikbaar';
        $straatnaam['straatnaamId'] = 0;
        $straatnaam['gemeenteId'] = 0;
        $straatnaam['taalCode'] = 'nl';
        $straatnaam['straatnaamLabel'] = 'CRAB Onbereikbaar';
        return $straatnaam;
    }

    public function getStraatnaamByStraatnaam( $straatnaam,  $gemeenteId ) 
    {
        $straatnaam = array( );
        $straatnaam['straatnaam'] = 'CRAB Onbereikbaar';
        $straatnaam['straatnaamId'] = 0;
        $straatnaam['gemeenteId'] = 0;
        $straatnaam['taalCode'] = 'nl';
        $straatnaam['straatnaamLabel'] = 'CRAB Onbereikbaar';
        return $straatnaam;
    }

    public function listHuisnummersByStraatnaamId( $straatnaamId,  $sorteerVeld = self::HUISNR_SORT_NAAM )
    {
        return array ( array ( 'huisnummer' => 'CRAB Onbereikbaar', 'huisnummerId' => 0 ) );
    }

    public function getHuisnummerByHuisnummerId( $huisnummerId ) 
    {
        return array(   'huisnummer' => 'CRAB Onbereikbaar',
                        'huisnummerId' => 0,        
                        'straatnaamId' => 0 );
    }
    
    public function getHuisnummerByHuisnummer( $huisnummer,  $straatnaamId ) 
    {
        return array(   'huisnummer' => 'CRAB Onbereikbaar',
                        'huisnummerId' => 0,        
                        'straatnaamId' => 0 );
    }
    public function getPostkantonByHuisnummerId ( $huisnummerId )
    {
        return array ( 'postkantonCode' => 0000 );
    }

    public function listWegobjectenByStraatnaamId( $straatnaamId,  $sorteerVeld = self::WEG_SORT_ID ) 
    {
       return array( array( 'identificatorWegobject' => 'CRAB Onbereikbaar', 'aardWegobject' => 0 ) );
    }
    
    public function listTerreinobjectenByHuisnummerId( $huisnummerId,  $sorteerVeld = self::TERREIN_SORT_ID )
    {
       return array( array( 'identificatorTerreinobject' => 'CRAB Onbereikbaar', 'aardTerreinobjectCode' => 0 ) );
    }
    
    public function getTerreinobjectByIdentificatorTerreinobject( $identificatorTerreinobject )
    {
        return array ( 'identificatorTerreinobject' => 'CRAB Onbereikbaar',
                       'aardTerreinobjectCode' => 0,
                       'centerX' => 0.0,
                       'centerY' => 0.0 );
    }
}
?>
