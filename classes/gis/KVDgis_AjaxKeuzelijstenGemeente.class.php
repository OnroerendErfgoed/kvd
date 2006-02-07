<?php

/**
 * @package KVD.gis.ajax.keuzelijsten
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
 
/**
 * @package KVD.gis.ajax.keuzelijsten
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
class KVDgis_AjaxKeuzelijstenGemeente extends KVDgis_AbstractAjaxKeuzelijsten
{

    /**
     * @param integer $nisnr Nisnummer van een provincie
     * @return array Array met kolommen id en naam
     */
    public function getLijstGemeente ( $nisnr )
    {
        $start = $nisnr;
        $end = $nisnr + 9998;
        $sql = "SELECT DISTINCT fgnisnr AS id, gemeente AS naam FROM alg_gis.niskad WHERE fgnisnr BETWEEN $start AND $end ORDER BY gemeente ASC;";
        return $this->sqlToArray( $sql );
    }

}
?>
