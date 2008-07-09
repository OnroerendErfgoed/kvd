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
class KVDdo_AdrStraat extends KVDdom_ReadonlyDomainObject {
    
    /**
     * @var string
     */
    protected $naam;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var KVDdo_AdrGemeente
     */
    protected $gemeente;

    /**
     * Collectie van alle huisnummers in deze straat.
     * @var KVDdom_DomainObjectCollection
     */
    protected $huisnummers;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param string $naam
     * @param string $label
     * @param KVDdo_AdrGemeente $gemeente
     * @param KVDdom_DomainObjectCollection $huisnummers
     */
    public function __construct ( $id , $sessie , $naam, $label, $gemeente, $huisnummers = null )
    {
        parent::__construct ( $id , $sessie);
        $this->naam = $naam;
        $this->label = $label;
        $this->gemeente = $gemeente;
        $this->huisnummers = ( $huisnummers === null ) ? self::PLACEHOLDER : $huisnummers;
    }

    /**
     * @return string
     */
    public function getStraatNaam( )
    {
        return $this->naam;
    }

    /**
     * @return string
     */
    public function getStraatLabel( )
    {
        return $this->label;
    }

    /**
     * @return KVDdo_AdrGemeente
     */
    public function getGemeente( )
    {
        return $this->gemeente;
    }

    /**
     * Een collectie van KVDdo_AdrHuisnummers.
     * @return KVDdom_DomainObjectCollection
     */
    public function getHuisnummers( )
    {
        if ( $this->huisnummers === self::PLACEHOLDER ) {
            $huisnummerMapper = $this->_sessie->getMapper( 'KVDdo_AdrHuisnummer');
            $this->huisnummers = $huisnummerMapper->findByStraat( $this );
            
        }
        return $this->huisnummers;    
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->naam;
    }

    /**
     * @return KVDdo_NullAdrStraat
     */
    public function newNull( )
    {
        return new KVDdo_NullAdrStraat( );
    }
}

/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since maart 2006
 */
class KVDdo_NullAdrStraat extends KVDdo_AdrStraat
{
    public function __construct( $gemeente = null )
    {
        $this->id = 0;
        $this->naam = 'Onbepaald';
        $this->label = 'Onbepaald';
        $this->gemeente = ( is_null( $gemeente ) || !( $gemeente instanceof KVDdo_AdrGemeente ) ) ? KVDdo_AdrGemeente::newNull( ) : $gemeente;
        $this->huisnummers = new KVDdom_DomainObjectCollection( array( ) );
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
        return 'KVDdo_AdrStraat';
    }
}
?>
