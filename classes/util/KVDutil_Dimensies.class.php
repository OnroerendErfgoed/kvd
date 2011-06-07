<?php
/**
 * @package    KVD.util
 * @subpackage dimensies
 * @version    $Id$
 * @copyright  2004 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Class die alle dimensies van een object (voorwerp, vindplaats, monument, spoor, ...) groepeert.
 *
 * @package    KVD.util
 * @subpackage dimensies
 * @since      lang, lang, lang geleden
 * @copyright  2004 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Dimensies implements ArrayAccess, IteratorAggregate
{
    /**
     * @var array Toegestane dimensies
     */
    private $allowedDimensies;
    
    /**
     * @var array De eigenlijke dimensies
     */
    protected $dimensies = array();

    /**
     * @param array $allowedDimensies
     */
    public function __construct ( array $allowedDimensies )
    {
        $this->allowedDimensies = $allowedDimensies;
    }

    /**
     * @param string $offset Naam van een soort dimensie
     *
     * @return boolean
     */
    public function offsetExists ( $offset )
    {
        return isset ($this->dimensies[$offset]);
    }

    /**
     * @param string $offset Naam van een soort dimensie
     *
     * @return KVDutil_Dimensie
     */
    public function offsetGet ( $offset )
    {
        if ( $this->offsetExists ( $offset ) ) {
            return $this->dimensies[$offset];
        } else {
            return false;
        }
    }

    /**
     * @param string offset Naam van een soort dimensie
     * @param KVDutil_Dimensie $value 
     */
    public function offsetSet( $offset , $value )
    {
        if ( in_array ($offset, $this->allowedDimensies ) ) {
            $this->dimensies[$offset] = $value;        
        } else {
            $toegestaneDimensies = implode (', ', $this->allowedDimensies );
            throw new InvalidArgumentException (
                "Deze dimensie hoort niet tot de toegstane dimensies. 
                Toegestane dimensies zijn $toegestaneDimensies.");
        }
    }

    /**
     * @param string $offset Naam van een soort dimensie
     */
    public function offsetUnset ( $offset )
    {
        unset ( $this->dimensies[$offset] );
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator ()
    {
        return new ArrayIterator ( $this->dimensies );
    }

    /**
     * @return array Toegestane dimensieSoorten.
     */
    public function getToegestaneDimensies()
    {
        return $this->allowedDimensies;    
    }

    /**
     * @return string Omschrijving
     */
    public function getOmschrijving ()
    {
        $omschrijving = '';
        foreach ($this->allowedDimensies as $dimensie) {
            if ( $this->offsetExists ( $dimensie ) ) {
                $omschrijving .= $this->offsetGet($dimensie)->getOmschrijving(). ', ';
            }
        }
        if ( $omschrijving !== '' ) {
            $omschrijving = substr($omschrijving, 0, -2) . '.';    
        }
        return $omschrijving;
    }
}
?>
