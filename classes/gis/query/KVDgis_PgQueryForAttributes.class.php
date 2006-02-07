<?php
    /**
     * @package KVD.gis.query
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @version $Id$
     */

    /**
     * Basisclass om lagen te querien op attributen.
     * 
     * @package KVD.gis.query
     * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
     * @since 1.0.0
     */
    abstract class KVDgis_PgQueryForAttributes
    {
        /**
         * @var PDO
         */
        protected $_conn;

        /**
         * @var string
         */
        protected $sql;

        /**
         * @param $conn PDO
         */
        public function __construct( $conn )
        {
            $this->_conn = $conn;
        }

        /**
         * @param string Een geldige sql-string.
         */
        public function setSQL ( $sql )
        {
            $this->sql = $sql;    
        }

        /**
         * @return array De gevraagde velden van de gevonden shapes.
         */
        protected function generateOutput ()
        {
            try {
                $stmt = $this->_conn->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO_FETCH_NUM);
            }
            return $rows;
        }

        /**
         * @return mixed De gevraagde velden van de gevonden shapes of false als er niets gevonden werd.
         */
        abstract public function query ();

    }
?>
