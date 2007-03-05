<?php
/**
 * KVDutil_SoapWSA 
 * 
 * @package KVD.util
 * @subpackage Soap
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Rob Richards <cdatazone.org>
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_SoapWSA 
 * 
 * @package KVD.util
 * @subpackage Soap
 * @since 5 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Rob Richards <cdatazone.org>
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_SoapWSA {
    const WSANS = 'http://schemas.xmlsoap.org/ws/2004/08/addressing';
    const WSAPFX = 'wsa';
    private $soapNS, $soapPFX;
    private $soapDoc = NULL;
    private $envelope = NULL;
    private $SOAPXPath = NULL;
    private $header = NULL;
    private $messageID = NULL;
    
    /**
     * locateHeader 
     * 
     * Zoek de security header.
     * 
     * @return DOMNodeList
     */
    private function locateHeader() {
        if ($this->header == NULL) {
            $headers = $this->SOAPXPath->query('//wssoap:Envelope/wssoap:Header');
            $header = $headers->item(0);
            if (! $header) {
                $header = $this->soapDoc->createElementNS($this->soapNS, $this->soapPFX.':Header');
                $this->envelope->insertBefore($header, $this->envelope->firstChild);
            }
            $this->header = $header;
        }
        return $this->header;
    }

    /**
     * __construct 
     * 
     * @param DOMDocument $doc 
     */
    public function __construct($doc) {
        $this->soapDoc = $doc;
        $this->envelope = $doc->documentElement;
        $this->soapNS = $this->envelope->namespaceURI;
        $this->soapPFX = $this->envelope->prefix;
        $this->SOAPXPath = new DOMXPath($doc);
        $this->SOAPXPath->registerNamespace('wssoap', $this->soapNS);
        $this->SOAPXPath->registerNamespace('wswsa', self::WSANS);
        
        $this->envelope->setAttributeNS("http://www.w3.org/2000/xmlns/", 'xmlns:'.self::WSAPFX, self::WSANS);
        $this->locateHeader();
    }

    /**
     * addAction 
     * 
     * @param string $action 
     * @return void
     */
    public function addAction($action) {
        /* Add the WSA Action */
        $header = $this->locateHeader();

        $nodeAction = $this->soapDoc->createElementNS(self::WSANS, self::WSAPFX.':Action',$action);
        $header->appendChild($nodeAction);
    }

    /**
     * addTo 
     * 
     * @param string $location 
     * @return void
     */
    public function addTo($location) {
        /* Add the WSA To */
        $header = $this->locateHeader();

        $nodeTo = $this->soapDoc->createElementNS(self::WSANS, self::WSAPFX.':To', $location);
        $header->appendChild($nodeTo);
    }

    /**
     * createID 
     * 
     * @return string
     */
    private function createID() {
        $uuid = md5(uniqid(rand(), true));
        $guid =  'uudi:'.substr($uuid,0,8)."-".
                substr($uuid,8,4)."-".
                substr($uuid,12,4)."-".
                substr($uuid,16,4)."-".
                substr($uuid,20,12);
        return $guid;
    }

    /**
     * addMessageID 
     * 
     * @param string $id 
     * @return void
     */
    public function addMessageID($id=NULL) {
        /* Add the WSA MessageID or return existing ID */
        if (! is_null($this->messageID)) {
            return $this->messageID;
        }

        if (empty($id)) {
            $id = $this->createID();
        }

        $header = $this->locateHeader();

        $nodeID = $this->soapDoc->createElementNS(self::WSANS, self::WSAPFX.':MessageID', $id);
        $header->appendChild($nodeID);
        $this->messageID = $id;
    }

    /**
     * addReplyTo 
     * 
     * @param string $address 
     * @return void
     */
    public function addReplyTo($address = NULL) {
            /* Create Message ID is not already added - required for ReplyTo */
            if (is_null($this->messageID)) {
                $this->addMessageID();
            }
            /* Add the WSA ReplyTo */
            $header = $this->locateHeader();
    
            $nodeReply = $this->soapDoc->createElementNS( self::WSANS, self::WSAPFX.':ReplyTo');
            $header->appendChild($nodeReply);
            
            if (empty($address)) {
                $address = 'http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous';
            }
            $nodeAddress = $this->soapDoc->createElementNS( self::WSANS, self::WSAPFX.':Address', $address);
            $nodeReply->appendChild($nodeAddress);
    }

    /**
     * getDoc 
     * 
     * @return DOMDocument
     */
    public function getDoc() {
        return $this->soapDoc;
    }
    
    /**
     * saveXML 
     * 
     * @return string
     */
    public function saveXML() {
        return $this->soapDoc->saveXML();
    }

    /**
     * save 
     * 
     * @param string $file Bestandsnaam
     * @return void
     */
    public function save($file) {
        return $this->soapDoc->save($file);
    }
}
?>
