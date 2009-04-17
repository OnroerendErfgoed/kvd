<?php
/**
 * @package     KVD.thes
 * @subpackage  visitor
 * @version     $Id$
 * @copyright   2004-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_ITermRenderer 
 * 
 * Interface waaraan een renderer voor een {@link KVDthes_RenderingTreeVisitor} moet voldoen.
 * @package     KVD.thes
 * @subpackage  visitor
 * @since       17 apr 2009
 * @copyright   2004-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDthes_ITermRenderer
{
    /**
     * renderTerm 
     * 
     * @param   KVDthes_Term $term 
     * @return  string
     */
    public function renderTerm( KVDthes_Term $term );

    /**
     * getResultStart 
     * 
     * @return  string
     */
    public function getResultStart( );

    /**
     * getResultEnd 
     * 
     * @return  string
     */
    public function getResultEnd( );

    /**
     * getVisitStart 
     * 
     * @return  string
     */
    public function getVisitStart( );

    /**
     * getVisitEnd 
     * 
     * @return  string
     */
    public function getVisitEnd( );

    /**
     * getCompositeStart 
     * 
     * @return  string
     */
    public function getCompositeStart( );

    /**
     * getCompositeEnd 
     * 
     * @return  string
     */
    public function getCompositeEnd( );

}
?>
