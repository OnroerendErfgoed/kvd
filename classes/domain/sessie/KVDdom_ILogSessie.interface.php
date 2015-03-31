<?php
/**
 * KVDdom_ILogSessie
 *
 * @package KVD.dom
 * @subpackage Sessie
 * @since 12 feb 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_ILogSessie
 *
 * UnitOfWork voor objecten die kunnen gelezen en geschreven worden waarvoor er ook aan versiebeheer wordt gedaan.
 * @package KVD.dom
 * @subpackage Sessie
 * @since 12 feb 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
interface KVDdom_ILogSessie extends KVDdom_IWriteSessie {

    /**
     * registerApproved
     *
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     * @throws <b>LogicException</b> - Indien er geprobeerd wordt een object goed te keuren dat niet goedgekeurd mag worden.
     */
    public function registerApproved ( $domainObject );

    /**
     * registerConfirmDelete
     *
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     * @throws <b>LogicException</b> - Indien er geprobeerd wordt het verwijderen van een ongeldig domainObject goed te keuren.
     */
    public function registerConfirmDelete( $domainObject );

    /**
     * registerUndoDelete
     *
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     * @throws <b>LogicException</b> - Indien er geprobeerd wordt het verwijderen van een ongeldig domainObject ongedaan te maken.
     */
    public function registerUndoDelete( $domainObject );

  }
?>
