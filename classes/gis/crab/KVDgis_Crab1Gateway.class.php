<?php
/**
 * @package KVD.gis.crab
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * class KVDgis_Crab1Gateway
 *
 * @package KVD.gis.crab
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDgis_Crab1Gateway
{
    const CRAB_NAMESPACE = "http://www.gisvlaanderen.be/webservices/";

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
     *
     * @param string $wsdl      
     * @param string $username      
     * @param string $password
     * @param string $crabcache
     * @return KVDgis_Crab1Gateway
     * @access public
     */
    public function __construct( $wsdl,  $username,  $password , $crabcache = null) {
        $this->_client = new SoapClient (  $wsdl);
        $this->authenticate (   $username , $password );
        if ( $crabcache != null ) {
            $this->_cache = $crabcache;
        } else {
            $this->_cache = new KVDgis_NullCrabCache( );
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
        $auth->Username = $username;
        $auth->Password = $password;
        
        $authvalues = new SoapVar( $auth, SOAP_ENC_OBJECT , 'AuthHeader' , self::CRAB_NAMESPACE );
        $header = new SoapHeader ( self::CRAB_NAMESPACE , 'AuthHeader' , $authvalues );
        $this->_client->__setSoapHeaders( array( $header) );
    } 

    /**
     *
     * @param integer $gewestId Moet altijd 2 zijn aangezien CRAB 1 enkel gemeenten in Vlaanderen kan weergeven.
     * @param integer $sorteerVeld Zie de voorlopige handleiding voor CRAB2. 1= gemeenteId, 2= gemeenteNaam, 3= nisGemeenteCode.
     * @return array
     * @access public
     */
    public function listGemeentenByGewestId( $gewestId,  $sorteerVeld ) {
        if ( $gewestId != 2 ) {
            throw new InvalidArgumentException ( "De parameter gewestId van de functie listGemeentenByGewestId moet altijd 2 zijn!");
        }
        $params = new stdClass( );
        switch ( $sorteerVeld ) {
            case 1:
                $sorteerVeld = 3;
                break;
            case 2:
                $sorteerVeld = 1;
                break;
            case 6:
                $sorteerVeld = 2;
                break;
            default:
                throw new IllegalArgumentException ( "De parameter sorteerVeld van de functie listGemeentenByGewestId moet 1,2 of 6 zijn.");
        }
        $functionParameters = func_get_args( );
        $result = $this->_cache->cacheGet ( __FUNCTION__ , $functionParameters );
        if ( $result != false ) {
            return unserialize( $result );
        }
        $params->sortval = $sorteerVeld; 
        $paramsWrapper = new SoapParam ( $params , "CRABgetgemeentenlijstVlaanderen" );
        $result = $this->_client->CRABgetgemeentenlijstVlaanderen( $paramsWrapper );
        
        $xml = $result->CRABgetgemeentenlijstVlaanderenResult->any;
        $xmlObject = @simplexml_load_string ( $xml );
        $gemeenten = array( );
        foreach ( $xmlObject->CRAB_gemeentenlijst->gemeentenlijst as $gemeente) {
            $gemeenteArray = array( );
            $gemeenteArray['gemeenteNaam'] = utf8_decode( $gemeente->gemeenteNaam );
            $gemeenteArray['gemeenteId'] = ( int ) $gemeente->gemeenteId;
            $gemeenteArray['nisGemeenteCode'] = ( int ) $gemeente->nisGemeenteCode;
            $gemeenten[] = $gemeenteArray;
        }
        $this->_cache->cachePut ( __FUNCTION__ , $functionParameters , serialize( $gemeenten) );
        return $gemeenten;
    }

    /**
     * @param string $xml XML data die de gemeente bevat.
     * @return array Associatieve array met data over de gemeente.
     */
    private function loadGemeenteArray ( $xml )
    {
        $xmlObject = @simplexml_load_string ( $xml );
        $gemeenteObject = $xmlObject->CRAB_gemeente->gemeente;
        if ( !( $gemeenteObject instanceof SimpleXMLElement ) ) {
            throw new UnexpectedValueException ( 'De CRAB service heeft een ongeldig XML-element teruggeven!');
        }
        $gemeente = array( );
        $gemeente['gemeenteNaam'] = utf8_decode ( $gemeenteObject->GemeenteNaam);
        $gemeente['gemeenteId'] = ( int ) $gemeenteObject->gemeenteId;
        $gemeente['nisGemeenteCode'] = ( int ) $gemeenteObject->nisGemeenteCode;
        $gemeente['taalCode'] = utf8_decode (  $gemeenteObject->taalcode);
        $gemeente['taalCodeGemeenteNaam'] = utf8_decode (  $gemeenteObject->taalCodeGemeenteNaam );
        return $gemeente;
    }
    
    /**
     * @param integer $vraagtype
     * @param mixed $vraagwaarde Kan zowel een integer als een string zijn, afhankelijk van het vraagtype.
     * @return array
     */
    private function CRABgetgemeente( $vraagtype, $vraagwaarde )
    {
        $params->vraagtype = $vraagtype;
        $params->vraagwaarde = $vraagwaarde;
        $params->eigentaal = true;
        $paramsWrapper= new SoapParam ( $params, "CRABgetgemeente");
        $result = $this->_client->CRABgetgemeente ( $paramsWrapper );

        return $this->loadGemeenteArray( $result->CRABgetgemeenteResult->any );
    }

    /**
     *
     * @param integer $gemeenteId      
     * @return array
     * @access public
     * @throws RuntimeException Indien de gemeente niet kon geladen worden.
     */
    public function getGemeenteByGemeenteId( $gemeenteId )
    {
        try {
            return $this->CRABgetgemeente ( 10 , $gemeenteId );
        } catch ( UnexpectedValueException $e ) {
            throw new RuntimeException ( "Kon de gemeente met gemeenteId $gemeenteId niet laden. Controleer of deze wel bestaat.");
        }
    } 

    /**
     *
     * @param string $gemeenteNaam      
     * @param integer $gewestId Moet altijd 2 zijn aangezien Crab1 enkel voor Vlaanderen is.     
     * @return array
     * @access public
     * @throws RuntimeException Indien de gemeente niet kon geladen worden.
     */
    public function getGemeenteByGemeenteNaam( $gemeenteNaam,  $gewestId = 2) 
    {
        try {
            return $this->CRABgetgemeente( 21 , $gemeenteNaam );
        } catch ( UnexpectedValueException $e ) {
            throw new RuntimeException ( "Kon de gemeente met gemeenteNaam $gemeenteNaam niet laden. Controleer of deze wel bestaat.");
        }
    }

    /**
     *
     * @param integer $nisGemeenteCode      
     * @return array
     * @access public
     * @throws RuntimeException Indien de gemeente niet kon geladen worden.
     */
    public function getGemeenteByNISGemeenteCode( $nisGemeenteCode ) 
    {
        try {
            return $this->CRABgetgemeente ( 20, $nisGemeenteCode );
        } catch ( UnexpectedValueException $e ) {
            throw new RuntimeException ( "Kon de gemeente met nisGemeenteCode $nisGemeenteCode niet laden. Controleer of deze wel bestaat.");
        }
    } 

    /**
     *
     * @param integer $gemeenteId      
     * @param integer $sorteerVeld Moet altijd 2 zijn aangezien Crab1 enkel kan sorteren op straatnaam.
     * @return array
     * @access public
     */
    public function listStraatnamenByGemeenteId( $gemeenteId,  $sorteerVeld ) 
    {
        if ( $sorteerVeld != 2 ) {
            throw new InvalidArgumentException ( "De parameter sorteerVeld van de functie listStraatnamenByGemeenteId moet altijd 2 zijn!");
        }
        $functionParameters = func_get_args( );
        $result = $this->_cache->cacheGet ( __FUNCTION__ , $functionParameters );
        if ( $result != false ) {
            return unserialize( $result );
        }
        $params = new StdClass( );
        $params->gemid = (int) $gemeenteId;
        $paramsWrapper = new SoapParam ( $params , "CRABgetstraatnamenlijstvlaanderen");
        $result = $this->_client->CRABgetstraatnamenlijstvlaanderen( $paramsWrapper );

        $xmlObject = @simplexml_load_string ( $result->CRABgetstraatnamenlijstVlaanderenResult->any );
        $straatnamen = array( );
        foreach ( $xmlObject->CRAB_straatnamenlijst->straatnamenlijst as $straatnaam ) {
            $straatnaamArray = array( );
            $straatnaamArray['straatnaam'] = utf8_decode( $straatnaam->straatNaam);
            $straatnaamArray['straatnaamId'] = ( int ) $straatnaam->straatNaamId;
            $straatnaamArray['straatnaamLabel'] = utf8_decode ( $straatnaam->STRAATNM0 );
            $straatnamen[] = $straatnaamArray;
        }
        $this->_cache->cachePut ( __FUNCTION__ , $functionParameters , serialize( $straatnamen ));
        return $straatnamen;
    }

    /**
     * @param integer $vraagtype
     * @param mixed $vraagwaarde
     * @param integer $gemid
     * @return array
     */
    private function CRABgetstraatnaam ( $vraagtype , $vraagwaarde, $gemid)
    {
        $params->vraagtype = $vraagtype;
        $params->vraagwaarde = $vraagwaarde;
        $params->gemid = $gemid;
        $paramsWrapper= new SoapParam ( $params, "CRABgetstraatnaam");
        $result = $this->_client->CRABgetstraatnaam ( $paramsWrapper );

        return $this->loadStraatnaamArray( $result->CRABgetstraatnaamResult->any );
    }

    /**
     * @param string $xml
     * @return array
     */
    private function loadStraatnaamArray ( $xml )
    {
        
        $xmlObject = @simplexml_load_string ( $xml );
        $straatnaamObject = $xmlObject->CRAB_straatnaam->straatnaam;
        if ( !( $straatnaamObject instanceof SimpleXMLElement ) ) {
            throw new UnexpectedValueException ( 'De CRAB service heeft een ongeldig XML-element teruggeven!');
        }
        $straatnaam = array( );
        $straatnaam['straatnaam'] = utf8_decode ( $straatnaamObject->straatNaam);
        $straatnaam['straatnaamId'] = ( int ) $straatnaamObject->straatNaamId;
        $gemeente = $this->getGemeenteByNISGemeenteCode ( $straatnaamObject->nisGemeenteCode);
        $straatnaam['gemeenteId'] = $gemeente['gemeenteId'];
        $straatnaam['taalCode'] = utf8_decode ( $straatnaamObject->taalcode);
        $straatnaam['straatnaamLabel'] = utf8_decode ( $straatnaamObject->STRAATNM0 );
        return $straatnaam;
    }

    /**
     *
     * @param integer $straatnaamId      
     * @return array
     * @access public
     * @throws RuntimeException Indien de straatnaam niet kon geladen worden
     */
    public function getStraatnaamByStraatnaamId( $straatnaamId ) 
    {
        try {
            return $this->CRABgetstraatnaam( 10 , $straatnaamId , 0 );
        } catch ( UnexpectedValueException $e ) {
            throw new RuntimeException ( "Kon de straatnaam met straatnaamId $straatnaamId niet laden. Controleer of deze wel bestaat.");
        }
    }

    /**
     *
     * @param string $straatnaam      
     * @param integer $gemeenteId
     * @return array
     * @throws RuntimeException Indien de straatnaam niet kon geladen worden 
     * @access public
     */
    public function getStraatnaamByStraatnaam( $straatnaam,  $gemeenteId ) 
    {
        try {
            return $this->CRABgetstraatnaam( 21 , $straatnaam , $gemeenteId );
        } catch ( UnexpectedValueException $e ) {
            throw new RuntimeException ( "Kon de straatnaam met straatnaam $straatnaam in gemeente $gemeenteId niet laden. Controleer of deze wel bestaat.");
        }
    } 
    /**
     *
     * @param int straatnaamId      
     * @param int sorteerVeld      
     * @return assocArray
     * @access public
     */
    public function listHuisnummersByStraatnaamId( $straatnaamId,  $sorteerVeld ) {
        if ( $sorteerVeld != 2 ) {
            throw new InvalidArgumentException ( "De parameter sorteerVeld van de functie listHuisnummersByStraatnaamId moet altijd 2 zijn!");
        }
        $functionParameters = func_get_args( );
        $result = $this->_cache->cacheGet ( __FUNCTION__ , $functionParameters );
        if ( $result != false ) {
            return unserialize( $result );
        }
        $params = new StdClass( );
        $params->straatid = (int) $straatnaamId;
        $paramsWrapper = new SoapParam ( $params , "CRABgethuisnummerlijst");
        $result = $this->_client->CRABgethuisnummerlijst( $paramsWrapper );

        $xmlObject = @simplexml_load_string ( $result->CRABgethuisnummerlijstResult->any );
        $huisnummers = array( );
        foreach ( $xmlObject->CRAB_huisnummerlijst->huisnummerlijst as $huisnummer ) {
            $huisnummerArray = array( );
            $huisnummerArray['huisnummer'] = utf8_decode( $huisnummer->huisNummer );
            $huisnummerArray['huisnummerId'] = ( int ) $huisnummer->huisNummerId;
            $huisnummers[] = $huisnummerArray;
        }
        $this->_cache->cachePut ( __FUNCTION__ , $functionParameters , serialize( $huisnummers ) );
        return $huisnummers;
    }


    /**
     * @param integer $vraagtype
     * @param mixed $vraagwaarde
     * @param integer $straatnaamid
     * @return array
     */
    private function CRABgethuisnummer ( $vraagtype , $vraagwaarde, $straatnaamid)
    {
        $params->vraagtype = $vraagtype;
        $params->vraagwaarde = $vraagwaarde;
        $params->straatnaamid = $straatnaamid;
        $paramsWrapper= new SoapParam ( $params, "CRABgethuisnummer");
        $result = $this->_client->CRABgethuisnummer ( $paramsWrapper );

        return $this->loadHuisnummerArray( $result->CRABgethuisnummerResult->any );
    }

    /**
     * @param string $xml
     * @return array
     */
    private function loadHuisnummerArray ( $xml )
    {
        $xmlObject = @simplexml_load_string ( $xml );
        $huisnummerObject = $xmlObject->CRAB_huisnummer->huisnummer;
        if ( !( $huisnummerObject instanceof SimpleXMLElement ) ) {
            throw new UnexpectedValueException ( 'De CRAB service heeft een ongeldig XML-element teruggeven!');
        }
        $huisnummer = array( );
        $huisnummer['huisnummer'] = utf8_decode ( $huisnummerObject->huisNummer);
        $huisnummer['huisnummerId'] = ( int ) $huisnummerObject->huisNummerId;
        $huisnummer['straatnaamId'] = ( int ) $huisnummerObject->straatNaamId;
        return $huisnummer;
    }


    /**
     *
     * @param integer huisnummerId
     * @return array
     * @access public
     * @throws RuntimeException Indien het huisnummer niet geladen kon worden.
     */
    public function getHuisnummerByHuisnummerId( $huisnummerId ) 
    {
        try {
            return $this->CRABgethuisnummer( 10 , $huisnummerId , 0 );
        } catch ( UnexpectedValueException $e ) {
            throw new RuntimeException ( "Kon het huisnummer met huisnummerId $huisnummerId niet laden. Controleer of deze wel bestaat.");
        }
    } 
    
    /**
     *
     * @param string huisnummer
     * @param integer straatnaamId
     * @return array
     * @access public
     * @throws RuntimeException Indien het huisnummer niet geladen kon worden.
     */
    public function getHuisnummerByHuisnummer( $huisnummer,  $straatnaamId ) 
    {
        try {
            return $this->CRABgethuisnummer( 21 , $huisnummer , $straatnaamId );
        } catch ( UnexpectedValueException $e ) {
            throw new RuntimeException ( "Kon het huisnummer met huisnummer $huisnummer in de straat $straatnaamId niet laden. Controleer of deze wel bestaat.");
        }
    }

    /**
     *
     * @param integer straatnaamId
     * @param integer sorteerVeld      
     * @return array
     * @access public
     * @throws InvalidArgumentException Indien er op een ongeldig sorteerVeld gesorteerd wordt.
     */
    public function listWegobjectenByStraatnaamId( $straatnaamId,  $sorteerVeld ) 
    {
        if ( $sorteerVeld != 1 ) {
            throw new InvalidArgumentException ( "De parameter sorteerVeld van de functie listWegobjectenByStraatnaamId moet altijd 1 zijn!");
        }
        $params = new StdClass( );
        $params->vraagtype = 1;
        $params->vraagwaarde = (int) $straatnaamId;
        $paramsWrapper = new SoapParam ( $params , "CRABgetstraatmultinet");
        $result = $this->_client->CRABgetstraatmultinet( $paramsWrapper );

        $xmlObject = @simplexml_load_string ( $result->CRABgetstraatmultinetResult->any );
        $wegobjecten = array( );
        foreach ( $xmlObject->CRAB_multinet->multinet as $wegobject ) {
            $wegobjectArray = array( );
            $wegobjectArray['identificatorWegobject'] = utf8_decode( $wegobject->identificatorWegObject );
            $wegobjecten[] = $wegobjectArray;
        }
        return $wegobjecten;
    }

    /**
     * Geef alle terreinobjecten die horen tot een huisnummer.
     *
     * Opgelet! Momenteel geeft deze functie meer data terug dan Crab2 zal doen. Naast de Crab2 elementen ( identificatorTerreinobject en 
     * aardTerreinobjectCode) geeft ze ook de elementen centerX en centerY terug. Dit zijn de X en Y-coordinaat van de centroide van het terreinobject.
     * Calls via Crab2 zullen eerst deze functie moeten aanroepen en dan nog eens getTerreinobjectByIdentificatorTerreinobject. vb:
     * <code>
     *  //Nu
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
     * @param integer sorteerVeld
     * @return array
     * @access public
     * @throws InvalidArgumentException Indien er op een ongeldig sorteerVeld wordt gesorteerd.
     */
    public function listTerreinobjectenByHuisnummerId( $huisnummerId,  $sorteerVeld ) 
    {
        if ( $sorteerVeld != 1 ) {
            throw new InvalidArgumentException ( "De parameter sorteerVeld van de functie listTerreinobjectenByStraatnaamId moet altijd 1 zijn!");
        }
        $functionParameters = func_get_args( );
        $result = $this->_cache->cacheGet ( __FUNCTION__ , $functionParameters );
        if ( $result != false ) {
            return unserialize( $result );
        }
        $params = new StdClass( );
        $params->huisnrid = (int) $huisnummerId;
        $paramsWrapper = new SoapParam ( $params , "CRABgethuisnummerpositie");
        $result = $this->_client->CRABgethuisnummerpositie( $paramsWrapper );

        $xmlObject = @simplexml_load_string ( $result->CRABgethuisnummerpositieResult->any );
        $terreinobjecten = array( );
        foreach ( $xmlObject->CRAB_huisnummerpositie->huisnummerpositie as $terreinobject ) {
            $terreinobjectArray = array( );
            $terreinobjectArray['identificatorTerreinobject'] = utf8_decode( $terreinobject->identificatorTerreinObject );
            $terreinobjectArray['aardTerreinobjectCode'] = utf8_decode ( $terreinobject->naam );
            $terreinobjectArray['centerX'] = ( float ) $terreinobject->x_coordinaat;
            $terreinobjectArray['centerY'] = ( float ) $terreinobject->y_coordinaat;
            $terreinobjecten[] = $terreinobjectArray;
        }
        $this->_cache->cachePut ( __FUNCTION__ , $functionParameters , serialize( $terreinobjectArray ) );
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
