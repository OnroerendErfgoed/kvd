<?php
/**
 * @package     KVD.gis
 * @subpackage  util
 * @version     $Id$
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

require_once ( 'PHPUnit/Framework.php' );

/**
 * @package     KVD.gis
 * @subpackage  util
 * @since       1.4.1
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDgis_UtilSridTest extends PHPUnit_Framework_Testcase
{
    public function testClassExists(  )
    {
        $util = new KVDgis_UtilSrid(  );
        $this->assertType( 'KVDgis_UtilSrid', $util );
    }

    public function testConstants( )
    {
        $this->assertEquals( '4326', KVDgis_UtilSrid::WGS84 );
        $this->assertEquals( '31370', KVDgis_UtilSrid::LAMBERT72 );
    }

}
?>
