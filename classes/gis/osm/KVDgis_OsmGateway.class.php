<?php
/**
 * @package    KVD.gis
 * @subpackage osm
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Gateway die requests kan doen naar de openstreetmap tile servers.
 * 
 * @package    KVD.gis
 * @subpackage osm
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDgis_OsmGateway implements KVDutil_Gateway
{
    protected $parameters = array(
        'cache'      => array( 'active' => false ),
        'osmservers' => array( 'mapnik' => array( 'a.tile.openstreetmap.org',
                                                  'b.tile.openstreetmap.org',
                                                  'c.tile.openstreetmap.org' ),
                               'osma'   => array( 'a.tah.openstreetmap.org',
                                                  'b.tah.openstreetmap.org',
                                                  'c.tah.openstreetmap.org' ) 
        ) 
    );

    /**
     * Maak een nieuwe gateway aan met de nodige parameters.
     *
     * Mogelijke parameters (array):
     *  * cache.active: boolean die aangeeft of er moet gecached worden.
     *  * cache.cache_dir: string die aangeeft waar er moet gecached worden. 
     *      Required indien er gecached moet worden.
     *  * proxy_host: host voor een proxy server.
     *  * proxy_port: poort van de proxy server.
     * 
     * @param array $parameters 
     * @return void
     */
    public function __construct ( $parameters = array( ) )
    {
        if ( isset( $parameters['cache']['active'] ) && ( $parameters['cache']['active'] == true ) ) {
            if (!isset( $parameters['cache']['cache_dir'] ) ) {
                throw new InvalidArgumentException( 'Er moet steeds een cache_dir aanwezig zijn!' );
            } else {
                if ( !is_writeable( $parameters['cache']['cache_dir'] ) ) {
                    throw new InvalidArgumentException( 'De cache_dir moet schrijfbaar zijn!' );
                }
                if ( !file_exists( $parameters['cache']['cache_dir'] . '/mapnik') ) {
                    mkdir($parameters['cache']['cache_dir'] . '/mapnik');
                }
                if ( !file_exists( $parameters['cache']['cache_dir'] . '/osma' ) ) {
                    mkdir($parameters['cache']['cache_dir'] . '/osma');
                }
            }
        }
        $this->parameters = array_merge( $this->parameters, $parameters );
    }

    /**
     * doRequest 
     * 
     * @param string $type 
     * @param integer $z 
     * @param integer $x 
     * @param integer $y 
     * @return string De afbeelding als string.
     */
    protected function doRequest( $type, $z, $x, $y)
    {
        if ( $this->parameters['cache']['active'] == true ) {
            $file = $this->parameters['cache']['cache_dir'] . "/$type/${z}_${x}_${y}.png";
            if ( is_file( $file) && filemtime($file) > time()-(86400*30) ) {
                return file_get_contents( $file );
            }
            $url = $this->getUrl( $type, $z, $x, $y );
            $ch = curl_init( $url);
            curl_setopt( $ch, CURLOPT_HEADER, false);
            $fp = fopen( $file, "w");
            curl_setopt( $ch, CURLOPT_FILE, $fp );
        } else {
            $url = $this->getUrl( $type, $z, $x, $y );
            $ch = curl_init( $url);
            curl_setopt( $ch, CURLOPT_HEADER, false);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        }
        // @codeCoverageIgnoreStart
        if ( isset( $this->parameters['proxy_host'] ) ) {
            $proxy = $this->parameters['proxy_host'];
            if ( isset( $this->parameters['proxy_port'] ) ) {
                $proxy .= ':' . $this->parameters['proxy_port'];
            }
            curl_setopt( $ch, CURLOPT_PROXY, $proxy );
        }
        // @codeCoverageIgnoreEnd
        if ( $this->parameters['cache']['active'] == false ) {
            $response = curl_exec( $ch);
            curl_close( $ch);
            return $response;
        } else {
            $response = curl_exec( $ch);
            fflush( $fp );
            fclose( $fp );
            curl_close( $ch);
            return file_get_contents( $file );
        }
    }

    /**
     * Genereer de url om een bepaalde tegel op te halen.
     * 
     * @param string $type 
     * @param integer $z 
     * @param integer $x 
     * @param integer $y 
     * @return string
     */
    private function getUrl( $type, $z, $x, $y )
    {
        if ( $type == 'mapnik' ) {
            $server = $this->parameters['osmservers']['mapnik'][array_rand($this->parameters['osmservers']['mapnik'])];
            $url = 'http://' . $server;
            $url .= "/".$z."/".$x."/".$y.".png";
        } else {
            $server = $this->parameters['osmservers']['osma'][array_rand($this->parameters['osmservers']['osma'])];
            $url = 'http://' . $server . '/Tiles/tile.php';
            $url .= "/".$z."/".$x."/".$y.".png";
        }
        return $url;
    }

    /**
     * Vraag een tegel gerenderd door mapnik op.
     * 
     * @param integer $z Zoomlevel
     * @param integer $x X-waarde
     * @param integer $y Y-waarde
     * @return string De afbeelding als string.
     */
    public function getMapnik( $z, $x, $y )
    {
        return $this->doRequest( 'mapnik', $z, $x, $y );
    }

    /**
     * Vraag een tegel gerenderd door osma op.
     * 
     * @param integer $z Zoomlevel
     * @param integer $x X-waarde
     * @param integer $y Y-waarde
     * @return string De afbeelding als string.
     */
    public function getOsma( $z, $x, $y )
    {
        return $this->doRequest( 'osma', $z, $x, $y );
    }
}
?>
