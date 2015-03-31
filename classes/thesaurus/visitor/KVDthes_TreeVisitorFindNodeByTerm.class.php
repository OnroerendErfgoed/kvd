<?php
/**
 * @package KVD.thes
 * @subpackage visitor
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_TreeVisitorFindNode
 *
 * @package KVD.thes
 * @subpackage visitor
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_TreeVisitorFindNodeByTerm extends KVDthes_AbstractTreeVisitor
{
    /**
     * result
     *
     * @var KVDthes_Term
     */
	public $result = null;
    /**
     * searchTerm
     *
     * @var string
     */
	public $searchTerm;

    /**
     * __construct
     *
     * @param string $search
     * @return void
     */
	public function __construct($search) {
		$this->searchTerm = $search;
	}

    /**
     * visit
     *
     * @param KVDthes_Term $node
     * @return void
     */
	public function visit(KVDthes_Term $node)
	{
		if ($this->result == null ) {
			if ($node->getTerm() == $this->searchTerm ) {
				$this->result = $node;
                return false;
			}
		}
        return true;
	}

    /**
     * enterComposite
     *
     * @param KVDthes_Term $node
     * @return boolean
     */
	public function enterComposite(KVDthes_Term $node)
	{
        return ( $this->result == null);
	}

    /**
     * leaveComposite
     *
     * @param KVDthes_Term $node
     * @return boolean
     */
	public function leaveComposite(KVDthes_Term $node)
	{
        return ( $this->result == null);
	}

    /**
     * getIterator
     *
     * @param KVDthes_Relations $relations
     * @return KVDthes_RelationIterator
     */
    public function getIterator( KVDthes_Relations $relations )
    {
        return $relations->getNTIterator( );
    }
}

?>
