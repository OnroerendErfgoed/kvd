<?php
/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since maart 2006
 */
class KVDdo_AdrProvincie extends KVDdom_ReadonlyDomainObject{

    /**
     * @var string
     */
    protected $naam;

    /**
     * @var KVDdom_DomainObjectCollection
     */
    protected $gemeenten;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param string $naam
     * @param KVDdom_DomainObjectCollection $gemeenten
     */
    public function __construct ( $id , $sessie , $naam, $gemeenten = null )
    {
        parent::__construct ( $id , $sessie);
        $this->naam = $naam;
        $this->gemeenten = ( $gemeenten === null ) ? self::PLACEHOLDER : $gemeenten;
    }
    
    /**
     * @return string
     */
    public function getProvincieNaam( )
    {
        return $this->naam;
    }

    /**
     * @return KVDdom_DomainObjectCollection
     */
    public function getGemeenten( )
    {
        if ( $this->gemeenten === self::PLACEHOLDER ) {
            $gemeentenMapper = $this->_sessie->getMapper( 'KVDdo_AdrGemeente');
            $this->gemeenten = $gemeentenMapper->findByProvincie( $this );
            
        }
        return $this->gemeenten;    
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
     * @since   22 aug 2008
     * @return  string
     */
    public function getVolledigeOmschrijving( )
    {
        return $this->naam;
    }

    /**
     * @return KVDdo_NullAdrProvincie( )
     */
    static public function newNull( )
    {
        return new KVDdo_NullAdrProvincie( );
    }
}

/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 25 jul 2006
 */
class KVDdo_NullAdrProvincie extends KVDdo_AdrProvincie
{
    public function __construct ( )
    {
        $this->id = 0;
        $this->naam = 'Onbepaald';
        $this->gemeenten = new KVDdom_DomainObjectCollection( array( ) );
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
        return 'KVDdo_AdrProvincie';
    }
}
?>
