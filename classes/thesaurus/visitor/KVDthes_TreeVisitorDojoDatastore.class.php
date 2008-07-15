<?php
/**
 * @package KVD.thes
 * @subpackage visitor
 * @version $Id$
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_TreeVisitorDojoDatastore 
 * 
 * @package KVD.thes
 * @subpackage visitor
 * @since 12 sep 2007
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_TreeVisitorDojoDatastore extends KVDthes_AbstractTreeVisitor
{

    /**
     * depth 
     * 
     * @var integer
     */
	private $depth = 1;
    
    /**
     * result 
     * 
     * @var KVDthes_DojoDatastore
     */
    private $result;

    /**
     * currItem 
     * 
     * @var KVDthes_DojoDatastoreTerm
     */
    private $currItem;

    /**
     * __construct 
     * 
     * @param integer $sortOrder    Zie de constanten in KVDthes_Relations
     * @return void
     */
    public function __construct( $sortOrder = KVDthes_Relations::SORT_UNSORTED )
    {
        $this->relationsSortOrder = $sortOrder;
        $this->result = new KVDthes_DojoDatastore();
    }

    /**
     * enterRelations 
     * 
     * @param KVDthes_Term $node 
     * @return boolean
     */
    public function enterRelations( KVDthes_Term $node )
    {
        $this->sortRelations( $node );
        return true;
    }

    /**
     * visit 
     * 
     * @param KVDthes_Term $node 
     * @return void
     */
	public function visit(KVDthes_Term $node)
	{
        $this->currItem = new KVDthes_DojoDatastoreTerm( $node->getId( ) , $node->getQualifiedTerm( ) , $this->depth );
        $this->result->addItem( $this->currItem );
        return true;
    }

    /**
     * visitRelation 
     * 
     * @param KVDthes_Relation $rel 
     * @return boolean
     */
    public function visitRelation( KVDthes_Relation $rel )
    {
        $this->currItem->addChild( $rel->getTerm( )->getId( ) );
        return true;
    }

    /**
     * enterComposite 
     * 
     * @return boolean
     */
	public function enterComposite()
	{
        $this->depth++;
		return true;
	}

    /**
     * leaveComposite 
     * 
     * @return boolean
     */
	public function leaveComposite()
	{
        $this->depth--;
        return true;
	}

    /**
     * getIterator
     * 
     * @param KVDthes_Relations $relations 
     * @return void
     */
    public function getIterator( KVDthes_Relations $relations )
    {
        return $relations->getNTIterator( );
    }

    /**
     * getResult 
     * 
     * @return KVDthes_DojoDatastore
     */
    public function getResult( )
    {
        $this->result->clean( );
        return $this->result;
    }
}

/**
 * KVDthes_DojoDatastore 
 * 
 * @package KVD.thes 
 * @subpackage visitor
 * @since 12 sep 2007
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_DojoDatastore
{
    /**
     * identifier 
     * 
     * @var string
     */
    public $identifier = 'id';

    /**
     * label 
     * 
     * @var string
     */
    public $label = 'term';

    /**
     * items 
     * 
     * @var array
     */
    public $items = array( );

    /**
     * addItem 
     * 
     * @param KVDthes_DojoDatastoreTerm $item 
     * @return void
     */
    public function addItem( KVDthes_DojoDatastoreTerm $item )
    {
        $id = $item->id;
        if ( !array_key_exists( $id, $this->items ) ) {
            $this->items[$id] = $item;
        }
    }

    /**
     * clean 
     * 
     * Kuis de item op zodat de datastore structuur correct is.
     * @return void
     */
    public function clean( )
    {
        $this->items = array_values( $this->items );
    }
    
}

/**
 * KVDthes_DojoDatastoreTerm 
 * 
 * @package KVD.thes
 * @subpackage visitor
 * @since 12 sep 2007
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_DojoDatastoreTerm
{
    /**
     * id 
     * 
     * Het id van de term waarover het gaat.
     * @var string
     */
    public $id;

    /**
     * term 
     * 
     * De naam van de term waarover het gaat.
     * @var string
     */
    public $term;

    /**
     * type 
     * 
     * Het type van de term in de Dojo Datastore.
     * Dit is nodig om de term op het correcte niveau te plaatsen.
     * @var string
     */
    public $type;

    /**
     * children 
     * 
     * Een lijst van termen die een NT zijn van deze term.
     * @var array
     */
    public $children = array( );

    /**
     * __construct 
     * 
     * @param integer $id 
     * @param string $term 
     * @param integer $depth 
     * @return void
     */
    public function __construct( $id, $term , $depth)
    {
        $this->id = (string) $id;
        $this->term = $term;
        $this->type = 'L ' . $depth;
    }

    /**
     * addChild 
     * 
     * @param integer $id 
     * @return void
     */
    public function addChild( $id )
    {
        $this->children[]= new KVDthes_DojoDatastoreReference( $id );
    }
}

/**
 * KVDthes_DojoDatastoreReference 
 * 
 * @package KVD.thes
 * @subpackage visitor
 * @since 12 sep 2007
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_DojoDatastoreReference
{
    /**
     * _reference 
     * 
     * @var string
     */
    public $_reference;

    /**
     * __construct 
     * 
     * @param integer $id 
     * @return void
     */
    public function __construct( $id )
    {
        $this->_reference = (string) $id;
    }
}
?>
