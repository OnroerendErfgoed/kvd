<?php
/**
 * @package     KVD.dom
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version     $Id$
 */

/**
 * KVDdom_PDOChunkyQuery 
 * 
 * Deze class voert een query in stukjes uit zodat er aan lazy loading gedaan kan worden.
 * Een andere class kan bepalen welk stuk van de resultaten moet teruggeven 
 * worden door middel van setChunk.
 * Bv. setChunk(2) zal er voor zorgen dat de tweede blok records wordt geladen.
 *
 * @package     KVD.dom
 * @since       24 jul 2006
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_PDOChunkyQuery
{
    /**
     * Geeft aan in welke mode de query moet werken. 
     * Deze mode geeft aan dat er wordt verondersteld dat de sql string die wordt doorgegeven
     * al compleet is en gewoon kan uitgevoerd worden.
     * @var integer 
     */
    const MODE_FILLED = 1;

    /**
     * Geeft aan in welke mode de query moet werken. 
     * Deze mode geeft aan dat de class een geparemeteriseerde statement ontvagen waarin
     * de parameters nog moeten ingepast worden dmv. bindValue.
     * @var integer 
     */
    const MODE_PARAMETERIZED = 2;

    /**
     * @var PDO
     */
    protected $conn;
    
    /**
     * @var KVDdom_DataMapper
     */
    protected $dataMapper;

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
     * mode 
     * 
     * @var integer
     */
    private $mode;

    /**
     * values 
     * 
     * @var array
     */
    private $values;

    /**
     * stmt 
     * 
     * @var PDOStatement
     */
    private $stmt;

    /**
     * index 
     * 
     * Array dat de indexen bevat waarop bepaalde parameters zoals max en offset gebonden moeten worden.
     * @var array
     */
    private $index = array( );

    /**
     * @param PDO                   $conn           Een PDO connectie.
     * @param KVDdom_PDODataMapper  $dataMapper     Een DataMapper waarmee de sql kan omgezet worden naar DomainObjects.
     * @param string                $sql            De uit te voeren query. Opgelet, er moeten wat vervangingen doorgevoerd worden om het aantal records te kunnen ophalen. Waarschijnlijk zullen hier nog fouten inzitten.
     *                                              Voorlopig blijkt alles te werken zolang het om eenvoudige select queries gaat, een distinct op het id-veld kan ook.
     * @param string                $idField        Naam van het veld dat dienst doet als id-field ( om het totale aantal records te kunnen tellen).
     * @param integer               $mode           Gevuld of geparameteriseerd.
     * @param array                 $values         Waarden om te gebruiken in de placeholders van de prepared statements.
     * @param KVDdom_SqlLogger      $logger         Een logger waarop sql statements gelogd kunnen worden.
     * @throws <b>InvalidArgumentException</b>      Indien er een ongeldige parameter wordt doorgegeven.
     */
    public function __construct (   $conn , 
                                    KVDdom_PDODataMapper $dataMapper , 
                                    $sql , 
                                    $idField = 'id', 
                                    $mode = self::MODE_FILLED, 
                                    array $values = null, 
                                    $logger = null)
    {
        $this->conn = $conn;
        $this->dataMapper = $dataMapper;
        if ( !is_string( $sql ) ) {
            throw new InvalidArgumentException ( 'De parameter sql moet een string zijn!' );
        }
        $this->sql = $sql;
        $this->idField = $idField;
        $this->setChunk ( 1 );
        $this->setRowsPerChunk ( 100 );
        if ( $mode == self::MODE_PARAMETERIZED && is_null( $values ) ) {
            throw new InvalidArgumentException ( 'Als u met geparameterizeerde queries wilt werken moet u ook de veldwaarden opgeven.' );
        }
        $this->values = $values;
        $this->mode = $mode;
        $this->logger = ( is_null( $logger ) ) ? new KVDdom_SqlLogger( ) : $logger;
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
        $sql = preg_replace( '/(SELECT).*?(FROM)/is' , 'SELECT COUNT('.$idField.') FROM' , $sql , 1);
        $sql = preg_replace ( '/[\n\s]*ORDER.*/is','',$sql);
        $this->logger->log ( $sql );
        return $sql;
    }

    /**
     * Bereken het totale aantal records dat de query kan ophalen.
     */
    private function initializeTotalRecordCount()
    {
        if ( $this->mode == self::MODE_FILLED ) {
            $stmt = $this->conn->query( $this->getTotalRecordCountSql( $this->sql , $this->idField ) );
        }
        if ( $this->mode == self::MODE_PARAMETERIZED ) {
            $stmt = $this->conn->prepare( $this->getTotalRecordCountSql( $this->sql, $this->idField ) );
            for ( $i = 0; $i<count( $this->values); $i++) {
                if ( is_bool( $this->values[$i] ) ) {
                    $stmt->bindValue( $i+1, $this->values[$i], PDO::PARAM_BOOL );
                } else {
                    $stmt->bindValue( $i+1, $this->values[$i] );
                }
            }
            $stmt->execute( );
        }
        $this->totalRecordCount = $stmt->fetchColumn(0);        
    }

    /**
     * Deze functie geeft de DomainObjects terug die tot een bepaalde chunk horen.
     *
     * Op voorhand moet setChunk() aangeroepen worden om te bepalen wat er wordt teruggeven.
     * @return array Array van KVDdom_DomainObjects
     */
    public function getDomainObjects()
    {
        $this->initializeStatement( );
        $this->stmt->bindValue( $this->index['max'], $this->max, PDO::PARAM_INT );
        $this->stmt->bindValue( $this->index['start'], $this->start, PDO::PARAM_INT );
        $this->stmt->execute();
        $domainObjects = array();
        while ($row = $this->stmt->fetch(PDO::FETCH_OBJ) ) {
            $domainObjects[] = $this->dataMapper->doLoad ( $row->id , $row );
        }
        return $domainObjects;
    }

    /**
     * initializeStatement 
     * 
     * Initialiseer de statement en berekent de index waarop parameters zoals max en start gebonden moeten worden.
     * @since   23 okt 2007
     * @param   boolean     $clear  Afdwingen dat de statement opnieuw wordt aangemaakt.  
     * @return  void
     */
    private function initializeStatement( $clear = false )
    {
        if ( isset( $this->stmt ) && $this->stmt instanceof PDOStatement && !$clear ) {
            return;
        }
        $sql = $this->sql . " LIMIT ? OFFSET ?";
        $this->logger->log ( $sql );
        $this->stmt = $this->conn->prepare( $sql );
        $nextIndex = 1;
        if ( $this->mode == self::MODE_PARAMETERIZED ) {
            for ( $i = 0; $i < count( $this->values); $i++) {
                if ( is_bool( $this->values[$i] ) ) {
                    $this->stmt->bindValue( $i+1, $this->values[$i], PDO::PARAM_BOOL );
                } else {
                    $this->stmt->bindValue( $i+1, $this->values[$i] );
                }
            }
            $nextIndex = ++$i;
        }
        $this->index['max'] = $nextIndex++;
        $this->index['start'] = $nextIndex++;
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
