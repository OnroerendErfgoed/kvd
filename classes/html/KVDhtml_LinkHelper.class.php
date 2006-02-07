<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDhtml_LinkHelper.class.php,v 1.1 2006/01/12 12:30:15 Koen Exp $
 */

/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDhtml_LinkHelper
{
    /**
     * @var string
     */
    private $linkformat = '<a href="%s" title="%s" %s>%s</a>';

    /**
     * Genereer een html link.
     * 
     * Voorbeeld:
     * <code>
     *      $link = $helper->genHtmlLink ( 'index.php', 'Home','Terug naar huis','VetteLink');
     *
     *      echo $link;
     *
     *      levert op:
     *      <a href="index.php" title="Terug naar huis" class="VetteLink">Home</a>
     * </code>
     * @param string $href Href van de link
     * @param string $naam Waarde die getoond wordt aan de gebruiker.
     * @param string $titel Title attribuut van het element.
     * @param string $class Css-class die wordt toegekend aan het element.
     * @return string Een volledig opgemaakt a element (<a>...</a>)
     */
    public function genHtmlLink ($href, $naam, $titel='',$class='')
    {
        if ($titel == '') {
            $titel=$naam;
        }
        if ($class != '') {
            $class = "class=\"$class\"";
        }
        $retval = sprintf($this->linkformat, $href, $titel, $class, $naam);
        return $retval;
    }
}
?>
