<?php
/**
 * @package    KVD.util
 * @subpackage auth
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 */

/**
 * Interface voor een provider.
 * De Provider handelt het verkeer met de datasource af, specifiek voor zijn type connectie
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      29 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 */
interface KVDutil_Auth_IProvider
{
    /**
     * @param string $gebruikersnaam
     * @param string $paswoord
     */
    public function aanmelden($gebruikersnaam, $paswoord);

    /**
     * @param   KVDutil_Auth_Gebruiker      $gebruiker
     * @param   string                      $applicatieNaam
     * @return  KVDutil_AuthRolCollectie    $rollen
     */
    public function getRollenVoorApplicatieNaam( KVDutil_Auth_Gebruiker $gebruiker, $applicatieNaam);

    /**
     * @param   KVDutil_Auth_Gebruiker      $gebruiker
     * @param   Object  Een Applicatie-object dat de methode getId() bevat,
     *                  om dan op basis van het applicatieId de rollen op te halen
     * @return  KVDutil_AuthRolCollectie    $rollen
     */
    public function getRollenVoorApplicatie( KVDutil_Auth_Gebruiker $gebruiker, $applicatie);
}
?>
