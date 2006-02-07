<?php
    /**
     * @package KVD.gis.query.tohtml
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @version $Id: KVDgis_MsQueryGemeenteForAttributesToHtml.class.php,v 1.1 2005/12/23 11:04:01 Koen Exp $
     */
    
    /**
     * @package KVD.gis.query.tohtml
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @since 1.0.0
     */
    class KVDgis_MsQueryGemeenteForAttributesToHtml extends KVDgis_MsQueryForAttributesToHtml
    {
        public function __construct ( $query )
        {
            parent::__construct ( $query );
            $this->layerName = 'Gemeenten';
            $this->fields = array ( array ( 'fieldname' => 'NAAM',
                                            'header' => 'Naam')
                                   );
            $this->caption = 'Gemeente';
        }
    }
?>
