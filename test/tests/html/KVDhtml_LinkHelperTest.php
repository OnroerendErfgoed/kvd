<?php
/**
 * @package     KVD.html
 * @version     $Id$
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * @package     KVD.html
 * @since       1.4.1
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDhtml_LinkHelperTest extends PHPUnit_Framework_TestCase
{
    public function linkDataProvider(  )
    {
        return array(   array( '<a href="href" title="naam">naam</a>', 'href', 'naam' ),
                        array( '<a href="href" title="titel">naam</a>', 'href', 'naam', 'titel' ),
                        array( '<a href="href" title="titel" class="class">naam</a>', 'href', 'naam', 'titel', 'class' ),
                         array( '<a href="href" title="titel" target="target">naam</a>', 'href', 'naam', 'titel', '', 'target' ) );
    }

    public function setUp(  )
    {
        $this->lh = new KVDhtml_LinkHelper( );
    }

    /**
     * @dataProvider    linkDataProvider
     */
    public function testGenHtmlLink( $result, $href, $naam, $titel = '', $class = '', $target = '' )
    {
        $this->assertEquals( $result, $this->lh->genHtmlLink( $href, $naam, $titel, $class, $target ) );

    }
}
?>
