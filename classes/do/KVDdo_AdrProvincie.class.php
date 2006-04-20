<?php
/**
 * @package KVD.do.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.do.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdo_AdrProvincie extends KVDdom_ReadonlyDomainObject{

    /**
     * @var string
     */
    private $naam;

    /**
     * @var KVDdom_DomainObjectCollection
     */
    private $_gemeenten;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param string $naam
     * @param KVDdom_DomainObjectCollection $gemeenten
     */
    public function __construct ( $id , $sessie , $naam = 'Onbepaald', $gemeenten = null )
    {
        parent::__construct ( $id , $sessie);
        $this->naam = $naam;
        $this->_gemeenten = ( $gemeenten === null ) ? self::PLACEHOLDER : $gemeenten;
    }

    public function getProvincieNaam( )
    {
        return $this->naam;
    }

    public function getGemeenten( )
    {
        if ( $this->_gemeenten === self::PLACEHOLDER ) {
            $gemeentenMapper = $this->_sessie->getMapper( 'KVDdo_AdrGemeente');
            $this->_gemeenten = $gemeentenMapper->findByProvincie( $this->getId( ) );
            
        }
        return $this->_gemeenten;    
    }

    public function getOmschrijving( )
    {
        return $this->naam;
    }
}
?>
