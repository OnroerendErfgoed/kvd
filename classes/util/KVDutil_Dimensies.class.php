<?php
/**
 * @package KVD.util
 * @subpackage dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Class die alle dimensies van een object (voorwerp, vindplaats, monument, spoor, ...) groepeert.
 *
 * @package KVD.util
 * @subpackage dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
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
    public function __construct ( $allowedDimensies )
    {
        if ( is_array ( $allowedDimensies ) ) {
            $this->allowedDimensies = $allowedDimensies;
        } else {
            throw new Exception ("Illegal parameter. Parameter allowedDimensies moet array zijn.");
        }
    }

    /**
     * @param string $offset Naam van een soort dimensie
     * @return boolean
     */
    public function offsetExists ( $offset )
    {
        if ( isset ($this->dimensies[$offset]) ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @param string $offset Naam van een soort dimensie
     * @return KVDutil_Dimensie
     */
    public function offsetGet ( $offset )
    {
        if ( $this->offsetExists ( $offset ) ) {
            return $this->dimensies[$offset];
        } else {
            return FALSE;
        }
    }

    /**
     * @param string offset Naam van een soort dimensie
     * @param KVDutil_Dimensie $value 
     */
    public function offsetSet( $offset , $value )
    {
        if ( in_array ( $offset , $this->allowedDimensies ) ) {
            $this->dimensies[$offset] = $value;        
        } else {
            $toegestaneDimensies = implode ( ', ' , $this->allowedDimensies );
            throw new Exception ("Deze dimensie hoort niet tot de toegstane dimensies. Toegestane dimensies zijn $toegestaneDimensies.");
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
            $omschrijving = substr($omschrijving,0,-2) . '.';    
        }
        return $omschrijving;
    }
}
?>
