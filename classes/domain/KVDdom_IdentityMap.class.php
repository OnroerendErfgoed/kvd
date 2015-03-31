<?php
/**
 * @package KVD.dom
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_GenericIdentityMap
 *
 * Een generieke Identity Map (geen identity map per class, maar één voor alle classes)
 * @package KVD.dom
 * @since 2005
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
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
    public function addDomainObject( KVDdom_DomainObject $domainObject )
    {
        $type = $domainObject->getClass();
        $id = $domainObject->getId();
        /*
        if (!array_key_exists($type, $this->maps)) {
            $this->maps[$type] = array($id => $domainObject );
        } else {
            if (!array_key_exists($id, $this->maps[$type])) {
                $this->maps[$type][$id] = $domainObject;
            }
        }

        Werkt dit niet evengoed?
        */
        $this->maps[$type][$id] = $domainObject;
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
     * Verwijder alle DomainObjecten van een bepaald type of alle
     * DomainObjecten
     *
     * @param mixed $type Het type waarvan de objecten moeten gewist worden of
     *                    null om alle objecten te wissen.
     * @return boolean True indien er iets gewist werd, anders false.
     */
    public function removeDomainObjects( $type = null )
    {
        if ($type === null) {
            $this->maps = array();
            return true;
        } elseif(array_key_exists($type, $this->maps)) {
            unset($this->maps[$type]);
            return true;
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
