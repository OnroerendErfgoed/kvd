<?php

class TestOfHuisnummerFacade extends UnitTestCase
{
    /**
     * @var KVDutil_HuisnummerFacade
     */
    private $facade;
    
    public function setUp( )
    {
       $this->facade = new KVDutil_HuisnummerFacade( ); 
    }

    public function tearDown( )
    {
        $this->facade = null;
    }

    public function testToArrayOneElement( )
    {
        $string = '25';
        $array = $this->facade->toArray( $string );
        $this->assertIsA( $array, 'array' );
        $this->assertEqual ( count( $array ) , 1);
        $this->assertIdentical ( $array[0], '25' );

    }

    public function testToArraySeveralElements( )
    {
        $string = '25,35,45';
        $array = $this->facade->toArray( $string );
        $this->assertIsA( $array, 'array' );
        $this->assertEqual ( count( $array ) , 3);
        $this->assertIdentical ( $array[0], '25' );
        $this->assertIdentical ( $array[0], '35' );
        $this->assertIdentical ( $array[0], '45' );
    }

    public function testToArrayUglyInput( )
    {
        $string = ' 25 , 35,45 ';
        $array = $this->facade->toArray( $string );
        $this->assertIsA( $array, 'array' );
        $this->assertEqual ( count( $array ) , 3);
        $this->assertIdentical ( $array[0], '25' );
        $this->assertIdentical ( $array[0], '35' );
        $this->assertIdentical ( $array[0], '45' );
    }

    public function testToStringOneElement( )
    {
        $array = array( '25' );
        $string = $this->facade->toString( $array );
        $this->assertIsA( $string, 'string' );
        $this->assertIdentical ( $string, '25' );
    }

    public function testToStringSeveralElements( )
    {
        $array = array( '25', '35', '45' );
        $string = $this->facade->toString( $array );
        $this->assertIsA( $string, 'string' );
        $this->assertIdentical ( $string, '25, 35, 35' );
    }

    public function testToStringUglyInput
    {
        $array = array( ' 25 ', '   35', '45   ' );
        $string = $this->facade->toString( $array );
        $this->assertIsA( $string, 'string' );
        $this->assertIdentical ( $string, '25, 35, 35' );
    }

