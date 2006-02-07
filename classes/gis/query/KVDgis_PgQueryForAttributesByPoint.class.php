<?php
    /**
     * @package KVD.gis.query
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @version $Id$
     */

    /**
     * Query een laag op basis van een punt
     * 
     * @package KVD.gis.query
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @since 1.0.0
     */
    class KVDgis_PgQueryForAttributesByPoint extends KVDgis_PgQueryForAttributes
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
         * @param $conn PDO
         * @param $point PointObj
         * @param integer $buffer
         */
        public function __construct( $conn , $point , $buffer = 0)
        {
            parent::__construct( $conn );
            $this->_point = $point;
            $this->buffer = $buffer;
        }

        /**
         * @return mixed De gevraagde velden van de gevonden shapes of false als er niets gevonden werd.
         */
        public function query()
        {
            $geometry = "POINT({$this->_point->x} {$this->_point->y})";
            $sql = "SELECT locatie.id AS locatie, locatie.kadaster_id AS kadaster, metakataloog.id AS metakataloog, metakataloog.benaming AS benaming 
                     FROM mel_gis.locatie LEFT JOIN mel_gis.locatie_metakataloog ON locatie.id = locatie_metakataloog.locatie_id LEFT JOIN metakataloog ON locatie_metakataloog.metakataloog.id = metakataloog.id
                     WHERE locatie.the_geom && GeomFromText('$geometry',-1)";
            $this->generateOutput($sql);
            $layer = $this->_map->getLayerByName($this->layerName);
            $qRes = @$layer->queryByPoint( $this->_point , MS_MULTIPLE , $this->buffer );
            if ( $qRes == MS_SUCCESS ) {
                return $this->generateOutput($layer);
            } 
            return false;   
        }
    }
?>
