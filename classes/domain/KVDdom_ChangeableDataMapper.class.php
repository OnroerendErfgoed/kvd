<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_ChangeableDataMapper.class.php,v 1.1 2006/01/12 14:46:02 Koen Exp $
 */

/**
 * KVDdom_ChangeableDataMapper
 *
 * Een basis class die de mapping-functies voor alle DataMappers die werken met aanpasbare DomainObjects bevat.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
abstract class KVDdom_ChangeableDataMapper extends KVDdom_DataMapper {

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
            $stmt = $this->_conn->prepareStatement($this->getInsertStatement());
            $this->doInsert ( $stmt , $domainObject );    
            return $stmt->executeUpdate();
        } catch (SQLException $e) {
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
            $stmt = $this->_conn->prepareStatement($this->getDeleteStatement());
            $stmt->setInt (1, $domainObject->getId());
            $stmt->executeUpdate();
        } catch (Exception $e) {
            throw $e;
        }
    }

    abstract public function update ($domainObject);
    abstract protected function doInsert ( $stmt , $domainObject );
    
}

?>
