<?php
/**
 * @package KVD.thes
 * @subpackage mapper
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_XmlMapper
 *
 * @package KVD.thes
 * @subpackage mapper
 * @since 3 apil 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
abstract class KVDthes_XmlMapper implements KVDthes_IDataMapper
{
    /**
     * sessie
     *
     * @var KVDthes_ISessie
     */
    protected $sessie;

    /**
     * dom
     *
     * @var DOMDocument
     */
    protected $dom;

    /**
     * xp
     *
     * @var DOMXPath
     */
    protected $xp;

    /**
     * root
     *
     * De root node van deze thesaurus.
     * @var KVDthes_Term
     */
    protected $root = null;

    /**
     * __construct
     *
     * @param KVDthes_ISessie $sessie
     * @param array $parameters
     */
    public function __construct ( $sessie , $parameters )
    {
        $this->sessie = $sessie;
        if ( !array_key_exists( 'file' , $parameters ) ) {
            throw new KVDdom_MapperConfigurationException( 'De benodigde parameter file ontbreekt.' , $this );
        }
        if ( !file_exists( $parameters['file'] ) ) {
            throw new KVDdom_MapperConfigurationException( 'De parameter file verwijst niet naar een geldig bestand.' , $this );
        }
        $this->dom = new DOMDocument( '1.0' );
        $this->dom->load( $parameters['file'] );
        $this->xp = new DOMXPath( $this->dom );
        $this->loadIdentityMap( );
    }

    /**
     * findById
     *
     * @param integer $id
     * @return KVDthes_Term
     * @throws Exception - Indien de term niet bestaat
     */
    public function findById( $id )
    {
        if ( ( $term = $this->sessie->getIdentityMap( )->getDomainObject( $this->getReturnType( ) , $id ) ) === false ) {
            throw new Exception ( 'Term bestaat niet.' );
        }
        return $term;
    }

    /**
     * findAll
     *
     * @return KVDdom_DomainObjectCollection
     */
    public function findAll( )
    {
        if ( ( $all = $this->sessie->getIdentityMap( )->getDomainObjects( $this->getReturnType( ) ) ) === null ) {
            $all = array( );
        }
        usort( $all, array ( $this, 'compareTerm' ) );
        return new KVDdom_DomainObjectCollection( $all );
    }

    /**
     * compareTerm
     *
     * Callback functie voor de findAll methode.
     * @param KVDthes_Term $a
     * @param KVDthes_Term $b
     * @return integer
     */
    private function compareTerm( KVDthes_Term $a, KVDthes_Term $b )
    {
        if ( $a->getTerm( ) < $b->getTerm( ) ) {
            return -1;
        }
        if ( $a->getTerm( ) > $b->getTerm( ) ) {
            return 1;
        }
        return 0;
    }

    /**
     * findRoot
     *
     * @todo error handling indien de root niet gevonden werd.
     * @return KVDthes_Term
     */
    public function findRoot( )
    {
        return $this->root;
    }

    /**
     * Een two-pass loading systeem. Eerst worden de termen allemaal geladen en dan worden de relaties gelegd.
     */
    private function loadIdentityMap( )
    {
        foreach ( $this->dom->getElementsByTagName( 'term' ) as $term ) {
            $id = $term->getElementsByTagName( 'termId' )->item( 0 )->nodeValue;
            $tc = $term->getElementsByTagName( 'termType' )->item( 0 )->nodeValue;
            $type = $this->loadType( $tc );
            $name = $term->getElementsByTagName( 'termName' )->item( 0 )->nodeValue;
            $language = $term->getElementsByTagName( 'termLanguage' )->item( 0 )->nodeValue;
            $list = $this->xp->query( 'termQualifier', $term );
            $qualifier = ( $list->length > 0 ) ? $list->item(0)->nodeValue : null;
            $list = $this->xp->query( 'termSortKey', $term );
            $sortKey = ( $list->length > 0 ) ? $list->item(0)->nodeValue : null;
            $termType = $this->getReturnType( );
            $thesaurus = $this->doLoadThesaurus( );
            $termObj = new $termType($id, $this->sessie , $name, $type, $qualifier, $language, $sortKey, null, $thesaurus);
            if ( $this->root == null ) {
                $this->root = $termObj;
            }
        }
    }

    /**
     * doLoadThesaurus
     *
     * @return KVDthes_Thesaurus
     */
    private function doLoadThesaurus( )
    {
        return KVDthes_Thesaurus::newNull( );
    }

    /**
     * loadType
     *
     * @param   string              $code
     * @return  KVDthes_TermType
     */
    private function loadType( $code )
    {
        switch ( $code ) {
            case 'ND':
                return new KVDthes_TermType( 'ND', 'Preferred Term');
                break;
            case 'HR':
                return new KVDthes_TermType( 'HR', 'Hierarchy Root');
                break;
            case 'NL':
                return new KVDthes_TermType( 'NL', 'Guide Term');
                break;
            case 'PT':
            default:
                return new KVDthes_TermType( 'PT', 'Preferred Term');
                break;
        }
    }

    /**
     * findNodeForTerm
     *
     * @param   KVDthes_Term $termObj
     * @return  DOMNode
     */
    private function findNodeForTerm( KVDthes_Term $termObj )
    {
        $list = $this->xp->query( '/Zthes/term[termId="' . $termObj->getId( ) . '"]' );

        if ( $list->length == 0 ) {
            throw new RuntimeException ( 'Unable to find term node with id ' . $termObj->getId( ) . '!' );
        }

        return $list->item(0);
    }


    /**
     * loadRelations
     *
     * @param KVDdom_DomainObject $termObj
     * @return KVDdom_DomainObject
     */
    public function loadRelations( KVDthes_Term $termObj )
    {
        $term = $this->findNodeForTerm( $termObj );
        foreach ( $term->getElementsByTagName( 'relation' ) as $relation ) {
            $relId = $relation->getElementsByTagName( 'termId' )->item( 0 )->nodeValue;
            $relType = $relation->getElementsByTagName( 'relationType' )->item( 0 )->nodeValue;
            $termObj->loadRelation ( new KVDthes_Relation ( $relType , $this->findById( $relId ) ) );
        }
        $termObj->setLoadState( KVDthes_Term::LS_REL );

        return $termObj;
    }

    /**
     * loadNotes
     *
     * @param   KVDthes_Term $term
     * @return  KVDthes_Term
     */
    public function loadNotes( KVDthes_Term $term )
    {
        $termElem = $this->findNodeForTerm( $term );

        $notes = array( );

        foreach ( $termElem->getElementsByTagName( 'termNote' ) as $note ) {
            if ( $note->hasAttribute( 'label' ) ) {
                $noteType = strtolower( $note->getAttribute( 'label' ) ) . 'Note';
            } else {
                $noteType = 'scopeNote';
            }
            $notes[$noteType] = trim( $note->nodeValue );
        }
        $term->loadNotes( $notes );
        return $term;
    }

    /**
     * getReturnType
     *
     * @return string
     */
    abstract protected function getReturnType( );
}
?>
