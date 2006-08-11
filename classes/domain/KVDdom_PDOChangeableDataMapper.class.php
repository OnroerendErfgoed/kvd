<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * KVDdom_PDOChangeableDataMapper
 *
 * Een basis class die de mapping-functies voor alle DataMappers die werken met aanpasbare DomainObjects bevat.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 24 jul 2006
 */
abstract class KVDdom_PDOChangeableDataMapper extends KVDdom_PDODataMapper {

    /**
     * @return string SQL statement
     */
    abstract protected function getInsertStatement();
    /**
     * @return string SQL statement
     */
    abstract protected function getDeleteStatement();
    /**
     * @return string SQL statement
     */
    abstract protected function getUpdateStatement();

    /**
     * Voeg een nieuw DomainObject toe aan de databank
     *
     * @param KVDdom_DomainObject $domainObject Het DomainObject dat moet toegevoegd worden aan de databank.
     * @throws Exception - Indien er een databank probleem optreed.
     */
    public function insert ($domainObject)
    {
        try {
            $stmt = $this->_conn->prepare ($this->getInsertStatement() );
            $this->doInsert ( $stmt , $domainObject );    
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception ( 'Het object kon niet aan de databank worden toegevoegd omwille van een SQL probleem: ' . $e->getMessage() );
        } catch (Exception $e) {
            throw new Exception ( 'Het object kon niet aan de databank worden toegevoegd omwille van een onbekend probleem.' );
        }
    }

    /**
     * Verwijder een bepaald DomainObject uit de databank.
     *
     * Deze versie kent geen concurrency control. Voor Optimistic Offline Concurrency moeten we bij CAI_LogableDataMapper zijn.
     * @param KVDdom_DomainObject $domainObject Het DomainObject dat moet verwijderd worden.
     * @throws Exception
     */
    public function delete ( $domainObject )
    {
        try {
            $stmt = $this->_conn->prepare ($this->getDeleteStatement() );
            $id = $domainObject->getId( );
            $stmt->bindParam ( 1, $id , PDO::PARAM_INT );
            $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param string $sequenceName
     * @return integer $id
     */
    protected function getIdFromSequence( $sequenceName )
    {
        $stmt = $this->_conn->query( "SELECT nextval ( '$sequenceName' )" );
        return $stmt->fetchColumn( );
    }
    
    /**
     * @param KVDdom_ChangeableDomainObject
     */
    abstract public function update ($domainObject);
    
    /**
     * @param Statement $stmt
     * @param KVDdom_ChangeableDomainObject
     */
    abstract protected function doInsert ( $stmt , $domainObject );
    
    /**
     * @param mixed $id
     * @param KVDdom_Sessie $sessie
     */
    abstract public function create ();
    
}

?>
