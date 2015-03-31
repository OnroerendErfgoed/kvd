<?php
/**
 * @package   KVD.html
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Utility methodes om te helpen met het verwerken van HTML.
 *
 * @package   KVD.html
 * @since     16 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDhtml_Tools
{
    /**
     * out
     *
     * Methode om output goed te escapen naar html.
     * @param string $value
     * @return string Opgekuiste string
     */
    public static function out( $value )
    {
        return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
    }

    /**
     * outImplode
     *
     * @param array $value
     * @param string $delimiter
     * @return void
     */
    public static function outImplode( array $value , $delimiter )
    {
        return implode( $delimiter ,
                        array_map( array ( 'KVDhtml_Tools' , 'out' ), $value ) );
    }

    /**
     * dateOut
     *
     * @param DateTime $value
     * @return string
     */
    public static function dateOut( DateTime $value )
    {
        if ( $value === null ) {
            return '';
        }
        return $value->format( KVDdom_DomainObject::DATE_FORMAT );
    }

    /**
     * dateTimeOut
     *
     * @param DateTime $value
     * @return string
     */
    public static function dateTimeOut( DateTime $value )
    {
        if ( $value === null ) {
            return '';
        }
        return $value->format( KVDdom_DomainObject::DATETIME_FORMAT );
    }
}
?>