    public function testSplitEenNummer( )
    {
        $label = '25';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 1);
        $this->assertIdentical ( $huisnummers[0], '25' );
    }

    public function testSplitNummerMetLetterBisnummer( )
    {
        $label = '25A';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 1);
        $this->assertIdentical ( $huisnummers[0], '25A' );
    }

    public function testSplitNummerMetCijferBisnummer( )
    {
        $label = '25/1';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 1);
        $this->assertIdentical ( $huisnummers[0], '25/1' );
    }

    public function testSplitHuisnummerMetCijferBisnummerGescheidenDoorUnderscore( )
    {
        $label = '111_1';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 1);
        $this->assertIdentical ( $huisnummers[0], '111_1' );
    }

    public function testSplitNummerMetBusnummer( )
    {
        $label = '25 bus 3';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 1, '25 bus 3 wordt gesplitst in een verkeerd aantal elementen: '.count( $huisnummers ) );
        $this->assertIdentical ( $huisnummers[0], '25 bus 3' );
    }

    public function testHuisnummerReeks( )
    {
        $label = '25,27,29,31';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4);
        $this->assertIdentical ( $huisnummers[0], '25' );
        $this->assertIdentical ( $huisnummers[1], '27' );
        $this->assertIdentical ( $huisnummers[2], '29' );
        $this->assertIdentical ( $huisnummers[3], '31' );
    }

    public function testHuisnummerBereikEvenVerschil( )
    {
        $label = '25-31';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4);
        $this->assertIdentical ( $huisnummers[0], '25' );
        $this->assertIdentical ( $huisnummers[1], '27' );
        $this->assertIdentical ( $huisnummers[2], '29' );
        $this->assertIdentical ( $huisnummers[3], '31' );
    }

    public function testHuisnummerBereikOnevenVerschil( )
    {
        $label = '25-32';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 8);
        $this->assertIdentical ( $huisnummers[0], '25' );
        $this->assertIdentical ( $huisnummers[1], '26' );
        $this->assertIdentical ( $huisnummers[2], '27' );
        $this->assertIdentical ( $huisnummers[3], '28' );
        $this->assertIdentical ( $huisnummers[4], '29' );
        $this->assertIdentical ( $huisnummers[5], '30' );
        $this->assertIdentical ( $huisnummers[6], '31' );
        $this->assertIdentical ( $huisnummers[7], '32' );
    }

    public function testHuisnummerBereikSpeciaal( )
    {
        $label = '25,26-31';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 7);
        $this->assertIdentical ( $huisnummers[0], '25' );
        $this->assertIdentical ( $huisnummers[1], '26' );
        $this->assertIdentical ( $huisnummers[2], '27' );
        $this->assertIdentical ( $huisnummers[3], '28' );
        $this->assertIdentical ( $huisnummers[4], '29' );
        $this->assertIdentical ( $huisnummers[5], '30' );
        $this->assertIdentical ( $huisnummers[6], '31' );
    }

    public function testCombinatieHuisnummerBereiken( )
    {
        $label = '25-31,18-26';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 9);
        $this->assertIdentical ( $huisnummers[0], '25' );
        $this->assertIdentical ( $huisnummers[1], '27' );
        $this->assertIdentical ( $huisnummers[2], '29' );
        $this->assertIdentical ( $huisnummers[3], '31' );
        $this->assertIdentical ( $huisnummers[4], '18' );
        $this->assertIdentical ( $huisnummers[5], '20' );
        $this->assertIdentical ( $huisnummers[6], '22' );
        $this->assertIdentical ( $huisnummers[7], '24' );
        $this->assertIdentical ( $huisnummers[8], '26' );
    }

    public function testBusnummerBereik( )
    {
        $label = '25 bus 3-7';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 5);
        $this->assertIdentical ( $huisnummers[0], '25 bus 3' );
        $this->assertIdentical ( $huisnummers[1], '25 bus 4' );
        $this->assertIdentical ( $huisnummers[2], '25 bus 5' );
        $this->assertIdentical ( $huisnummers[3], '25 bus 6' );
        $this->assertIdentical ( $huisnummers[4], '25 bus 7' );
    }

    public function testAlfaBusnummerBereik( )
    {
        $label = '25 bus C-F';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4);
        $this->assertIdentical ( $huisnummers[0], '25 bus C' );
        $this->assertIdentical ( $huisnummers[1], '25 bus D' );
        $this->assertIdentical ( $huisnummers[2], '25 bus E' );
        $this->assertIdentical ( $huisnummers[3], '25 bus F' );
    }

    public function testHuisnummerBereikMetLetterBisnummer( )
    {
        $label = '25C-F';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4);
        $this->assertIdentical ( $huisnummers[0], '25C' );
        $this->assertIdentical ( $huisnummers[1], '25D' );
        $this->assertIdentical ( $huisnummers[2], '25E' );
        $this->assertIdentical ( $huisnummers[3], '25F' );
    }
    
    public function testHuisnummerBereikMetCijferBisnummer( )
    {
        $label = '25/3-7';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 5);
        $this->assertIdentical ( $huisnummers[0], '25/3' );
        $this->assertIdentical ( $huisnummers[1], '25/4' );
        $this->assertIdentical ( $huisnummers[2], '25/5' );
        $this->assertIdentical ( $huisnummers[3], '25/6' );
        $this->assertIdentical ( $huisnummers[4], '25/7' );
    }

    public function testCombinatieBereiken( )
    {
        $label = '25C-F,28-32,29 bus 2-5';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 11);
        $this->assertIdentical ( $huisnummers[0], '25C' );
        $this->assertIdentical ( $huisnummers[1], '25D' );
        $this->assertIdentical ( $huisnummers[2], '25E' );
        $this->assertIdentical ( $huisnummers[3], '25F' );
        $this->assertIdentical ( $huisnummers[4], '28' );
        $this->assertIdentical ( $huisnummers[5], '30' );
        $this->assertIdentical ( $huisnummers[6], '32' );
        $this->assertIdentical ( $huisnummers[7], '29 bus 2' );
        $this->assertIdentical ( $huisnummers[8], '29 bus 3' );
        $this->assertIdentical ( $huisnummers[9], '29 bus 4' );
        $this->assertIdentical ( $huisnummers[10], '29 bus 5' );
    }

    public function testBisnummerEnHuisnummerBereik( )
    {
        $label = '2A,7-11';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4 );
        $this->assertIdentical ( $huisnummers[0], '2A' );
        $this->assertIdentical ( $huisnummers[1], '7' );
        $this->assertIdentical ( $huisnummers[2], '9' );
        $this->assertIdentical ( $huisnummers[3], '11' );
    }

    public function testBogusInput( )
    {
        $label = 'A,1/3,?';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual( count( $huisnummers ) , 3 );
        $this->assertIdentical ( $huisnummers[0], 'A' );
        $this->assertIdentical ( $huisnummers[1], '1/3' );
        $this->assertIdentical ( $huisnummers[2], '?' );
    }

    public function testInputWithSpaces( )
    {
        $label = ' A , 1/3 , 5 - 7 ';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual( count( $huisnummers ) , 4 );
        $this->assertIdentical ( $huisnummers[0], 'A' );
        $this->assertIdentical ( $huisnummers[1], '1/3' );
        $this->assertIdentical ( $huisnummers[2], '5' );
        $this->assertIdentical ( $huisnummers[3], '7' );
    }

    public function testMergeEenHuisnummer( )
    {
        $array = array( '25' );
        $label = $this->facade->merge( $array );
        $this->assertIsA( $label, 'string' );
        $this->assertEqual( $label, '25' );
    }

    public function testMergeHuisnummerMetBisnummerLetter
    {
        $array = array ( '25A' );
        $label = $this->facade->merge( $array );
        $this->assertIsA( $label, 'string' );
        $this->assertEqual( $label, '25A' );
    }

    public function testMergeNummerMetCijferBisnummer( )
    {
        $array = array( '25/1' );
        $label = $this->facade->merge( $array );
        $this->assertIsA( $label, 'string' );
        $this->assertEqual( $label, '25/1' );
    }

    public function testMergeHuisnummerMetCijferBisnummerGescheidenDoorUnderscore( )
    {
        $array = array( '111_1' );
        $label = $this->facade->merge( $array );
        $this->assertIsA( $label, 'string' );
        $this->assertEqual( $label, '111_1' );
    }


    public function testSplitNummerMetBusnummer( )
    {
        $array = array( '25 bus 3' );
        $label = $this->facade->merge( $array );
        $this->assertIsA( $label, 'string' );
        $this->assertEqual( $label, '25 bus 3' );
    }


    public function testMergeHuisnummerReeks( )
    {
        $array = '25-31';
        $label = $this->facade->merge( $array );
        $this->assertIsA( $label, 'string' );
        $this->assertEqual( $label, '25-31' );
    }

    public function testMergeHuisnummerBereikEvenVerschil( )
    {
        $array = '25, 27, 29, 31';
        $label = $this->facade->merge( $array );
        $this->assertIsA( $label, 'string' );
        $this->assertEqual( $label, '25-31' );

}

?>
