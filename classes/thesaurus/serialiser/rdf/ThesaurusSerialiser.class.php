<?php
/**
 * @package    KVD.thes
 * @subpackage serialiser
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Object dat een thesaurus kan serialiseren naar rdf/xml.
 *
 * Opgelet, dit zal niet de volledige thesaurus omzetten naar xml, maar zal de
 * informatie over de thesaurus as such omzetten naar een skos:ConceptScheme
 *
 * @package    KVD.thes
 * @subpackage serialiser
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_Serialiser_Rdf_ThesaurusSerialiser extends KVDthes_Serialiser_Rdf_AbstractSerialiser
{
    /**
     * transform
     *
     * @param KVDthes_Term $thes
     * @return EasyRdf_Resource
     */
    public function transform( KVDthes_Term $thes )
    {
        if ( $thes->isNull( ) ) {
            throw new InvalidArgumentException(
                'Een nullobject kan niet geserialiseerd worden!' );
        }
        if ( $thes->getType( )->getId( ) != 'HR'  ) {
            throw new InvalidArgumentException(
                'Om een thesaurus te kunnen serialiseren hebben we de HR term nodig.' );
        }
        $term = $thes;
        $thes = $term->getThesaurus( );
        $type = 'skos:ConceptScheme';
        try {
            $uri = $this->genThesaurusUri( $thes );
            $res = $this->graph->resource( $uri, $type );
        } catch ( InvalidArgumentException $e ) {
            $res = $this->graph->newBNode( $type );
        }
        $res->add( 'skos:prefLabel', $thes->getNaam( ) );
        $res->add( 'dc:title', $thes->getNaam( ) );
        $res->add( 'dc:language', $thes->getLanguage( ) );
        if ( $term->hasNTRelations( ) ) {
            $term->sortRelations( KVDthes_TermSorter::SORT_ID );
            $rels = $term->getNarrowerTerms( );
            foreach ( $rels as $rel ) {
                $tt = $this->graph->resource( $this->genTermUri($rel) );
                $res->add( 'skos:hasTopConcept', $tt );
            }
        }
        return $res;
    }

}
?>
