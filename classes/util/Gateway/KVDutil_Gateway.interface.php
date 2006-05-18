<?php
/**
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @package KVD.util
 * @version $Id$
 */

/**
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @package KVD.util
 * @since 1.0.0
 */
interface KVDutil_Gateway {
    /**
     * De constructor aanvaardt een associatieve array met de nodige connectie parameters ( wsdl, username, paswoord, etc..)
     * @param array $parameters
     */
    public function __construct ( $parameters );
}
?>
