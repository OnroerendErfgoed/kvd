<?php
/**
 * @package KVD.thes
 * @subpackage visitor
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_XMLVisitor
 *
 * @package KVD.thes
 * @subpackage visitor
 * @since 9 jan 2008
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_GetRelationsVisitor extends KVDthes_AbstractSimpleVisitor
{
    /**
     * result
     *
     * @var string
     */
    private $result = array( );

    /**
     * from
     *
     * @var mixed String of integer.
     */
    private $from;


    /**
     * visit
     *
     * @param KVDthes_Term $node
     * @return void
     */
	public function visit(KVDthes_Term $node)
	{
        $this->from = $node->getId( );
        return true;
    }

    /**
     * visitRelation
     *
     * @param KVDthes_Relation $relation
     * @return boolean
     */
    public function visitRelation(KVDthes_Relation $relation)
    {
        $this->result[] = array( 'id_from' => $this->from, 'rel_type' => $relation->getType( ), 'id_to' => $relation->getTerm( )->getId( ) );
        return true;
    }

    /**
     * enterComposite
     *
     * @param KVDthes_Term $term
     * @return boolean
     */
	public function enterComposite( KVDthes_Term $term )
	{
        return true;
	}

    /**
     * leaveComposite
     *
     * @param KVDthes_Term $term
     * @return boolean
     */
	public function leaveComposite( KVDthes_Term $term )
	{
        return true;
	}

    /**
     * getIterator
     *
     * @param KVDthes_Relations $relations
     * @return void
     */
    public function getIterator( KVDthes_Relations $relations )
    {
        return $relations->getIterator( );
    }

    /**
     * getResult
     *
     * Deze methode geeft het resultaat van de visit terug, een array waarmee
     * de relatie-structuur kan gepersisteerd worden in een andere vorm,
     * meest waarschijnlijk een databank.
     * @return array    Een array met arrays met telkens drie sleutels ( id_from, rel_type, id_to ).
     */
    public function getResult( )
    {
        return $this->result;
    }

    public function clearResult( )
    {
        $this->result = array( );
    }

}
?>
