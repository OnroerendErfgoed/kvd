<?php
/**
 * @package KVD.util
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.util
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 9 aug 2006
 */

class KVDutil_HuisnummerLabelSplitter
{
    /**
     * @param string $huisnummerLabel
     */
    public function split( $huisnummerLabel )
    {
        $this->huisnummerLabel = $huisnummerLabel;
        $huisnummerArray = explode( ',' , $huisnummerLabel);
        $newArray = array( );
        foreach ( $huisnummerArray as $huisnummerBereik ) {
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
       $start = substr ( $huisnummerBereik, 0, $pos );
       $end = substr ( $huisnummerBereik, $pos+1);
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
        return ( preg_match( '/^[0-9][0-9]*[-]{1}[0-9]*/'  , $label ) ) ? true : false;
    }

}
?>
