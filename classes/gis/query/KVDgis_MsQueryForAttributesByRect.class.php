<?php
    /**
     * @package KVD.gis.query
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @version $Id: KVDgis_MsQueryForAttributesByRect.class.php,v 1.1 2005/12/23 11:04:28 Koen Exp $
     */
     
    /**
     * Query een laag op basis van een rechthoek
     * 
     * @package KVD.gis.query
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @since 1.0.0
     */
    class KVDgis_MsQueryForAttributesByRect extends KVDgis_MsQueryForAttributes
    {
        /**
         * @var RectObj
         */
        protected $_rect;

        /**
         * @param $map MapObj
         * @param $rect RectObj
         */
        public function __construct( $map , $rect )
        {
            parent::__construct( $map );
            $this->_rect = $rect;
        }

        /**
         * @return mixed De gevraagde velden van de gevonden shapes of false als er niets gevonden werd.
         */
        public function query()
        {
            $layer = $this->_map->getLayerByName($this->layerName);
            $qRes = @$layer->queryByRect( $this->_rect );
            if ( $qRes == MS_SUCCESS ) {
                return $this->generateOutput($layer);
            } 
            return false;    
        }

    }
?>
