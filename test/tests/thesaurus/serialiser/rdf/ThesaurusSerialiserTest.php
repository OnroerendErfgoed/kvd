<?php
/**
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_serialiser_rdf_ThesaurusSerialiserTest extends PHPUnit_Framework_TestCase
{

    public function setUp( )
    {
        $this->sessie = $this->getMock(  'KVDthes_Sessie' );

        $this->thes = new KVDthes_Thesaurus( $this->sessie, 1, 'Typologie Onroerend Erfgoed', 'nl-BE' );

        $config = array( 'uri_templates' => 
                    array( '1' => 
                        array( 'term'      => 'http://id.vioe.be/inventaris/thesaurus/typologie/%d',
                               'thesaurus' => 'http://id.vioe.be/inventaris/thesaurus/typologie' ) ) );
        $this->gen = new KVDthes_Serialiser_Rdf_ConfigUriGenerator( $config );

    }

    public function tearDown( )
    {
        $this->sessie = null;
        $this->thes = null;
        $this->gen = null;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTermShouldNotBeNull( )
    {
        $term = KVDthes_TestTerm::newNull( );

        $transformer = new KVDthes_Serialiser_Rdf_ThesaurusSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $term );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTermShouldBeHR( )
    {
        $gs = new KVDthes_TestTerm( 500, 
                                    $this->sessie,
                                    'Gebouwen en structuren',
                                    new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                    null,
                                    'nl-BE',
                                    null,
                                    array( 
                                    ),
                                    $this->thes );
        $gs->setLoadState(KVDthes_Term::LS_NOTES);
        $gs->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_ThesaurusSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $gs);
    }

    public function testWithoutRelations( )
    {
        $oe = new KVDthes_TestTerm( 1, 
                                    $this->sessie,
                                    'Onroerend Erfgoed',
                                    new KVDthes_TermType( 'HR', 'Hierarchy Root' ),
                                    null,
                                    'nl-BE',
                                    null,
                                    array( 
                                    ),
                                    $this->thes );
        $oe->setLoadState(KVDthes_Term::LS_NOTES);
        $oe->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_ThesaurusSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $oe );
        $this->assertType( 'EasyRdf_Resource', $res );
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                    xmlns:dc="http://purl.org/dc/terms/"
                >
                <skos:ConceptScheme rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie">
                    <skos:prefLabel>Typologie Onroerend Erfgoed</skos:prefLabel>
                    <dc:title>Typologie Onroerend Erfgoed</dc:title>
                    <dc:language>nl-BE</dc:language>
                </skos:ConceptScheme>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        //$this->assertEquals( $xml, $ser );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );
    }

    public function testWithoutUri( )
    {
        $thes = new KVDthes_Thesaurus( $this->sessie, 
                                       2, 
                                       'Typologie Onroerend Erfgoed 2', 
                                       'nl-BE' );

        $oe = new KVDthes_TestTerm( 1, 
                                    $this->sessie,
                                    'Onroerend Erfgoed',
                                    new KVDthes_TermType( 'HR', 'Hierarchy Root' ),
                                    null,
                                    'nl-BE',
                                    null,
                                    array( 
                                    ),
                                    $thes );
        $oe->setLoadState(KVDthes_Term::LS_NOTES);
        $oe->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_ThesaurusSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $oe );
        $this->assertType( 'EasyRdf_Resource', $res );
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                    xmlns:dc="http://purl.org/dc/terms/"
                >
                <skos:ConceptScheme rdf:nodeID="eid1">
                    <skos:prefLabel>Typologie Onroerend Erfgoed 2</skos:prefLabel>
                    <dc:title>Typologie Onroerend Erfgoed 2</dc:title>
                    <dc:language>nl-BE</dc:language>
                </skos:ConceptScheme>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        //$this->assertEquals( $xml, $ser );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );
    }

    public function testWithTopTerms( )
    {
        $oe = new KVDthes_TestTerm( 1, 
                                    $this->sessie,
                                    'Onroerend Erfgoed',
                                    new KVDthes_TermType( 'HR', 'Hierarchy Root' ),
                                    null,
                                    'nl-BE',
                                    null,
                                    array( 
                                     'scopeNote' => 'Erfgoed dat niet verplaatst kan worden.',
                                     'indexingNote' => 'Wordt nooit toegekend in de inventaris.',
                                     'historyNote' => 'Vroeger gewoon erfgoed genoemd.',
                                     'sourceNote' => 'Vlaams Instituut voor het Onroerend Erfgoed'
                                    ),
                                    $this->thes );
        $oe->setLoadState(KVDthes_Term::LS_NOTES);

        $gs = new KVDthes_TestTerm( 500, 
                                    $this->sessie,
                                    'Gebouwen en structuren',
                                    new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                    null,
                                    'nl-BE',
                                    null,
                                    array( 
                                    ),
                                    $this->thes );
        $gs->setLoadState(KVDthes_Term::LS_NOTES);

        $oe->loadRelation( new KVDthes_Relation( KVDthes_Relation::REL_NT, $gs ) );

        $ke = new KVDthes_TestTerm( 1024, 
                                    $this->sessie,
                                    'Klein erfgoed',
                                    new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                    null,
                                    'nl-BE',
                                    null,
                                    array( 
                                    ),
                                    $this->thes );
        $ke->setLoadState(KVDthes_Term::LS_NOTES);
        $oe->loadRelation( new KVDthes_Relation( KVDthes_Relation::REL_NT, $ke ) );

        $ne = new KVDthes_TestTerm( 178, 
                                    $this->sessie,
                                    'Nederzettingen',
                                    new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                    null,
                                    'nl-BE',
                                    null,
                                    array( 
                                    ),
                                    $this->thes );
        $ne->setLoadState(KVDthes_Term::LS_NOTES);
        $oe->loadRelation( new KVDthes_Relation( KVDthes_Relation::REL_NT, $ne ) );

        $oe->setLoadState(KVDthes_Term::LS_REL);
        $gs->setLoadState(KVDthes_Term::LS_REL);
        $ne->setLoadState(KVDthes_Term::LS_REL);
        $ke->setLoadState(KVDthes_Term::LS_REL);


        $transformer = new KVDthes_Serialiser_Rdf_ThesaurusSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $oe );
        $this->assertType( 'EasyRdf_Resource', $res );
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                    xmlns:dc="http://purl.org/dc/terms/"
                >
                <skos:ConceptScheme rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie">
                    <skos:prefLabel>Typologie Onroerend Erfgoed</skos:prefLabel>
                    <dc:title>Typologie Onroerend Erfgoed</dc:title>
                    <dc:language>nl-BE</dc:language>
                    <skos:hasTopConcept rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/178" />
                    <skos:hasTopConcept rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/500" />
                    <skos:hasTopConcept rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/1024" />
                </skos:ConceptScheme>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        //$this->assertEquals( $xml, $ser );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );
    }
   
}
?>
