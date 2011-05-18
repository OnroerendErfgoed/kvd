<?php
/**
 * @package    KVD.thes
 * @subpackage visitor
 * @version    $Id$
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Deze vistitor transformeert een in-memory tree van thesaurus termen tot een zthes file.
 * 
 * @package    KVD.thes
 * @subpackage visitor
 * @since      19 maart 2007
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_XMLVisitor extends KVDthes_AbstractSimpleVisitor
{
    /**
     * dom 
     * 
     * @var DOMDocument
     */
    private $dom;

    /**
     * zthes 
     * 
     * @var mixed
     */
    private $zthes;

    /**
     * result 
     * 
     * @var string
     */
    public $result = '';

    public function __construct( )
    {
        $this->dom = new DOMDocument( '1.0' );
        $this->zthes = $this->dom->createElement( 'Zthes');
        $this->dom->appendChild( $this->zthes );
    }

    /**
     * visit 
     * 
     * @param KVDthes_Term $node 
     * @return void
     */
    public function visit(KVDthes_Term $node)
    {
        $this->term = $this->dom->createElement( 'term' );
        $this->term->appendChild( $this->createTermId( $node->getId( ) ) );
        $this->term->appendChild( $this->createTermName( $node->getTerm( ) ) );
        $this->term->appendChild( $this->createTermType( $node->getType( )->getId( ) ) );
        $this->term->appendChild( $this->createTermLanguage( 'nl-BE' ) );
        return true;
    }

    public function visitRelation(KVDthes_Relation $relation)
    {
        $relNode = $this->dom->createElement( 'relation' );
        $relNode->appendChild( $this->createRelationType( $relation->getType( ) ) );
        $relNode->appendChild( $this->createTermId( $relation->getTerm( )->getId( ) ) );
        $relNode->appendChild( $this->createTermName( $relation->getTerm( )->getTerm( ) ) );
        $this->term->appendChild( $this->createTermType( $node->getType( )->getId( ) ) );
        $relNode->appendChild( $this->createTermLanguage( 'nl-BE' ) );
        $this->term->appendChild( $relNode );
        return true;
    }

    private function createTermName( $name )
    {
        return $this->createNode( 'termName', $name );
    }

    private function createTermLanguage( $language )
    {
        return $this->createNode( 'termLanguage', $language );
    }

    private function createTermType( $type )
    {
        return $this->createNode( 'termType', $type );
    }

    private function createTermId( $id )
    {
        return $this->createNode( 'termId', $id );
    }

    private function createNode( $type, $value )
    {
        $node = $this->dom->createElement( $type );
        $node->appendChild( $this->dom->createTextNode( $value ) );
        return $node;
    }

    private function createRelationType( $type )
    {
        return $this->createNode( 'relationType', $type );
    }

    private function createRelations( $term )
    {
        $nodes = array( );
        foreach ( $term->getRelations( ) as $rel ) {
            $nodes[] = $relNode;
        }
        return $nodes;
    }

    /**
     * enterComposite 
     * 
     * @return boolean
     */
    public function enterComposite( $node )
    {
        return true;
    }

    /**
     * leaveComposite 
     * 
     * @return boolean
     */
    public function leaveComposite( $node )
    {
        $this->zthes->appendChild( $this->term );
        $this->term = null;
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
        return $relations->getIterator( );
    }

    public function getResult( )
    {
        return $this->dom->saveXML( );
    }

    public function saveAsFile( $file )
    {
        return $this->dom->save( $file );
    }
}
?>
