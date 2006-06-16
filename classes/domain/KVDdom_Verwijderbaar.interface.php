<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Een interface om aan te geven dat een DomainObject verwijderd kan zijn, maar toch nog bestaat aangezien het nog in de log-tabellen zit.
 * Dit komt dus neer op het gebruik van het Special Case patroon.
 * @link http://www.martinfowler.com/eaaCatalog/specialCase.html
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
interface KVDdom_Verwijderbaar {

    /**
     * Is dit een verwijderd Object of niet?
     * @return boolean
     */
    public function isVerwijderd( );

}
?>
