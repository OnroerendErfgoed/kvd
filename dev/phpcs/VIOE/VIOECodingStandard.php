<?php
/**
 * VIOE Coding Standard.
 * 
 * @package     KVD.dev
 * @subpackage  phpcs
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * VIOE Coding Standard
 * 
 * @package     KVD.dev
 * @subpackage  phpcs
 * @since       8 jan 2009
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_VIOE_VIOECodingStandard extends PHP_CodeSniffer_Standards_CodingStandard {
    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The VIOE coding standard uses some generic sniffs, and
     * some custom sniffs.
     * 
     * @return array
     */
    public function getIncludedSniffs( ) 
    {
        return array( 
                    'Generic',
                    );
    }

    /**
     * Return a list of external sniffs to exclude with this standard.
     *
     * The VIOE coding standard uses some generic sniffs, and
     * some custom sniffs.
     * 
     * @return array
     */
    public function getExcludedSniffs( )
    {
        return array(
                    'Generic/Sniffs/Functions/OpeningFunctionBraceKernighanRitchieSniff.php',
                    'Generic/Sniffs/VersionControl/SubversionPropertiesSniff.php',
                    'Generic/Sniffs/Formatting/MultipleStatementAlignmentSniff.php',
                    'Generic/Sniffs/PHP/UpperCaseConstantSniff.php',
                    'Generic/Sniffs/Formatting/NoSpaceAfterCastSniff.php'
                    );
    }
}
?> 
