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

    public function testSplitEenNummer( )
    {
        $label = '25';
        $huisnummers = $this->facade->speedySplit( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 1);
        $this->assertEqual (  $huisnummers[0], '25' );
    }

    public function testSplitNummerMetLetterBisnummer( )
    {
        $label = '25A';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 1);
        $this->assertEqual (  $huisnummers[0], '25A' );
    }

    public function testSplitNummerMetCijferBisnummer( )
    {
        $label = '25/1';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 1);
        $this->assertEqual (  $huisnummers[0], '25/1' );
    }

   public function testSplitHuisnummerMetCijferBisnummerGescheidenDoorUnderscore( )
    {
        $label = '111_1';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 1);
        $this->assertEqual (  $huisnummers[0], '111/1' );
    }

    public function testSplitNummerMetBusnummer( )
    {
        $label = '25 bus 3';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 1, '25 bus 3 wordt gesplitst in een verkeerd aantal elementen: '.count( $huisnummers ) );
        $this->assertEqual (  $huisnummers[0], '25 bus 3' );
    }

    public function testHuisnummerReeks( )
    {
        $label = '25,27,29,31';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4);
        $this->assertEqual (  $huisnummers[0], '25' );
        $this->assertEqual (  $huisnummers[1], '27' );
        $this->assertEqual (  $huisnummers[2], '29' );
        $this->assertEqual (  $huisnummers[3], '31' );
    }

    public function testHuisnummerBereikEvenVerschil( )
    {
        $label = '25-31';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4);
        $this->assertEqual (  $huisnummers[0], '25' );
        $this->assertEqual (  $huisnummers[1], '27' );
        $this->assertEqual (  $huisnummers[2], '29' );
        $this->assertEqual (  $huisnummers[3], '31' );
    }

    public function testHuisnummerBereikOnevenVerschil( )
    {
        $label = '25-32';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 8);
        $this->assertEqual (  $huisnummers[0], '25' );
        $this->assertEqual (  $huisnummers[1], '26' );
        $this->assertEqual (  $huisnummers[2], '27' );
        $this->assertEqual (  $huisnummers[3], '28' );
        $this->assertEqual (  $huisnummers[4], '29' );
        $this->assertEqual (  $huisnummers[5], '30' );
        $this->assertEqual (  $huisnummers[6], '31' );
        $this->assertEqual (  $huisnummers[7], '32' );
    }

    public function testHuisnummerBereikSpeciaal( )
    {
        $label = '25,26-31';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 7);
        $this->assertEqual (  $huisnummers[0], '25' );
        $this->assertEqual (  $huisnummers[1], '26' );
        $this->assertEqual (  $huisnummers[2], '27' );
        $this->assertEqual (  $huisnummers[3], '28' );
        $this->assertEqual (  $huisnummers[4], '29' );
        $this->assertEqual (  $huisnummers[5], '30' );
        $this->assertEqual (  $huisnummers[6], '31' );
    }

    public function testCombinatieHuisnummerBereiken( )
    {
        $label = '25-31,18-26';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 9);
        $this->assertEqual (  $huisnummers[0], '25' );
        $this->assertEqual (  $huisnummers[1], '27' );
        $this->assertEqual (  $huisnummers[2], '29' );
        $this->assertEqual (  $huisnummers[3], '31' );
        
				$this->assertEqual (  $huisnummers[4], '18' );
        $this->assertEqual (  $huisnummers[5], '20' );
        $this->assertEqual (  $huisnummers[6], '22' );
        $this->assertEqual (  $huisnummers[7], '24' );
        $this->assertEqual (  $huisnummers[8], '26' );
    }

    public function testBusnummerBereik( )
    {
        $label = '25 bus 3-7';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 5);
        $this->assertEqual (  $huisnummers[0], '25 bus 3' );
        $this->assertEqual (  $huisnummers[1], '25 bus 4' );
        $this->assertEqual (  $huisnummers[2], '25 bus 5' );
        $this->assertEqual (  $huisnummers[3], '25 bus 6' );
        $this->assertEqual (  $huisnummers[4], '25 bus 7' );
    }

    public function testAlfaBusnummerBereik( )
    {
        $label = '25 bus C-F';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4);
        $this->assertEqual (  $huisnummers[0], '25 bus C' );
        $this->assertEqual (  $huisnummers[1], '25 bus D' );
        $this->assertEqual (  $huisnummers[2], '25 bus E' );
        $this->assertEqual (  $huisnummers[3], '25 bus F' );
    }

    public function testHuisnummerBereikMetLetterBisnummer( )
    {
        $label = '25C-F';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4);
        $this->assertEqual (  $huisnummers[0], '25C' );
        $this->assertEqual (  $huisnummers[1], '25D' );
        $this->assertEqual (  $huisnummers[2], '25E' );
        $this->assertEqual (  $huisnummers[3], '25F' );
    }
    
    public function testHuisnummerBereikMetCijferBisnummer( )
    {
        $label = '25/3-7';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 5);
        $this->assertEqual (  $huisnummers[0], '25/3' );
        $this->assertEqual (  $huisnummers[1], '25/4' );
        $this->assertEqual (  $huisnummers[2], '25/5' );
        $this->assertEqual (  $huisnummers[3], '25/6' );
        $this->assertEqual (  $huisnummers[4], '25/7' );
    }

    public function testCombinatieBereiken( )
    {
        $label = '25C-F,28-32,29 bus 2-5';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 11);
        $this->assertEqual (  $huisnummers[0], '25C' );
        $this->assertEqual (  $huisnummers[1], '25D' );
        $this->assertEqual (  $huisnummers[2], '25E' );
        $this->assertEqual (  $huisnummers[3], '25F' );
        $this->assertEqual (  $huisnummers[4], '28' );
        $this->assertEqual (  $huisnummers[5], '30' );
        $this->assertEqual (  $huisnummers[6], '32' );
        $this->assertEqual (  $huisnummers[7], '29 bus 2' );
        $this->assertEqual (  $huisnummers[8], '29 bus 3' );
        $this->assertEqual (  $huisnummers[9], '29 bus 4' );
        $this->assertEqual (  $huisnummers[10], '29 bus 5' );
    }

    public function testBisnummerEnHuisnummerBereik( )
    {
        $label = '2A,7-11';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4 );
        $this->assertEqual (  $huisnummers[0], '2A' );
        $this->assertEqual (  $huisnummers[1], '7' );
        $this->assertEqual (  $huisnummers[2], '9' );
        $this->assertEqual (  $huisnummers[3], '11' );
    }

