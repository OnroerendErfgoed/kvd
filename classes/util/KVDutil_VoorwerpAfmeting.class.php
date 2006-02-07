<?php
/**
 * @package KVD.util.dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Class die een afmeting voorstelt.
 *
 * Kan later mogelijk ook gebruikt worden voor de gewone CAI.
 * @package KVD.util.dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDutil_VoorwerpAfmeting extends KVDutil_Dimensie
{
    /**
     * Alle afmetingen worden herleid naar mm.
     * @var string Afkorting van een maateenheid voor afmeting
     */
    const BASISMAAT = "mm";

    public function getDimensieMaat()
    {
        return self::BASISMAAT;
    }
}
?>
