<?php
/**
 * @package    KVD.util
 * @subpackage dimensie
 * @version    $Id$
 * @copyright  2004-2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * @package    KVD.util
 * @subpackage dimensie
 * @since      lang geleden
 * @copyright  2004-2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_DimensiesTest extends PHPUnit_Framework_TestCase
{
    private $_dimensies;

    private $_breedte;

    private $_gewicht;

    public function setUp ()
    {
        $toegestaneDimensies = array ( 'lengte' , 'breedte' , 'hoogte' , 'dikte' , 'diameter' , 'gewicht' );
        $this->_dimensies = new KVDutil_Dimensies ( $toegestaneDimensies );

    }

    public function tearDown ()
    {
        $this->_dimensies = null;
    }
    
    public function testOfEmptyCollection()
    {
        $this->assertFalse ( isset ( $this->_dimensies['lengte'] ) );
        $this->assertFalse ( $this->_dimensies['lengte'] );
    }

    public function testOfAddDimensie()
    {
        $breedte = new KVDutil_VoorwerpAfmeting ( 5 , 'cm', 'breedte' );
        $gewicht = new KVDutil_VoorwerpGewicht ( 0.346 , 'kg', 'gewicht' );
        
        $this->_dimensies['breedte'] = $breedte;
        $this->_dimensies['gewicht'] = $gewicht;

        $breedte2 = $this->_dimensies['breedte'];
        $gewicht2 = $this->_dimensies['gewicht'];
        
        $this->assertSame ( $breedte2 , $breedte );
        $this->assertSame ( $gewicht2 , $gewicht );
    }
    
    public function testOfRemoveDimensie()
    {
        $breedte = new KVDutil_VoorwerpAfmeting ( 5 , 'cm', 'breedte' );
        $this->_dimensies['breedte'] = $breedte;
        $this->assertSame ( $this->_dimensies['breedte'] , $breedte );
        unset ( $this->_dimensies['breedte'] );
        $this->assertFalse ( $this->_dimensies['breedte'] );
    }
    
    /**
     * testOfIllegalDimensie 
     * 
     * @expectedException InvalidArgumentException
     */
    public function testOfIllegalDimensie()
    {
        $diepte = new KVDutil_VoorwerpAfmeting ( 5 , 'cm' , 'diepte' );
        $this->_dimensies['diepte'] = $diepte;    
    }

    public function testOfOmschrijving()
    {
        $breedte = new KVDutil_VoorwerpAfmeting ( 5 , 'cm', 'breedte' );
        $gewicht = new KVDutil_VoorwerpGewicht ( 0.346 , 'kg', 'gewicht' );
        
        $this->_dimensies['breedte'] = $breedte;
        $this->_dimensies['gewicht'] = $gewicht;

        $this->assertEquals ( 'B: 50mm, G: 346gr.', $this->_dimensies->getOmschrijving() );
    }
    
    
}
?>
