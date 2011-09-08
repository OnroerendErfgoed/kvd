<?php
/**
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_SimpleTestDomainObject implements KVDdom_DomainObject
{
    protected $id;

    protected $titel;

    protected $dirty;

    public function __construct( $id, $titel = null ) {
        $this->id = $id;
        $this->titel = $titel;
    }

    public function getId( ) {
        return $this->id;
    }

    public function getTitel( ) {
        return $this->titel;
    }

    public function getOmschrijving(  ) {
        return $this->titel;
    }

    public function __toString(  )
    {
        return $this->getOmschrijving( );
    }

    public function getClass( ) 
    {
        return get_class( $this );
    }

    protected function markDirty( )
    {
        $this->dirty = true;
    }

    public function markFieldAsDirty( KVDdom_Fields_AbstractField $field )
    {
        $this->markDirty( );
    }
}

/**
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_TestValueDomainObject extends KVDdom_ValueDomainObject
{
    protected $titel;

    protected $fields = array( );

    public function __construct( $id, $titel, $data = array( ) )
    {
        parent::__construct( $id );
        $this->titel = $titel;
        $this->initializeFields( $data );
    }

    public function getTitel( ) 
    {
        return $this->titel;
    }

    protected function configureFields( )
    {
        $this->fields['naam'] = new KVDdom_Fields_SingleField( $this, 'naam', 'Onbepaald' );
        $this->fields['voornaam'] = new KVDdom_Fields_SingleField( $this, 'voornaam', 'X.' );
        $this->fields['ouders'] = new KVDdom_Fields_StaticCollectionField( $this, 'ouders', 'KVDdom_TestChangeableDomainObject' );
    }

    protected function pluralize( $property )
    {
        if ( $property == 'ouder' ) {
            return 'ouders';
        }
        return false;
    }

    public function getOmschrijving( ) 
    {
        return $this->titel;
    }
}

/**
 * KVDdom_TestChangeableDomainObject 
 * 
 * @package     KVD.dom
 * @category    test
 * @version     $Id$
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_TestChangeableDomainObject extends KVDdom_ChangeableDomainObject
{
    public function __construct( $id, $sessie, $data)
    {
        parent::__construct( $id, $sessie );
        $this->initializeFields( $data );
    }

    protected function configureFields( )
    {
        $this->fields['naam'] = new KVDdom_Fields_SingleField( $this, 'naam', 'Onbepaald' );
        $this->fields['voornaam'] = new KVDdom_Fields_SingleField( $this, 'voornaam', 'X.' );
        $this->fields['ouders'] = new KVDdom_Fields_CollectionField( $this, 'ouders', 'KVDdom_TestChangeableDomainObject', $this->_sessie );
    }

    protected function pluralize( $property )
    {
        if ( $property == 'ouder' ) {
            return 'ouders';
        }
        return false;
    }

    public function remove( )
    {
        $this->sessie->markRemoved( );
    }

    public function getOmschrijving( )
    {
        return $this->getNaam( ) . ', ' . $this->getVoornaam( );
    }
}
?>
