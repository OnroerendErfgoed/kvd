<?php
/**
 * @package     KVD.agavi
 * @subpackage  validation
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
 *  <li>'domain_object' : Name of the domain object whose mapper has the find method. Generally this is the domain object for which the id should be checked. Required.</li>
 *  <li>'session_name' : Name of the session that knows where to find the datamapper for the domain object. Optional and defaults to 'sessie'.</li>
 *  <li>'finder_name' : Name of the finder that can return the domain object whose id is being checked. Optional and defaults to 'findById'.</li>
 *  <li>'export' : Name of the exported domain object.</li>
 * </ul>
 * @package     KVD.agavi
 * @subpackage  validation
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

        $mapper = $this->sessie->getMapper( $this->getParameter( 'domain_object' ) );

        $finder = $this->getParameter( 'finder_name', 'findById' );

        if ( !method_exists( $mapper, $finder ) ) {
            throw new InvalidArgumentException( 'De methode met naam ' . $finder . ' bestaat niet op de mapper voor domain object ' . $this->getParameter( 'domain_object' ) );
        }

        try {
            $dom = $mapper->$finder( $value );
        } catch ( PDOException $e ) {
            $this->getContext( )->getLoggerManager( )->log( 
                sprintf(    'De KVDag_IdValidator kon een object niet vinden wegens een databank fout. De uitgevoerd methode was %s::%s. Dit leverde de volgende exception op: %s',
                            $this->getParameter( 'domain_object'),
                            $finder,
                            $e->getMessage( ) )
                            );
            $this->throwError( );
            return false;
            
        } catch ( KVDdom_DomainObjectNotFoundException $e ) {
            $this->throwError( );
            return false;
        }

		$this->export( $dom, $this->getParameter( 'export', 'exportedDo' ) );
        return true;
    }
}
?>
