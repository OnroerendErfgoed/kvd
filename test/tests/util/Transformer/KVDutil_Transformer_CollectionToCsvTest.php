<?php
class TestOfDateRange extends PHPUnit_Framework_TestCase
{
    private $config;
    private $collection;
    private $CollectionToCsv;
    
    public function setUp( )
    {
        $this->config = array(
             'max' => 1000,
             'max_error' => 'Kan maximaal 1000 records exporteren naar csv.',
             'fields' => array(
                 'id' => 'getId',
                 'naam' => 'getTitel'
             )
        );
        
        $collection = array(
            new KVDdom_SimpleTestDomainObject( 1, 'domainobject1' ),
            new KVDdom_SimpleTestDomainObject( 2, 'domainobject2' ),
            new KVDdom_SimpleTestDomainObject( 3, 'domainobject3' ),
        );
        
        $this->collection = new KVDdom_DomainObjectCollection( $collection );
        
        $this->CollectionToCsv = new KVDutil_Transformer_CollectionToCsv( $this->collection , $this->config);
    }
    
    public function testGetCollection( )
    {
        $this->assertEquals( $this->collection, $this->CollectionToCsv->getCollection( ) );
    }
    
    public function testSetCollection( )
    {
        $collection = array(
            new KVDdom_SimpleTestDomainObject( 1, 'domainobject1' ),
            new KVDdom_SimpleTestDomainObject( 4, 'domainobject4' ),
            new KVDdom_SimpleTestDomainObject( 5, 'domainobject5' ),
        );
        
        $this->collection = new KVDdom_DomainObjectCollection( $collection );
        
        $this->CollectionToCsv->setCollection( new KVDdom_DomainObjectCollection( $collection ));
        $this->assertEquals( $this->collection, $this->CollectionToCsv->getCollection( ) );
    }
    
    public function testGetConfig( )
    {
        $this->assertEquals( $this->config, $this->CollectionToCsv->getConfig( ) );
    }
    
    public function testSetConfig( )
    {
        $config = array(
             'max' => 1000,
             'max_error' => 'Kan maximaal 1000 records exporteren naar csv.',
             'fields' => array(
                 'id' => 'getId',
                 'naam' => 'getTitel'
             )
        );
        $this->CollectionToCsv->setConfig( $config );
        
        $this->assertEquals( $config, $this->CollectionToCsv->getConfig( ) );
    }
    
    public function testTransform( )
    {
        $result = $this->CollectionToCsv->transform( );
        $this->assertEquals( '﻿id,naam
1,domainobject1
2,domainobject2
3,domainobject3
', $result );
    }
}
?>