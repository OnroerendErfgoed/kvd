<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDhtml_PagedTableHelper.class.php,v 1.1 2006/01/12 12:30:15 Koen Exp $
 */
 
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDhtml_PagedTableHelper extends KVDhtml_TableHelper {

    /**
     * @var KVDhtml_LinkHelper
     */
    private $_HtmlLinkHelper;

    /**
     * Maak een nieuwe KVDhtml_PagedTableHelper aan.
     */
    public function __construct() {
        parent::__construct();
        $this->_HtmlLinkHelper = New CAI_HtmlLinkHelper();
    }
    
    /**
     * Stel de pagina-links in.
     *
     * Deze methode genereert Html die dan wordt toegevoegd aan het tfoot element van de tabel.
     * De voornaamste paramete $links is een array waarvan de sleutel ofwel een paginanummer is,
     * ofwel één van de volgende termen: vorige, volgende, eerste, laatste.
     * De elementen in de array zelf moeten telkens een url zijn. Een mogelijke aanroep kan er dus als volgt uitzien:
     * <code>
     *      $currentPage = 2;
     *      $totalPages = 3;
     *      $links = array (    '1' => 'index.php?p=1',
     *                          '2' => 'index.php?p=2',
     *                          '3' => 'index.php?p=3',
     *                          'eerste' => 'index.php?p=1',
     *                          'laatste'=> 'index.php?p=3',
     *                          'vorige' => 'index.php?p=1',
     *                          'volgende' => 'index.php?p=3'
     *                          );
     *      $tabel->setPageLinks ( $currentPage , $totalPages , $links );                     
     * </code>
     * @param integer $currentPage
     * @param integer $totalPages
     * @param array $links Een array waarmee links aangemaakt moeten worden.
     */
    public function setPageLinks($currentPage, $totalPages, &$links) {
        $pageLinks='';
        foreach ($links as $key => &$url) {
            if (is_numeric($key)) {
                $title = "Toon pagina $key";
                $numericLinks[] = $this->_HtmlLinkHelper->genHtmlLink($url,$key,$title);
            } else {
                $title = "Toon $key pagina";
                $url = $this->_HtmlLinkHelper->genHtmlLink($url,$key,$title);
            }
        }
        if (array_key_exists('vorige',$links)) {
            $pageLinks .= $links['vorige'] . ' | ';
        }
        $pageLinks .= "Pagina $currentPage van $totalPages";
        if (array_key_exists('volgende',$links)) {
            $pageLinks .= ' | ' . $links['volgende'];
        }
        $pageLinks .= '<br />';
        if (array_key_exists('eerste',$links)) {
            $pageLinks .= $links['eerste'] . ' | ';
        }
        if (isset($numericLinks)) {
            $pageLinks .= implode (' | ', $numericLinks);
        }
        if (array_key_exists('laatste',$links)) {
            $pageLinks .= ' | ' . $links['laatste'];
        }
        $this->setFooter($pageLinks);
    }
}
?>
