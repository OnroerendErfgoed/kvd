<?php
/**
 * @package    KVD.thes
 * @subpackage visitor
 * @copyright  2004-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_TermHtmlRenderer
 *
 * Basis renderer voor een {@link KVDthes_RenderingTreeVisitor} die
 * de termen rendert als geneste unordererd lists.
 *
 * @package    KVD.thes
 * @subpackage visitor
 * @since      19 apr 2009
 * @copyright  2004-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_TermHtmlRenderer implements KVDthes_ITermRenderer
{
    /**
     * parameters
     *
     * @var array
     */
    protected $parameters = array( );

    /**
     * __construct
     *
     * @param   array $parameters
     * @return  void
     */
    public function __construct( array $parameters = array( ) )
    {
        $this->parameters = array_merge( $this->parameters, $parameters );
    }

    /**
     * renderTerm
     *
     * @param   KVDthes_Term $term
     * @return  string
     */
    public function renderTerm( KVDthes_Term $term )
    {
        return '<p>'.$term->getQualifiedTerm( ).'</p>';
    }

    /**
     * getResultStart
     *
     * @return  string
     */
    public function getResultStart( )
    {
        return '<ul>';
    }

    /**
     * getResultEnd
     *
     * @return  string
     */
    public function getResultEnd( )
    {
        return '</ul>';
    }

    /**
     * getVisitStart
     *
     * @return  string
     */
    public function getVisitStart( )
    {
        return '<li>';
    }

    /**
     * getVisitEnd
     *
     * @return  string
     */
    public function getVisitEnd( )
    {
        return '</li>';
    }

    /**
     * getCompositeStart
     *
     * @return  string
     */
    public function getCompositeStart( )
    {
        return '<ul>';
    }

    /**
     * getCompositeEnd
     *
     * @return  string
     */
    public function getCompositeEnd( )
    {
        return '</ul>';
    }


}

?>
