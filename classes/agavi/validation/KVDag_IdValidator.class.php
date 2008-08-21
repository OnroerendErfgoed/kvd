<?php
/**
 * @package     KVD.agavi
 * @subpackage  validator
 * @since       20 aug 2008
 * @copyright   2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * A validator that determines whether the input is the id of a domain object.
 * 
 * Parameters:
 * <ul>
 *  <li>'domain_object' : Name of the domain object for which the id should be checked. Required.</li>
 *  <li>'session_name' : Name of the session that knows where to find the datamapper for the domain object. Optional and defaults to 'sessie'.</li>
 * </ul>
 * @package     KVD.agavi
 * @subpackage  validator
 * @since       20 aug 2008
 * @copyright   2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_IdValidator extends AgaviValidator
{

    /**
     * Validates whether the argument is a valid id for a domain object. 
     * 
     * @return  bool    True when valid.
     */
    protected function validate( )
    {
        $value = $this->getData( $this->getArgument( ) );

        $this->sessie = $this->getContext( )->getDatabaseManager( )->getDatabase( $this->getParameter( 'session_name', 'sessie' ) )->getConnection( );

        try {
            $dom = $this->sessie->getMapper( $this->getParameter( 'domain_object' ) )->findById( $value );
        } catch ( KVDdom_DomainObjectNotFoundException $e ) {
            $this->throwError( );
            return false;
        }

        return true;
    }
}
?>
