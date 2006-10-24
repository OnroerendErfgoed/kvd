<?php
/**
 * @package KVD.dom
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id$
 */

/**
 * KVDdom_PDOChunkyQuery 
 * 
 * Een andere class kan bepalen welk stuk van de resultaten moet teruggeven worden doormiddel van setChunk. Bv. setChunk(2) zal er voor zorgen dat de tweede blok records wordt geladen.
 * @package KVD.dom
 * @since 24 jul 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_PDOChunkyQuery
{
    /**
     * @var PDO
     */
    protected $_conn;
    
    /**
     * @var KVDdom_DataMapper
     */
    protected $_dataMapper;

    /**
     * @var string
     */
    private $sql;

    /**
     * @var string
     */
    private $idField;

    /**
     * @var integer
     */
    private $chunk;

    /**
     * @var integer
     */
    protected $start;

    /**
     * @var integer
     */
    protected $max;

    /**
     * @var integer
     */
    private $totalRecordCount;

    /**
     * @var KVDdom_SqlLogger
     */
    private $logger;

    /**
     * @param PDO $conn Een PDO connectie.
     * @param KVDdom_PDODataMapper $dataMapper Een DataMapper waarmee de sql kan omgezet worden naar DomainObjects.
     * @param string $sql De uit te voeren query. Opgelet, er moeten wat vervangingen doorgevoerd worden om het aantal records te kunnen ophalen. Waarschijnlijk zullen hier nog fouten inzitten.
     *                      Voorlopig blijkt alles te werken zolang het om eenvoudige select queries gaat, een distinct op het id-veld kan ook.
     * @param string $idField Naam van het veld dat dienst doet als id-field ( om het totale aantal records te kunnen tellen).
     * @param integer $chunk Het initieel gevraagde data-blok.
     * @param integer $rowsPerChunk Aantal rijen in een chunk.
     * @throws <b>InvalidArgumentException</b> Indien er een ongeldige parameter wordt doorgegeven.
     */
    public function __construct ( $conn , $dataMapper , $sql , $idField = 'id', $chunk = 1 , $rowsPerChunk=100, $logger = null)
    {
        $this->_conn = $conn;
        $this->_dataMapper = $dataMapper;
        if ( !is_string( $sql ) ) {
            throw new InvalidArgumentException ( 'De parameter sql moet een string zijn!' );
        }
        $this->sql = $sql;
        $this->idField = $idField;
        $this->setChunk ( $chunk );
        $this->setRowsPerChunk ( $rowsPerChunk );
        if ( $logger === null ) {
            $this->logger = new KVDdom_SqlLogger( );
        } else {
            $this->logger = $logger;
        }
        $this->initializeTotalRecordCount();
    }

    /**
     * @param string $sql
     * @param string $idField
     * @return string Een sql statement dat het totale aantal records kan berekenen.
     */
    private function getTotalRecordCountSql ( $sql , $idField='id' )
    {
        if ( stripos( $sql, 'DISTINCT') !== FALSE ) {
            $idField = 'DISTINCT ' . $idField;
        }
        $sql = preg_replace( '/(SELECT).*?(FROM)/i' , 'SELECT COUNT('.$idField.') FROM' , $sql , 1);
        $sql = preg_replace ( '/ ORDER.*/i','',$sql);
        $this->logger->log ( $sql );
        return $sql;
    }

    /**
     * Bereken het totale aantal records dat de query kan ophalen.
     */
    private function initializeTotalRecordCount()
    {
        $row = $this->_conn->query( $this->getTotalRecordCountSql( $this->sql , $this->idField ) );
        $this->totalRecordCount = $row->fetchColumn(0);        
    }

    /**
     * Deze functie geeft de DomainObjects terug die tot een bepaalde chunk horen.
     *
     * Op voorhand met setChunk() aangeroepen worden om te bepalen wat er wordt teruggeven.
     * @return array Array van KVDdom_DomainObjects
     */
    public function getDomainObjects()
    {
        $sql = $this->sql . " LIMIT {$this->max} OFFSET {$this->start}";
        $this->logger->log ( $sql );
        $stmt = $this->_conn->prepare( $sql );
        $stmt->execute();
        $domainObjects = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ) ) {
            $domainObjects[] = $this->_dataMapper->doLoad ( $row->id , $row );
        }
        return $domainObjects;
    }

    /**
     * @return integer
     */
    public function getChunk()
    {
        return $this->chunk;
    }

    /**
     * @return integer
     */
    public function getRowsPerChunk()
    {
        return $this->max;
    }

    /**
     * @return integer
     */
    public function getTotalRecordCount()
    {
        return $this->totalRecordCount;
    }

    /**
     * @return integer
     */
    public function getTotalChunksCount()
    {
        return ceil ( $this->getTotalRecordCount() / $this->getRowsPerChunk() );   
    }

    /**
     * @param integer $chunk
     */
    public function setChunk ( $chunk )
    {
        $this->chunk = $chunk;
        $this->calculateStart();
    }

    /**
     * @param integer $rowsPerPage
     */
    public function setRowsPerChunk ( $rowsPerChunk )
    {
        $this->max = $rowsPerChunk;
        $this->calculateStart();
    }

    /**
     * Bereken het startrecord.
     */
    private function calculateStart()
    {
        $this->start = ( ($this->chunk - 1) * $this->max );    
    }    
}
?>
