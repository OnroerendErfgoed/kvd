<?php
class TestOfDateRange extends UnitTestCase
{
    const DATE_FORMAT = 'd-m-Y';
    
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
        $this->assertEqual( date( self::DATE_FORMAT , $this->now ) , $this->dr->getStart( ) );
        $this->assertEqual( date( self::DATE_FORMAT , $this->oneWeek) , $this->dr->getEinde( ) );
    }

    public function testGetOmschrijving( )
    {
        $this->assertEqual(    date( self::DATE_FORMAT , $this->now ) . 
                                ' tot ' . 
                                date( self::DATE_FORMAT , $this->oneWeek ) ,
                                $this->dr->getOmschrijving( ) );
    }

    public function testStringInput( )
    {
        $dr = new KVDutil_DateRange( '01-01-2004' , '01-12-2004' );
        $this->assertEqual( date( self::DATE_FORMAT , strtotime( '01-01-2004' ) ) , $dr->getStart( ) );
        $this->assertEqual( date( self::DATE_FORMAT , strtotime ( '01-12-2004' ) ) , $dr->getEinde( ) );
    }
}
?>
