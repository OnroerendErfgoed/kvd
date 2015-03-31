<?php
/**
 * @package    KVD.thes
 * @subpackage serialiser
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Abstracte class die door alle serialiser kan geerfd worden.
 *
 * @package    KVd.thes
 * @subpackage serialiser
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
abstract class KVDthes_Serialiser_Rdf_AbstractSerialiser
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
    abstract public function transform( KVDthes_Term $term );

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
     * genTermUri
     *
     * @param KVDthes_Term $term
     * @return string
     */
    protected function genTermUri( KVDthes_Term $term )
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
    protected function genThesaurusUri( KVDthes_Thesaurus $thesaurus )
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
            sprintf( 'Kan geen uri genereren voor de thesaurus %s.',
                     $thesaurus->getOmschrijving( ) )
        );
    }
}
?>
