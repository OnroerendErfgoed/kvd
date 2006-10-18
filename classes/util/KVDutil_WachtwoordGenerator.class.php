<?php
/**
 * @package KVD.util 
 * @version $Id$
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_WachtwoordGenerator 
 * 
 * @package KVD.util 
 * @since 18 okt 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_WachtwoordGenerator
{
    /**
     * De standaard-lengte van een wachtwoord.
     * @var integer 
     */
    const LENGTE = 8;
    
    /**
     * lengte 
     * 
     * @var integer
     */
    private $lengte;

    /**
     * hoofdletters 
     * 
     * @var boolean
     */
    private $hoofdlettersGebruiken;

    /**
     * teGebruikenTekens 
     * 
     * @var array
     */
    private $teGebruikenTekens = array (    '0123456789',
                                            'abcdefghijklmnopqrstuvwxyz',
                                            'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    
    /**
     * @param $lengte integer Lengte van het aan te maken wachtwoord.
     * @param $hoofdletters boolean Of het wachtwoord hoofdletters moet bevatten.
     * @throws <b>InvalidArgumentException</b> Indien er ongeldige parameters worden doorgegeven.
     */
    public function __construct ( $lengte = self::LENGTE , $hoofdletters = false )
    {
        if ( !is_int( $lengte ) ) {
            throw new InvalidArgumentException ( 'Lengte moet een getal zijn.' ); 
        }
        if ( $lengte < 6 ) {
            throw new InvalidArgumentException ( 'Een wachtwoord mag nooit minder dan 6 tekens bevatten.' );
        }
        $this->lengte = $lengte;
        $this->hoofdlettersGebruiken = $hoofdletters;
    }
    
    /**
     * generate 
     * 
     * @return string Een geldig paswoord
     * @throws <b>RuntimeException</b> Indien het niet mogelijk is een geldig paswoord aan te maken.
     */
    public function generate( )
    {
        $counter = 0;
        do {
            $wachtwoord = $this->generateWachtwoord( );
            $counter++;
            if ( $counter > 10 ) {
                throw new Exception ( 'Kan geen geldig paswoord genereren na 10 pogingen.');
            }
        } while ( !$this->validateWachtwoord( $wachtwoord) );
        return $wachtwoord;
    }

    /**
     * generateWachtwoord 
     *
     * Genereert een enkel wachtwoord dat mogelijk niet aan alle regels voldoet.
     * @return string
     */
    private function generateWachtwoord ( )
    {
        $buffer = '';
        $maxStrings = $this->hoofdlettersGebruiken ? 2 : 1; 
        for ( $i=0 ; $i<$this->lengte ; $i++) {
            $str = $this->teGebruikenTekens[mt_rand( 0 , $maxStrings ) ];
            $maxTeken = strlen( $str ) -1;
            $buffer .= $str[mt_rand( 0 , $maxTeken ) ];
        }
        return $buffer;
    }

    /**
     * validateWachtwoord 
     * 
     * @param string $wachtwoord 
     * @return boolean
     */
    private function validateWachtwoord( $wachtwoord )
    {
        if ( strlen( $wachtwoord ) != $this->lengte ) {
            return false;
        }
        if ( !preg_match( '/[0-9]+/' , $wachtwoord ) ) {
            return false;
        }
        if ( !preg_match( '/[a-z]+/' , $wachtwoord ) ) {
            return false;
        }
        if ( $this->hoofdlettersGebruiken && !preg_match( '/[A-Z]+/' , $wachtwoord ) ) {
            return false;
        }
        return true;
    }
}
?>