/*    public function testBogusInput( )
    {
        $label = 'A,1/3,?';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual( count( $huisnummers ) , 3 );
        $this->assertEqual (  $huisnummers[0], 'A' );
        $this->assertEqual (  $huisnummers[1], '1/3' );
        $this->assertEqual (  $huisnummers[2], '?' );
    }
*/
    public function testInputWithSpaces( )
    {
        $label = ' A , 1/3 , 5 - 7 ';
        $huisnummers = $this->facade->split( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual( count( $huisnummers ) , 4 );
        $this->assertEqual (  $huisnummers[0], ' A ' );
        $this->assertEqual (  $huisnummers[1], '1/3' );
        $this->assertEqual (  $huisnummers[2], '5' );
        $this->assertEqual (  $huisnummers[3], '7' );
    }

		public function testMergeUnits()
		{
				$label = '32-36, 25-31, 1A-F, 2/1-10, 4 bus 1-30 , 43, 44 bus 1, 45/1, 46A';
        $huisnummers = $this->facade->merge( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 9);
        $this->assertEqual (  $huisnummers[0], '1A-F' );
        $this->assertEqual (  $huisnummers[1], '2/1-10' );
        $this->assertEqual (  $huisnummers[2], '4 bus 1-30' );
        $this->assertEqual (  $huisnummers[3], '25-31' );
        $this->assertEqual (  $huisnummers[4], '32-36' );
        $this->assertEqual (  $huisnummers[5], '43' );
        $this->assertEqual (  $huisnummers[6], '44 bus 1' );
        $this->assertEqual (  $huisnummers[7], '45/1' );
        $this->assertEqual (  $huisnummers[8], '46A' );
		}

    public function testMergeHuisnummerReeksen( )
    {
        $label = '32, 34, 36, 38, 25,27,29,31, 39, 40, 41, 42, 43, 44, 46, 47, 48, 49, 50';
        $huisnummers = $this->facade->merge( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 4);
        $this->assertEqual (  $huisnummers[0], '25-31' );
        $this->assertEqual (  $huisnummers[1], '32-50' );
        $this->assertEqual (  $huisnummers[2], '39-43' );
        $this->assertEqual (  $huisnummers[3], '47-49' );
    }
    
    public function testMergeCombinatieHuisnummerBereiken( )
    {
        $label = '25-31,18-26';
        $huisnummers = $this->facade->merge( $label );
        $this->assertIsA( $huisnummers, 'array' );
        $this->assertEqual ( count( $huisnummers ) , 2);
        $this->assertEqual (  $huisnummers[0], '18-26' );
        $this->assertEqual (  $huisnummers[1], '25-31' );
    }
    
    public function testPerformantie()
    {
			$start = microtime(true);
			$input = "25C-F,28-32,29 bus 2-5, 35C-F,38-42,39 bus 2-5, 45C-F,48-52,59 bus 2-5";
			for($i = 0; $i < 1000 ; $i++) {
				$this->facade->split($input);
			}
			$stop = microtime(true);
			echo "** NummerFacade\n";
			echo "Time: ".($stop-$start)." ($start - $stop);\n";
    }
    
    public function testSpeedyPerformantie()
    {
			$input = "25C-F,28-32,29 bus 2-5, 35C-F,38-42,39 bus 2-5, 45C-F,48-52,59 bus 2-5";
			$start = microtime(true);
			for($i = 0; $i < 1000 ; $i++) {
				$this->facade->speedySplit($input);
			}
			$stop = microtime(true);
			echo "** NummerFacade fast\n";
			echo "Time: ".($stop-$start)." ($start - $stop);\n";
    }


}

?>

