<?php
/**
 * @package    KVD.thes
 * @subpackage visitor
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_TreeVisitorText
 *
 * @package    KVD.thes
 * @subpackage visitor
 * @since      19 maart 2007
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_TreeVisitorText extends KVDthes_AbstractTreeVisitor
{
    /**
     * depth
     *
     * @var integer
     */
    private $depth = 1;

    /**
     * result
     *
     * @var string
     */
    private $result = '';

    /**
     * __construct
     *
     * @param integer $sortOrder    Zie de constanten in KVDthes_Relations
     * @return void
     */
    public function __construct( $sortOrder = KVDthes_TermSorter::SORT_UNSORTED )
    {
        $this->relationsSortOrder = $sortOrder;
    }

    /**
     * pad
     *
     * @return string
     */
    private function pad()
    {
        $buf = '';
        for ($i=1;$i<=$this->depth;++$i) {
            $buf .= "\t";
        }
        return $buf;
    }

    /**
     * visit
     *
     * @param KVDthes_Term $node
     * @return void
     */
    public function visit(KVDthes_Term $node)
    {
        $this->result .= $this->depth . $this->pad() . $node->getQualifiedTerm() . "\n";
        return true;
    }

    /**
     * enterComposite
     *
     * @param KVDthes_Term $term
     * @return boolean
     */
    public function enterComposite(KVDthes_Term $term)
    {
        $this->depth++;
        return true;
    }

    /**
     * leaveComposite
     *
     * @param KVDthes_Term $term
     * @return boolean
     */
    public function leaveComposite(KVDthes_Term $term)
    {
        $this->depth--;
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
        return $relations->getNTIterator( );
    }

    /**
     * getResult
     *
     * @return string
     */
    public function getResult( )
    {
        return $this->result;
    }
}
?>
