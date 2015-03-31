<?php
/**
 * @package KVD.dom
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_IDataMapper
 *
 * Een KVDdom_IDataMapper is een class die een domainobject kan laden uit een datasource.
 * Onder datasource verstaan we een externe bron die een object kan opslaan buiten
 * de php request cycle. Dit kan dus een databank, een bestand, een LDAP server,
 * een SOAP service, XML-RPC service of iets anders zijn.
 * Vaak zal een class die deze methode implementeert nog andere methodes zoals findAll
 * of findBy* hebben, maar deze zijn optioneel. Per class moet er gekeken worden welke
 *
 * methodes zinvol zijn.
 * @package KVD.dom
 * @since 19 okt 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
interface KVDdom_IDataMapper
{
    /**
     * findById
     *
     * @param   mixed   $id     Een unieke identifier die toelaat een object terug te vinden.
     *                          Meestal is dit een integer, soms ook eens string.
     * @return  KVDdom_DomainObject     Het gevonden object
     * @throws  KVDdom_DomainObjectNotFoundException    Indien het gevraagde object niet kon gevonden worden.
     */
    public function findById( $id );

}
?>
