<?php
/**
 * @package    KVD.thes
 * @subpackage Core
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_Match
 *
 * @package    KVD.thes
 * @subpackage Core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_Match
{
    /**
     * Broad Match
     */
    const MATCH_BM = 'BM';

    /**
     * Narrow Match
     */
    const MATCH_NM = 'NM';

    /**
     * Related Match
     */
    const MATCH_RM = 'RM';

    /**
     * Exact Match
     */
    const MATCH_EM = 'EM';

    /**
     * Close Match
     */
    const MATCH_CM = 'CM';

    /**
     * inverse
     *
     * Een array dat aangeeft wat de inverse matches zijn.
     * @var array
     */
    private static $inverse = array ( self::MATCH_BM => self::MATCH_NM ,
                                      self::MATCH_NM => self::MATCH_BM ,
                                      self::MATCH_RM => self::MATCH_RM ,
                                      self::MATCH_EM => self::MATCH_EM ,
                                      self::MATCH_CM => self::MATCH_CM );

    /**
     * Omschrijving per type match.
     *
     * @var array
     */
    private static $typeomschrijving = array (
        self::MATCH_BM => 'Specifieker dan' ,
        self::MATCH_NM => 'Algemener dan' ,
        self::MATCH_RM => 'Gerelateerd aan' ,
        self::MATCH_EM => 'Exact gelijk aan' ,
        self::MATCH_CM => 'Bijna gelijk aan'
    );

    /**
     * type
     *
     * @var string
     */
    private $type;

    /**
     * The thing being matched
     *
     * @var KVDthes_Matchable
     */
    private $matchable;

    /**
     * __construct
     *
     * @param string $type Een class-constante die de soort match aangeeft.
     * @param KVDthes_Matchable $matchable
     */
    public function __construct( $type , KVDthes_Matchable $matchable )
    {
        if ( !array_key_exists( $type , self::$inverse ) ) {
            throw new InvalidArgumentException ( 'U hebt een ongeldig match-type ' . $type . ' opgegeven.');
        }
        $this->type = $type;
        $this->matchable = $matchable;
    }

    /**
     * getType
     *
     * @return string
     */
    public function getType ()
    {
        return $this->type;
    }

    /**
     * getTypeOmschrijving
     *
     * @return string
     */
    public function getTypeOmschrijving()
    {
        return self::$typeomschrijving[$this->type];
    }

    /**
     * getMatchable
     *
     * @return KVDthes_Matchable
     */
    public function getMatchable( )
    {
        return $this->matchable;
    }


    /**
     * equals
     *
     * @param KVDthes_Match $match
     * @return boolean
     */
    public function equals( KVDthes_Match $match )
    {
        return ( $match->getMatchable( ) === $this->getMatchable( ) && $match->getType( ) === $this->getType( ) );
    }

    /**
     * getInverseMatch
     *
     * @return string Type van de inverse match
     */
    public function getInverseMatch()
    {
        return self::$inverse[$this->type];
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString( )
    {
        return 'Match ' . $this->type . ' ' . $this->matchable->getOmschrijving( );
    }
}
?>
