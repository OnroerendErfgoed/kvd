<?php
/**
 * @package    KVD.gis
 * @subpackage util
 * @copyright  2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDgis_UtilGoogleMapsHelper
 *
 * @package    KVD.gis
 * @subpackage util
 * @since      2010
 * @copyright  2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDgis_UtilGoogleMapsHelper
{
    /**
     * getStaticMap
     *
     * @param   array               $parameters     De sleutels 'base_url' en 'size' moeten
     *                                              altijd aanwezig zijn.
     * @param   KVDgis_GeomPoint    $punt           Het punt dat op de kaart
     *                                              moet geplaatst worden.
     * @return  string
     */
    public static function getStaticMap( array $parameters, KVDgis_GeomPoint $punt )
    {
        self::checkStaticParameters( $parameters );
        if ( !$punt->getSrid( ) == KVDgis_UtilSrid::WGS84 ) {
            throw new InvalidArgumentException(
                'U kunt enkel punten in WGS84 formaat zetten op google maps.' );
        }
        $parameters['markers'] = $punt->getY( ). ',' . $punt->getX( );
        $base = $parameters['base_url'];
        unset( $parameters['base_url'] );
        return $base . '?' . http_build_query( $parameters, '', '&amp;' );
    }

    /**
     * getLinkToGoogleMaps
     *
     * @param array            $parameters De sleutel 'base_url' moet altijd aanwzig zijn.
     *                                     Andere mogelijkheden zijn 'label' en andere
     *                                     parameters die door google maps ondersteund worden.
     * @param KVDgis_GeomPoint $punt
     * @return string
     */
    public static function getLinkToGoogleMaps( array $parameters, KVDgis_GeomPoint $punt )
    {
        self::checkLinkParameters( $parameters );
        if ( !$punt->getSrid( ) == KVDgis_UtilSrid::WGS84 ) {
            throw new InvalidArgumentException(
                'U kunt enkel punten in WGS84 formaat zetten op google maps.' );
        }
        $q = $punt->getY( ). ',' . $punt->getX( );

        if ( isset( $parameters['label'] ) ) {
            $q .= ' (' . str_replace(array( '(',')' ), '', $parameters['label']) . ')';
            unset( $parameters['label'] );
        }
        $parameters['q'] = $q;
        $base = $parameters['base_url'];
        unset( $parameters['base_url'] );
        return $base . '?' . http_build_query( $parameters, '', '&amp;' );
    }

    private static function checkLinkParameters( array $parameters )
    {
        if ( !isset( $parameters['base_url'] ) ) {
            throw new InvalidArgumentException( 'base_url' );
        }
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
