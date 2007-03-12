<?php
/**
 * KVDutil_BestandenToolkit 
 * 
 * @package KVD.util 
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_BestandenToolkit 
 * 
 * @package KVD.util 
 * @since 29 jan 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_BestandenToolkit
{
    /**
     * bestandsGrootte 
     * 
     * @param string $pad 
     * @return string Geformatteerde bestandsgrootte.
     */
    public static function bestandsGrootte( $pad )
    {
        $grootte = file_exists ( $pad ) ? filesize( $pad ) : 0;
        return self::formatBestandsGrootte( $grootte );
    }

    /**
     * formatBestandsGrootte 
     * 
     * @param integer $grootte Grootte van een bestand in bytes. 
     * @return string
     */
    public static function formatBestandsGrootte( $grootte )
    {
        $i = 0;
        $iec = array ( 'B' , 'KB' , 'MB' , 'GB' );
        while ( ( $grootte/1024 ) >= 1 ) {
            $grootte = $grootte/1024;
            $i++;
        }
        return round( $grootte , 2 ) . $iec[$i];
        //return substr( $grootte , 0 , strpos( $grootte , '.' )+2) . $iec[$i];
    }
}
?>
