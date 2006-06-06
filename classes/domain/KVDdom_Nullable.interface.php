<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Een interface om aan te geven dat een DomainObject een Null Object kan zijn.
 * @link http://www.martinfowler.com/eaaCatalog/specialCase.html
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
interface KVDdom_Nullable {

    /**
     * Is dit een Null Object of niet?
     * @return boolean
     */
    public function isNull( );

}
?>
