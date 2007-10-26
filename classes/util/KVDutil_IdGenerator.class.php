<?php
/**
 * @package KVD.util
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_IdGenerator 
 * 
 * @package KVD.util
 * @subpackage Util
 * @since 28 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_IdGenerator
{
    /**
     * id 
     * 
     * @var integer
     */
    private $id;

    /**
     * __construct 
     * 
     * @param integer $start 
     * @return void
     */
    public function __construct( $start = 0 )
    {
        $this->id = 0;
    }

    /**
     * next 
     * 
     * @return integer
     */
    public function next( )
    {
        return $this->id++;
    }
}
?>
