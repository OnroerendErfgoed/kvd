<?php
/**
 * @package KVD.thes
 * @subpackage Core
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_Relation 
 * 
 * @package KVD.thes
 * @subpackage Core
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Relation
{
    /**
     * Broader Term  
     */
    const REL_BT = 'BT';

    /**
     * Narrower Term 
     */
    const REL_NT = 'NT';

    /**
     * Related Term 
     */
    const REL_RT = 'RT';

    /**
     * Use 
     */
    const REL_USE = 'USE';

    /**
     * Use For 
     */
    const REL_UF = 'UF';

    /**
     * inverse 
     * 
     * Een array dat aangeeft wat de inverse relaties zijn aangezien elke relatie in 2 richtingen verloopt.
     * @var array
     */
    private static $inverse = array (   self::REL_BT => self::REL_NT ,
                                        self::REL_NT => self::REL_BT ,
                                        self::REL_RT => self::REL_RT ,
                                        self::REL_USE => self ::REL_UF ,
                                        self::REL_UF => self::REL_USE );

    /**
     * type 
     * 
     * @var string
     */
    private $type;

    /**
     * term 
     * 
     * @var KVDthes_Term
     */
    private $term;

    /**
     * __construct 
     * 
     * @param string $type Een class-constante die het soort relatie aangeeft.
     * @param KVDthes_Term $term 
     * @return void
     */
    public function __construct( $type , KVDthes_Term $term )
    {
        if ( !array_key_exists( $type , self::$inverse ) ) {
            throw new InvalidArgumentException ( 'U hebt een ongeldig relatie-type ' . $type . ' opgegeven.');
        }
        $this->type = $type;
        $this->term = $term;
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
     * getTerm 
     * 
     * @return KVDthes_Term
     */
    public function getTerm( )
    {
        return $this->term;
    }


    /**
     * equals 
     * 
     * @param KVDthes_Relation $relation 
     * @return void
     */
    public function equals( KVDthes_Relation $relation )
    {
        return ( $relation->getTerm( ) === $this->getTerm( ) && $relation->getType( ) === $this->getType( ) );
    }

    /**
     * getInverseRelation 
     * 
     * @return string Type van de inverse relatie
     */
    public function getInverseRelation()
    {
        return self::$inverse[$this->type];
    }
}
?>
