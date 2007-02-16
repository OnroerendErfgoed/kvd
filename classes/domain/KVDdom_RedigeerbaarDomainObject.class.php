<?php
/**
 * @package KVD.dom
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id$
 */
 
/**
 * KVDdom_RedigeerbaarDomainObject
 * 
 * Is even een interface geweest maar nu dus opnieuw een class ( anders begon de LogableDomainObject class te veel rommel te bevatten).
 * @package KVD.dom
 * @since 13 feb 2007
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDdom_RedigeerbaarDomainObject extends KVDdom_LogableDomainObject
{
    /**
     * Markeert dit object als Approved
     *
     * Dit record zal worden goedgekeurd bij het verwerken van de UnitOfWork.
     */
    protected function markApproved(  )
    {
        $this->_sessie->registerApproved( $this );
    }

    /**
     * markConfirmDelete
     *
     * Dit object zal definitief verwijderd worden bij het verwerken van de UnitOfWork.
     */
    protected function markConfirmDelete(  )
    {
        $this->_sessie->registerConfirmDelete( $this );
    }

    /**
     * markUndoDelete 
     * 
     * Het verwijderen van dit object zal ongedaan gemaakt worden bij het verwerken van de UnitOfWork.
     */
    protected function markUndoDelete( )
    {
        $this->_sessie->registerUndoDelete( $this );
    }
    
   /**
     * Keur het domeinobject goed.
     * @param string $approver Naam van de redacteur die zijn goedkeuring geeft.
     */
    public function approve( $approver )
    {
        $this->markApproved( );
        $this->_systemFields->setApproved( $approver );
    }

    /**
     * confirmDelete
     * 
     * Verwijder de geschiedenis van het object.
     * Heette vroeger verwijderGeschiedenis.
     * @since 13 feb 2007
     * @throws <b>KVDdom_RedactieException</b>
     */
    public function confirmDelete( )
    {
        throw new KVDdom_RedactieException( 'Kan het verwijderen niet bevestigen van een object dat niet verwijderd is.' );
    }

    /**
     * undoDelete 
     * 
     * Maak het verwijderen van een object ongedaan. Dit is eigenlijk het tegenovergestelde van 
     * {@link KVDdom_Redigeerbaar.confirmDelete} aangezien die methode leidt tot de total vernietiging van een object.
     * @since 13 feb 2007
     * @throws <b>KVDdom_RedactieException</b>
     */
    public function undoDelete( )
    {
        throw new KVDdom_RedactieException( 'Kan het verwijderen niet ongedaan maken van een object dat niet verwijderd is.' );
    }

    /**
     * updateToPreviousVersion 
     * 
     * Stel het huidige object opnieuw in met de waarden van een vorig object
     * @since 09 nov 2006 Zat vroeger op het LogableDomainObject
     * @return void
     */
    abstract public function updateToPreviousVersion( $previous );

    /**
     * isVerwijderd 
     * 
     * Is het object een placeholder voor een object dat enkel nog in de logtabellen zit maar
     * kan teruggeroepen worden?
     * Vervangt de oude KVDdom_Verwijderbaar interface
     * @return boolean
     */
    public function isVerwijderd( )
    {
        return false;
    }

    /**
     * Controleer of het mogelijk is het huidige DomainObject te updaten naar de vorige versie.
     * @param KVDdom_RedigeerbaarDomainObject
     * @throws <b>KVDdom_RedactieException</b> Indien het object geen vorige versie van het huidige is en er dus geen update zou mogen plaatsvinden.
     */
    protected function checkPreviousVersion( $previous )
    {
        if ( !$previous->getClass( ) === $this->getClass( ) ) {
            throw new KVDdom_RedactieException ( 'Kan enkel updaten naar een vorige versie van mezelf!' );
        }
        if ( !$this->isCurrentRecord( ) ) {
            throw new KVDdom_RedactieException ( 'Dit object is niet de huidige versie en kan dus niet geupdate worden!');
        }
        if ( $previous->isCurrentRecord( ) ) {
            throw new KVDdom_RedactieException ( 'Er kan enkel geupate worden naar een oude versie van een record, niet naar de huidige! ');
        }
    }
}
?>
