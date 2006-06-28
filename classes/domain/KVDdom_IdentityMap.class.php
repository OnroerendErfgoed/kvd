<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Een generieke Identity Map (geen identity map per class, maar één voor alle classes)
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdom_GenericIdentityMap implements Iterator, Countable {
    
    /**
     * @var array;
     */
    private $maps = array();

    /**
     * @return void
     */
    public function rewind() {
        reset ($this->maps);
    }

    /**
     * @return array Een array van KVDdom_DomainObjects van een bepaald type.
     */
    public function current() {
        return current ($this->maps);
    }

    /**
     * @return string Een bepaald type van KVDdom_DomainObjects.
     */
    public function key() {
        return key ($this->maps);
    }

    /**
     * @return array Een array van KVDdom_DomainObjects van een bepaald type.
     */
    public function next() {
        return next($this->maps);
    }

    /**
     * @return boolean. Is dit een geldige record?
     */
    public function valid() {
        return !is_null(key($this->maps));
    }

    /**
     * @param KVDdom_DomainObject $domainObject
     * @return void
     */
    public function addDomainObject( $domainObject )
    {
        $type = $domainObject->getClass();
        $id = $domainObject->getId();
        if (!array_key_exists($type, $this->maps)) {
            $this->maps[$type] = array($id => $domainObject );
        } else {
            if (!array_key_exists($id, $this->maps[$type])) {
                $this->maps[$type][$id] = $domainObject;
            }
        }
    }

    /**
     * @param string $type Naam van de class waarvan een KVDdom_DomainObject nodig is.
     * @param integer $id Id van het gevraagde KVDdom_DomainObject.
     * @return KVDdom_DomainObject Het gevraagde object of null als het object niet gevonden werd.
     */
    public function getDomainObject( $type, $id )
    {
        if (array_key_exists($type, $this->maps)) {
            if (array_key_exists($id, $this->maps[$type])) {
                return $this->maps[$type][$id];
            }
        }
        return null;
    }

    /**
     * @param string $type Naam van de class waarvan de DomainObject's nodig zijn.
     * @return array Array van KVDdom_DomainObjects van het gevraagde type of null indien er geen gevonden werden.
     */
    public function getDomainObjects ( $type )
    {
        if (array_key_exists($type, $this->maps)) {
            return $this->maps[$type];            
        }
        return null;
    }

    /**
     * @param string $type Naam van de class waarvan een DomainObject nodig is.
     * @param integer $id Id van het gevraagde DomainObject.
     * @return boolean True indien een DomainObject verwijderd werd, anders false.
     */
    public function removeDomainObject( $type, $id )
    {
        if (array_key_exists($type, $this->maps)) {
            if (array_key_exists($id, $this->maps[$type])) {
                unset($this->maps[$type][$id]);
                return true;
            }
        }
        return false;
    }
    
    /**
     * @return integer Het aantal domainObject die nog aanwezig zijn in deze identity map. Onafhankelijk van hun type.
     */
    public function count( )
    {
        return count( $this->maps, COUNT_RECURSIVE) - count( $this->maps,0 );        
    }
}
?>
