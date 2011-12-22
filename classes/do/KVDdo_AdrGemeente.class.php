<?php
/**
 * @package     KVD.do
 * @subpackage  Adr
 * @version     $Id$
 * @copyright   2006-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * @package     KVD.do
 * @subpackage  Adr
 * @since       maart 2006
 * @copyright   2006-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdo_AdrGemeente extends KVDdom_ReadonlyDomainObject {
    
    /**
     * Het id dat voor de crab-webservice gebruikt wordt.
     * @var integer
     */
    protected $crabId;

    /**
     * @var string
     */
    protected $naam;

    /**
     * @var KVDdo_AdrProvincie
     */
    protected $provincie;

    /**
     * Collectie van alle straten in deze gemeente.
     * @var KVDdom_DomainObjectCollection
     */
    protected $straten;

    /**
     * Collectie van alle deelgemeenten in deze gemeente.
     * @var KVDdom_DomainObjectCollection
     */
    protected $deelgemeenten;

    /**
     * kadastergemeenten 
     * 
     * @var KVDdom_DomainObjectCollection
     */
    protected $kadastergemeenten;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param string $naam
     * @param integer $crabId
     * @param KVddo_AdrProvincie $provincie
     * @param KVDdom_DomainObjectCollection $straten
     * @param KVDdom_DomainObjectCollection $deelgemeenten
     */
    public function __construct ( $id , $sessie , $naam = 'Onbepaald', $crabId = 0, $provincie = null, $straten = null, $deelgemeenten = null)
    {
        parent::__construct ( $id , $sessie);
        $this->naam = $naam;
        $this->crabId = $crabId;
        $this->provincie = ( $provincie === null ) ? new KVDdo_AdrProvincie( 0 , $sessie) : $provincie;
        $this->straten = ( $straten === null ) ? self::PLACEHOLDER : $straten;
        $this->deelgemeenten = ( $deelgemeenten === null ) ? self::PLACEHOLDER : $deelgemeenten;
        $this->kadastergemeenten = self::PLACEHOLDER;
    }

    /**
     * @return string
     */
    public function getGemeenteNaam( )
    {
        return $this->naam;
    }

    /**
     * @return integer
     */
    public function getCrabId( )
    {
        return $this->crabId;
    }

    /**
     * @return KVDdo_AdrProvincie
     */
    public function getProvincie( )
    {
        return $this->provincie;
    }

    /**
     * Een collectie van KVDdo_AdrStraten.
     * @return KVDdom_DomainObjectCollection
     */
    public function getStraten( )
    {
        if ( $this->straten === self::PLACEHOLDER ) {
            $stratenMapper = $this->_sessie->getMapper( 'KVDdo_AdrStraat');
            $this->straten = $stratenMapper->findByGemeente( $this );
            
        }
        return $this->straten;    
    }

    /**
     * Een collectie van KVDdo_Deelgemeente objecten.
     * @return KVDdom_DomainObjectCollection
     */
    public function getDeelgemeenten( )
    {
        if ( $this->deelgemeenten === self::PLACEHOLDER ) {
            $mapper = $this->_sessie->getMapper( 'KVDdo_AdrDeelgemeente' );
            $this->deelgemeenten = $mapper->findByGemeente( $this , 'deelgemeenteNaam');
        }
        return $this->deelgemeenten;
    }

    /**
     * getKadastergemeenten 
     * 
     * @since 31 aug 2007
     * @return KVDdom_DomainObjectCollection
     */
    public function getKadastergemeenten( )
    {
        if ( $this->kadastergemeenten === self::PLACEHOLDER ) {
            $mapper = $this->_sessie->getMapper( 'KVDdo_AdrKadastergemeente' );
            $this->kadastergemeenten = $mapper->findByGemeente( $this , 'afdeling' );
        }
        return $this->kadastergemeenten;
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->naam;
    }

    /**
     * getVolledigeOmschrijving 
     * 
     * @since 14 maart 2007
     * @return string
     */
    public function getVolledigeOmschrijving( )
    {
        return $this->provincie->getVolledigeOmschrijving( ) . ' > ' . $this->naam;
    }

    /**
     * @return KVDdo_NullAdrProvincie
     */
    public static function newNull( KVDdo_AdrProvincie $provincie = null )
    {
        return new KVDdo_NullAdrGemeente( $provincie );
    }
}

/**
 * KVDdo_NullAdrGemeente 
 * 
 * @package     KVD.do
 * @subpackage  Adr
 * @since       25 jul 2006
 * @copyright   2006-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdo_NullAdrGemeente extends KVDdo_AdrGemeente
{
    /**
     * @param KVDdo_AdrProvincie
     */
    public function __construct ( KVDdo_AdrProvincie $provincie = null ) 
    {
        $this->provincie = ( $provincie === null ) ? KVDdo_AdrProvincie::newNull() : $provincie;
        $this->naam = 'Onbepaald';
        $this->crabId = 0;
        $this->id = ( $provincie === null ) ? null : $provincie->getId( ) ;
        $this->straten = new KVDdom_DomainObjectCollection( array( ) );
        $this->deelgemeenten = new KVDdom_DomainObjectCollection( array( ) );
        $this->kadastergemeente = new KVDdom_DomainObjectCollection( array( ) );
    }
    
    /**
     * @return boolean
     */
    public function isNull( )
    {
        return true;
    }

    /**
     * @return string
     */
    public function getClass( )
    {
        return 'KVDdo_AdrGemeente';
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->naam;
    }

    /**
     * getVolledigeOmschrijving 
     * 
     * @since 14 maart 2007
     * @return string
     */
    public function getVolledigeOmschrijving( )
    {
        return $this->provincie->getOmschrijving( ) . ' > ' . $this->naam;
    }

}
?>
