<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @version    $Id$
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_Thesaurus
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      24 juni 2008
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
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
     * @param string $language 
     * @return void
     */
    public function __construct( KVDdom_IReadSessie $sessie, $id, $naam, $language = 'Nederlands' )
    {
        $this->sessie = $sessie;
        $this->id = $id;
		$this->naam = $naam;
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

/**
 * KVDthes_NullTerm 
 * 
 * @package KVD.thes
 * @subpackage Core
 * @since i19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_NullThesaurus extends KVDthes_Thesaurus
{
    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct( )
    {
        $this->id = 0;
        $this->naam = 'Onbepaald';
        $this->language = 'Nederlands';
    }

    /**
     * isNull 
     * 
     * @return boolean
     */
    public function isNull()
    {
        return true;
    }
}
?>
