<?php
/**
 * @package     KVD.agavi
 * @subpackage  validation
 * @version     $Id$
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDag_VtiValidator 
 * 
 * @package     KVD.agavi
 * @subpackage  validation
 * @since       21 okt 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_VtiValidator extends AgaviValidator
{

    /**
     * validate 
     * 
     * @return boolean  True when valid
     */
    public function validate(  )
    {
        $flags = array(  );

        $flags[] = $this->validateValue( 'sa', 'sa' );
        $flags[] = $this->validateValue( 'ka', 'ka' );
        $flags[] = $this->validateValue( 'kb', 'kb' );
        $flags[] = $this->validateValue( 'sb', 'sb' );

        return !in_array( false, $flags, true  );
    }

    /**
     * validateValue 
     * 
     * @param string $param     Naam van een argument
     * @param string $export    Naam waaronder het argument na validatie wordt 
     *                          geexporteerd.
     * @return boolean  Indien de waarde geldig is of niet.
     */
    protected function validateValue( $param, $export ) 
    {
        $arg = $this->getArgument( $param );

        $val = $this->getData( $arg );

        if ( is_int( $val ) ) {
            $date = $year;
        } elseif ( preg_match( '#\d{4}-\d{2}-\d{2}#', $val ) == 1 ) {
            try {
                $date = new DateTime( $val );
            } catch( Exception $e ) {
                $this->throwError( 'ongeldige_datum', $arg );
                return false;
            }
        } else {
            $this->throwError( 'ongeldige_datum', $arg );
            return false;
        }


        if ( $date instanceof DateTime ) {
            if ( $date < new DateTime( $this->getParameter( 'min_datum', '0100-01-01' ) ) || $date > new DateTime( $this->getParameter( 'max_datum', '2099-12-13' ) ) ) {
                $date = ( integer ) $date->format( 'y' );
            }
        }

        if ( is_int( $date ) ) {
            if ( $date < $this->getParameter( 'min_jaar', '-1000000' ) || $date > $this->getParameter( 'max_jaar', '2100' ) ) {
                $this->throwError( 'ongeldig_jaar' );
                return false;
            }
        }

        $this->export( $date, $export );

        return true;
    }

}
?>
