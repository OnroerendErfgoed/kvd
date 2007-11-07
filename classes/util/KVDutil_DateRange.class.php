<?php
/**
 * @package KVD.util
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_DateRange 
 * 
 * @package KVD.util
 * @since 2 april 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_DateRange
{
    const DATE_FORMAT = 'd-m-Y';
    
    /**
     * start 
     * 
     * Timestamp
     * @var integer
     */
    private $start;

    /**
     * einde 
     * 
     * Timestamp
     * @var integer
     */
    private $einde;
    
    /**
     * __construct 
     * 
     * @param mixed $start Integer of string. 
     * @param mixed $einde Integer of string.
     */
    public function __construct( $start , $einde )
    {
        $this->start = ( !is_numeric ( $start )&& !is_null ( $start )) ? strtotime( $start ) : $start ;
        $this->einde = ( !is_numeric ( $einde )&& !is_null ( $einde ) ) ? strtotime( $einde ) : $einde ;
    }

    /**
     * getStart 
     * 
     * @return integer
     */
    public function getStart( )
    {
        return ( !is_null($this->start)?date ( self::DATE_FORMAT , $this->start):null);
    }

    /**
     * getEinde 
     * 
     * @return integer
     */
    public function getEinde( )
    {
        return ( !is_null($this->einde)?date ( self::DATE_FORMAT , $this->einde ):null);
    }

    /**
     * getOmschrijving 
     * 
     * @return string
     */
    public function getOmschrijving( )
    {
        if( $this->getStart() != null && $this->getEinde( )!=null){
            return $this->getStart( ) . ' tot ' . $this->getEinde( );
        }
    }
}
?>
