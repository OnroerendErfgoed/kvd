<?php

/**
 * @package KVD.gis.ajax.keuzelijsten
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
 
/**
 * @package KVD.gis.ajax.keuzelijsten
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
abstract class KVDgis_AbstractAjaxKeuzelijsten
{
    /**
     * @var PDO
     */
    private $_db;

    public function __construct ( $db )
    {
        $this->_db = $db;
    }

    /**
     * @param string $sql
     * @return string Array van het query-resultaat
     */
    protected function sqlToArray ( $sql )
    {
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO_FETCH_ASSOC);
        }   catch ( PDOException $e) {
            $rows = array();
        }
        return $rows;
    }
}
?>
