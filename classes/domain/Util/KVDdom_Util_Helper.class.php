<?php
/**
 * @package   KVD.dom
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Philip Verbist <philip.verbist@hp.com>
 */

/**
 * Een aantal utility methodes voor zoekacties.
 *
 * @package   KVD.dom
 * @since     7 april 2012
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Philip Verbist <philip.verbist@hp.com>
 */
class KVDdom_Util_Helper
{
    /**
     * __construct
     *
     * Deze constructor werd private gemaakt om te verhinderen
     * dat er een instantie van deze class gemaakt wordt.
     *
     * @return void
     */
    private function __construct( )
    {

    }

    /**
     * @param   KVDdom_DomainObject     $domainObject
     * @param   string                  $fieldString
     * @throws  <b>RuntimeException</b> Indien de gevraagde data
     *                                  niet geleverd kon worden.
     */
    public static function getDataForFieldString(
                                KVDdom_DomainObject $domainObject,
                                $fieldString)
    {
        if(preg_match("#(^\w[\w\.]*\w$)|(^\w$)#", $fieldString) == 0){
            throw new InvalidArgumentException ('U probeert een ongeldige fieldstring ('. $fieldString . ') op te vragen ' );
        }
        $fields = explode( '.', $fieldString );
        foreach ( $fields as $field) {
            if ( !is_object( $domainObject) ) {
                throw new RuntimeException (
                    'U probeert een veld ('. $field . ') op te vragen
                    van een niet bestaande object: ' . $domainObject );
            }
            $domainObject = $domainObject->$field( );
        }
        if ( is_bool( $domainObject ) ) {
            $domainObject = ( $domainObject ) ? 'Ja' : 'Nee';
        }
        if ( is_array( $domainObject ) ) {
            $domainObject = implode(', ', $domainObject);
        }
        return $domainObject;
    }
}
?>
