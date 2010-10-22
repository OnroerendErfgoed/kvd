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
     * sa 
     * 
     * @var mixed   {@link KVDutil_Date_FuzzyDateRange_Date} of integer
     */
    protected $sa;

    /**
     * ka 
     * 
     * @var mixed   {@link KVDutil_Date_FuzzyDateRange_Date} of integer
     */
    protected $ka;

    /**
     * kb 
     * 
     * @var mixed   {@link KVDutil_Date_FuzzyDateRange_Date} of integer
     */
    protected $kb;

    /**
     * sb 
     * 
     * @var mixed   {@link KVDutil_Date_FuzzyDateRange_Date} of integer
     */
    protected $sb;

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

        $flags[] = $this->validateIsNotAfter( 'sa', 'ka' );
        $flags[] = $this->validateIsNotAfter( 'ka', 'kb' );
        $flags[] = $this->validateIsNotAfter( 'kb', 'sb' );

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
                $date = new KVDutil_Date_FuzzyDateRange_Date( $val );
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

        $this->$param = $date;
        $this->export( $date, $export );

        return true;
    }

    /**
     * validateIsNotAfter 
     * 
     * @param   string  $a  Naam van de member variable die moet vergeleken 
     *                      worden.
     * @param   string  $b  Naam van de tweede member variable.
     * @return  boolean     True indien $a voor $b komt of gelijk is aan $b.
     */
    protected function validateIsNotAfter( $a, $b )
    {
        if ( $this->$a instanceOf DateTime && $this->$b instanceOf DateTime ) {
            // We vergelijken 2 DateTimes
            $f = $this->$a;
            $s = $this->$b;
        } else {
            // Minstens 1 argument is geen datum. 
            // Herleiden tot jaartal.
            if ( $this->$a instanceOf DateTime ) {
                $f = ( integer ) $this->$a( 'y' );
            } else {
                $f = $this->$a;
            }
            if ( $this->$b instanceOf DateTime ) {
                $s = ( integer ) $this->$b( 'y' );
            } else {
                $s = $this->$b;
            }
        }

        if ( $f > $s ) {
            $this->throwError( 'ongeldige_volgorde' );
            return false;
        }

        return true;
    }

}
?>
