<?php
/**
 * @package     KVD.thes
 * @subpackage  Util
 * @version     $Id$
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_SqlGenerator 
 * 
 * Een class die ons in staat stelt om van een Thesaurus een weergave in sql statements te krijgen. 
 * Door deze sql in een databank te voeden kan deze Thesaurus dan ook aangesproken worden door middel 
 * van een KVDthes_DbMapper.
 * @package     KVD.thes
 * @subpackage  Util
 * @since       12 jul 2008
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_SqlGenerator
{
    /**
     * sessie 
     * 
     * @var KVDthes_ISessie
     */
    protected $sessie;

    /**
     * __construct 
     * 
     * @param KVDdom_IReadSessie $sessie 
     * @return void
     */
    public function __construct( KVDdom_IReadSessie $sessie )
    {
        $this->sessie = $sessie;

    }

    /**
     * generateSql 
     * 
     * @param string    $domainObject       Naam van het domain object dat de thesaurus kan leveren. 
     *                                      Er zal altijd afgedwongen worden dat deze door de xml-mapper wordt geleverd.
     * @param integer   $thesaurus_id       Id dat de thesaurus in de databank moet krijgen. Het is de verantwoordelijkheid van de programmeur
     *                                      er voor te zorgen dat dit id nog niet in gebruik is.
     * @param string    $thesaurus_naam     Naam die de thesaurus in de databank moet krijgen.
     * @return string                       Een string die alle sql statements bevat nodig om deze thesaurus in een databank op te vragen. 
     */
    public function generateSql( $domainObject, $thesaurus_id, $thesaurus_naam )
    {
		$this->sessie->setDefaultMapper( $domainObject, 'xml');
        
        $sql = "--Thesaurus.\n";
        $sql .= sprintf( "INSERT INTO thes.thesaurus VALUES ( %d, '%s');\n" , $thesaurus_id , $thesaurus_naam );
        $sql .= "\n";

        $sql .= "--Termen.\n";
        $termen = $this->sessie->getMapper( $domainObject )->findAll( );
        foreach ( $termen as $term ) {
            $sql .= sprintf( "INSERT INTO thes.term VALUES ( %d, %d, '%s', %s, '%s');\n", 
                                $thesaurus_id, 
                                $term->getId( ),
                                $term->getTerm( ), 
                                $term->getQualifier( ) !== null ? "'" . $term->getQualifier( ) . "'" : 'null', 
                                $term->getLanguage(),
                                $term->getSortKey( ), 
                                );
        }
        $sql .= "\n";

        $sql .= "--Relaties.\n";
        $v = new KVDthes_GetRelationsVisitor( );
        foreach ( $termen as $term ) {
            $v->clearResult( );
            $term->acceptSimple( $v );
            foreach( $v->getResult( ) as $result ) {
                $sql.= sprintf( "INSERT INTO thes.relation VALUES ( %d, %d, '%s', %d);\n", $thesaurus_id, $result['id_from'], $result['rel_type'], $result['id_to'] );
            }
        }
        $sql .= "\n";

        $sql .= "--Notes.\n";
        foreach ( $termen as $term ) {
            if ( $term->getScopeNote( ) != null || $term->getSourceNote( ) != null ) {
                $sql .= sprintf( "INSERT INTO thes.notes VALUES ( %d, %d, '%s', '%s');\n", $thesaurus_id, $term->getId( ), $term->getScopeNote( ), $term->getSourceNote( ) );
            }
        }
        $sql .= "\n";
       
        $sql .= "--Visitation.\n";
        $topTerm = $this->sessie->getMapper( $domainObject )->findRoot( );
        $v = new KVDthes_VisitationVisitor( );
        $topTerm->accept( $v );
        foreach ( $v->getResult( ) as $row ) {
            $sql .= sprintf("INSERT INTO thes.visitation VALUES ( default, %d, %d, %d, %d, %d );\n", $row['left'], $row['right'], $thesaurus_id, $row['id'], $row['depth']);
        }

        return $sql;
    }
        
}
?>
