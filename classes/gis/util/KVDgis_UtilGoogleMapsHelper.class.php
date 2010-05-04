<?php
/**
 * @package     KVD.gis
 * @subpackage  util
 * @version     $Id$
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDgis_UtilGoogleMapsHelper 
 * 
 * @package     KVD.gis
 * @version     util
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDgis_UtilGoogleMapsHelper 
{
    /**
     * getStaticMap 
     * 
     * @param   array   $parameters     De sleutels 'base_url' en 'size' moeten 
     *                                  altijd aanwezig zijn.  
     * @param   array   $geometries     Een array van geometries. Momenteel worden 
     *                                  enkel {@link KVDgis_GeomPoint} objecten ondersteund.
     * @return  string
     */
    public static function getStaticMap( array $parameters, array $geometries )
    {
        self::checkStaticParameters( $parameters );
        $m = array( );
        foreach ( $geometries as $geom ) {
            $m[] = $geom->getY( ). ', ' . $geom->getX( );
        }
        $markers = implode( '|', $m );
        $parameters['markers'] = $markers;
        $base = $parameters['base_url'];
        unset( $parameters['base_url'] );
        return $base . '?' . http_build_query( $parameters, '', '&amp;' );
    }

    /**
     * checkStaticParameters 
     * 
     * @param   array $parameters 
     * @throws  InvalidArgumentException
     * @return  void
     */
    private static function checkStaticParameters( array $parameters )
    {
        if ( !isset( $parameters['base_url'] ) ) {
            throw new InvalidArgumentException( 'base_url' );
        }
        if ( !isset( $parameters['size'] ) ) {
            throw new InvalidArgumentException( 'size' );
        }
    }
}
?>
