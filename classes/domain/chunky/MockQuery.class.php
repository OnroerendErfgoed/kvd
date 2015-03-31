<?php
/**
 * @package    KVD.dom
 * @subpackage chunky
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Simpele implementatie van de query interface met een array van
 * domainobjecten.
 *
 * @package    KVD.dom
 * @subpackage chunky
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdom_Chunky_MockQuery implements KVDdom_Chunky_IQuery
{
    /**
     * rowsPerChunk
     *
     * @var integer
     */
    private $rowsPerChunk;

    /**
     * coll
     *
     * @var KVDdom_DomainObjectCollection
     */
    private $coll;

    /**
     * Het nummer van het eerste record van de chunk
     *
     * @var integer
     */
    private $start;

    public function __construct( KVDdom_DomainObjectCollection $coll, $rowsPerChunk = 2 )
    {
        $this->coll = $coll;
        $this->rowsPerChunk = $rowsPerChunk;
        $this->chunk = 1;
    }

    /**
     * Het aantal records per blok
     *
     * @return integer
     */
    public function getRowsPerChunk( )
    {
        return $this->rowsPerChunk;
    }

    /**
     * Stel het aantal records per blok in.
     *
     * @param integer $rows
     * @return void
     */
    public function setRowsPerChunk( $rows )
    {
        $this->rowsPerChunk = $rows;
        $this->calculateStart();
    }

    /**
     * Het totale aantal records dat de query kan leveren
     *
     * @return integer
     */
    public function getTotalRecordCount( )
    {
        return count( $this->coll );
    }

    /**
     * Het aantal brokjes waarin de records worden opgedeeld op basis van het
     * aantal rijen per brokje.
     *
     * @return integer
     */
    public function getTotalChunksCount( )
    {
        return ceil ( $this->getTotalRecordCount() / $this->getRowsPerChunk() );
    }

    /**
     * Vraag het nummer van het actieve brokje op.
     *
     * @return void
     */
    public function getChunk(  )
    {
        return $this->chunk;
    }

    /**
     * Stel het brokje in dat actief moet worden.
     *
     * @param  integer $chunk
     * @return void
     */
    public function setChunk( $chunk )
    {
        $chunk = ( int ) $chunk;
        if ( $chunk < 1 || $chunk > $this->getTotalChunksCount( ) ) {
            throw new InvalidArgumentException( 'U vraagt een chunk op die
                niet bestaat.' );
        }
        $this->chunk = $chunk;
        $this->calculateStart();
    }

    /**
     * Bereken het startrecord.
     */
    private function calculateStart()
    {
        $this->start = ( ($this->chunk - 1) * $this->rowsPerChunk );
    }

    /**
     * getDomainObjects
     *
     * @return array Een array met de domainobjecten uit dit blok.
     */
    public function getDomainObjects(  )
    {
        $it = new LimitIterator( $this->coll, $this->start, $this->rowsPerChunk);
        return iterator_to_array( $it, false );
    }
}
?>
