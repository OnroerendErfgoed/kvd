<?php
/**
 * @package KVD.do.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.do.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 21 jun 2006
 */
class KVDdo_AdrDeelgemeente extends KVDdom_ReadonlyDomainObject {
    
    /**
     * @var string
     */
    private $naam;

    /**
     * @var KVDdo_AdrGemeente
     */
    private $_gemeente;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param string $naam
     * @param KVddo_AdrGemeente $gemeente
     */
    public function __construct ( $id , $sessie , $naam = 'Onbepaald', $gemeente = null )
    {
        parent::__construct ( $id , $sessie);
        $this->naam = $naam;
        $this->_gemeente = ( $gemeente === null ) ? new KVDdo_AdrGemeente( 0 , $sessie) : $gemeente;
    }

    /**
     * @return string
     */
    public function getDeelgemeenteNaam( )
    {
        return $this->naam;
    }

    /**
     * @return KVDdo_AdrGemeente
     */
    public function getGemeente( )
    {
        return $this->_gemeente;
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->naam;
    }
}
?>
