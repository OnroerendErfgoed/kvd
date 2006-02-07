<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_MapperRegistry.class.php,v 1.1 2006/01/12 14:46:02 Koen Exp $
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
    private $_mapperFactory;

    /**
     * @var array
     */
    private $mappers = array();

    /**
     * @param KVDdom_MapperFactory $mapperFactory
     */
    public function __construct( $mapperFactory )
    {
        $this->_mapperFactory = $mapperFactory;
    }

    /**
     * @param string $key Naam van de class waarvoor $domainObjectMapper een mapper is.
     * @param KVDdom_DataMapper $domainObjectMapper
     * @return void
     */ 
    protected function setMapper ( $key , $domainObjectMapper )
    {
        $this->mappers[$key] = $domainObjectMapper;
    }

    /**
     * @param string $key
     * @return boolean
     */
    private function isMapper ( $key )
    {
        return array_key_exists( $key , $this->mappers);
    }

    /**
     * @param string $teMappenClass Naam van de class waarvoor de mapper gevraagd wordt.
     * @return KVDdom_DataMapper Een concrete implementatie van een KVDdom_DataMapper.
     * @throws Exception - Wanneer de gevraagde mapper niet gevonden werd.
     */
    public function getMapper( $teMappenClass )
    {
        if (!$this->isMapper($teMappenClass)) {
            $this->setMapper( $teMappenClass , $this->_mapperFactory->createMapper( $teMappenClass ) );
        }
        return $this->mappers[$teMappenClass];
    }
}

?>
