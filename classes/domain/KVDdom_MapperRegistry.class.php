<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Registry voor KVDdom_DataMappers
 *
 * Wanneer er een mapper gezocht wordt via getMapper wordt er gekeken of deze mapper al geladen is.
 * Indien niet wordt er aan de mapperfactory gevraagd om de mapper te laden.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDdom_MapperRegistry {

    /**
     * @var KVDdom_MapperFactory
     */
    private $mapperFactory;

    /**
     * @var array
     */
    private $mappers = array();

    private $domainObjectMappers = array( );

    /**
     * @param KVDdom_MapperFactory  $mapperFactory
     * @param array                 $domainObjectMappers
     */
    public function __construct( $mapperFactory , $domainObjectMappers = array( ) )
    {
        $this->mapperFactory = $mapperFactory;
        $this->domainObjectMappers = $domainObjectMappers;
    }

    /**
     * @param string $key Naam van de class waarvoor $domainObjectMapper een mapper is.
     * @param KVDdom_DataMapper $domainObjectMapper
     * @return void
     */ 
    private function setMapper ( $key , $domainObjectMapper , $type = null )
    {
        if ( $type === null ) {
            $this->mappers[$key] = $domainObjectMapper;
        } else {
            $this->mappers[$key][$type] = $domainObjectMapper;
        }
    }

    /**
     * @param string $key
     * @param string $type
     * @return boolean
     */
    private function isMapper ( $key , $type = null )
    {
        return array_key_exists( $key , $this->mappers) && ( ( $type !== null ) ? array_key_exists( $type, $this->mappers[$key] ) : true );
    }

    /**
     * @param string $teMappenClass Naam van de class waarvoor de mapper gevraagd wordt.
     * @param string $type          Een eventueel subtype voor deze class.
     * @return KVDdom_DataMapper    Een concrete implementatie van een KVDdom_DataMapper.
     * @throws Exception - Wanneer de gevraagde mapper niet gevonden werd.
     */
    public function getMapper( $teMappenClass, $type = null )
    {
        if ( $type === null && $this->hasSubTypes( $teMappenClass ) ) {
            $type = $this->defaultMapper( $teMappenClass );
        }
        $this->checkMapper( $teMappenClass, $type);
        return ( $type === null ) ? $this->mappers[$teMappenClass] : $this->mappers[$teMappenClass][$type];
    }

    /**
     * hasSubTypes 
     * 
     * @param string $teMappenClass 
     * @return boolean
     */
    private function hasSubTypes( $teMappenClass )
    {
        return isset( $this->domainObjectMappers[$teMappenClass] );
    }

    /**
     * defaultMapper 
     * 
     * @param string $teMappenClass 
     * @return string
     */
    private function defaultMapper( $teMappenClass )
    {
        if ( isset($this->domainObjectMappers[$teMappenClass]['mappers']['default'] ) ) {
            return $this->domainObjectMappers[$teMappenClass]['mappers']['default'];
        } else {
            throw new Exception ( sprintf ( 'Er is geen default type ingesteld voor de te mappen class %s.', $teMappenClass ) ); 
        }
    }

    /**
     * checkMapper 
     * 
     * Controleer of een bepaalde mapper al geladen is en laadt ze indien nodig.
     * @param string $teMappenClass 
     * @param string $type 
     * @return void
     */
    private function checkMapper( $teMappenClass, $type = null )
    {
        if (!$this->isMapper($teMappenClass, $type)) {
            $this->setMapper( $teMappenClass , $this->mapperFactory->createMapper( $teMappenClass, $type ) , $type );
        }
    }
}

?>
