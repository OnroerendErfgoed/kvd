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
 * @package    KVd.thes
 * @subpackage serialiser
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Serialiser_Rdf_TermSerialiser
{
    protected $graph;
    
    /**
     * uriGenerators 
     * 
     * @var array
     */
    protected $uriGenerators;
    
    public function __construct( EasyRdf_Graph $graph = null)
    {
        if ( $graph === null ) {
            $graph = new EasyRdf_Graph( );
        }
        $this->graph = $graph;
        $this->uriGenerators = array( );

        EasyRdf_Format::register( 'rdfxml',
                                  'RDF/XML',
                                  'http://www.w3.org/TR/rdf-syntax-grammar',
                                  'application/rdf+xml' );

        //EasyRdf_Format::registerParser( 'rdfxml', 'EasyRdf_Parser_RdfXml');

        EasyRdf_Format::registerSerialiser( 'rdfxml', 'EasyRdf_Serialiser_RdfXml');
    }

    /**
     * addUriGenerator 
     * 
     * @param KVDthes_Serialiser_Rdf_IUriGenerator $urigen 
     * @return void
     */
    public function addUriGenerator( KVDthes_Serialiser_Rdf_IUriGenerator $urigen )
    {
        $this->uriGenerators[] = $urigen;
    }

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
            $uri = $this->genUri( $term );
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
                    $uri = $this->genUri( $nt );
                    $ntres = $this->graph->resource( $uri );
                    $res->add( $rel_uri, $ntres );
                }
            }
        }

        if ( $term->getType( )->getId( ) != 'NL' && $term->hasRelations( KVDthes_Relation::REL_BT ) ) {
            $bt = $term->getBroaderTerm( );
            while ( $bt->getType( )->getId( ) == 'NL' ) {
                $bt = $bt->getBroaderTerm( );
            }
            $uri = $this->genUri( $bt);
            $bt = $this->graph->resource( $uri );
            $res->add( 'skos:broader', $bt );
        }

        if ( $term->hasRelations( KVDthes_Relation::REL_RT ) ) {
            $rels = $term->getRelatedTerms( );
            foreach( $rels as $rel ) {
                $uri = $this->genUri( $rel );
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

    /**
     * getGraph 
     * 
     * @return EasyRdf_Graph
     */
    public function getGraph( )
    {
        return $this->graph;
    }

    /**
     * Bekom een geserialiseerde vorm van de rdf graph.
     * 
     * @param  string $format 
     * @return string
     */
    public function serialise( $format = 'rdfxml' )
    {
        return $this->graph->serialise( $format );
    }

    /**
     * genUri 
     * 
     * @param KVDthes_Term $term 
     * @return string
     */
    private function genUri( KVDthes_Term $term )
    {
        foreach ( $this->uriGenerators as $gen ) {
            try {
                $uri = $gen->generateTermUri( $term );
                return $uri;
            } catch ( InvalidArgumentException $e ) {
                //Verder loopen
            }
        }
        throw new InvalidArgumentException( 
            sprintf( 'Kan geen uri genereren voor de term %s.', $term->getTerm( ) )
        );
    }

    /**
     * genThesaurusUri 
     * 
     * @param KVDthes_Thesaurus $thesaurus 
     * @return string
     */
    private function genThesaurusUri( KVDthes_Thesaurus $thesaurus )
    {
        foreach ( $this->uriGenerators as $gen ) {
            try {
                $uri = $gen->generateThesaurusUri( $thesaurus );
                return $uri;
            } catch ( InvalidArgumentException $e ) {
                //Verder loopen
            }
        }
        throw new InvalidArgumentException( 
            sprintf( 'Kan geen uri genereren voor de thesaurus %s.', $thesaurus->getOmschrijving( ) )
        );
    }
}
?>
