<?php
/**
 * @package     KVD.dom
 * @subpackage  fields
 * @version     $Id$
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_Fields_ArrayField 
 * 
 * Dit soort veld kan gebruikt worden om arrays van letterlijke waarden 
 * ( string, integer, ... ) op te slaan. Voor arrays van 
 * {@link KVDdom_DomainObject} moet het {@link KVDdom_Fields_CollectionField} 
 * gebruikt worden.
 *
 * @package     KVD.dom
 * @subpackage  fields
 * @since       3 sep 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_Fields_ArrayField extends KVDdom_Fields_AbstractField
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
        $this->value = array( );
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
     * @param   array $value 
     * @return  void
     */
    public function initializeValue( $value )
    {
        $this->value = $value;
    }

    /**
     * getValue 
     * 
     * @return  array
     */
    public function getValue( )
    {
        return $this->value;
    }

    /**
     * add 
     * 
     * @param   mixed   $value 
     * @return  void
     */
    public function add( $value)
    {
        if ( array_search( $value, $this->value ) === false ) {
            $this->value[] = $value;
            $this->dom->markFieldAsDirty($this);
        }
    }

    /**
     * remove 
     * 
     * @param   mixed   $value 
     * @return  void
     */
    public function remove( $value )
    {
        $key = array_search( $value, $this->value );
        if ( $key !== false ) {
            unset ( $this->value[$key] );
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
            $this->value = array( );
            $this->dom->markFieldAsDirty($this);
        }
    }
}
?>
