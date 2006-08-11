<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version $Id$
 */

/**
 * Deze class doet dienst als interface en een soort nullLogger tegelijk.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 9 aug 2006
 */
class KVDdom_SqlLogger
{
    /**
     * @param string $sql Te loggen sql.
     * @return boolean Werd de data gelogd?
     */
    public function log ( $sql )
    {
        return false;
    }
}
?>
