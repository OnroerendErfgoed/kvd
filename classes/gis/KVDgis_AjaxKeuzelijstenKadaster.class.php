<?php

/**
 * @package KVD.gis.ajax.keuzelijsten
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
 
/**
 * @package KVD.gis.ajax.keuzelijsten
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
class KVDgis_AjaxKeuzelijstenKadaster extends KVDgis_AbstractAjaxKeuzelijsten
{

    /**
     * @param integer $nisnr Nisnummer van een provincie
     * @return string JSON versie van array met kolommen id en naam
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
     * @return string JSON versie van array met kolommen id en naam
     */
    public function getLijstAfdeling( $nisnr )
    {
        $sql = "SELECT kadgemnr AS id, afdeling AS naam FROM alg_gis.niskad WHERE fgnisnr = $nisnr ORDER BY afdeling ASC;";
        return $this->sqlToArray( $sql );
    }

    /**
     * @param integer $nisnr Nisnummer van een afdeling van een gemeente
     * @return string JSON versie van array met kolommen id en naam
     */
    public function getLijstSectie ( $nisnr )
    {
        $sql = "SELECT DISTINCT sectie AS id, sectie AS naam FROM alg_gis.KSOVL_P WHERE kadgemnr = $nisnr ORDER BY sectie ASC;";
        return $this->sqlToArray( $sql );
    }

    /**
     * @param integer $nisnr Nisnummer van een afdeling van een gemeente
     * @param string $sectie Sectie binnen een afdeling van een gemeente
     * @return string JSON versie van array met kolommen id en naam
     */
    public function getLijstGrondnr ( $nisnr , $sectie )
    {
        $sql = "SELECT DISTINCT grondnr AS id, grondnr AS naam FROM alg_gis.KSOVL_P WHERE kadgemnr = $nisnr AND sectie = '$sectie' ORDER BY grondnr ASC;";
        return $this->sqlToArray( $sql );
    }

    /**
     * @param integer $nisnr Nisnummer van een afdeling van een gemeente
     * @param string $sectie Sectie binnen een afdeling van een gemeente
     * @param integer $grondr Grondnr binnen de sectie binnen de afdeling van een gemeente
     * @return string JSON versie van array met kolommen id en naam
     */
    public function getLijstExponent ( $nisnr , $sectie , $grondnr )
    {
        $sql = "SELECT DISTINCT exponent AS id, exponent AS naam FROM alg_gis.KSOVL_P WHERE kadgemnr = $nisnr AND sectie = '$sectie' AND grondnr=$grondnr ORDER BY exponent ASC;";
        return $this->sqlToArray( $sql );
    }

}
?>
