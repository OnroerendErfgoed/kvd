<?php

class TestOfDimensieConvertor extends UnitTestCase
{
    private $_dimensieConvertor;

    public function setUp()
    {
        $this->_dimensieConvertor = new KVDutil_DimensieConvertor;
    }

    public function tearDown()
    {
        $this->_dimensieConvertor = null;
    }

    public function testAfmetingen()
    {
        $this->assertEqual ( $this->_dimensieConvertor->convertDimensie ( 1000 , 'km', 'm'), 1000000 );
        $this->assertEqual ( $this->_dimensieConvertor->convertDimensie ( 100 , 'm', 'mm'), 100000 );
        $this->assertEqual ( $this->_dimensieConvertor->convertDimensie ( 25 , 'dm', 'm'), 2.5 );
        $this->assertEqual ( $this->_dimensieConvertor->convertDimensie ( 399 , 'cm', 'mm'), 3990 );
        $this->assertEqual ( $this->_dimensieConvertor->convertDimensie ( 45 , 'mm', 'm'), 0.045 );
    }

    public function testGewichten()
    {
        $this->assertEqual ( $this->_dimensieConvertor->convertDimensie ( 10 , 'kg', 'gr'), 10000 );
        $this->assertEqual ( $this->_dimensieConvertor->convertDimensie ( 25 , 'gr', 'kg'), 0.025 );
    }

}
?>
