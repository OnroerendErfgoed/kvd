<?php
/**
 * @package     KVD.dom
 * @subpackage  exception
 * @copyright   2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_ExceptionConvertor
 *
 * @package     KVD.dom
 * @subpackage  exception
 * @since       27 april 2007
 * @copyright   2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdom_ExceptionConvertor
{
    /**
     * convert
     *
     * Converteert indien mogelijk een PDOException naar een leesbaarder exception
     * @param Exception $e
     * @param KVDdom_DomainObject $dom
     * @return Exception
     */
    public static function convert( Exception $e , KVDdom_DomainObject $dom )
    {
        if ( !$e instanceof PDOException ) {
            return $e;
        }
        $msg = $e->getMessage( );
        if ( stripos( $msg , 'foreign key violation' ) ) {
            return new KVDdom_ReferenceViolationException( $dom, $e );
        }
        return $e;
    }
}
?>
