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
 * TestOfDimensie 
 * 
 * @package    KVD.util
 * @subpackage dimensie
 * @since      a long, long time ago
 * @copyright  2004-2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class TestOfDimensie extends PHPUnit_Framework_TestCase
{
    public function testOfVoorwerpAfmeting()
    {
        $dim = new KVDutil_VoorwerpAfmeting ( 5 , 'm', 'lengte' );
        $this->assertEquals ( '5000', $dim->getDimensie() );
        $this->assertEquals ( 'mm', $dim->getDimensieMaat() );
        $this->assertEquals ( 'lengte', $dim->getDimensieSoort() );
        $this->assertEquals ( 'L: 5000mm', $dim->getOmschrijving() );
    }

    public function testOfVoorwerpGewicht()
    {
        $dim = new KVDutil_VoorwerpGewicht ( 0.346 , 'kg', 'gewicht' );
        $this->assertEquals ( '346', $dim->getDimensie() );
        $this->assertEquals ( 'gr', $dim->getDimensieMaat() );
        $this->assertEquals ( 'gewicht', $dim->getDimensieSoort() );
        $this->assertEquals ( 'G: 346gr', $dim->getOmschrijving() );
    }
}

?>
