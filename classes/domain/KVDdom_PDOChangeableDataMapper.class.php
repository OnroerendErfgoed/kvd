<?php
/**
 * @package     KVD.dom
 * @copyright 	2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author 		Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_PDOChangeableDataMapper
 *
 * Een basis class die de mapping-functies voor alle DataMappers die werken met aanpasbare DomainObjects bevat.
 *
 * @package     KVD.dom
 * @since       24 jul 2006
 * @copyright 	2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author 		Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
abstract class KVDdom_PDOChangeableDataMapper extends KVDdom_PDODataMapper {

    /**
     * getUpdateFieldsStatement
     *
     * @return  string  String om te gebruiken in update statement ( bv. naam = ?, beschrijving = ?)
     */
    protected function getUpdateFieldsStatement( )
    {
        if ( $this->velden == null ) {
            return '';
        }
        $fields = explode ( ', ' , $this->velden );
        foreach ( $fields as &$field ) {
            $field = "$field = ?";
        }
        return implode ( ', ' , $fields);
    }

    /**
     * @return  string  SQL statement
     */
    protected function getInsertStatement( )
    {
        $fields = 'id' .
            ( $this->velden != null ? ',' . $this->velden : '') .
            ( $this->systemFieldsMapper->getSystemFields( ) <> "" ? ', ' . $this->systemFieldsMapper->getSystemFields( ) : '');
        $parameters = '?' .
            ( $this->getVeldenAsParameters( ) <> "" ? ', ' . $this->getVeldenAsParameters( ) : '') .
            ( $this->systemFieldsMapper->getInsertSystemFieldsString( ) <> "" ? ', ' . $this->systemFieldsMapper->getInsertSystemFieldsString( ) : '');
        $sql = sprintf( "INSERT INTO %s ( %s )VALUES ( %s)" , $this->tabel, $fields, $parameters );
        $this->_sessie->getSqlLogger( )->log( $sql );
        return $sql;
    }

    /**
     * @return  string  SQL statement
     */
    protected function getDeleteStatement()
    {
        return  "DELETE FROM " . $this->tabel .
                " WHERE " . $this->id . " = ?";
    }

    /**
     * @return  string  SQL statement
     */
    protected function getUpdateStatement()
    {
        return  "UPDATE " . $this->tabel . " SET " .
                $this->getUpdateFieldsStatement( ) . ( $this->velden == null ? '' : ', ') . $this->systemFieldsMapper->getUpdateSystemFieldsString() .
                " WHERE " . $this->id . " = ?";
    }

    /**
     * Voeg een nieuw DomainObject toe aan de databank
     *
     * @param   KVDdom_DomainObject     $domainObject   Het DomainObject dat moet toegevoegd worden aan de databank.
     * @throws  PDOException            Indien er een databank probleem optreed.
     * @todo    Beslissen of er hier nog iets van error handling moet komen.
     */
    public function insert ($domainObject)
    {
        try {
            $stmt = $this->_conn->prepare ($this->getInsertStatement() );
            $this->doInsert( $stmt , $domainObject );
            $stmt->execute();
            return $domainObject;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * doInsert
     *
     * Dit is een stub methode die de standaard handelingen uitvoert maar verder kan overschreven worden.
     * @since   25 okt 2006
     * @param   PDOStatement                    $stmt
     * @param   KVDdom_ChangeableDomainObject   $domainObject
     * @return  integer                         Nummer van de volgende te gebruiken index in het sql statement.
     */
    protected function doInsert( $stmt , $domainObject )
    {
        $this->systemFieldsMapper->updateSystemFields( $domainObject , $this->_sessie->getGebruiker( )->getGebruikersNaam( ) );
        $stmt->bindValue ( 1 , $domainObject->getId( ) , PDO::PARAM_INT );
        $next = $this->bindValues ( $stmt , 2 , $domainObject );
        return $this->systemFieldsMapper->doSetSystemFields( $stmt , $domainObject, $next );
    }

    /**
     * Verwijder een bepaald DomainObject uit de databank.
     *
     * Deze versie kent geen concurrency control. Voor Optimistic Offline Concurrency moeten we bij KVDdom_PDOLogableDataMapper zijn.
     * @param   KVDdom_DomainObject     $domainObject   Het DomainObject dat moet verwijderd worden.
     * @throws  PDOException            Indien er een databank probleem optreed.
     */
    public function delete ( $domainObject )
    {
        try {
            $stmt = $this->_conn->prepare ($this->getDeleteStatement() );
            $stmt->bindValue ( 1, $domainObject->getId( ) , PDO::PARAM_INT );
            $stmt->execute();
        } catch (PDOException $e) {
            throw KVDdom_ExceptionConvertor::convert( $e , $domainObject );
        }
    }

    /**
     * @param   string  $sequenceName
     * @return  integer Het volgende nummer uit de sequentie.
     */
    protected function getIdFromSequence( $sequenceName )
    {
        $stmt = $this->_conn->query( "SELECT nextval ( '$sequenceName' )" );
        return $stmt->fetchColumn( );
    }

    /**
     * getIdFromMysqlSequence
     *
     * @param   string  $sequenceName
     * @return  integer Het volgende nummer uit de sequentie.
     */
    protected function getIdFromMysqlSequence( $sequenceName )
    {
        $this->_conn->exec( "UPDATE $sequenceName SET id = LAST_INSERT_ID(id+1)" );
        $stmt = $this->_conn->query( "SELECT LAST_INSERT_ID( )" );
        return $stmt->fetchColumn( );
    }

    /**
     * @param   KVDdom_ChangeableDomainObject
     * @return  KVDdom_ChangeableDomainObject
     */
    public function update ($domainObject)
    {
        $this->systemFieldsMapper->updateSystemFields( $domainObject , $this->_sessie->getGebruiker( )->getGebruikersNaam( ) );
        $stmt = $this->_conn->prepare(  $this->getUpdateStatement(  ));
        $nextIndex = $this->bindValues(  $stmt , 1 , $domainObject );
        $nextIndex =  $this->systemFieldsMapper->doSetSystemFields( $stmt , $domainObject, $nextIndex );
        $stmt->bindValue(  $nextIndex , $domainObject->getId(  ) , PDO::PARAM_INT );
        $stmt->execute(  );
        return $domainObject;
    }

    /**
     * bindValues
     *
     * Methode waarin alle inhouds-velden in het sql-statement een waarde moeten toegewezen krijgen.
     * Dus niet de id of systeemvelden, maar wel de echte data.
     * @since   25 okt 2006
     * @param   PDOStatement                    $stmt
     * @param   integer                         $startIndex
     * @param   KVDdom_ChangeableDomainObject   $domainObject
     * @return  integer                         Volgende te gebruiken index in het statement.
     */
    abstract protected function bindValues ( $stmt , $startIndex , $domainObject );

    /**
     * Maak een nieuw object van dit type aan.
     *
     * @return  KVDdom_ChangeableDomainObject
     */
    abstract public function create ();


    /**
     * insertDependentCollection
     *
     * @param KVDdom_DomainObject           $owner
     * @param KVDdom_DomainObjectCollection $coll
     * @param string                        $sql            Een sql statement dat de koppeltabel kan vullen. Er wordt verwacht dat er in
     *                                                      dit statement 3 parameters beschikbaar zijn. De eerste bevat het id van de eigenaar,
     *                                                      de tweede het id van een element in de collection en het derde het huidige versienummer.
     * @param Integer                       $ownerIdType    Een PDO constante
     * @param Integer                       $collIdType     Een PDO constante
     * @return void
     */
    protected function insertDependentCollection( KVDdom_DomainObject $owner, KVDdom_DomainObjectCollection $coll , $sql, $ownerIdType = PDO::PARAM_INT, $collIdType = PDO::PARAM_INT )
    {
        if ( count( $coll ) > 0 ) {
                $stmt = $this->_conn->prepare( $sql );
                $stmt->bindValue( 1 , $owner->getId( ) , $ownerIdType);
                $stmt->bindValue( 3 , $owner->getSystemFields( )->getTargetVersie( ) , PDO::PARAM_INT );
                foreach ( $coll as $item ) {
                    if ( !$item instanceof KVDdom_DomainObject ) {
                        throw new InvalidArgumentException( 'Een collection mag alleen maar DomainObjecten bevatten!' );
                    }
                    $stmt->bindValue( 2 , $item->getId( ) , $collIdType);
                    $stmt->execute( );
                }
        }
    }

    /**
     * deleteDependentCollection
     *
     * @param KVDdom_DomainObject   $owner
     * @param string                $sql            Een sql statement dat de afhankelijke collectie verwijderd. Er wordt verwacht dat
     *                                              er twee parameters beschikbaar zijn, de eerste bevat het id van de eigenaar,
     *                                              de tweede het huidige versienummer.
     * @param integer               $owerIdType     Een PDO constante
     * @return void
     */
    protected function deleteDependentCollection( KVDdom_DomainObject $owner, $sql, $owerIdType = PDO::PARAM_INT )
    {
        $stmt = $this->_conn->prepare ($sql );
        $stmt->bindValue ( 1, $owner->getId( ) , PDO::PARAM_INT );
        $stmt->bindValue ( 2, $owner->getSystemFields( )->getVersie( ) , PDO::PARAM_INT );
        $stmt->execute();
    }

}
?>
