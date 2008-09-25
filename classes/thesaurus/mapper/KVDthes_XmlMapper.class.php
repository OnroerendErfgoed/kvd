<?php
/**
 * @package KVD.thes
 * @subpackage mapper
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_XmlMapper 
 * 
 * @package KVD.thes
 * @subpackage mapper
 * @since 3 apil 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
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
            $name = $term->getElementsByTagName( 'termName' )->item( 0 )->nodeValue;
            $language = $term->getElementsByTagName( 'termLanguage' )->item( 0 )->nodeValue;
            $qualifier = $term->getElementsByTagName( 'termQualifier')->length > 0 ? $term->getElementsByTagName( 'termQualifier' )->item( 0 )->nodeValue : null;
            if ( $qualifier === '' ) {
                $qualifier = null;
            }
            $sortKey = $term->getElementsByTagName( 'termSortKey')->length > 0 ? $term->getElementsByTagName( 'termSortKey' )->item( 0 )->nodeValue : null;
            $termType = $this->getReturnType( );
            $thesaurus = $this->doLoadThesaurus( );
            $termObj = new $termType( $this->sessie , $id , $name , $qualifier, $language, $sortKey, null, null, $thesaurus);
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
     * findNodeForTerm 
     * 
     * @param   KVDthes_Term $termObj 
     * @return  DOMNode
     */
    private function findNodeForTerm( KVDthes_Term $termObj )
    {
        $list = $this->xp->query( '/Zthes/term[termId="' . $termObj->getId( ) . '"]' );

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
            $termObj->addRelation ( new KVDthes_Relation ( $relType , $this->findById( $relId ) ) );
        }
        $termObj->setLoadState( KVDthes_Term::LS_REL );

        return $termObj;
    }

    /**
     * loadScopeNote 
     * 
     * @param KVDthes_Term $termObj 
     * @return KVDthes_Term
     */
    public function loadScopeNote( KVDthes_Term $termObj )
    {
        $this->loadNote( $this->findNodeForTerm($termObj), 'Scope' , $termObj);
        
        return $termObj;
    }

    /**
     * loadSourceNote 
     * 
     * @param KVDthes_Term $termObj 
     * @return KVDthes_Term
     */
    public function loadSourceNote( KVDthes_Term $termObj )
    {
        $this->loadNote( $this->findNodeForTerm($termObj), 'Source' , $termObj);
        return $termObj;
    }

    /**
     * loadNote 
     * 
     * @param DOMElement $term 
     * @param string $type 
     * @param KVDthes_Term $termObj
     * @return boolean Was the note loaded?
     */
    private function loadNote( $term, $type, $termObj)
    {
        foreach ( $term->getElementsByTagName( 'termNote' ) as $note ) {
            if ( $note->hasAttribute( 'label' ) && $note->getAttribute( 'label' ) == $type ) {
                if ( $type == 'Source' ) {
                    $termObj->addSourceNote ( trim( $note->nodeValue ) );
                    $termObj->setLoadState( KVDthes_Term::LS_SOURCENOTE );
                }
                if ( $type == 'Scope' ) {
                    $termObj->addScopeNote ( trim( $note->nodeValue ) );
                    $termObj->setLoadState( KVDthes_Term::LS_SCOPENOTE );
                }
                return true;
            }
            if ( !( $note->hasAttribute( 'label' ) ) ) {
                $termObj->addScopeNote ( trim( $note->nodeValue ) );
                $termObj->setLoadState( KVDthes_Term::LS_SCOPENOTE );
                return true;
            }
        }
        return false;
    }

    /**
     * getReturnType 
     * 
     * @return string
     */
    abstract protected function getReturnType( );
}
?>
