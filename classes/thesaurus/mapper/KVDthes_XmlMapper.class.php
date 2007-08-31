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
abstract class KVDthes_XmlMapper
{
    /**
     * sessie 
     * 
     * @var KVDthes_ISessie
     */
    private $sessie;

    /**
     * dom 
     * 
     * @var DOMDocument
     */
    private $dom;

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
     * Een two-pass loading systeem. Eerst worden de termen allemaal geladen en dan worden de relaties gelegd. 
     */
    private function loadIdentityMap( )
    {
        foreach ( $this->dom->getElementsByTagName( 'term' ) as $term ) {
            $id = $term->getElementsByTagName( 'termId' )->item( 0 )->nodeValue;
            $name = $term->getElementsByTagName( 'termName' )->item( 0 )->nodeValue;
            $language = $term->getElementsByTagName( 'termLanguage' )->item( 0 )->nodeValue;
            $termType = $this->getReturnType( );
            $termObj = new $termType( $this->sessie , $id , $name , $language );
        }
    }

    /**
     * loadRelations 
     * 
     * @param KVDdom_DomainObject $termObj 
     * @return KVDdom_DomainObject
     */
    public function loadRelations( KVDthes_Term $termObj )
    {
        foreach (  $this->dom->getElementsByTagName( 'term' ) as $term ) {
            $id = $term->getElementsByTagName( 'termId' )->item( 0 )->nodeValue;
            if ( $id == $termObj->getId( ) ) {
                foreach ( $term->getElementsByTagName( 'relation' ) as $relation ) {
                    $relId = $relation->getElementsByTagName( 'termId' )->item( 0 )->nodeValue;
                    $relType = $relation->getElementsByTagName( 'relationType' )->item( 0 )->nodeValue;
                    $termObj->addRelation ( new KVDthes_Relation ( $relType , $this->findById( $relId ) ) );
                }
                $termObj->setLoadState( KVDthes_Term::LS_REL );
                break;
            }
        }
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
        foreach (  $this->dom->getElementsByTagName( 'term' ) as $term ) {
            $id = $term->getElementsByTagName( 'termId' )->item( 0 )->nodeValue;
            if ( $id == $termObj->getId( ) ) {
                $this->loadNote( $term, 'Scope' );
                break;
            }
        }
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
        foreach (  $this->dom->getElementsByTagName( 'term' ) as $term ) {
            $id = $term->getElementsByTagName( 'termId' )->item( 0 )->nodeValue;
            if ( $id == $termObj->getId( ) ) {
                $this->loadNote( $term, 'Source' );
                break;
            }
        }
        return $termObj;
    }

    /**
     * loadNote 
     * 
     * @param DOMElement $term 
     * @param sring $type 
     * @return boolean Was the note loaded?
     */
    private function loadNote( $term, $type = 'Scope' )
    {
        foreach ( $term->getElementsByTagName( 'termNote' ) as $note ) {
            if ( !$note->hasAttribute( 'label' ) || ( $note->getAttribute( 'label' ) == 'Scope' ) ) {
                $termObj->addScopeNote ( $note->nodeValue );
                $termObj->setLoadState( KVDthes_Term::LS_SCOPENOTE );
                return true;
            }
            if ( $note->hasAttribute( 'label' ) && ( $note->getAttribute( 'label' ) == $type ) ) {
                if ( $type == 'Source' ) {
                    $termObj->addSourceNote ( $note->nodeValue );
                    $termObj->setLoadState( KVDthes_Term::LS_SOURCENOTE );
                }
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
