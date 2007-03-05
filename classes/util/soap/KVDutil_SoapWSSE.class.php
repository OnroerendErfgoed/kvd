<?php
/**
 * KVDutil_SoapWSSE 
 * 
 * @package KVD.util
 * @subpackage soap
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Rob Richards <cdatazone.org>
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_SoapWSSE 
 * 
 * @package KVD.util
 * @subpackage soap
 * @since 5 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Rob Richards <cdatazone.org>
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_SoapWSSE {
    const WSSENS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
    const WSUNS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
    const WSSEPFX = 'wsse';
    const WSUPFX = 'wsu';
    private $soapNS, $soapPFX;
    private $soapDoc = NULL;
    private $envelope = NULL;
    private $SOAPXPath = NULL;
    private $secNode = NULL;
    public $signAllHeaders = FALSE;

    /**
     * locateSecurityHeader 
     * 
     * @param boolean $bMustUnderstand 
     * @param string $setActor 
     * @return DOMNodeList
     */
    private function locateSecurityHeader($bMustUnderstand = TRUE, $setActor = NULL) {
        if ($this->secNode == NULL) {
            $headers = $this->SOAPXPath->query('//wssoap:Envelope/wssoap:Header');
            $header = $headers->item(0);
            if (! $header) {
                $header = $this->soapDoc->createElementNS($this->soapNS, $this->soapPFX.':Header');
                $this->envelope->insertBefore($header, $this->envelope->firstChild);
            }
            $secnodes = $this->SOAPXPath->query('./wswsse:Security', $header);
            $secnode = NULL;
            foreach ($secnodes AS $node) {
                $actor = $node->getAttributeNS($this->soapNS, 'actor');
                if ($actor == $setActor) {
                    $secnode = $node;
                    break;
                }
            }
            if (! $secnode) {
                $secnode = $this->soapDoc->createElementNS(self::WSSENS, self::WSSEPFX.':Security');
                $header->appendChild($secnode);
                if ($bMustUnderstand) {
                    $secnode->setAttributeNS($this->soapNS, $this->soapPFX.':mustUnderstand', '1');
                }
                if (! empty($setActor)) {
                    $secnode->setAttributeNS($this->soapNS, $this->soapPFX.':actor', $setActor);
                }
            }
            $this->secNode = $secnode;
        }
        return $this->secNode;
    }

    /**
     * __construct 
     * 
     * @param DOMDocuement $doc 
     * @param boolean $bMustUnderstand 
     * @param string $setActor 
     * @return void
     */
    public function __construct($doc, $bMustUnderstand = TRUE, $setActor=NULL) {
        $this->soapDoc = $doc;
        $this->envelope = $doc->documentElement;
        $this->soapNS = $this->envelope->namespaceURI;
        $this->soapPFX = $this->envelope->prefix;
        $this->SOAPXPath = new DOMXPath($doc);
        $this->SOAPXPath->registerNamespace('wssoap', $this->soapNS);
        $this->SOAPXPath->registerNamespace('wswsse', self::WSSENS);
        $this->locateSecurityHeader($bMustUnderstand, $setActor);
    }

    /**
     * addTimestamp 
     * 
     * @param int $secondsToExpire 
     * @return void
     */
    public function addTimestamp($secondsToExpire=3600) {
        /* Add the WSU timestamps */
        $security = $this->locateSecurityHeader();

        $timestamp = $this->soapDoc->createElementNS(self::WSUNS, self::WSUPFX.':Timestamp');
        $security->insertBefore($timestamp, $security->firstChild);
        $currentTime = time();
        $created = $this->soapDoc->createElementNS( self::WSUNS, self::WSUPFX.':Created', gmdate("Y-m-d\TH:i:s", $currentTime).'Z');
        $timestamp->appendChild($created);
        if (! is_null($secondsToExpire)) {
            $expire = $this->soapDoc->createElementNS( self::WSUNS,  self::WSUPFX.':Expires', gmdate("Y-m-d\TH:i:s", $currentTime + $secondsToExpire).'Z');
            $timestamp->appendChild($expire);
        }
    }

    /**
     * addUserToken 
     * 
     * @param string $userName 
     * @param string $password 
     * @param boolean $passwordDigest 
     * @return void
     */
    public function addUserToken($userName, $password=NULL, $passwordDigest=FALSE) {
        if ($passwordDigest && empty($password)) {
            throw new InvalidArgumentException("Cannot calculate the digest without a password");
        }
        
        $security = $this->locateSecurityHeader();

        $token = $this->soapDoc->createElementNS(self::WSSENS, self::WSSEPFX.':UsernameToken');
        $security->insertBefore($token, $security->firstChild);

        $username = $this->soapDoc->createElementNS( self::WSSENS,  self::WSSEPFX.':Username', $userName);
        $token->appendChild($username);
        
        /* Generate nonce - create a 256 bit session key to be used */
        $objKey = new KVDutil_XMLSecurityKey(KVDutil_XMLSecurityKey::AES256_CBC);
        $nonce = $objKey->generateSessionKey();
        unset($objKey);
        $createdate = gmdate("Y-m-d\TH:i:s").'Z';
        
        if ($password) {
            $passType = '#PasswordText';
            if ($passwordDigest) {
                $password = base64_encode(sha1($nonce . $createdate . $password , true ));
                $passType='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest';
            }
            $passwordNode = $this->soapDoc->createElementNS(self::WSSENS, self::WSSEPFX.':Password', $password);
            $token->appendChild($passwordNode);
            $passwordNode->setAttribute('Type', $passType);
        }

        $nonceNode = $this->soapDoc->createElementNS(self::WSSENS,  self::WSSEPFX.':Nonce', base64_encode($nonce));
        $token->appendChild($nonceNode);

        $created = $this->soapDoc->createElementNS( self::WSUNS, self::WSUPFX.':Created', $createdate);
        $token->appendChild($created);
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
     * @param string $file Naam van een bestand
     * @return void
     */
    public function save($file) {
        return $this->soapDoc->save($file);
    }
}

?>
