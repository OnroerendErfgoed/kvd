<?php
/**
 * @package KVD.do
 * @subpackage Adr
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdo_AdrKadastergemeente
 *
 * @package KVD.do
 * @subpackage Adr
 * @since 31 aug 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdo_AdrKadastergemeente extends KVDdom_ReadonlyDomainObject {

    /**
     * @var string
     */
    protected $naam;

    /**
     * afdeling
     *
     * @var integer
     */
    protected $afdeling;

    /**
     * @var KVDdo_AdrGemeente
     */
    protected $gemeente;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param integer $afdeling
     * @param string $naam
     * @param KVddo_AdrGemeente $gemeente
     */
    public function __construct ( $id , $sessie , $afdeling, $naam , $gemeente )
    {
        parent::__construct ( $id , $sessie);
        $this->afdeling = $afdeling;
        $this->naam = $naam;
        $this->gemeente = $gemeente;
    }

    /**
     * getAfdeling
     *
     * @return integer
     */
    public function getAfdeling( )
    {
        return $this->afdeling;
    }

    /**
     * @return string
     */
    public function getKadastergemeenteNaam( )
    {
        return $this->naam;
    }

    /**
     * @return KVDdo_AdrGemeente
     */
    public function getGemeente( )
    {
        return $this->gemeente;
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
     * @return string
     */
    public function getVolledigeOmschrijving( )
    {
        return $this->gemeente->getGemeenteNaam( ) . ', afdeling ' . $this->afdeling . '(' . $this->naam . ')';
    }

    /**
     * @param mixed $gemeente Een NullKadastergemeente kan behoren aan een geldige gemeente.
     * @return KVDdo_NullAdrKadastergemeente
     */
    public static function newNull( $gemeente = null )
    {
        return new KVDdo_NullAdrKadastergemeente( $gemeente );
    }
}

/**
 * KVDdo_NullAdrKadastergemeente
 *
 * @package KVD.do
 * @subpackage Adr
 * @since 31 aug 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdo_NullAdrKadastergemeente extends KVDdo_AdrKadastergemeente
{
    /**
     * @param mixed $gemeente Ofwel de gemeente waartoe de NullKadastergemeente behoort ofwel null
     */
    public function __construct( $gemeente = null )
    {
        $this->gemeente = ( is_null( $gemeente ) || !( $gemeente instanceof KVDdo_AdrGemeente ) ) ? KVDdo_AdrGemeente::newNull( ) : $gemeente;
        $this->afdeling = null;
        $this->naam = 'Onbepaald';
        $this->id = null;
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
        return 'KVDdo_AdrKadastergemeente';
    }
}
?>
