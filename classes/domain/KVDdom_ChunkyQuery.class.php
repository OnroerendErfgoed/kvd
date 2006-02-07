<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_ChunkyQuery.class.php,v 1.1 2006/01/12 14:46:02 Koen Exp $
 */

/**
 * Een Query object voor een Creole Statement dat zijn resultaten in stukjes terug haalt.
 * De class doet die door een Creole Statement mee te nemen en wanneer nodig haalt het een nieuw stuk van de query op via limit en offset.
 * Om de door Creole teruggeven data om te zetten naar DomainObjects is een KVDdom_DataMapper referentie nodig.
 * Een andere class kan bepalen welk stuk van de resultaten moet teruggeven worden doormiddel van setChunk. Bv. setChunk(2) zal er voor zorgen dat de tweede blok records wordt geladen.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdom_ChunkyQuery
{
    /**
     * @var Connection
     */
    protected $_stmt;
    
    /**
     * @var KVDdom_DataMapper
     */
    protected $_dataMapper;

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
     * @param Statement $stmt Een Creole Statement
     * @param KVDdom_DataMapper $dataMapper Een DataMapper waarmee de sql kan omgezet worden naar DomainObjects.
     * @param integer $chunk Het initieel gevraagde data-blok.
     * * @param integer $rowsPerChunk Aantal rijen in een chunk.
     */
    public function __construct ( $stmt , $dataMapper , $chunk = 1 , $rowsPerChunk=25)
    {
        $this->_stmt = $stmt;
        $this->_dataMapper = $dataMapper;
        $this->setChunk ( $chunk );
        $this->setRowsPerChunk ( $rowsPerChunk );
        $this->initializeTotalRecordCount();
    }

    /**
     * Bereken het totale aantal records dat de query kan ophalen.
     */
    private function initializeTotalRecordCount()
    {
        $this->_stmt->setLimit(0);
        $rs = $this->_stmt->executeQuery();
        $this->totalRecordCount = $rs->getRecordCount();        
    }

    /**
     * Deze functie geeft de DomainObjects terug die tot een bepaalde chunk horen.
     *
     * Op voorhand met setChunk() aangeroepen worden om te bepalen wat er wordt teruggeven.
     * @return array Array van KVDdom_DomainObjects
     */
    public function getDomainObjects()
    {
        $this->_stmt->setLimit ( $this->max );
        $this->_stmt->setOffset ( $this->start );
        $rs = $this->_stmt->executeQuery();
        $domainObjects = array();
        while ($rs->next()) {
            $domainObjects[] = $this->_dataMapper->doLoad ( $rs->getInt('id') , $rs );
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
