<?php
    /**
     * @package KVD.gis.query
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @version $Id: KVDgis_MsQueryForAttributesByPoint.class.php,v 1.1 2005/12/23 11:04:28 Koen Exp $
     */

    /**
     * Query een laag op basis van een punt
     * 
     * @package KVD.gis.query
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @since 1.0.0
     */
    class KVDgis_MsQueryForAttributesByPoint extends KVDgis_MsQueryForAttributes
    {
        /**
         * @var PointObj
         */
        private $_point;

        /**
         * @var integer
         */
        private $buffer; 

        /**
         * @param $map MapObj
         * @param $point PointObj
         */
        public function __construct( $map , $point , $buffer = 0)
        {
            parent::__construct( $map );
            $this->_point = $point;
            $this->buffer = $buffer;
        }

        /**
         * @return mixed De gevraagde velden van de gevonden shapes of false als er niets gevonden werd.
         */
        public function query()
        {
            $layer = $this->_map->getLayerByName($this->layerName);
            $qRes = @$layer->queryByPoint( $this->_point , MS_MULTIPLE , $this->buffer );
            if ( $qRes == MS_SUCCESS ) {
                return $this->generateOutput($layer);
            } 
            return false;   
        }
    }
?>
