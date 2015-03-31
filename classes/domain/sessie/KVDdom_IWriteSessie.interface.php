<?php
/**
 * KVDdom_IWriteSessie
 *
 * @package KVD.dom
 * @subpackage Sessie
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_IWriteSessie
 *
 * De UOW die zorgt voor zowel lezen als schrijven. Deze werkt dus samen met Changeable DomainObjects.
 *
 * @package KVD.dom
 * @subpackage Sessie
 * @since 12 feb 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
interface KVDdom_IWriteSessie extends KVDdom_IReadSessie{

    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     */
    public function registerNew ( $domainObject );

    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     */
    public function registerDirty ( $domainObject );

    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     */
    public function registerRemoved ( $domainObject );

    /**
     * @return array Een array met keys ( bv. 'insert' , 'update' , 'delete' ) die het aantal affected records bevatten.
     */
    public function commit();

    /**
     * getGebruiker
     *
     * De eigenaar van de sessie.
     * Is belangrijk omdat de DataMappers dit nodig hebben om vast te stellen wie de wijzigingen doorvoert zodat ze kunnen gelogd worden.
     * @return KVDdom_Gebruiker
     */
    public function getGebruiker( );

}
?>
