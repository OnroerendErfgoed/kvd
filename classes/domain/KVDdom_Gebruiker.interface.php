<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_Gebruiker.interface.php,v 1.1 2006/01/12 14:46:58 Koen Exp $
 */

/**
 * Alle gebruikers-objecten voor een applicatie moeten aan deze interface voldoen.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

interface KVDdom_Gebruiker extends KVDdom_DomainObject {

    public function getGebruikersNaam();

    public function getWachtwoord();

    public function setGebruikersNaam ( $gebruikersNaam );

    public function setWachtwoord ( $wachtwoord);
    
    }
?>
