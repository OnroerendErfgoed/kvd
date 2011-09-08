<?php
/**
 * @package     KVD.dom
 * @subpackage  gebruiker
 * @version     $Id$    
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_NullGebruikerTest 
 * 
 * @package     KVD.dom
 * @subpackage  gebruiker
 * @since       2 mrt 2010     
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_NullGebruikerTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->gebruiker = new KVDdom_NullGebruiker( );
    }

    public function testNullGebruikerIsGebruiker( )
    {
        $this->assertType( 'KVDdom_Gebruiker', $this->gebruiker );
    }

    public function testGebruikerIsAnoniem( )
    {
        $this->assertEquals( 'anoniem', $this->gebruiker->getGebruikersNaam() );
        $this->assertEquals( 'anoniem', $this->gebruiker->getOmschrijving() );
    }

    public function testIdIsNull( )
    {
        $this->assertEquals( null, $this->gebruiker->getId() );
    }

    public function testGetClass( )
    {
        $this->assertEquals( 'KVDdom_NullGebruiker', $this->gebruiker->getClass( ) );
    }
}
?>
