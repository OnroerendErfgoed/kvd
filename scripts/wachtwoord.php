<?php
/**
 * @package KVD.util
 * @category scripts
 * @version $Id$
 * @since 6 jan 2008
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . '../vendor/autoload.php' );

$wg = new KVDutil_WachtwoordGenerator( );
echo $wg->generate( ) . "\n";
?>
