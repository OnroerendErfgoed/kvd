<?php
/**
 * @package KVD.util.dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
 
/**
 * Class die een gewicht voorstelt.
 *
 * Kan later mogelijk ook gebruikt worden voor de gewone CAI.
 * @package KVD.util.dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDutil_VoorwerpGewicht extends KVDutil_Dimensie
{
    /**
     * Alle gewichten worden herleid naar gr.
     * @var string Afkorting van een maateenheid voor gewicht
     */
     
    const BASISMAAT = "gr";

    public function getDimensieMaat()
    {
        return self::BASISMAAT;
    }
}
?>
