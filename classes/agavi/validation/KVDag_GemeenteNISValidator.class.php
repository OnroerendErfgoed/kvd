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
 * A validator that determines whether the input is the NIS number of an existing Gemeente.
 * 
 * Parameters:
 * <ul>
 *  <li>'session_name' : Name of the session that knows where to find the datamapper for Gemeentes.</li> 
 * </ul>
 * @package     KVD.agavi
 * @subpackage  validator
 * @since       19 aug 2008
 * @copyright   2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_GemeenteNISValidator extends AgaviValidator
{
    /**
     * Validates whether the argument is a valid gemeente. 
     * 
     * @return  bool    True when valid.
     */
    protected function validate( )
    {
        $value = $this->getData( $this->getArgument( ) );

        $this->sessie = $this->getContext( )->getDatabaseManager( )->getDatabase( $this->getParameter( 'session_name', 'sessie' ) )->getConnection( );

        try {
            $dom = $this->sessie->getMapper( 'KVDdo_AdrGemeente' )->findById( $value );
        } catch ( KVDdom_DomainObjectNotFoundException $e ) {
            $this->throwError( );
            return false;
        }

        return true;
    }
}
