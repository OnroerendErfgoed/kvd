<?php

class KVDutil_DimensieConvertorTest extends PHPUnit_Framework_TestCase
{
    protected $dimensieConvertor;

    public function setUp()
    {
        $this->dimensieConvertor = new KVDutil_DimensieConvertor;
    }

    public function tearDown()
    {
        $this->dimensieConvertor = null;
    }

    public function testAfmetingen()
    {
        $this->assertEquals ( $this->dimensieConvertor->convertDimensie ( 1000 , 'km', 'm'), 1000000 );
        $this->assertEquals ( $this->dimensieConvertor->convertDimensie ( 100 , 'm', 'mm'), 100000 );
        $this->assertEquals ( $this->dimensieConvertor->convertDimensie ( 25 , 'dm', 'm'), 2.5 );
        $this->assertEquals ( $this->dimensieConvertor->convertDimensie ( 399 , 'cm', 'mm'), 3990 );
        $this->assertEquals ( $this->dimensieConvertor->convertDimensie ( 45 , 'mm', 'm'), 0.045 );
    }

    public function testGewichten()
    {
        $this->assertEquals ( $this->dimensieConvertor->convertDimensie ( 10 , 'kg', 'gr'), 10000 );
        $this->assertEquals ( $this->dimensieConvertor->convertDimensie ( 25 , 'gr', 'kg'), 0.025 );
    }

}
?>
