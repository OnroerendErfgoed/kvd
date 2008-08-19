<?php
/**
 * @package     KVD.agavi
 * @subpackage  validator
 * @version     $Id$
 * @copyright   2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Validate whether the input is a valid NIS provincie number.
 * 
 * @package     KVD.agavi
 * @subpackage  validator
 * @since       19 aug 2008
 * @copyright   2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_ProvincieNISValidator extends AgaviValidator
{
    /**
     * Validate if the argument is a valid NIS provincie number. 
     * 
     * @return  bool    If the argument is valid.
     */
    protected function validate( )
    {
        $value = (int) $this->getData( $this->getArgument( ) );

        if ( !in_array( $value, array ( 10000, 20001, 30000, 40000, 70000 ) ) ) {
            $this->throwError( );
            return false;
        }

        $this->export( $value );

        return true;
    }
}
