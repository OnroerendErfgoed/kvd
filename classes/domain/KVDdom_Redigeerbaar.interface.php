<?php
/**
 * @package KVD.dom
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id$
 */
 
/**
 * KVDdom_Redigeerbaar 
 * 
 * @package KVD.dom
 * @since 27 okt 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDdom_Redigeerbaar
{
   /**
     * Keur het domeinobject goed.
     * @param string $approver Naam van de redacteur die zijn goedkeuring geeft.
     */
    public function approve( $approver );

    /**
     * Verwijder de geschiedenis van het object
     */
    public function verwijderGeschiedenis( );

    /**
     * updateToPreviousVersion 
     * 
     * Stel het huidige object opnieuw in met de waarden van een vorig object
     * @since 09 nov 2006 Zat vroeger op het LogableDomainObject
     * @return void
     */
    public function updateToPreviousVersion( $previous );

}
?>
