<?php
    /**
     * @package KVD.gis.query.tohtml
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @version $Id: KVDgis_MsQueryForAttributesToHtml.class.php,v 1.1 2005/12/23 11:04:01 Koen Exp $
     */
    
    /**
     * @package KVD.gis.query.tohtml
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @since 1.0.0 
     */
    abstract class KVDgis_MsQueryForAttributesToHtml
    {
        
        /**
         * @var KVD_HtmlTableHelper
         */
        protected $_table;

        /**
         * @var KVDgis_MsMapQueryForAttributes
         */
        protected $_query;

        /**
         * @var string
         */
        protected $layerName;

        /**
         * @var array
         */
        protected $fields;
        
        /**
         * @param $query KVDgis_MsMapQueryForAttributes
         */
        public function __construct( $query )
        {
            $this->_query = $query;
            $this->_table = new KVDhtml_TableHelper();
        }

        protected function initQuery ()
        {
            $this->_query->setLayerName ( $this->layerName );
            $this->_query->setFields ( $this->fields ); 
        }

        /**
         * @param array $cssClasses Een array van cssClass-namen die aan de tabel-elementen moeten toegekend worden.
         * @return string Html van het queryresultaat in een tabel gegoten.
         */
        public function toHtml ( $cssClasses )
        {
            $this->_table->setCaption ( $this->caption );
            $this->_table->setCssClasses ( $cssClasses );
            
            $headers = array();
            foreach ($this->fields as $field) {
                if (isset($field['header'])) {
                    $headers[] = $field['header'];
                } else {
                    $headers[] = $field['fieldname'];    
                }
            }
            
            $this->_table->setHeaders ( $headers );

            $this->initQuery();
            
            if ($rows = $this->_query->query()) {
                $this->_table->setRows ( $rows );
                return $this->_table->toHtml();  
            } else {
                return '';
            }
        }
    }
?>
