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
     * @throws PDOException Indien er een databank probleem optreed.
     */
    public function insert ($domainObject)
    {
        try {
            $stmt = $this->_conn->prepare ($this->getInsertStatement() );
            $this->doInsert( $stmt , $domainObject );
            $stmt->execute();
            return $persoon;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * doInsert 
     *
     * Dit is een stub methode die de standaard handelingen uitvoert maar verder kan overschreven worden.
     * @since 25 okt 2006
     * @param PDOStatement $stmt 
     * @param KVDdom_ChangeableDomainObject $domainObject 
     * @return integer Nummer van de volgende te gebruiken index in het sql statement.
     */
    protected function doInsert( $stmt , $domainObject )
    {
            $stmt->bindValue ( 1 , $domainObject->getId( ) , PDO::PARAM_INT );
            return $this->bindValues ( $stmt , 2 , $domainObject );    
    }

    /**
     * Verwijder een bepaald DomainObject uit de databank.
     *
     * Deze versie kent geen concurrency control. Voor Optimistic Offline Concurrency moeten we bij CAI_LogableDataMapper zijn.
     * @param KVDdom_DomainObject $domainObject Het DomainObject dat moet verwijderd worden.
     * @throws PDOException Indien er een databank probleem optreed.
     */
    public function delete ( $domainObject )
    {
        try {
            $stmt = $this->_conn->prepare ($this->getDeleteStatement() );
            $stmt->bindValue ( 1, $domainObject->getId( ) , PDO::PARAM_INT );
            $stmt->execute();
        } catch (PDOException $e) {
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
     * bindValues 
     *
     * Methode waarin alle inhouds-velden in het sql-statement een waarde moeten toegewezen krijgen. Dus niet de id of systeemvelden, maar wel de echte data.
     * @since 25 okt 2006
     * @param PDOStatement $stmt 
     * @param integer $startIndex 
     * @param KVDdom_ChangeableDomainObject $domainObject 
     * @return integer Volgende te gebruiken index in het statement.
     */
    abstract protected function bindValues ( $stmt , $startIndex , $domainObject );
    
    /**
     * @param mixed $id
     * @param KVDdom_Sessie $sessie
     */
    abstract public function create ();
    
}

?>
