<?php
/**
 * @package KVD.thes
 * @subpackage visitor
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_TreeVisitorDojoDatastore 
 * 
 * @package KVD.thes
 * @subpackage visitor
 * @since 12 sep 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_TreeVisitorDojoDatastore extends KVDthes_AbstractTreeVisitor
{

	private $depth = 1;
    
    /**
     * result 
     * 
     * @var KVDthes_DojoDatastore
     */
    private $result;

    private $currItem;

    public function __construct( )
    {
        $this->result = new KVDthes_DojoDatastore();
    }

    public function enterRelations( KVDthes_Term $node )
    {
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
        $this->currItem = new KVDthes_DojoDatastoreTerm( $node->getId( ) , $node->getTerm( ) , $this->depth );
        $this->result->addItem( $this->currItem );
        return true;
    }

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

class KVDthes_DojoDatastore
{
    public $identifier = 'id';

    public $label = 'term';

    public $items = array( );

    public function addItem( KVDthes_DojoDatastoreTerm $item )
    {
        $id = $item->id;
        if ( !array_key_exists( $id, $this->items ) ) {
            $this->items[$id] = $item;
        }
    }

    public function clean( )
    {
        $this->items = array_values( $this->items );
    }
    
}

class KVDthes_DojoDatastoreTerm
{
    public $id;

    public $term;

    public $type;

    public $children = array( );

    public function __construct( $id, $term , $depth)
    {
        $this->id = $id;
        $this->term = $term;
        $this->type = 'L ' . $depth;
    }

    public function addChild( $id )
    {
        $ref = new KVDthes_DojoDatastoreReference( $id );
        $this->children[] = $ref;
    }
}

class KVDthes_DojoDatastoreReference
{
    public $_reference;

    public function __construct( $id )
    {
        $this->_reference = $id;
    }
}
?>
