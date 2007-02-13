<?php
/**
 * KVDdom_IWriteSessie 
 * 
 * @package KVD.dom
 * @subpackage Sessie
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_IWriteSessie 
 * 
 * De UOW die zorgt voor zowel lezen als schrijven. Deze werkt dus samen met Changeable DomainObjects.
 * @package KVD.dom
 * @subpackage Sessie
 * @since 12 feb 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
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
     * @return array Een array met 3 keys ( 'insert' , 'update' , 'delete' ) die het aantal affected records bevatten.
     */
    public function commit();
     
}
?>
