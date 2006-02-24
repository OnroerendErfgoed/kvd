<?php
/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Class die messages uit het agavi framework logt met inbegrip van datum en tijd.
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDag_DateTimeLayout extends Layout
{
    public function &format( $message )
    {
        $msg = sprintf( "[%s] %s", date( 'd-m-Y H:i:s'), $message->__toString( ));
        return $msg;
    }
}
?>
