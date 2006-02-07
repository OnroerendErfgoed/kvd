<?php
    /**
     * @package KVD.gis.query.tohtml
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @version $Id: KVDgis_MsQueryKadasterPuntenForAttributesToHtml.class.php,v 1.1 2005/12/23 11:04:01 Koen Exp $
     */
    
    /**
     * @package KVD.gis.query.tohtml
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @since 1.0.0
     */
    class KVDgis_MsQueryKadasterPuntenForAttributesToHtml extends KVDgis_MsQueryForAttributesToHtml
    {
        public function __construct ( $query )
        {
            parent::__construct ( $query );
            $this->layerName = 'Kadaster OVL Punten';
            $this->fields = array ( array ( 'fieldname' => 'percid',
                                            'header' => 'Perceel Id'),
                                    array ( 'fieldname' => 'fgnisnr',
                                            'header' => 'Gemeente'),
                                    array ( 'fieldname' => 'kadgemnr',
                                            'header' => 'Afdeling'),
                                    array ( 'fieldname' => 'sectie',
                                            'header' => 'Sectie'),
                                    array ( 'fieldname' => 'grondnr',
                                            'header' => 'Grondnummer'),
                                    array ( 'fieldname' => 'exponent',
                                            'header' => 'Exponent'),
                                    array ( 'fieldname' => 'macht',
                                            'header' => 'Macht'),
                                    array ( 'fieldname' => 'bisnr',
                                            'header' => 'Bisnummer')        
                                   );
            $this->caption = 'Kadaster';
        }
        
    }
?>
