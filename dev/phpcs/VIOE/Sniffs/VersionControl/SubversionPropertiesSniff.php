<?php
/**
 * VIOE_Sniffs_VersionControl_SubversionPropertiesSniff 
 * 
 * @package     KVD.dev
 * @subpackage  phpcs
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * VIOE_Sniffs_VersionControl_SubversionPropertiesSniff 
 * 
 * @package     KVD.dev
 * @subpackage  phpcs
 * @since       8 jan 2009
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class VIOE_Sniffs_VersionControl_SubversionPropertiesSniff extends Generic_Sniffs_VersionControl_SubversionPropertiesSniff
{
    protected $properties = array( 'svn:keywords'  => 'Id',
                                   'svn:eol-style' => 'native'
                                 );
}
?>
