<?php
    /**
     * @package KVD.gis.query
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @version $Id: KVDgis_AbstractMsQueryForAttributes.class.php,v 1.1 2005/12/23 11:04:28 Koen Exp $
     */

    /**
     * Basisclass om lagen te querien op attributen.
     * 
     * @package KVD.gis.query
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @since 1.0.0
     */
    abstract class KVDgis_MsQueryForAttributes
    {
        /**
         * @var MapObj
         */
        protected $_map;

        /**
         * @var array
         */
        protected $fields;

        /**
         * @var string
         */
        protected $layerName;

        /**
         * @param $map MapObj
         * @param $point PointObj
         */
        public function __construct( $map )
        {
            $this->_map = $map;
        }

        /**
         * @param array $fields Array met veldnamen die moeten opgezocht worden.
         */
        public function setFields ( $fields )
        {
            if (!is_array($fields)) {
                throw new Exception ('Illegal parameter!' . "$fields is geen array.");    
            }
            $this->fields = $fields;    
        }

        /**
         * @param string $layerName Naam van de laag die moet gequeried worden.
         */
        public function setLayerName ( $layerName )
        {
            $this->layerName = $layerName;    
        }

        /**
         * @param $layer LayerObj Een mapserver laag
         * @return array De gevraagde velden van de gevonden shapes.
         */
        protected function generateOutput ( $layer )
        {
            $layer->open();
            $rows = array();
            for ( $i=0 ; $i<$layer->getNumResults() ; $i++ ) {
                $shape = $layer->getShape($layer->getResult($i)->tileindex , $layer->getResult($i)->shapeindex);
                $row = array();
                foreach ($this->fields as $field) {
                    $row[] = $shape->values[$field['fieldname']];    
                }
                $rows[] = $row;
            }
            $layer->close();
            return $rows;
        }

        /**
         * @return mixed De gevraagde velden van de gevonden shapes of false als er niets gevonden werd.
         */
        abstract public function query ();

    }
?>
