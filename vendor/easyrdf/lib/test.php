<?php
/**
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
    require_once( 'EasyRdf.php' );

    $rdf = '<rdf:RDF
        xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
        xmlns:dcterms="http://purl.org/dc/terms/"
        xmlns:bibo="http://purl.org/ontology/bibo/"
        xmlns:foaf="http://xmlns.com/foaf/0.1/"
        xmlns:address="http://schemas.talis.com/2005/address/schema#">
        <foaf:Person rdf:about="http://id.vioe.be/biblio/actor/1">
            <foaf:name>Van Daele, Koen</foaf:name>
        </foaf:Person>
        <bibo:Document rdf:about="http://id.vioe.be/biblio/bron/1">
            <dcterms:title>Imperfecte tijdsmodellering in historische databanken.</dcterms:title>
            <dcterms:date>2010</dcterms:date>
            <dcterms:creator rdf:resource="http://id.vioe.be/biblio/bron/1"/>
        </bibo:Document>
    </rdf:RDF>';

    EasyRdf_Format::register(
        'rdfxml',
        'RDF/XML',
        'http://www.w3.org/TR/rdf-syntax-grammar',
        'application/rdf+xml'
    );

    EasyRdf_Format::registerParser('rdfxml', 'EasyRdf_Parser_RdfXml');

    $graph = new EasyRdf_Graph( 'biblio', $rdf );
    echo $graph->dump( false );
?>
