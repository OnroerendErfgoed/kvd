<?php
/**
 * @package     KVD.dom
 * @subpackage  fields
 * @version     $Id$
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Dit veld beheert een collection van domain objecten die niet lazy loaded 
 * kunnen zijn. Het veld bevat standaard een lege collection en kan op de 
 * normale manier geïnitialiseerd worden.
 * 
 * @package     KVD.dom
 * @subpackage  fields
 * @since       1 mrt 2011
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_Fields_StaticCollectionField extends KVDdom_Fields_AbstractField
{

    /**
     * type 
     * 
     * @var string
     */
    protected $type;

    /**
     * __construct 
     * 
     * @param KVDdom_DomainObject   $dom 
     * @param string                $name 
     * @param string                $type 
     * @return void
     */
    public function __construct( KVDdom_DomainObject $dom, $name, $type = null )
    {
        parent::__construct( $dom, $name );
        $this->type = $type;
        if ( $type === null ) {
            $type = 'KVDdom_DomainObject';
        }
        $this->value = new KVDdom_EditeerbareDomainObjectCollection( array(  ), $type );
    }

    /**
     * getType 
     * 
     * @return string
     */
    public function getType( )
    {
        return $this->type;
    }

    /**
     * initializeValue 
     * 
     * @param   KVDdom_EditeerbareDomainObjectCollection   $value 
     * @return  void
     */
    public function initializeValue( $value )
    {
        $this->value = $value;
    }

    /**
     * getValue 
     * 
     * @return  KVDdom_DomainObjectCollection
     */
    public function getValue( )
    {
        return $this->value->getImmutableCollection();
    }

    /**
     * setValue 
     * 
     * @param   KVDdom_DomainObjectCollection   $coll
     * @return  void
     */
    public function setValue( $coll  )
    {
        $this->clear( );
        foreach ( $coll as $elem ) {
            $this->add( $elem );
        }
    }

    /**
     * add 
     * 
     * @param   KVDdom_DomainObject $value 
     * @return  void
     */
    public function add( KVDdom_DomainObject $value)
    {
        if ( !$this->value->hasDomainObject( $value ) ) {
            $this->value->add( $value );
            $this->dom->markFieldAsDirty($this);
        }
    }

    /**
     * remove 
     * 
     * @param   KVDdom_DomainObject $value 
     * @return  void
     */
    public function remove( KVDdom_DomainObject $value )
    {
        if ( $this->value->hasDomainObject( $value ) ) {
            $this->value->remove( $value );
            $this->dom->markFieldAsDirty($this);
        }
    }

    /**
     * clear 
     * 
     * @return void
     */
    public function clear( )
    {
        if ( count( $this->value ) > 0 ) {
            $this->value->clear( );
            $this->dom->markFieldAsDirty($this);
        }
    }
}
?>
