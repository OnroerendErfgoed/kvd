<?php
/**
 * @package KVD.thes
 * @subpackage visitor
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_TreeVisitorDojoDatastore
 *
 * @package KVD.thes
 * @subpackage visitor
 * @since 12 sep 2007
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
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
    public function __construct( $sortOrder = KVDthes_TermSorter::SORT_UNSORTED )
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
        if($node->hasRelations(KVDthes_Relation::REL_NT)) {
            $this->currItem = new KVDthes_DojoDatastoreComposite(
                $node->getId( ), $node->getQualifiedTerm( ),
                $this->depth, $node->getType()->getId()
            );
        } else {
            $this->currItem = new KVDthes_DojoDatastoreTerm(
                $node->getId( ), $node->getQualifiedTerm( ), $this->depth,
                $node->getType()->getId()
            );
        }
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
     * @param KVDthes_Term $term
     * @return boolean
     */
	public function enterComposite(KVDthes_Term $term)
    {
        $this->depth++;
		return true;
	}

    /**
     * leaveComposite
     *
     * @param KVDthes_Term $term
     * @return boolean
     */
    public function leaveComposite(KVDthes_Term $term)
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
     * term_type
     *
     * Het term_type van de term in de Dojo Datastore.
     * Dit is nodig om het onderscheid te kunnen maken tussen stam, gids en preferred termen
     * @var string
     */
    public $term_type;

    /**
     * __construct
     *
     * @param integer $id
     * @param string $term
     * @param integer $depth
     * @param string $term_type
     * @return void
     */
    public function __construct( $id, $term , $depth, $term_type)
    {
        $this->id = (string) $id;
        $this->term = $term;
        $this->type = 'L ' . $depth;
        $this->term_type = $term_type;
    }

    /**
     * addChild
     *
     * @param integer $id
     * @return void
     */
    public function addChild( $id )
    {

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
class KVDthes_DojoDatastoreComposite extends KVDthes_DojoDatastoreTerm
{

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
     * @param string $term_type
     * @return void
     */
    public function __construct( $id, $term , $depth, $term_type)
    {
        $this->id = (string) $id;
        $this->term = $term;
        $this->type = 'L ' . $depth;
        $this->term_type = $term_type;
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
