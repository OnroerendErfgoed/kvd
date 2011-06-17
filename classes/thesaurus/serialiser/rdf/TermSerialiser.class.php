<?php
/**
 * @package    KVD.thes
 * @subpackage serialiser
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Object dat een thesaurusterm kan serialiseren naar rdf/xml. 
 * 
 * @package    KVD.thes
 * @subpackage serialiser
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Serialiser_Rdf_TermSerialiser extends KVDthes_Serialiser_Rdf_AbstractSerialiser
{
    /**
     * transform 
     * 
     * @param KVDthes_term $thes
     * @return EasyRdf_Resource
     */
    public function transform( KVDthes_Term $term )
    {
        if ( !$term->isPreferredTerm( ) ) {
            $term = $term->getPreferredTerm( );
        }
        if ( $term->getType( )->getId( ) == 'NL' ) {
            $type = 'skos:Collection';
        } else {
            $type = 'skos:Concept';
        }
        try {
            $uri = $this->genTermUri( $term );
            $res = $this->graph->resource( $uri, $type );
        } catch ( InvalidArgumentException $e ) {
            $res = $this->graph->newBNode( $type );
        }
        $thes = $term->getThesaurus( );
        try {
            $thes_uri = $this->genThesaurusUri( $thes );
            $thres = $this->graph->resource( $thes_uri );
            $res->add( 'skos:inScheme', $thres );
        } catch ( InvalidArgumentException $e ) {
            // Kennen geen uri voor de thesaurus.
        }
        $res->add( 'skos:prefLabel', $term->getTerm( ) );
        if ( $term->hasRelations( KVDthes_Relation::REL_UF ) ) {
            $rels = $term->getNonPreferredTerms( );
            foreach( $rels as $rel ) {
                $res->add( 'skos:altLabel', $rel->getTerm( ) );
            }
        }

        if ( $term->hasRelations( KVDthes_Relation::REL_NT ) ) {
            $rels = $term->getNarrowerTerms( );
            foreach( $rels as $rel ) {
                if ( $rel->getType( )->getId( ) == 'NL' ) {
                    $nts = $rel->getNarrowerTerms( );
                } else {
                    $nts = new KVDthes_DomainObjectCollection( array( $rel ) );
                }
                foreach ( $nts as $nt ) {
                    if ( $term->getType( )->getId( ) == 'NL' ) {
                        $rel_uri = 'skos:member';
                    } else {
                        $rel_uri = 'skos:narrower';
                    }
                    $uri = $this->genTermUri( $nt );
                    $ntres = $this->graph->resource( $uri );
                    $res->add( $rel_uri, $ntres );
                }
            }
        }

        if ( $term->getType( )->getId( ) != 'NL' && 
             $term->hasRelations( KVDthes_Relation::REL_BT ) ) {
            $bt = $term->getBroaderTerm( );
            while ( $bt->getType( )->getId( ) == 'NL' ) {
                $bt = $bt->getBroaderTerm( );
            }
            $uri = $this->genTermUri( $bt);
            $bt = $this->graph->resource( $uri );
            $res->add( 'skos:broader', $bt );
        }

        if ( $term->hasRelations( KVDthes_Relation::REL_RT ) ) {
            $rels = $term->getRelatedTerms( );
            foreach( $rels as $rel ) {
                $uri = $this->genTermUri( $rel );
                $rt = $this->graph->resource( $uri );
                $res->add( 'skos:related', $rt );
            }
        }

        if ( $term->getScopeNote( ) != '' ) {
            $res->add( 'skos:definition', $term->getScopeNote( ) );
        }
        if ( $term->getIndexingNote( ) != '' ) {
            $res->add( 'skos:scopeNote', $term->getIndexingNote( ) );
        }
        if ( $term->getHistoryNote( ) != '' ) {
            $res->add( 'skos:historyNote', $term->getHistoryNote( ) );
        }
        if ( $term->getSourceNote( ) != '' ) {
            $res->add( 'dc:source', $term->getSourceNote( ) );
        }
        return $res;
    }

}
?>
