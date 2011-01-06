<?php
/**
 * @package KVD.dom
 * @version $Id$
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_MapperRegistry 
 * 
 * Wanneer er een mapper gezocht wordt via getMapper wordt er gekeken of deze mapper al geladen is.
 * Indien niet wordt er aan de mapperfactory gevraagd om de mapper te laden.
 * @package KVD.dom
 * @since 2005
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_MapperRegistry 
{

    /**
     * @var KVDdom_MapperFactory
     */
    private $mapperFactory;

    /**
     * @var array
     */
    private $mappers = array();

    /**
     * domainObjectMappers 
     * 
     * @var array
     */
    private $domainObjectMappers = array( );

    /**
     * __construct
     *
     * Een geldige $domainObjectMappers array ziet er als volgt uit heeft een sleutel voor elk domainObject die een array bevat
     * met de sleutel mappers die terug een array bevat met als sleutels de namen van de geldige subtype. Er mag ook een
     * speciale sleutel gedefinieerd worden die aangeeft op welk subtype een mapper terugvalt als er geen subtype gekozen is.
     * Voorbeeld:
     * <code>
     * $domainObjectMappers = array ( 'DIBEdo_Typologie' => array ( 'mappers' => array ( 'default'  => 'xml',
     *                                                                                   'db'       => '',
     *                                                                                   'xml'      => '') ) );
     * </code>
     * @param KVDdom_MapperFactory  $mapperFactory          Een mapperfactory die de mappers die nog niet geladen zijn aanmaakt.
     * @param array                 $domainObjectMappers    Een array dat domeinobject bevat die meerdere mappers kunnen hebben.
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
     * @return KVDdom_DataMapper    Een concrete implementatie van een KVDdom_DataMapper.
     * @throws LogicException - Indien er geen standaard type is ingesteld voor een bepaalde te mappen class.
     * @throws Exception - Wanneer de gevraagde mapper niet gevonden werd.
     */
    public function getMapper( $teMappenClass )
    {
        $type = $this->defaultMapper( $teMappenClass );
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
     * subTypeExists 
     * 
     * Nagaan of een bepaald subtype gekend is voor de teMappenClass.
     * @since 10 jan 2008
     * @param string $teMappenClass 
     * @param string $type 
     * @return boolean
     */
    private function subTypeExists( $teMappenClass, $type )
    {
        return isset( $this->domainObjectMappers[$teMappenClass]['mappers'][$type]);
    }

    /**
     * defaultMapper 
     * 
     * @param string $teMappenClass 
     * @return string
     * @throws LogicException Indien er geen standaard type is ingesteld voor een bepaalde te mappen class.
     */
    private function defaultMapper( $teMappenClass )
    {
        if ( !isset( $this->domainObjectMappers[$teMappenClass] ) ) {
            return null;
        }
        if ( isset($this->domainObjectMappers[$teMappenClass]['mappers']['default'] ) ) {
            return $this->domainObjectMappers[$teMappenClass]['mappers']['default'];
        } else {
            throw new LogicException ( sprintf ( 'Er is geen default type ingesteld voor de te mappen class %s.', $teMappenClass ) ); 
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

    /**
     * setDefaultMapper 
     * 
     * @since 10 jan 2008
     * @param string $teMappenClass 
     * @param string $type 
     * @throws <b>LogicException</b>    Indien u een standaard type probeert in te stellen voor een class die maar 1 mapper heeft 
     *                                  of indien u een ongekend type opgeeft.
     * @return void
     */
    public function setDefaultMapper( $teMappenClass, $type )
    {
        if ( !$this->hasSubTypes( $teMappenClass ) ) {
            throw new LogicException( sprintf( 'U probeert een default type in te stellen voor de te mappen class %s. 
                                        Deze heeft echter geen geen gekende subtypes.', $teMappenClass ) );
        }
        if ( !$this->subTypeExists( $teMappenClass, $type ) ) {
            throw new LogicException ( sprintf( 'U probeert een standaard type in te stellen voor de te mappen class %s.
                                                Het standaard type %s dat u gekozen heeft is echter geen gekend subtype.',
                                                $teMappenClass, $type ) );
        }
        $this->domainObjectMappers[$teMappenClass]['mappers']['default'] = $type;

    }
}
?>
