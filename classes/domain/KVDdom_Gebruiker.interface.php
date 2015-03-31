<?php
/**
 * @package     KVD.dom
 * @subpackage  gebruiker
 * @copyright   2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_Gebruiker
 *
 * Alle gebruikers-objecten voor een applicatie moeten aan deze interface voldoen zodat de datamappers kunnen
 * nagaan wie de gebruiker is die een update uitvoert.
 * @package     KVD.dom
 * @subpackage  gebruiker
 * @since       2005
 * @copyright   2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
interface KVDdom_Gebruiker extends KVDdom_DomainObject {

    /**
     * getGebruikersNaam
     *
     * @return string
     */
    public function getGebruikersNaam();

}
?>
