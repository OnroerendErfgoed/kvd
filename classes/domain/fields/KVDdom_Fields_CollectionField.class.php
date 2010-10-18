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
 * KVDdom_Fields_CollectionField 
 * 
 * @package     KVD.dom
 * @subpackage  fields
 * @since       23 feb 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_Fields_CollectionField extends KVDdom_Fields_AbstractField
{
    const PLACEHOLDER = 'TE LADEN';

    /**
     * type 
     * 
     * @var string
     */
    protected $type;

    /**
     * sessie 
     * 
     * @var KVDdom_IReadSessie
     */
    protected $sessie;

    /**
     * mapper 
     * 
     * @var string
     */
    protected $mapper;

    /**
     * finder 
     * 
     * @var string
     */
    protected $finder;

    /**
     * __construct 
     * 
     * @param KVDdom_DomainObject   $dom 
     * @param string                $name 
     * @param string                $type 
     * @param KVDdom_IReadSessie    $sessie 
     * @param string                $domain_object_mapper 
     * @param string                $finder 
     * @return void
     */
    public function __construct( KVDdom_DomainObject $dom, $name, $type = null, KVDdom_IReadSessie $sessie = null, $domain_object_mapper = null, $finder = null )
    {
        parent::__construct( $dom, $name );
        $this->type = $type;
        $this->value = self::PLACEHOLDER;
        $this->sessie = $sessie;
        $this->mapper = $domain_object_mapper;
        $this->finder = $finder;
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
     * checkValues 
     * 
     * @return void
     */
    protected function checkValues( )
    {
        if ( $this->value === self::PLACEHOLDER ) {
            $this->value = $this->sessie->getMapper( $this->mapper )->{$this->finder}( $this->dom );
        }
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
        $this->checkValues();
        return $this->value->getImmutableCollection();
    }

    /**
     * add 
     * 
     * @param   KVDdom_DomainObject $value 
     * @return  void
     */
    public function add( KVDdom_DomainObject $value)
    {
        $this->checkValues();
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
        $this->checkValues();
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
        $this->checkValues();
        if ( count( $this->value ) > 0 ) {
            $this->dom->markFieldAsDirty($this);
            $this->value->clear( );
        }
    }
}
?>
