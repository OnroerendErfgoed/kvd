<?php
/**
 * @package KVD.thes
 * @subpackage visitor
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_AbstractSimpleVisitor
 *
 * @todo Nagaan of Deze visitor en de KVDthes_AbstractTreeVisitor eigenlijk niet dezelfde kunnen worden.
 * @package KVD.thes
 * @subpackage visitor
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
abstract class KVDthes_AbstractSimpleVisitor
{
    /**
     * visit
     *
     * @param KVDthes_Term $node
     * @return void
     */
	public function visit(KVDthes_Term $node)
    {
        return true;
    }

    /**
     * visitRelation
     *
     * @param KVDthes_Relation $relation
     * @return boolean
     */
    abstract public function visitRelation( KVDthes_Relation $relation );

    /**
     * enterComposite
     *
     * @param KVDthes_Term $node
     * @return boolean
     */
	public function enterComposite(KVDthes_Term $node)
    {
        return true;
    }

    /**
     * leaveComposite
     *
     * @param KVDthes_Term $node
     * @return true
     */
	public function leaveComposite(KVDthes_Term $node)
    {
        return true;
    }

    /**
     * getIterator
     *
     * @param KVDthes_Relations $relations
     * @return KVDthes_RelationsIterator
     */
    public function getIterator( KVDthes_Relations $relations )
    {
        return $relations->getIterator( );
    }
}
?>
