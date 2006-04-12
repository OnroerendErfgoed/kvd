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
     * @var KVDutil_CacheFile
     * @access private
     */
    private $_cache;

    /**
     *
     * @param string $wsdl      
     * @param string $username      
     * @param string $password      
     * @return KVDgis_Crab1Gateway
     * @access public
     */
    public function __construct( $wsdl,  $username,  $password ) {
        $this->_client = new SoapClient (  $wsdl);
        $this->authenticate (   $username , $password );
    } // end of member function __construct

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
    } // end of member function authenticate

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
        return $gemeenten;
    } // end of member function listGemeentenByGewestId

    /**
     * @param string $xml XML data die de gemeente bevat.
     * @return array Associatieve array met data over de gemeente.
     */
    private function loadGemeenteArray ( $xml )
    {
        $xmlObject = @simplexml_load_string ( $xml );
        $gemeenteObject = $xmlObject->CRAB_gemeente->gemeente;
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
     */
    public function getGemeenteByGemeenteId( $gemeenteId )
    {
        return $this->CRABgetgemeente ( 10 , $gemeenteId );
    } 

    /**
     *
     * @param string $gemeenteNaam      
     * @param integer $gewestId Moet altijd 2 zijn aangezien Crab1 enkel voor Vlaanderen is.     
     * @return array
     * @access public
     */
    public function getGemeenteByGemeenteNaam( $gemeenteNaam,  $gewestId = 2) 
    {
        return $this->CRABgetgemeente( 21 , $gemeenteNaam );
    }

    /**
     *
     * @param integer $nisGemeenteCode      
     * @return array
     * @access public
     */
    public function getGemeenteByNISGemeenteCode( $nisGemeenteCode ) 
    {
        return $this->CRABgetgemeente ( 20, $nisGemeenteCode );
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
        return $straatnamen;
    } 

    /**
     *
     * @param integer $straatnaamId      
     * @return array
     * @access public
     */
    public function getStraatnaamByStraatnaamId( $straatnaamId ) {
        
    }

    /**
     *
     * @param string $straatnaam      
     * @param integer $gemeenteId
     * @return array
     * @access public
     */
    public function getStraatnaamByStraatnaam( $straatnaam,  $gemeenteId ) {
        
    } 
    /**
     *
     * @param int straatnaamId      * @param int sorteerVeld      * @return assocArray
     * @access public
     */
    public function listHuisnummersByStraatnaamId( $straatnaamId,  $sorteerVeld ) {
        
    }

    /**
     *
     * @param int huisnummerId      * @return assocArray
     * @access public
     */
    public function getHuisnummerByHuisnummerId( $huisnummerId ) {
        
    } // end of member function getHuisnummerByHuisnummerId

    /**
     *
     * @param string huisnummer      * @param int straatnaamId      * @return assocArray
     * @access public
     */
    public function getHuisnummerByHuisnummer( $huisnummer,  $straatnaamId ) {
        
    } // end of member function getHuisnummerByHuisnummer

    /**
     *
     * @param int straatnaamId      * @param int sorteerVeld      * @return assocArray
     * @access public
     */
    public function listWegobjectenByStraatnaamId( $straatnaamId,  $sorteerVeld ) {
        
    } // end of member function listWegobjectenByStraatnaamId

    /**
     *
     * @param int huisnummerId      * @param int sorteerVeld      * @return assocArray
     * @access public
     */
    public function listTerreinobjectenByHuisnummerId( $huisnummerId,  $sorteerVeld ) {
        
    } // end of member function listTerreinobjectenByHuisnummerId






} // end of KVDgis_Crab1Gateway
?>
