<?php
/**
 * @package    KVD.agavi
 * @subpackage validation
 * @version    $Id$
 * @copyright  2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * A validator that determines whether the input is the id of a domain object.
 * 
 * Parameters:
 * <ul>
 *  <li>'domain_object' : Name of the domain object whose mapper has the find method. 
 *  Generally this is the domain object for which the id should be checked. Required.</li>
 *  <li>'session_name' : Name of the session that knows where to find the datamapper 
 *  for the domain object. Optional and defaults to 'sessie'.</li>
 *  <li>'finder_name' : Name of the finder that can return the domain object whose 
 *  id is being checked. Optional and defaults to 'findById'.</li>
 *  <li>'export' : Name of the exported domain object.</li>
 * </ul>
 *
 * @package    KVD.agavi
 * @subpackage validation
 * @since      20 aug 2008
 * @copyright  2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
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

        // First we check if the object is of the correct type. 
        // If so, we are ok and export the argument.
        if ( $value instanceof KVDdom_DomainObject )  {
            if ( $value->getClass( ) == $this->getParameter( 'domain_object' ) ) {
                $this->export( $value );
                return true;
            } else {
                return false;
            }
        }

        // If the value isn't a KVDdom_DomainObject yet,
        // we will check if the ID has the correct data type (standard: int)
        $id_data_type = $this->getParameter( 'id_data_type', 'int');
        $toegelaten_waarden = array('int', 'string');
        if (!in_array($id_data_type, $toegelaten_waarden)){
            $this->throwError( 'invalid_id_data_type' );
            return false;
        }
        
        // If the value isn't a KVDdom_DomainObject yet,
        // we will check if the ID has the correct data type (standard: int)
        switch ($id_data_type) {
            case 'string':
                if(!is_string($value)){
                    return false;
                }
                break;
            case 'int': 
            default:
                $value = (int)$value;
                if(!is_int($value)){
                    return false;
                }
                break;
        }
        
        $this->sessie = $this->getContext( )
                             ->getDatabaseManager( )
                             ->getDatabase( $this->getParameter( 'session_name', 'sessie' ) )
                             ->getConnection( );

        $mapper = $this->sessie->getMapper( $this->getParameter( 'domain_object' ) );

        $finder = $this->getParameter( 'finder_name', 'findById' );

        if ( !method_exists( $mapper, $finder ) ) {
            throw new InvalidArgumentException( 
                'De methode met naam ' . $finder . 
                ' bestaat niet op de mapper voor domain object ' . 
                $this->getParameter( 'domain_object' ) );
        }

        try {
            $dom = $mapper->$finder( $value );
        } catch ( PDOException $e ) {
            $this->getContext( )->getLoggerManager( )->log( 
                sprintf( 'De KVDag_IdValidator kon een object niet vinden wegens een databank fout. 
                         De uitgevoerd methode was %s::%s. Dit leverde de volgende exception op: %s',
                         $this->getParameter('domain_object'),
                         $finder,
                         $e->getMessage( ) ) );
            $this->throwError( );
            return false;
        } catch ( KVDdom_DomainObjectDeletedException $e ) {
            $this->throwError( 'deleted_object' );
            return false;
        } catch ( KVDdom_DomainObjectNotFoundException $e ) {
            $this->throwError( 'unexisting_object' );
            return false;
        }

        $this->export( $dom );
        return true;
    }
}
?>
