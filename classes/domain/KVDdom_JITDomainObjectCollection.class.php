<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Object om collecties van DomainObjects te beheren waarbij de objecten pas geladen worden als ze effectief opgevraagd worden.
 *
 * @deprecated  Heeft nooit echt dienst gedaan.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 24 jul 2006
 */
class KVDdom_JITDomainObjectCollection extends KVDdom_DomainObjectCollection
{
    /**
     * De ruwe waarden
     * @var array
     */
    private $raw;

    /**
     * De mapper waarmee records geladen moeten worden.
     * @var KVDdom_DataMapper
     */
    private $_mapper;

    /**
     * @var integer
     */
    private $totalRecords = 0;

    /**
     * @var integer;
     */
    private $pointer = 0;
    
    /**
     * @param PDOStatement $stmt
     * @param KVDdom_DataMapper $mapper
     */
    public function __construct ( $stmt , $mapper )
    {
        $this->collection = array( );
        $this->initDb( $stmt, $mapper );
    }

    /**
     * @param ResultSet $rs
     * @param KVDdom_DataMapper $mapper
     */
    private function initDb( $stmt , $mapper )
    {
        $this->raw = $stmt->fetchAll( PDO::FETCH_OBJ );
        $this->_mapper = $mapper;
        $this->totalRecords = count ( $this->raw );
    }

    private function getObjectAt ( $position )
    {
        if ( $position < 0 || $position >= $this->totalRecords ) {
            return null; 
        }

        if ( !array_key_exists( $position , $this->collection) ) {
            $this->collection[$position] = $this->_mapper->doLoad ( $this->raw[$position]->id , $this->raw[$position] );
        } 
        
        return $this->collection[$position];
        
    }

    /**
     * @return integer
     */
    public function getTotalRecordCount()
    {
        return $this->totalRecords;
    }

    /**
     * @return integer
     */
    public function count( )
    {
        return $this->totalRecords;
    }

    /**
     * @return KVDdom_DomainObject
     */
    public function current()
    {
        return $this->getObjectAt ( $this->pointer );
    }

    /**
     * @return integer
     */
    public function key()
    {
        return $this->pointer;   
    }

    public function next()
    {
        $this->pointer++;
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    /**
     * @return KVDdom_DomainObject
     * @throws Exception - Indien een ongeldige index gevraagd wordt.
     */
    public function seek ($index)
    {
        if ( $index < 0 || $index >= $this->getTotalRecordCount() ) {
            $index = 0; 
        }
        $this->pointer = $index;
        return $this->current( );
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return ( !is_null ( $this->current( ) ) );
    }
    
}
?>
