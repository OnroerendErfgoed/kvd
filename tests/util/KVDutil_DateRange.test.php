<?php
class TestOfDateRange extends UnitTestCase
{
    private $now;

    private $oneWeek;
    
    private $dr;
    
    public function setUp( )
    {
        $this->now = time();
        $this->oneWeek = time( ) + ( 7*24*60*60);
        $this->dr = new KVDutil_DateRange( $this->now , $this->oneWeek );
    }
    
    public function testGetters( )
    {
        $this->assertEqual( $this->now , $this->dr->getStart( ) );
        $this->assertEqual( $this->oneWeek , $this->dr->getEinde( ) );
    }

    public function testGetOmschrijving( )
    {
        $this->assertEqual(    date( KVDutil_DateRange::DATE_FORMAT , $this->now ) . 
                                ' tot ' . 
                                date( KVDutil_DateRange::DATE_FORMAT , $this->oneWeek ) ,
                                $this->dr->getOmschrijving( ) );
    }
}
?>
