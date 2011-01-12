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
 * KVDag_VtiMetadataValidator 
 * 
 * @package     KVD.agavi
 * @subpackage  validation
 * @since       21 okt 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_VtiMetadataValidator extends AgaviValidator
{
    /**
     * metadata 
     * 
     * @var array
     */
    protected $metadata = array(  );

    /**
     * validate 
     * 
     * @return boolean  True when valid
     */
    public function validate(  )
    {
        $flags = array(  );

        $flags[] = $this->validateType( 'type_van', 'type_van' );
        $flags[] = $this->validateType( 'type_tot', 'type_tot' );
        $flags[] = $this->validateOmschrijving( 'omschrijving_van_omschrijving', 'omschrijving_van_omschrijving', 50 );
        $flags[] = $this->validateOmschrijving( 'omschrijving_tot_omschrijving', 'omschrijving_tot_omschrijving', 50 );
        $flags[] = $this->validateOmschrijvingManueel( 'omschrijving_van_manueel', 'omschrijving_van_manueel' );
        $flags[] = $this->validateOmschrijvingManueel( 'omschrijving_tot_manueel', 'omschrijving_tot_manueel' );
        $flags[] = $this->validateOmschrijving( 'omschrijving', 'omschrijving', 110 );

        if ( $ret =  !in_array( false, $flags, true  ) ) {
            $this->exportMetadata(  );
        }

        return $ret;

    }

    /**
     * validateType 
     * 
     * @param string $param     Naam van het argument dat moet gevalideerd 
     * worden.
     * @param string $export    Naam waaronder de waarde zal worden opgeslagen 
     * in het metadata attribuut.
     * @return boolean
     */
    public function validateType($param,$export)
    {
        $arg = $this->getArgument( $param );
		$list = $this->getParameter('types','dag,maand,jaar');
		if(!is_array($list)) {
			$list = explode($this->getParameter('type_sep',','), $list);
		}
		$value = $this->getData($arg);
		
		if(!is_scalar($value)) {
			$this->throwError('ongeldig_type', $arg);
			return false;
		}
		
        $value = strtolower($value);
        $list = array_map('strtolower', $list);
		
		if(!in_array($value, $list, false)) {
			$this->throwError('ongeldig_type', $arg);
			return false;
        }

        $this->metadata[$export] = $value;

		return true;
    }

    /**
     * validateOmschrijving 
     * 
     * @param string $param     Naam van het argument
     * @param string $export    Naam waaronder het argument zal worden 
     * opgeslagen in het metadata attribuut
     * @param int $length       Maximum lengte van de omschrijving.
     * @return boolean
     */
    public function validateOmschrijving( $param, $export, $length = 50 )
    {
        $arg = $this->getArgument( $param );

		$utf8 = $this->getParameter('utf8', true);

		$originalValue =& $this->getData($arg);
		
		if(!is_scalar($originalValue)) {
			// non scalar values would cause notices
			$this->throwError('ongeldige_omschrijving',$arg);
			return false;
		}
		
        if($utf8) {
            $pattern = '/^[\pZ\pC]*+(?P<trimmed>.*?)[\pZ\pC]*+$/usDS';
        } else {
            $pattern = '/^\s*+(?P<trimmed>.*?)\s*+$/sDS';
        }
        if(preg_match($pattern, $originalValue, $matches)) {
            $originalValue = $matches['trimmed'];
        }
		
		$value = $originalValue;
		
		if($utf8) {
			$value = utf8_decode($value);
		}
		
		if(strlen($value) > $length) {
			$this->throwError('ongeldige_omschrijving',$arg);
			return false;
		}

		$this->metadata[$export] = $originalValue;

		return true;
    }

    /**
     * validateOmschrijvingManueel 
     * 
     * @param string $param     Naam van het argument
     * @param string $export    Naam waaronder de waarde wordt opgeslagen in 
     * het metadata attribuut.
     * @return boolean
     */
    public function validateOmschrijvingManueel($param,$export)
    {
        $arg = $this->getArgument( $param );

        $value = $this->getData( $arg );

		if(!is_scalar($value)) {
			// non scalar values would cause notices
			$this->throwError('ongeldige_omschrijving_manueel',$arg);
			return false;
        }

        $this->metadata[$export] = ( boolean ) $value;

        return true;
    }

    /**
     * exportMetadata 
     * 
     * Exporteer de metadata in een formaat dat kan gebruikt worden in {@link 
     * KVDutil_Date_FuzzyDateRange}.
     *
     * @return void
     */
    public function exportMetadata(  )
    {
        $this->metadata['omschrijving_van'] = array(    'omschrijving'  => $this->metadata['omschrijving_van_omschrijving'],
                                                        'manueel'       => $this->metadata['omschrijving_van_manueel'] );

        $this->metadata['omschrijving_tot'] = array(    'omschrijving'  => $this->metadata['omschrijving_tot_omschrijving'],
                                                        'manueel'       => $this->metadata['omschrijving_tot_manueel'] );
        unset( $this->metadata['omschrijving_van_omschrijving'] );
        unset( $this->metadata['omschrijving_tot_omschrijving'] );
        unset( $this->metadata['omschrijving_van_manueel'] );
        unset( $this->metadata['omschrijving_tot_manueel'] );

        $this->export( $this->metadata, $this->getParameter( 'export', 'vti_metadata' ) );
    }
}
?>
