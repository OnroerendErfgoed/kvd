<?php
/**
 * @package KVD.gis.crab
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version $Id$
 */

/**
 * KVDgis_Crab2Gateway
 *
 * Een Gateway om te connecteren met crab2.
 * @package KVD.gis.crab
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since feb 2006
 */
class KVDgis_Crab2Gateway implements KVDutil_Gateway
{
    /**
     * @var string 
     */
    const CRAB_NAMESPACE = "http://ws.agiv.be/crab_1_0";

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
     */
    const WEG_SORT_AARD = 2;
    
    /**
     * @var SoapClient
     * @access private
     */
    private $_client;

    /**
     * @var KVDgis_CrabCache
     * @access private
     */
    private $_cache;

    /**
     * Initializeer de Crab2Gateway voor gebruik.
     *
     * Parameters is een associatieve array met de volgende sleutels: wsdl ( url naar de wsdl file), username, password. Deze parameters zijn altijd vereist.
     * De parmeter cache is optioneel. Indien ze weggelaten wordt zal er geen data gecached worden. De parameter is eveneens een array met de volgende sleutels:
     * - active ( boolean ): indien false zal er geen data gecached worden
     * - cacheDir ( string ): dir waarin de caches worden aangemaakt
     * - expirationTimes ( array ): een array met verschillende sleutels per te cachen functie. Er moet minstens een sleutel default aanwezig zijn.
     *   ExpirationTimes is optioneel. Indien niet aanwezig worden er caches aangemaakt die nooit verlopen tenzij door manuele tussenkomst.
     * bv.:
     * <code>
     *  $parameters = array (   'wsdl'      => 'http://webservices.gisvlaanderen.be/crab_1_0/ws_crab_NDS.asmx?WSDL',
     *                          'username'  => 'USERNAME',
     *                          'password'  => 'PASSWORD',
     *                          'cache'     => array (  'active'    => true,
     *                                                  'cacheDir'  => '/tmp/',
     *                                                  'expirationTimes' => array (    'default' => false,
     *                                                                                  'listStraatnamenByGemeenteId' => 3600
     *                                                                             )
     *                                                )
     *                      )
     * </code>
     * @param array $parameters
     * @throws <b>IllegalArgumentException</b> - Indien er foute of ontbrekende parameters zijn.
     */
    public function __construct ( $parameters )
    {
        if ( !isset( $parameters['wsdl'] ) ) {
            throw new InvalidArgumentException ( 'De array parameters moet een sleutel wsdl bevatten!' );
        }
        $this->_client = new SoapClient ( $parameters['wsdl'] , array ( 'exceptions'    => 1,
                                                                        'encoding'      => 'ISO-8859-1',
                                                                        'trace'         => 1
                                                                        ));
        
        if ( !isset( $parameters['username']) || !isset( $parameters['password'])) {
            throw new InvalidArgumentException ( 'De array parameters moet de sleutels username en password bevatten!');
        }
        $this->authenticate( $parameters['username'], $parameters['password']);

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
     * Stel de authenticatie-informatie in.
     * @param string $username      
     * @param string $password      
     * @return void 
     * @access private
     */
    private function authenticate( $username,  $password ) {
        $auth = new StdClass( );
        $auth->Username = $username;
        $auth->Password = $password;
        
        $authvalues = new SoapVar( $auth, SOAP_ENC_OBJECT , 'AuthHeader' , self::CRAB_NAMESPACE );
        $header = new SoapHeader ( self::CRAB_NAMESPACE , 'AuthHeader' , $authvalues );
        $this->_client->__setSoapHeaders( array( $header) );
    } 

    /**
     * getCacheName 
     * 
     * @param string $function 
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
     * @return array
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
            $gemeenteArray['nisGemeenteCode'] = null;
            $gemeenten[] = $gemeenteArray;
        }
        $this->_cache->cachePut ( $this->getCacheName ( __FUNCTION__ ), $functionParameters , serialize( $gemeenten) );
        return $gemeenten;
    }

    /**
     * @param stdClass $gemeenteObject.
     * @return array Associatieve array met data over de gemeente.
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
     *
     * @param integer $gemeenteId      
     * @return array
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
     *
     * @param string $gemeenteNaam      
     * @param integer $gewestId Zie de KVDgis_Crab2Gateway::GEWEST_ constanten 
     * @return array
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
     *
     * @param integer $nisGemeenteCode      
     * @return array
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
     * @return array
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
     * @return array
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
     *      <li>    straatnaam      - Naam van de straat</li>
     *      <li>    straatnaamId    - Id van de straatnaam binnen Crab</li>
     *      <li>    gemeenteId      - Id (geen NIS-code) van de gemeente waarin de straat ligt</li>
     *      <li>    taalCode        - Taal waarin de straatnaam is opgesteld</li>
     *      <li>    straatnaamLabel - Een label voor de straatnaam, te gebruiken in keuzelijsten</li>
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
     *  <li> straatnaam      - Naam van de straat</li>
     *  <li> straatnaamId    - Id van de straatnaam binnen Crab</li>
     *  <li> gemeenteId      - Id (geen NIS-code) van de gemeente waarin de straat ligt</li>
     *  <li> taalCode        - Taal waarin de straatnaam is opgesteld</li>
     *  <li> straatnaamLabel - Een label voor de straatnaam, te gebruiken in keuzelijsten</li>
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
     * @return array Een array met de sleutels huisnummer en huisnummerId.
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
     * @return array
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
     *  <li>huisnummer      - Een string voorstelling van het huisnummer ( kan bv. bis bevatten)</li>
     *  <li>huisnummerId    - Het id van het huisnummer in Crab.</li>
     *  <li>straatnaamId    - Het id van de straat waarin het huisnummer ligt.</li>
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
     *  <li>huisnummer      - Een string voorstelling van het huisnummer ( kan bv. bis bevatten)</li>
     *  <li>huisnummerId    - Het id van het huisnummer in Crab.</li>
     *  <li>straatnaamId    - Het id van de straat waarin het huisnummer ligt.</li>
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
     * @return array Een array met maar 1 sleutel, nl. postkantonCode.
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
     * @return array
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
     * @return array
     * @throws InvalidArgumentException Indien er op een ongeldig sorteerVeld wordt gesorteerd.
     */
    public function listTerreinobjectenByHuisnummerId( $huisnummerId,  $sorteerVeld ) 
    {
        if ( $sorteerVeld != 1 ) {
            throw new InvalidArgumentException ( "De parameter sorteerVeld van de functie listTerreinobjectenByStraatnaamId moet altijd 1 zijn!");
        }
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
        if ( isset( $result->ListTerreinobjectenByHuisnummerIdResult ) ) {
            foreach ( $result->ListTerreinobjectenByHuisnummerIdResult as $terreinobject ) {
                $terreinobjectArray = array( );
                $terreinobjectArray['identificatorTerreinobject'] = $terreinobject->IdentificatorTerreinobject;
                $terreinobjectArray['aardTerreinobjectCode'] = ( int ) $terreinobject->AardTerreinobject;
                //$terreinobjectArray['centerX'] = ( float ) $terreinobject->x_coordinaat;
                //$terreinobjectArray['centerY'] = ( float ) $terreinobject->y_coordinaat;
                $terreinobjecten[] = $terreinobjectArray;
            }
        }
        $this->_cache->cachePut ( $this->getCacheName ( __FUNCTION__ ) , $functionParameters , serialize( $terreinobjecten ) );
        return $terreinobjecten;
    } 

    /**
     * @param string $identificatorTerreinobject
     * @return array
     * @todo implementeren. Probleem: deze functie bestaat niet in Crab1.
     */
    public function getTerreinobjectByIdentificatorTerreinobject( $identificatorTerreinobject )
    {
        throw new Exception ( 'Deze functie bestaat niet in Crab1!');
    }
} // end of KVDgis_Crab1Gateway
?>
