<?php
/**
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Serialiser_Rdf_TermSerialiserTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->sessie = $this->getMock(  'KVDthes_Sessie' );

        $this->thes = new KVDthes_Thesaurus( $this->sessie, 1, 'Typologie' );

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

    public function testTransformOnroerendErfgoed( )
    {

        $term = new KVDthes_TestTerm( 1, 
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

        $term->setLoadState(KVDthes_Term::LS_NOTES);
        $term->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $term );
        $this->assertType( 'EasyRdf_Resource', $res );
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                    xmlns:dc="http://purl.org/dc/terms/"
                >
                <skos:Concept rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie/1">
                    <skos:inScheme rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie" />
                    <skos:prefLabel>Onroerend Erfgoed</skos:prefLabel>
                    <skos:definition>Erfgoed dat niet verplaatst kan worden.</skos:definition>
                    <skos:scopeNote>Wordt nooit toegekend in de inventaris.</skos:scopeNote>
                    <skos:historyNote>Vroeger gewoon erfgoed genoemd.</skos:historyNote>
                    <dc:source>Vlaams Instituut voor het Onroerend Erfgoed</dc:source>
                </skos:Concept>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        //$this->assertEquals( $xml, $ser );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );
    }

    public function testTransformVaartuigen( )
    {
        $term = new KVDthes_TestTerm( 100, 
                                             $this->sessie,
                                             'Vaartuigen',
                                             new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                             null,
                                             'nl-BE',
                                             null,
                                             array( 
                                                'scopeNote' => 'Erfgoed dat zich op het water kan voortbewegen.',
                                                'indexingNote' => '',
                                                'historyNote' => '',
                                                'sourceNote' => 'Vlaams Instituut voor het Onroerend Erfgoed'
                                             ),
                                             $this->thes );

        $term->setLoadState(KVDthes_Term::LS_NOTES);

        $schepen = new KVDthes_TestTerm( 101, 
                                          $this->sessie,
                                          'Schepen',
                                          new KVDthes_TermType( 'ND', 'Niet-Voorkeursterm' ),
                                          null,
                                          'nl-BE',
                                          null,
                                          array( 'scopeNote' => '', 'indexingNote' => '', 'historyNote' => '', 'sourceNote' => '' ),
                                          $this->thes );

        $schepen->setLoadState(  KVDthes_Term::LS_NOTES);

        $term->loadRelation( new KVDthes_Relation( KVDthes_Relation::REL_UF, $schepen ) );

        $term->setLoadState(KVDthes_Term::LS_REL);

        $schepen->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $term );

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                    xmlns:dc="http://purl.org/dc/terms/"
                >
                <skos:Concept rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie/100">
                    <skos:inScheme rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie" />
                    <skos:prefLabel>Vaartuigen</skos:prefLabel>
                    <skos:altLabel>Schepen</skos:altLabel>
                    <skos:definition>Erfgoed dat zich op het water kan voortbewegen.</skos:definition>
                    <dc:source>Vlaams Instituut voor het Onroerend Erfgoed</dc:source>
                </skos:Concept>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );
    }

    public function testTransformTermWithRelatedTerms( )
    {
        $term = new KVDthes_TestTerm( 100, 
                                             $this->sessie,
                                             'Vaartuigen',
                                             new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                             null,
                                             'nl-BE',
                                             null,
                                             array( 
                                                'scopeNote' => 'Erfgoed dat zich op het water kan voortbewegen.',
                                                'indexingNote' => '',
                                                'historyNote' => '',
                                                'sourceNote' => 'Vlaams Instituut voor het Onroerend Erfgoed'
                                             ),
                                             $this->thes );

        $term->setLoadState(KVDthes_Term::LS_NOTES);

        $schepen = new KVDthes_TestTerm( 101, 
                                          $this->sessie,
                                          'Schepen',
                                          new KVDthes_TermType( 'ND', 'Niet-Voorkeursterm' ),
                                          null,
                                          'nl-BE',
                                          null,
                                          array( 'scopeNote' => '', 'indexingNote' => '', 'historyNote' => '', 'sourceNote' => '' ),
                                          $this->thes );

        $schepen->setLoadState(  KVDthes_Term::LS_NOTES);

        $term->loadRelation( new KVDthes_Relation( KVDthes_Relation::REL_RT, $schepen ) );

        $term->setLoadState(KVDthes_Term::LS_REL);

        $schepen->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $term );

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                    xmlns:dc="http://purl.org/dc/terms/"
                >
                <skos:Concept rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie/100">
                    <skos:inScheme rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie" />
                    <skos:prefLabel>Vaartuigen</skos:prefLabel>
                    <skos:related rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/101"/>
                    <skos:definition>Erfgoed dat zich op het water kan voortbewegen.</skos:definition>
                    <dc:source>Vlaams Instituut voor het Onroerend Erfgoed</dc:source>
                </skos:Concept>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );
    }

    public function testGetGraph( )
    {
        $term = new KVDthes_TestTerm ( 1, 
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

        $term->setLoadState(KVDthes_Term::LS_NOTES);
        $term->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );

        $res = $transformer->transform( $term );

        $this->assertType( 'EasyRdf_Graph', $transformer->getGraph( ) );
    }

    public function testNoUriGeneratorGeneratesBNodes( )
    {
        $term = new KVDthes_TestTerm ( 100, 
                                       $this->sessie,
                                       'Vaartuigen',
                                       new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                       null,
                                       'nl-BE',
                                       null,
                                       array( 
                                       ),
                                       $this->thes );

        $term->setLoadState(KVDthes_Term::LS_NOTES);
        $term->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );

        $res = $transformer->transform( $term );

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                >
                <skos:Concept rdf:nodeID="eid1">
                    <skos:prefLabel>Vaartuigen</skos:prefLabel>
                </skos:Concept>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );
    }

    public function testSkosBroaderAndNarrower( )
    {
        $term = new KVDthes_TestTerm( 100, 
                                      $this->sessie,
                                      'Vaartuigen',
                                      new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                      null,
                                      'nl-BE',
                                      null,
                                      array( 
                                         'scopeNote' => 'Erfgoed dat zich op het water kan voortbewegen.',
                                         'indexingNote' => '',
                                         'historyNote' => '',
                                         'sourceNote' => 'Vlaams Instituut voor het Onroerend Erfgoed'
                                      ),
                                      $this->thes );

        $term->setLoadState(KVDthes_Term::LS_NOTES);

        $schepen = new KVDthes_TestTerm( 101, 
                                          $this->sessie,
                                          'Schepen',
                                          new KVDthes_TermType( 'ND', 'Niet-Voorkeursterm' ),
                                          null,
                                          'nl-BE',
                                          null,
                                          array( 'scopeNote' => '', 'indexingNote' => '', 'historyNote' => '', 'sourceNote' => '' ),
                                          $this->thes );

        $schepen->setLoadState(  KVDthes_Term::LS_NOTES);

        $term->loadRelation( new KVDthes_Relation( KVDthes_Relation::REL_NT, $schepen ) );

        $term->setLoadState(KVDthes_Term::LS_REL);

        $schepen->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $term );

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                    xmlns:dc="http://purl.org/dc/terms/"
                >
                <skos:Concept rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie/100">
                    <skos:inScheme rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie" />
                    <skos:prefLabel>Vaartuigen</skos:prefLabel>
                    <skos:narrower rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/101"/>
                    <skos:definition>Erfgoed dat zich op het water kan voortbewegen.</skos:definition>
                    <dc:source>Vlaams Instituut voor het Onroerend Erfgoed</dc:source>
                </skos:Concept>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $schepen );

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                >
                <skos:Concept rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie/101">
                    <skos:inScheme rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie" />
                    <skos:prefLabel>Schepen</skos:prefLabel>
                    <skos:broader rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/100"/>
                </skos:Concept>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );
    }

    public function testNDIsSerialisedAsPT( )
    {
        $term = new KVDthes_TestTerm( 100, 
                                             $this->sessie,
                                             'Vaartuigen',
                                             new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                             null,
                                             'nl-BE',
                                             null,
                                             array( 
                                                'scopeNote' => 'Erfgoed dat zich op het water kan voortbewegen.',
                                                'indexingNote' => '',
                                                'historyNote' => '',
                                                'sourceNote' => 'Vlaams Instituut voor het Onroerend Erfgoed'
                                             ),
                                             $this->thes );

        $term->setLoadState(KVDthes_Term::LS_NOTES);

        $schepen = new KVDthes_TestTerm( 101, 
                                          $this->sessie,
                                          'Schepen',
                                          new KVDthes_TermType( 'ND', 'Niet-Voorkeursterm' ),
                                          null,
                                          'nl-BE',
                                          null,
                                          array( 'scopeNote' => '', 'indexingNote' => '', 'historyNote' => '', 'sourceNote' => '' ),
                                          $this->thes );

        $schepen->setLoadState(  KVDthes_Term::LS_NOTES);

        $term->loadRelation( new KVDthes_Relation( KVDthes_Relation::REL_UF, $schepen ) );

        $term->setLoadState(KVDthes_Term::LS_REL);

        $schepen->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $schepen );

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                    xmlns:dc="http://purl.org/dc/terms/"
                >
                <skos:Concept rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie/100">
                    <skos:inScheme rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie" />
                    <skos:prefLabel>Vaartuigen</skos:prefLabel>
                    <skos:altLabel>Schepen</skos:altLabel>
                    <skos:definition>Erfgoed dat zich op het water kan voortbewegen.</skos:definition>
                    <dc:source>Vlaams Instituut voor het Onroerend Erfgoed</dc:source>
                </skos:Concept>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );
    }

    public function testTransformGidsTerm( )
    {
        $term = new KVDthes_TestTerm( 100, 
                                         $this->sessie,
                                         'Kerken volgens geloof',
                                         new KVDthes_TermType( 'NL', 'Gidsterm' ),
                                         null,
                                         'nl-BE',
                                         null,
                                         array( 
                                         ),
                                         $this->thes );
        $term->setLoadState(KVDthes_Term::LS_NOTES);

        $kerken = new KVDthes_TestTerm( 90, 
                                         $this->sessie,
                                         'Kerken',
                                         new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                         null,
                                         'nl-BE',
                                         null,
                                         array( 
                                         ),
                                         $this->thes );
        $kerken->setLoadState(KVDthes_Term::LS_NOTES);


        $kk = new KVDthes_TestTerm( 150, 
                                         $this->sessie,
                                         'Katholieke kerken',
                                         new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                         null,
                                         'nl-BE',
                                         null,
                                         array( 
                                         ),
                                         $this->thes );
        $kk->setLoadState(KVDthes_Term::LS_NOTES);

        $pk = new KVDthes_TestTerm( 160, 
                                         $this->sessie,
                                         'Protestantse kerken',
                                         new KVDthes_TermType( 'PT', 'Voorkeursterm' ),
                                         null,
                                         'nl-BE',
                                         null,
                                         array( 
                                         ),
                                         $this->thes );
        $pk->setLoadState(KVDthes_Term::LS_NOTES);

        $term->loadRelation( new KVDthes_Relation( KVDthes_Relation::REL_BT, $kerken ) );

        $term->loadRelation( new KVDthes_Relation( KVDthes_Relation::REL_NT, $kk ) );
        $term->loadRelation( new KVDthes_Relation( KVDthes_Relation::REL_NT, $pk ) );

        $term->setLoadState(KVDthes_Term::LS_REL);
        $kerken->setLoadState(KVDthes_Term::LS_REL);
        $kk->setLoadState(KVDthes_Term::LS_REL);
        $pk->setLoadState(KVDthes_Term::LS_REL);

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $term );

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                >
                <skos:Collection rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie/100">
                    <skos:inScheme rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie" />
                    <skos:prefLabel>Kerken volgens geloof</skos:prefLabel>
                    <skos:member rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/150" />
                    <skos:member rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/160" />
                </skos:Collection>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $kk );

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                >
                <skos:Concept rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie/150">
                    <skos:inScheme rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie" />
                    <skos:prefLabel>Katholieke kerken</skos:prefLabel>
                    <skos:broader rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/90"/>
                </skos:Concept>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );

        $transformer = new KVDthes_Serialiser_Rdf_TermSerialiser( );
        $transformer->addUriGenerator( $this->gen );

        $res = $transformer->transform( $kerken );

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <rdf:RDF
                    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                    xmlns:skos="http://www.w3.org/2004/02/skos/core#"
                >
                <skos:Concept rdf:about="http://id.vioe.be/inventaris/thesaurus/typologie/90">
                    <skos:inScheme rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie" />
                    <skos:prefLabel>Kerken</skos:prefLabel>
                    <skos:narrower rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/150"/>
                    <skos:narrower rdf:resource="http://id.vioe.be/inventaris/thesaurus/typologie/160"/>
                </skos:Concept>
                </rdf:RDF>';
        $ser = $transformer->serialise( 'rdfxml' );
        $this->assertXmlStringEqualsXmlString( $xml, $ser );
    }

}
?>
