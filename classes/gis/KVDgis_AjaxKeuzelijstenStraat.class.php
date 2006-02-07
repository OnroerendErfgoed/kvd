<?php

/**
 * @package KVD.gis.ajax.keuzelijsten
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
 
/**
 * @package KVD.gis.ajax.keuzelijsten
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
class KVDgis_AjaxKeuzelijstenStraat extends KVDgis_AbstractAjaxKeuzelijsten
{
    /**
     * @param integer $nisnr Nisnummer van een provincie
     * @return Array Gemeentes en nisnrs
     */
    public function getLijstGemeente ( $nisnr )
    {
        $start = $nisnr;
        $end = $nisnr + 9998;
        $sql = "SELECT DISTINCT fgnisnr AS id, gemeente AS naam FROM alg_gis.niskad WHERE fgnisnr BETWEEN $start AND $end ORDER BY gemeente ASC;";
        return $this->sqlToArray( $sql );
    }

    /**
     * @param integer $nisnr Nisnummer van een gemeente
     * @return Array Straatnamen 
     */
    public function getLijstStraat ( $nisnr )
    {
        $sql = "SELECT DISTINCT stname AS id, stname AS naam FROM alg_gis.straten WHERE fgnisnr = $nisnr ORDER BY stname ASC;";
        return $this->sqlToArray( $sql );
    }

}
?>
