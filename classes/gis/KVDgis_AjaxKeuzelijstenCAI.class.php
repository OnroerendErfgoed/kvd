<?php

/**
 * @package KVD.gis.ajax.keuzelijsten
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
 
/**
 * @package KVD.gis.ajax.keuzelijsten
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
class KVDgis_AjaxKeuzelijstenCAI extends KVDgis_AbstractAjaxKeuzelijsten
{
    private $_map;
    
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
     * @return Array CAI nummers 
     */
    public function getLijstCAI ( $nisnr )
    {
        $this->_map = ms_newMapObj('KVDgis.map');
        $gemLayer = $this->_map->getLayerByName('Gemeenten');
        $qGemeenteRes = @$gemLayer->queryByAttributes( 'NISNR' , " = " . $nisnr , MS_SINGLE );
        if ( $qGemeenteRes == MS_SUCCESS ) {
            $gemLayer->open();
            $gem = $gemLayer->getShape($gemLayer->getResult(0)->tileindex , $gemLayer->getResult(0)->shapeindex);
            $gemLayer->close();
        }

        $return = array();
        $caiLayer = $this->_map->getLayerByName('CAI');
        $qCaiRes = @$caiLayer->queryByFeatures($gemLayer);
        if ( $qCaiRes == MS_SUCCESS ) {
            $caiLayer->open();
            for ($i=0;$i<$caiLayer->getNumResults();$i++) {
                $cai = $caiLayer->getShape($caiLayer->getResult($i)->tileindex , $caiLayer->getResult($i)->shapeindex);
                $return['id'] = $cai->values('ID');
                $return['naam'] = $cai->values('ID');
            }
            
            $caiLayer->close();
        }
        return $return;
    }

}
?>
