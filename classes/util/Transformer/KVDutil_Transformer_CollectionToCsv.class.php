<?php
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
    
    public function __construct( KVDdom_DomainObjectCollection $coll, $config )
    {
        $this->setConfig( $config );
        $this->setCollection( $coll );
    }
    
    public function setCollection( KVDdom_DomainObjectCollection $coll )
    {
        $this->coll = $coll;
    }
    
    public function getCollection( )
    {
        return $this->coll;
    }
    
    public function getConfig( )
    {
        return $this->config;
    }
    
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
    
    public function transform( )
    {
        $csv = fopen('php://temp', 'r+');
        //$this->coll->setRowsPerChunk( $this->config['max'] );
        
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
                /*
                $values=explode('.',$value);
                $var=$node;
                foreach($values as $variable) {
                    $var=$var->$variable;
                }
                */
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