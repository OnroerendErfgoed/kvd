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
 * @subpackage  Date
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
     * @param mixed $sa         KVDutil_Date_FuzzyDateRange_Date, string of integer
     * @param mixed $ka         KVDutil_Date_FuzzyDateRange_Date, string of integer
     * @param mixed $kb         KVDutil_Date_FuzzyDateRange_Date, string of integer
     * @param mixed $sb         KVDutil_Date_FuzzyDateRange_Date, string of integer
     * @param array $metadata 
     * @return void
     */
    public function __construct( $sa = null, $ka = null , $kb=null, $sb=null, array $metadata = array( ) )
    {
        $this->punten['sa'] = $this->corrigeerX( $sa );
        $this->punten['ka'] = $this->corrigeerX( $ka );
        $this->punten['kb'] = $this->corrigeerX( $kb );
        $this->punten['sb'] = $this->corrigeerX( $sb );
        $this->setMetadata( $metadata );
    }

    /**
     * corrigeerX 
     * 
     * @param   mixed   $punt 
     * @return  mixed   {@link KVDutil_Date_FuzzyDateRange_Date}, string of integer
     */
    protected function corrigeerX( $punt )
    {
        if ( is_string( $punt ) && preg_match( '#\d{4}-\d{2}-\d{2}#', $punt ) == 1 ) {
            $punt = new KVDutil_Date_FuzzyDateRange_Date( $punt );
        }
        if ( $punt instanceof KVDutil_Date_FuzzyDateRange_Date ) {
            if ( $punt < new DateTime( self::MIN_DATUM ) || $punt > new DateTime( self::MAX_DATUM )) {
                return ( integer ) $punt->format( 'y' );
            } else {
                return $punt;
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
            return $punten['a'] instanceof KVDutil_Date_FuzzyDateRange_Date ? 'dag' : 'jaar';
        } 
        if( $punten['a'] < $punten['b'] ) {
            if ( $punten['a'] instanceOf KVDutil_Date_FuzzyDateRange_Date && $punten['b'] instanceOf KVDutil_Date_FuzzyDateRange_Date ) {
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
     * getSb
     * 
     * @return mixed    DateTime of integer
     */
    public function getSb(  )
    {
        return $this->punten['sb'];
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
        return (bool) $this->metadata['omschrijving_van']['manueel'];
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
        return (bool) $this->metadata['omschrijving_tot']['manueel'];
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
     * getObject 
     * 
     * Een voorstelling van de FuzzyDateRange als een object.
     * @return stdClass
     */
    public function getObject(  )
    {
        $o = new stdClass(  );
        foreach ( $this->metadata as $k => $v ) {
            if ( is_array( $v ) ) {
                $tmp = new stdClass( );
                foreach ( $v as $tk => $tv ) {
                    $tmp->$tk = ( string ) $tv;
                }
                $o->$k = $tmp;
            } else {
                $o->$k = ( string ) $v;
            }
        }
        foreach( $this->punten as $k => $v ) {
            $o->$k = ( string ) $v;
        }
        return $o;
    }

    /**
     * __toString 
     * 
     * @return string
     */
    public function __toString( )
    {
        return $this->getOmschrijving( );
    }

    /**
     * newNull 
     * 
     * @return KVDutil_Date_FuzzyDateRange
     */
    public static function newNull(  )
    {
        return new KVDutil_Date_FuzzyDateRange();
    }

}

/**
 * KVDutil_Date_FuzzyDateRange_Date 
 * 
 * @package     KVD.util
 * @subpackage  Date
 * @since       20 okt 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Date_FuzzyDateRange_Date extends DateTime
{
    const FORMAT = 'Y-m-d';

    /**
     * __toString 
     * 
     * @return string
     */
    public function __toString( )
    {
        return (string) parent::format( self::FORMAT );
    }
}
?>
