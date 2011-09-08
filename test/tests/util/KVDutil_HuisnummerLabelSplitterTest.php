<?php
/**
 * @package     KVD.util
 * @version     $Id$
 * @copyright   2008-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author      Dieter Standaert <dieter.standaert@hp.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_TestOfHuisnummerLabelSplitter 
 * 
 * @package     KVD.util
 * @since       2008
 * @copyright   2008-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_TestOfHuisnummerLabelSplitter extends PHPUnit_Framework_TestCase
{
    /**
     * @var KVDutil_HuisnummerLabelSplitter
     */
    private $splitter;
    
    public function setUp( )
    {
       $this->splitter = new KVDutil_HuisnummerLabelSplitter( ); 
    }

    public function tearDown( )
    {
        $this->splitter = null;
    }

    public function testSplitEenNummer( )
    {
        $label = '25';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( 1, count( $huisnummers ) , 1);
        $this->assertEquals ( '25', $huisnummers[0] );
    }

    public function testSplitNummerMetLetterBisnummer( )
    {
        $label = '25A';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers);
        $this->assertEquals ( 1, count( $huisnummers ) );
        $this->assertEquals ( '25A', $huisnummers[0] );
    }

    public function testSplitNummerMetCijferBisnummer( )
    {
        $label = '25/1';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( 1, count( $huisnummers ) );
        $this->assertEquals ( '25/1', $huisnummers[0] );
    }

    public function testSplitHuisnummerMetCijferBisnummerGescheidenDoorUnderscore( )
    {
        $label = '111_1';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( count( $huisnummers ) , 1);
        $this->assertEquals ( $huisnummers[0], '111_1' );
    }

    public function testSplitNummerMetBusnummer( )
    {
        $label = '25 bus 3';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( count( $huisnummers ) , 1, '25 bus 3 wordt gesplitst in een verkeerd aantal elementen: '.count( $huisnummers ) );
        $this->assertEquals ( $huisnummers[0], '25 bus 3' );
    }

    public function testHuisnummerReeks( )
    {
        $label = '25,27,29,31';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( count( $huisnummers ) , 4);
        $this->assertEquals ( $huisnummers[0], '25' );
        $this->assertEquals ( $huisnummers[1], '27' );
        $this->assertEquals ( $huisnummers[2], '29' );
        $this->assertEquals ( $huisnummers[3], '31' );
    }

    public function testHuisnummerBereikEvenVerschil( )
    {
        $label = '25-31';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( count( $huisnummers ) , 4);
        $this->assertEquals ( $huisnummers[0], '25' );
        $this->assertEquals ( $huisnummers[1], '27' );
        $this->assertEquals ( $huisnummers[2], '29' );
        $this->assertEquals ( $huisnummers[3], '31' );
    }

    public function testHuisnummerBereikOnevenVerschil( )
    {
        $label = '25-32';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( count( $huisnummers ) , 8);
        $this->assertEquals ( $huisnummers[0], '25' );
        $this->assertEquals ( $huisnummers[1], '26' );
        $this->assertEquals ( $huisnummers[2], '27' );
        $this->assertEquals ( $huisnummers[3], '28' );
        $this->assertEquals ( $huisnummers[4], '29' );
        $this->assertEquals ( $huisnummers[5], '30' );
        $this->assertEquals ( $huisnummers[6], '31' );
        $this->assertEquals ( $huisnummers[7], '32' );
    }

    public function testHuisnummerBereikSpeciaal( )
    {
        $label = '25,26-31';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( 7, count( $huisnummers ) );
        $this->assertEquals ( $huisnummers[0], '25' );
        $this->assertEquals ( $huisnummers[1], '26' );
        $this->assertEquals ( $huisnummers[2], '27' );
        $this->assertEquals ( $huisnummers[3], '28' );
        $this->assertEquals ( $huisnummers[4], '29' );
        $this->assertEquals ( $huisnummers[5], '30' );
        $this->assertEquals ( $huisnummers[6], '31' );
    }

    public function testCombinatieHuisnummerBereiken( )
    {
        $label = '25-31,18-26';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( count( $huisnummers ) , 9);
        $this->assertEquals ( $huisnummers[0], '25' );
        $this->assertEquals ( $huisnummers[1], '27' );
        $this->assertEquals ( $huisnummers[2], '29' );
        $this->assertEquals ( $huisnummers[3], '31' );
        $this->assertEquals ( $huisnummers[4], '18' );
        $this->assertEquals ( $huisnummers[5], '20' );
        $this->assertEquals ( $huisnummers[6], '22' );
        $this->assertEquals ( $huisnummers[7], '24' );
        $this->assertEquals ( $huisnummers[8], '26' );
    }

    public function testBusnummerBereik( )
    {
        $label = '25 bus 3-7';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( 5, count( $huisnummers ) );
        $this->assertEquals ( $huisnummers[0], '25 bus 3' );
        $this->assertEquals ( $huisnummers[1], '25 bus 4' );
        $this->assertEquals ( $huisnummers[2], '25 bus 5' );
        $this->assertEquals ( $huisnummers[3], '25 bus 6' );
        $this->assertEquals ( $huisnummers[4], '25 bus 7' );
    }

    public function testAlfaBusnummerBereik( )
    {
        $label = '25 bus C-F';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( count( $huisnummers ) , 4);
        $this->assertEquals ( $huisnummers[0], '25 bus C' );
        $this->assertEquals ( $huisnummers[1], '25 bus D' );
        $this->assertEquals ( $huisnummers[2], '25 bus E' );
        $this->assertEquals ( $huisnummers[3], '25 bus F' );
    }

    public function testHuisnummerBereikMetLetterBisnummer( )
    {
        $label = '25C-F';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( 4, count( $huisnummers ) );
        $this->assertEquals ( $huisnummers[0], '25C' );
        $this->assertEquals ( $huisnummers[1], '25D' );
        $this->assertEquals ( $huisnummers[2], '25E' );
        $this->assertEquals ( $huisnummers[3], '25F' );
    }
    
    public function testHuisnummerBereikMetCijferBisnummer( )
    {
        $label = '25/3-7';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( 5, count( $huisnummers ) );
        $this->assertEquals ( $huisnummers[0], '25/3' );
        $this->assertEquals ( $huisnummers[1], '25/4' );
        $this->assertEquals ( $huisnummers[2], '25/5' );
        $this->assertEquals ( $huisnummers[3], '25/6' );
        $this->assertEquals ( $huisnummers[4], '25/7' );
    }

    public function testCombinatieBereiken( )
    {
        $label = '25C-F,28-32,29 bus 2-5';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( 11, count( $huisnummers ) );
        $this->assertEquals ( $huisnummers[0], '25C' );
        $this->assertEquals ( $huisnummers[1], '25D' );
        $this->assertEquals ( $huisnummers[2], '25E' );
        $this->assertEquals ( $huisnummers[3], '25F' );
        $this->assertEquals ( $huisnummers[4], '28' );
        $this->assertEquals ( $huisnummers[5], '30' );
        $this->assertEquals ( $huisnummers[6], '32' );
        $this->assertEquals ( $huisnummers[7], '29 bus 2' );
        $this->assertEquals ( $huisnummers[8], '29 bus 3' );
        $this->assertEquals ( $huisnummers[9], '29 bus 4' );
        $this->assertEquals ( $huisnummers[10], '29 bus 5' );
    }

    public function testBisnummerEnHuisnummerBereik( )
    {
        $label = '2A,7-11';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals ( 4, count( $huisnummers ) );
        $this->assertEquals ( $huisnummers[0], '2A' );
        $this->assertEquals ( $huisnummers[1], '7' );
        $this->assertEquals ( $huisnummers[2], '9' );
        $this->assertEquals ( $huisnummers[3], '11' );
    }

    public function testBogusInput( )
    {
        $label = 'A,1/3,?';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals( 3, count( $huisnummers ) );
        $this->assertEquals ( $huisnummers[0], 'A' );
        $this->assertEquals ( $huisnummers[1], '1/3' );
        $this->assertEquals ( $huisnummers[2], '?' );
    }

    public function testInputWithSpaces( )
    {
        $label = ' A , 1/3 , 5 - 7 ';
        $huisnummers = $this->splitter->split( $label );
        $this->assertType( 'array', $huisnummers );
        $this->assertEquals( 4, count( $huisnummers ) );
        $this->assertEquals ( $huisnummers[0], 'A' );
        $this->assertEquals ( $huisnummers[1], '1/3' );
        $this->assertEquals ( $huisnummers[2], '5' );
        $this->assertEquals ( $huisnummers[3], '7' );
    }

}

?>
