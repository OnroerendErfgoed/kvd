<?php
/**
 * @package KVD.util
 * @version $Id$
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_HuisnummerLabelSplitter 
 * 
 * Deze class dient om huisnummerlabels uit te splitsen naar de indivduele labels. bv.:
 * <code>
 *  $splitter = new KVDutil_HuisnummerLabelSplitter( );
 *  $huisnummers = $splitter->split( '15-21' );
 *  echo $huisnummers[0]; // 15
 *  echo $huisnummers[1]; // 17
 *  echo $huisnummers[2]; // 19
 *  echo $huisnummers[3]; // 21
 * </code>
 * @package     KVD.util
 * @since       9 aug 2006
 * @deprecated  Zal verwijderd worden in de volgende versie.
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_HuisnummerLabelSplitter
{
    /**
     * @param string $huisnummerLabel
     * @return array Een array met alle huisnummer bekomen uit de splitsing van de string.
     */
    public function split( $huisnummerLabel )
    {
        $this->huisnummerLabel = $huisnummerLabel;
        $huisnummerArray = explode( ',' , $huisnummerLabel);
        $newArray = array( );
        foreach ( $huisnummerArray as $huisnummerBereik ) {
            $huisnummerBereik = trim ( $huisnummerBereik );
            if ( $this->isBusNummer( $huisnummerBereik ) ) {
                $newArray = array_merge ( $newArray, $this->extractBusBereik( $huisnummerBereik ) );
            } else if ( $this->isBisNummerBereikLetter ( $huisnummerBereik ) ) {
                $newArray = array_merge ( $newArray, $this->extractBisBereikLetter( $huisnummerBereik) );
            } else if ( $this->isBisNummerBereikCijfer( $huisnummerBereik ) ) {
                $newArray = array_merge ( $newArray, $this->extractBisBereikCijfer( $huisnummerBereik ) );    
            } else if ( $this->isHuisnummerBereik( $huisnummerBereik ) ) {
                $newArray = array_merge ( $newArray , $this->extractHuisnummerBereik( $huisnummerBereik ) );
            } else {
                $newArray = array_merge ( $newArray , array ( $huisnummerBereik ) );
            }
        }
        return $newArray;
    }

    /**
     * @param string Een huisnummerbereik.
     * @return array De nummers in het bereik van elkaar gescheiden.
     */
    private function extractHuisnummerBereik ( $huisnummerBereik )
    {
       if ( !$pos = strpos( $huisnummerBereik, '-' ) ) {
           return array ( $huisnummerBereik );
       }
       $start = trim ( substr ( $huisnummerBereik, 0, $pos ) );
       $end = trim ( substr ( $huisnummerBereik, $pos+1) );
       return $this->calculateHuisnummerBereik( $start, $end );
    }

    /**
     * @param integer $start
     * @param integer $end
     * @return array
     */
    private function calculateHuisnummerBereik( $start, $end )
    {
        $increment = ( $end - $start ) % 2 == 0 ? 2 : 1;
        $huisnummers = array( );
        for ( $i = $start; $i <= $end; $i += $increment ) {
            $huisnummers[] = ( string ) $i;
        }
        return $huisnummers;
    }

    /**
     * @param string $busbereik
     * @return array
     */
    private function extractBusBereik ( $busBereik )
    {
       if ( !$pos = strpos( $busBereik, '-' ) ) {
           return array ( $busBereik );
       }
       $elements = explode ( ' ', $busBereik);
       $huisnummer = $elements[0];
       $pos = strpos( $elements[2], '-' );
       $start = substr ( $elements[2], 0 , $pos );
       $end = substr ( $elements[2], $pos+1 );
       $busNummers = $this->calculateBereik ( $start, $end );
       foreach ( $busNummers as &$busNummer ) {
           $busNummer = $huisnummer . ' bus ' . $busNummer;
       }
       return $busNummers;
    }

    /**
     * @param mixed $start Kan een integer of een letter zijn.
     * @param mixed $end Kan een integer of een letter zijn.
     * @return array
     */
    private function calculateBereik( $start, $end)
    {
        $busNummers = array( );
        for ( $i = $start; $i <= $end; $i++ ) {
            $busNummers[] = ( string ) $i;
        }
        return $busNummers;
    }

    /**
     * @param string $bisBereik
     * @return array
     */
    private function extractBisBereikLetter( $bisBereik )
    {
        preg_match ( '/^[0-9][0-9]*/', $bisBereik,$matches );
        $huisnummer = $matches[0];
        preg_match ( '/[A-Z][-]{1}[A-Z]/', $bisBereik, $matches );
        $array = explode ( '-' , $matches[0] );
        $bisNummers = $this->calculateBereik ( $array[0], $array[1] );
        foreach ( $bisNummers as &$bisNummer ) {
            $bisNummer = $huisnummer. $bisNummer;
        }
        return $bisNummers;
    }

    /**
     * @param string $bisBereik
     * @return array
     */
    private function extractBisBereikCijfer( $bisBereik )
    {
        $array = explode ( '/', $bisBereik);
        $huisnummer = $array[0];
        $bereikNummers = $array[1];
        $array = explode ( '-', $bereikNummers );
        $bisNummers = $this->calculateBereik ( $array[0], $array[1] );
        foreach ( $bisNummers as &$bisNummer ) {
            $bisNummer = $huisnummer .'/'. $bisNummer;
        }
        return $bisNummers;
    }
    
    /**
     * @param string $huisnummer
     * @return boolean
     */
    private function isBusNummer( $huisnummer ) 
    {
        return strpos( $huisnummer, 'bus');
    }

    /**
     * @param string $huisnummer
     * @return boolean
     */
    private function isBisNummerBereik ( $huisnummer ) 
    {
        return $this->isBisNummerBereikLetter( $huisnummer ) || $this->isBisNummerBereikCijfer( $huisnummer );
    }

    /**
     * @param string $huisnummer
     * @return boolean
     */
    private function isBisNummerBereikLetter( $label )
    {
        return ( preg_match( '/^[0-9][0-9]*[A-Z][-]{1}[A-Z]/', $label ) )? true : false;
    }

    /**
     * @param string $huisnummer
     * @return boolean
     */
    private function isBisNummerBereikCijfer( $label )
    {
        return ( preg_match( '/^[0-9][0-9]*[\/]{1}[0-9]+[-]{1}[0-9]+/', $label ) ) ? true : false;
    }

    /**
     * param string $label
     * @return boolean
     */
    private function isHuisnummerBereik ( $label ) 
    {
        return ( preg_match( '/^[\d][\d\s]*[-]{1}[\d\s]*/'  , $label ) ) ? true : false;
    }

}
?>
