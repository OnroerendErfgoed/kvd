<?php
/**
 * @package    KVD.gis
 * @subpackage crab
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version    $Id$
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDgis_Crab2SoapClient 
 * 
 * @package    KVD.gis
 * @subpackage crab
 * @since      5 maart 2007
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDgis_Crab2SoapClient extends SoapClient
{
    /**
     * user 
     * 
     * @var string
     */
    private $user = null;
    
    /**
     * password 
     * 
     * @var string
     */
    private $password = null;

    /**
     * setAuthentication
     *  
     * @param string $user 
     * @param string $password 
     */
    public function setAuthentication ( $user , $password )
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * __doRequest 
     * 
     * @param string $request 
     * @param string $location 
     * @param string $saction 
     * @param integer $version 
     * @return string
     */
    public function __doRequest( $request , $location , $saction , $version, $one_way = 0 )
    {
        if ( is_null( $this->user ) || is_null( $this->password ) ) {
            throw new SoapFault ( 'U hebt geen authenticatie credentials opgegeven.' );
        }
        $dom = new DOMDocument( '1.0' );
        $dom->loadXML( $request );

        $wsa = new KVDutil_SoapWSA( $dom );
        $wsa->addAction( $saction );
        $wsa->addTo( $location );
        $wsa->addMessageID( );
        $wsa->addReplyTo( );

        $dom = $wsa->getDoc( );

        $wsse = new KVDutil_SoapWSSE( $dom );

        $wsse->addUserToken( $this->user , $this->password , true );

        return parent::__doRequest( $wsse->saveXML( ) , $location , $saction , $version, $one_way );

    }
}
?>
