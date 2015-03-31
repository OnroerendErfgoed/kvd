<?php
/**
 * KVDutil_Transformer_CollectionToCsv
 *
 * @package KVD.util
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Philip Verbist <philip.verbist@hp.be>
 */

/**
 * KVDutil_BestandenToolkit
 *
 * @package KVD.util
 * @since 27 jan 2012
 * @copyright 2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Philip Verbist <philip.verbist@hp.be>
 */
class KVDutil_Transformer_CollectionToCsv
{
    private $coll = null;
    private $config = array(
        'max' => 250,
        'max_error' => 'Kan maximaal 250 records exporteren naar csv.',
        'fields' => array(
            'id' => 'getId',
        ),
    );

    /**
     * __construct
     *
     * @param KVDdom_DomainObjectCollection $coll
     * @param array $config
     */
    public function __construct( KVDdom_DomainObjectCollection $coll, $config = array(
            'max' => 250,
            'max_error' => 'Kan maximaal 250 records exporteren naar csv.',
            'fields' => array(
                'id' => 'getId',
                'omschrijving' => 'getOmschrijving',
            ),
        )
    )
    {
        $this->setConfig( $config );
        $this->setCollection( $coll );
    }

    /**
     * setCollection
     *
     * @param KVDdom_DomainObjectCollection $coll
     */
    public function setCollection( KVDdom_DomainObjectCollection $coll )
    {
        $this->coll = $coll;
    }

    /**
     * getCollection
     *
     * @return KVDdom_DomainObjectCollection
     */
    public function getCollection( )
    {
        return $this->coll;
    }

    /**
     * getConfig
     *
     * @return array
     */
    public function getConfig( )
    {
        return $this->config;
    }

    /**
     * setConfig
     *
     * @param Array $config
     */
    public function setConfig( $config )
    {
        foreach( $config as $key => $value ) {
            if( $key == 'fields' )
            {
                foreach( $value as $fieldKey => $fieldValue ) {
                    $this->config[$key][$fieldKey] = $fieldValue;
                }
            }
            else {
                $this->config[$key]=$value;
            }
        }
    }

    /**
     * transform
     *
     * @return string
     */
    public function transform( )
    {
        $csv = fopen('php://temp', 'r+');

        $maxRelicten = $this->config['max'];
        if($this->coll->count() > $this->config['max'] ) {
            fputcsv( $csv, array( $this->config['max_error'] ) );
        }

        $counter = 0;

        fputcsv($csv, array_keys($this->config['fields']) );

        //$array = iterator_to_array($this->coll);

        foreach($this->coll as $node) {
            $counter++;
            if ( $counter > $this->config['max'] ) {
                break;
            }

            $fieldValues = array();
            foreach($this->config['fields'] as $key => $value)
            {
                $var = KVDdom_Util_Helper::getDataForFieldString($node, $value);
                $fieldValues[] = $var;
            }

            fputcsv($csv, $fieldValues );
        }

        rewind ($csv);
        $output = stream_get_contents($csv);
        $output = "\xEF\xBB\xBF" . $output;
        return $output;
    }
}
?>
