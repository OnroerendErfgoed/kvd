<?php
/**
 * @package     KVD.util
 * @subpackage  Date
 * @version     $Id$
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_Date_FuzzyDateRange 
 * 
 * @package     KVD.util
 * @since       20 okt 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Date_FuzzyDateRange
{
    /**
     * Datum waaronder alles als jaartal wordt gezien. 
     */
    const MIN_DATUM = '0100-01-01';

    /**
     * Datum waarboven alles als jaartal wordt gezien. 
     */
    const MAX_DATUM = '2099-12-31';

    /**
     * punten 
     * 
     * @var array
     */
    protected $punten = array( );

    /**
     * metadata 
     * 
     * @var array
     */
    protected $metadata = array( );

    /**
     * construct 
     * 
     * @param mixed $sa         DateTime of integer
     * @param mixed $ka         DateTime of integer
     * @param mixed $kb         DateTime of integer
     * @param mixed $sb         DateTime of integer
     * @param array $metadata 
     * @return void
     */
    public function construct( $sa, $ka, $kb, $sb, array $metadata = array( ) )
    {
        $this->punten['sa'] = $this->corrigeerX( $sa );
        $this->punten['ka'] = $this->corrigeerX( $ka );
        $this->punten['kb'] = $this->corrigeerX( $kb );
        $this->punten['sb'] = $this->corrigeerX( $sb );
    }

    /**
     * corrigeerX 
     * 
     * @param   mixed   $punt 
     * @return  mixed   DateTime of integer
     */
    private function corrigeerX( $punt )
    {
        if ( $punt instanceof DateTime ) {
            if ( $punt < new DateTime( self::MIN_DATE ) || $punt > new DateTime( self::MAX_DATE )) {
                return ( integer ) $punt->format( 'y' );
            }
        } else {
            return ( integer ) $punt;
        }
    }

    /**
     * setMetadata 
     * 
     * @param   array $metadata 
     * @return  void
     */
    protected function setMetadata( array $metadata = array(  ) )
    {
        if ( !array_key_exists( 'omschrijving_van', $metadata ) ) {
            $metadata['omschrijving_van'] = array ( 'omschrijving' => '', 'manueel' => false );
        }
        if ( !array_key_exists( 'omschrijving_tot', $metadata ) ) {
            $metadata['omschrijving_tot'] = array ( 'omschrijving' => '', 'manueel' => false );
        }
        if ( !array_key_exists( 'type_van', $metadata ) ) {
            $metadata['type_van'] = $this->determineerType( 'van' );
        }
        if ( !array_key_exists( 'type_tot', $metadata ) ) {
            $metadata['type_tot'] = $this->determineerType( 'tot' );
        }
        $this->metadata = $metadata;
    }

    /**
     * determineerType 
     * 
     * @param   string $voor 
     * @return  void
     */
    protected function determineerType( $voor = 'van' )
    {
        $punten = ( $voor == 'van' ) ?   array( 'a' =>  $this->punten['sa'], 'b' => $this->punten['ka'] ) :
            array( 'a' =>  $this->punten['kb'], 'b' => $this->punten['sb'] );
        if( $punten['a'] == $punten['b'] ) {
            return $punten['a'] instanceof DateTime ? 'dag' : 'jaar';
        } 
        if( $punten['a'] < $punten['b'] ) {
            if ( $punten['a'] instanceOf DateTime && $punten['b'] instanceOf DateTime ) {
                if ( $punten['a']->format( 'MM-DD' ) == '01-01' && $punten['b']->format( 'MM-DD') == '12-31' ) {
                    return 'jaar';
                } else {
                    return 'maand';
                }
            }
        }
        return 'onbepaald';
    }

    /**
     * getSa 
     * 
     * @return mixed    DateTime of integer
     */
    public function getSa(  ) 
    {
        return $this->punten['sa'];
    }

    /**
     * getKa 
     * 
     * @return mixed    DateTime of integer
     */
    public function getKa(  ) 
    {
        return $this->punten['ka'];
    }

    /**
     * getKb 
     * 
     * @return mixed    DateTime of integer
     */
    public function getKb(  )
    {
        return $this->punten['kb'];
    }

    /**
     * getSa 
     * 
     * @return mixed    DateTime of integer
     */
    public function getSa(  )
    {
        return $this->punten['sa'];
    }

    /**
     * getOmschrijvingVan 
     * 
     * @return string
     */
    public function getOmschrijvingVan(  )
    {
        return $this->metadata['omschrijving_van']['omschrijving'];
    }

    /**
     * isOmschrijvingVanManueel 
     * 
     * @return boolean
     */
    public function isOmschrijvingVanManueel(  )
    {
        return $this->metadata['omschrijving_van']['manueel'];
    }

    /**
     * getOmschrijvingTot 
     * 
     * @return string
     */
    public function getOmschrijvingTot(  )
    {
        return $this->metadata['omschrijving_tot']['omschrijving'];
    }

    /**
     * isOmschrijvingTotManueel 
     * 
     * @return boolean
     */
    public function isOmschrijvingTotManueel(  )
    {
        return $this->metadata['omschrijving_tot']['manueel'];
    }

    /**
     * getTypeVan 
     * 
     * @return string
     */
    public function getTypeVan(  )
    {
        return $this->metadata['type_van'];
    }

    /**
     * getTypeTot 
     * 
     * @return string
     */
    public function getTypeTot(  )
    {
        return $this->metadata['type_tot'];
    }

    /**
     * getOmschrijving 
     * 
     * @return string
     */
    public function getOmschrijving(  )
    {
        return $this->getOmschrijvingVan( ) . ' - ' . $this->getOmschrijvingTot( );
    }

    /**
     * __toString 
     * 
     * @return string
     */
    public function __toString(  )
    {
        return $this->getOmschrijving(  );
    }

}
?>
