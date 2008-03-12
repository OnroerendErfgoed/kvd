<?php
/**
 * @package KVD.dom
 * @subpackage collection
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_EditeerbareDomainObjectCollection 
 * 
 * @package KVD.dom
 * @subpackage collection
 * @since 13 april 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_EditeerbareDomainObjectCollection extends KVDdom_DomainObjectCollection
{
    /**
     * type 
     * 
     * @var string
     */
    protected $type = null;

    /**
     * __construct 
     * 
     * @param array     $collection 
     * @param string    $type
     * @return void
     */
    public function __construct( array $collection, $type = null)
    {
        parent::__construct( $collection );
        if ( $this->type === null ) {
            if ( $type == null ) {
                throw new Exception ( 'Collection is niet correct geinitialiseerd. Er moet een type worden opgegeven.');
            } else {
                $this->type = $type;
            }
        }
    }

    /**
     * checkType 
     * 
     * @param KVDdom_DomainObject $object 
     * @return void
     */
    protected function checkType( KVDdom_DomainObject $object )
    {
        if ( !$object instanceof $this->type ) {
            throw new KVDdom_OngeldigTypeException($object->getClass( ), $this->type);
        }
    }

    /**
     * add
     * 
     * @param KVDdom_Domainobject $object 
     * @return void
     */
    public function add( KVDdom_DomainObject $object )
    {
        $this->checkType( $object );
        if ( !$this->hasDomainObject( $object ) ) {
            $this->collection[$object->getId( )] = $object;
        }
    }

    /**
     * remove
     * 
     * @param KVDdom_DomainObject $object
     * @return void
     */
    public function remove( KVDdom_DomainObject $object )
    {
        $this->checkType( $object );
        if ( !$this->hasDomainObject( $object ) ) {
            throw new LogicException( 'Het object dat u probeert te verwijderen bestaat niet!' );
        }
        $this->rewind( );
        unset( $this->collection[$object->getId( )] );
    }

    /**
     * clear
     * 
     * @return void
     */
    public function clear( )
    {
        $this->collection = array( );
    }

    /**
     * getImmutableCollection 
     * 
     * Geef een collectie terug waarvan het aantal elementen vast ligt.
     * @return KVDdom_DomainObjectCollection
     */
    public function getImmutableCollection( )
    {
        return new KVDdom_DomainObjectCollection( $this->collection );
    }
}
?>
