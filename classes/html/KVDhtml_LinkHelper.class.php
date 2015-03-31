<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 1.0.0
 */
class KVDhtml_LinkHelper
{
    /**
     * @var string
     */
    private $linkformat = '<a href="%s" title="%s"%s%s>%s</a>';

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
     * @param   string  $href   Href van de link
     * @param   string  $naam   Waarde die getoond wordt aan de gebruiker.
     * @param   string  $titel  Title attribuut van het element.
     * @param   string  $class  Css-class die wordt toegekend aan het element.
     * @param   string  $target Target die aan link wordt toegekend.
     * @return  string  Een volledig opgemaakt a element (<a>...</a>)
     */
    public function genHtmlLink ($href, $naam, $titel='',$class='',$target='')
    {
        if ($titel == '') {
            $titel=$naam;
        }
        if ($class != '') {
            $class = " class=\"$class\"";
        }
        if ( $target != '') {
            $target = " target=\"$target\"";
        }
        $retval = sprintf($this->linkformat, $href, $titel, $class, $target,$naam);
        return $retval;
    }
}
?>
