<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_Thesaurus
 *
 * @package    KVD.thes
 * @subpackage core
 * @since      24 juni 2008
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_Thesaurus implements KVDdom_DomainObject
{

    /**
     * sessie
     *
     * @var KVDthes_ISessie
     */
    protected $sessie;

    /**
     * id
     *
     * @var integer
     */
    protected $id = 0;

    /**
     * naam
     *
     * @var string
     */
    protected $naam;

    /**
     * korte_naam
     *
     * @var string
     */
    protected $korte_naam;

    /**
     * language
     *
     * @var string
     */
    protected $language = 'Nederlands';

    /**
     * __construct
     *
     * @param KVDdom_IReadSessie $sessie
     * @param intger $id
     * @param string $naam
     * @param string $korte_naam
     * @param string $language
     * @return void
     */
    public function __construct( KVDdom_IReadSessie $sessie, $id, $naam, $korte_naam = null, $language = 'Nederlands' )
    {
        $this->sessie = $sessie;
        $this->id = $id;
        $this->naam = $naam;
        $this->korte_naam = $korte_naam;
        $this->language = $language;
        $this->sessie->registerClean( $this );
    }

    /**
     * getTerm
     *
     * @return string
     */
    public function getNaam()
    {
        return $this->naam;
    }

    /**
     * getKorteNaam
     *
     * @since 1.5
     * @return string
     */
    public function getKorteNaam()
    {
        return ($this->korte_naam === null ) ? $this->naam : $this->korte_naam;
    }

    /**
     * getId
     *
     * @return integer
     */
    public function getId( )
    {
        return $this->id;
    }

    /**
     * getLanguage
     *
     * @return string
     */
    public function getLanguage( )
    {
        return $this->language;
    }

    /**
     * getClass
     *
     * @return string
     */
    public function getClass( )
    {
        return get_class( $this );
    }

    /**
     * getOmschrijving
     *
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->naam;
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
     * isNull
     *
     * @return boolean
     */
    public function isNull()
    {
        return false;
    }

    /**
     * newNull
     *
     * @return KVDthes_NullThesaurus
     */
    public static function newNull( )
    {
        return new KVDthes_NullThesaurus( );
    }
}
?>
