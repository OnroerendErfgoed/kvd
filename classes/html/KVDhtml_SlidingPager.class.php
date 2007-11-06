<?php
/**
 * @package KVD.html
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDhtml_SlidingPager 
 * 
 * Een Sliding Pager die gebruikt moet worden in combinatie met een KVDdom_DomainObejctCollectionPager en het Agavi Framework.
 * @package KVD.html
 * @since 21 aug 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDhtml_SlidingPager {

    /**
     * pager 
     * 
     * @var KVDdom_DomainObjectCollectionPager
     */
    protected $pager;

    /**
     * ro 
     * 
     * @var AgaviRouting
     */
    protected $ro;

    /**
     * route 
     * 
     * @var String
     */
    protected $route;

    /**
     * lh 
     * 
     * @var KVDhtml_LinkHelper
     */
    protected $lh;

    /**
     * parameters 
     * 
     * @var array
     */
    protected $parameters = array( 'pagina_naam' => 'pagina' );

    /**
     * @param KVDdom_DomainObjectCollectionPager    $pager 
     * @param AgaviRouting                          $ro 
     * @param String                                $route      Naam van de route die de overzichten genereert.
     * @param array                                 $parameters Mogelijke parameters zijn 'pagina_naam'.
     * @return void
     */
    public function __construct( KVDdom_DomainObjectCollectionPager $pager, AgaviRouting $ro, $route, $parameters = array( ) )
    {
        $this->pager = $pager;
        $this->ro = $ro;
        $this->route = $route;
        $this->parameters = array_merge ( $this->parameters, $parameters);
        $this->lh = new KVDhtml_LinkHelper( );
    }

    /**
     * toHtml 
     * 
     * @param int $range 
     * @return string
     */
    public function toHtml( $range = 5)
    {
        $html = '';
        if ( $this->pager->getPage( ) > $this->pager->getFirstPage( ) ) {
            $html .= $this->lh->genHtmlLink( $this->ro->gen( $this->route , array ( $this->parameters['pagina_naam'] => $this->pager->getPrev( ) ) ) , 'Vorige');
            $html .= ' [ ';
            $html .= $this->lh->genHtmlLink( $this->ro->gen( $this->route , array ( $this->parameters['pagina_naam'] => $this->pager->getFirstPage( ) ) ) , $this->pager->getFirstPage( ) ) . ' ';
        } else {
            $html .= 'Vorige [ ';
        }
	    
        $html .= ($this->pager->getPage() > $this->pager->getFirstPage( ) + $range ) ? '.. ' : '';
        
		foreach ($this->pager->getPrevLinks($range) as $page) {
			if ($page != $this->pager->getFirstPage( )) {
                $html .= $this->lh->genHtmlLink( $this->ro->gen( $this->route , array ($this->parameters['pagina_naam'] => $page ) ),$page) . ' ';
			}
		}

        $html .= '<strong>'.$this->pager->getPage( ).'</strong> ';
			
        foreach ($this->pager->getNextLinks($range ) as $page) {
			if ($page != $this->pager->getLastPage()) {
                $html .= $this->lh->genHtmlLink( $this->ro->gen( $this->route , array ($this->parameters['pagina_naam'] => $page ) ),$page) . ' ';
			}
		}	

        $html .= ($this->pager->getPage() < $this->pager->getLastPage( ) - $range ) ? '.. ' : '';
			 
   		if ( $this->pager->getPage( ) < $this->pager->getLastPage( ) ) {
            $html .= $this->lh->genHtmlLink( $this->ro->gen( $this->route , array ( $this->parameters['pagina_naam'] => $this->pager->getLastPage( ) ) ) , $this->pager->getLastPage( ) );
            $html .= ' ] ';
            $html .= $this->lh->genHtmlLink( $this->ro->gen( $this->route , array ( $this->parameters['pagina_naam'] => $this->pager->getNext( ) ) ) , 'Volgende' );
  		} else {
            $html .= ' ] Volgende';
        }
        return $html;
    }
}
?>
