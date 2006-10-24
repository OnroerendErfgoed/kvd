<?php    
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * DataMapper voor KVDdom_LogableDomainObject.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 * @deprecated Gebruik de PDO variant.
 */
abstract class KVDdom_LogableDataMapper extends KVDdom_ChangeableDataMapper
{

    /**
     * De naam van de table waarnaar de log-informatie geschreven wordt.
     */
    const LOGTABEL = "";

    /**
     * Constant om aan te geven dat de laatst gelogde versie van een bepaald record gezocht wordt.
     */
    const MAXVERSIE = "-1";

    /**
     * De velden die nodig zijn voor het SystemFields object.
     */
    const SFVELDEN = "gebruiker, bewerkt_op, versie, gecontroleerd";

    /**
     * @return string SQL statement
     */
    abstract protected function getFindByLogIdStatement();

    /**
     * @return string SQL statement
     */
    abstract protected function getFindByLogIdMaxVersieStatement(); 

    /**
     * @return string SQL statement
     */
    abstract protected function getLogInsertStatement();

    /**
     * @return string SQL statement
     */
    abstract protected function getLogFindAllStatement();

    /**
     * @return string SQL statement
     */
    abstract protected function getApproveRecordStatement( );

    /**
     * @return string SQL statement
     */
    abstract protected function getApproveLogRecordsStatement( );

    /**
     * @return string SQL statement
     */
    abstract protected function getFindTeRedacterenStatement( );

    /**
     * @return string SQL statement
     */
    abstract protected function getFindVerwijderdeStatement( );

    /**
     * @return string SQL statement
     */
    abstract protected function getClearLogStatement( );
    

    /**
     * @return string SQL statement
     */
    protected function getLogOrderStatement()
    {
        return " ORDER BY versie DESC";    
    }

    /**
     * @return string Lijst van de velden met een prefix toegepast.
     */
    protected function getLogVelden( $velden, $logtabel )
    {
        $fields = explode ( ', ' , $velden );
        foreach ( $fields as &$field ) {
            $field = "$logtabel.$field AS $field";
        }
        return implode ( ', ', $fields );
    }


    /**
     * Laad een SystemFields object op basis van een ResultSet
     *
     * @param ResultSet $rs Een ResultSet object dat de nodige velden bevat om een SystemFields object mee samen te stellen.
     * @param string $prefix Moet er voor zorgen dat bij een join van 2+ tabellen er 2+ systemfields objecten geladen kunnen worden. Standaard wordt er van uitgegaan dat er geen prefix nodig is.
     * @return KVDdom_SystemFields
     */
    public function doLoadSystemFields( $rs , $currentRecord = true , $prefix = null)
    {
        if ($prefix !== null) {
            $prefix = $prefix . '_';
        }
        return new KVDdom_SystemFields (    $rs->getString ( $prefix . 'gebruiker'),
                                            $currentRecord,
                                            $rs->getInt ( $prefix . 'versie' ),
                                            $rs->getTimeStamp ( $prefix . 'bewerkt_op' ),
                                            $rs->getBoolean ( $prefix .'gecontroleerd' )
                                        );
    }
     
    /**
     * Stel de waarden van het SystemFields object in in de SQL statement
     *
     * @param Statement $stmt
     * @param KVDdom_DomainObject $domainObject
     * @param integer $firstParam
     */
    public function doSetSystemFields($stmt, $domainObject, $firstParam)
    {
        $systemFields = $domainObject->getSystemFields();
        $stmt->setString($firstParam, $systemFields->getGebruikersNaam());
        $stmt->setTimeStamp($firstParam+1, $systemFields->getBewerktOp());
        $stmt->setInt($firstParam+2, $systemFields->getVersie());
        $stmt->setBoolean($firstParam+3, $systemFields->getGecontroleerd());
    }

    /**
     * Stel de waarden voor het selecteren van de te updaten record in
     * @param Statement $stmt 
     * @param integer $id
     * @param integer $versie
     * @param integer $firstParam De numerieke index in het Creole Statement van de id parameter.
     */
    public function doSetUpdateWhere($stmt, $id, $versie, $firstParam)
    {
        $stmt->setInt($firstParam, $id);
        $stmt->setInt($firstParam+1, $versie);
    }

    /**
     * Verwijder een bepaald DomainObject uit de databank.
     *
     * Implementeert Optimist Offline Locking, indien het object gewijzigd is door iemand anders sinds het geladen werd wordt er een Exception gegenereerd.
     * @param KVDdom_DomainObject $domainObject Het DomainObject dat moet verwijderd worden.
     * @throws KVDdom_ConcurrencyException
     */
    public function delete ( $domainObject )
    {
        try {
            $this->_conn->setAutoCommit (false);
            $currentVersie = $domainObject->getSystemFields()->getVersie();
            $this->LogInsert( $domainObject->getId() );
            $stmt = $this->_conn->prepareStatement($this->getDeleteStatement());
            $stmt->setInt (1, $domainObject->getId());
            if ($stmt->executeUpdate() == 0) {
                $message = 'Het object dat u probeert te verwijderen is gewijzigd sinds u het geopend hebt. <br/>';
                throw new KVDdom_ConcurrencyException ($message,$domainObject);
            }
            $this->_conn->commit();
        } catch (SQLException $e) {
            $this->_conn->rollback();
            throw $e;
        }
    }


    /**
     * Een abstract functie die het grootste deel van het opzoekwerk naar een DomainObject met een specifieke Id uitvoert
     *
     * @param string $returnType Het soort DomainObject dat gevraagd wordt, nodig om de IdentityMap te controleren.
     * @param integer $id Het id nummer van het gevraagd DomainObject.
     * @return KVDdom_DomainObject Een DomainObject van het type dat gevraagd werd met de parameter $returnType.
     * @throws <b>KVDdom_DomainObjectNotFoundException</b> - Indien het gevraagde DomainObject niet werd gevonden.
     */
    protected function abstractFindById ( $returnType , $id )
    {
        try {
            $domainObject = parent::abstractFindById( $returnType, $id );
        } catch ( KVDdom_DomainObjectNotFoundException $e ) {
            try {
                $laatsteVersie = $this->findByLogId( $id );
            } catch ( KVDdom_LogDomainObjectNotFoundException $le ) {
                throw $e;
            }
            $systemFields = new KVDdom_SystemFields( 'ongekend',false,$laatsteVersie->getSystemFields( )->getVersie( ) );
            $domainObject = $this->createDeleted( $id, $systemFields );
        }
        return $domainObject;
    }

    /**
     * Een abstracte methode om een gelogde versie van een domainObject aan te maken.
     *
     * Grote verschil is dat deze versie niet meer gewijzigd kan worden door een gebruiker. Ze moet ook niet worden opgenomen door de UOW.
     * @param integer $id Uniek nummer van het te laden object
     * @param integer $versie Versie van het te laden object. Indien versie gelijk is aan(@link KVDdom_LogableDataMapper::MAXVERSIE) wordt de recentste versie geladen.
     * @return KVDdom_DomainObject
     * @throws KVDdom_LogDomainObjectNotFoundException
     * */
    protected function abstractFindByLogId ( $returnType , $id , $versie )
    {
        if ( $versie == self::MAXVERSIE ) {
            $stmt = $this->_conn->prepareStatement ( $this->getFindByLogIdMaxVersieStatement() );
            $stmt->setInt ( 2 , $id);
        } else {
            $stmt = $this->_conn->prepareStatement ( $this->getFindByLogIdStatement() );
            $stmt->setInt ( 2 , $versie );
        }
        $stmt->setInt(1, $id);
        $rs = $stmt->executeQuery();
        if (!$rs->next()) {
            throw new KVDdom_LogDomainObjectNotFoundException ( '', $returnType, $id, $versie);
        }
        return $this->doLogLoad ( $id , $rs );
    }

    /**
     * Een abstract methode die een collectie met alle vorige versies van een bepaald domainObject teruggeeft.
     * @param integer $id
     * @return KVDdom_DomainObjectCollection
     */
    protected function abstractFindLogAll ( $id )
    {
        $stmt = $this->_conn->prepareStatement ( $this->getLogFindAllStatement() );
        $stmt->setInt( 1 , $id );
        return $this->executeLogFindMany ( $stmt );
    }

    /**
     * @param integer $id
     * @throws Exception - Indien het record niet gelogd kon worden.
     */
    protected function LogInsert( $id )
    {
        $stmt = $this->_conn->prepareStatement ( $this->getLogInsertStatement() );
        $stmt->setInt(1, $id );
        try {
            $stmt->executeUpdate();
        } catch (SQLException $e) {
            throw new Exception ( 'Het record kon niet gelogd worden omwille van een SQL probleem: ' . $e->getMessage() );
        }
    }

    /**
     * @param integer $id
     * @param ResultSet $rs
     * @return KVDdom_DomainObject
     */
    abstract public function doLogLoad ( $id , $rs);

    /**
     * @param integer $id
     * @return KVDdom_DomainObjectCollection
     */
    abstract public function findLogAll( $id );

    /**
     * @param KVDdom_DomainObjectCollection
     */
    abstract public function findTeRedacteren( );

    /**
     * @param KVDdom_DomainObjectCollection
     */
    abstract public function findVerwijderde( );

    /**
     * Zoek alle records van deze datamapper die nog niet gecontroleerd zijn.
     * @return KVDdom_DomainObjectCollection
     */
    protected function abstractFindTeRedacteren ( )
    {
        $stmt = $this->_conn->prepareStatement ( $this->getFindTeRedacterenStatement() );
        return $this->executeFindMany ( $stmt );
    }

    protected function abstractFindVerwijderde( )
    {
        $stmt = $this->_conn->prepareStatement( $this->getFindVerwijderdeStatement( ) );
        return $this->executeLogFindMany( $stmt );
    }

    /**
     * @param KVDdom_LogableDomainObject
     * @return KVDdom_LogableDomainObject
     */
    protected function restoreDeleted( $domainObject )
    {
        $domainObject->getSystemFields( )->updateSystemFields( $this->_sessie->getGebruiker( )->getGebruikersNaam( ));
        $this->insert( $domainObject );
        return $domainObject;
    }

    /**
     * Maak een Special Case aan van een domainObject dat null is maar wel kan geupadte worden.
     * @param integer $id
     * @param KVDdom_SystemFields $systemFields
     */
    abstract protected function createDeleted( $id, $systemFields );

    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien een record niet goedgekeurd kan worden.
     */
    public function approve ( $domainObject )
    {
        $stmt = $this->_conn->prepareStatement ( $this->getApproveRecordStatement( ) );
        $stmt->setInt(1, $domainObject->getId( ) );
        try {
            $stmt->executeUpdate( );
        } catch (SQLException $e) {
            throw new Exception ( 'Het record kan niet goedgekeurd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
        $stmt = $this->_conn->prepareStatement (  $this->getApproveLogRecordsStatement( ) );
        $stmt->setInt( 1, $domainObject->getId( ) );
        try {
            $stmt->executeUpdate( );
        } catch (SQLException $e) {
            throw new Exception ( 'Het record kan niet goedgekeurd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
    }

    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien een record zijn geschiedenis niet gewist kan worden.
     */
    public function clearHistory( $domainObject )
    {
        $stmt = $this->_conn->prepareStatement( $this->getClearLogStatement( ) );
        $stmt->setInt( 1,$domainObject->getId( ) );
        try {
            $stmt->executeUpdate( );
        } catch ( SQLException $e ) {
            throw new Exception ( 'De geschiedenis van het record kan niet verwijderd worden omwille van een SQL probleem: ' . $e->getMessage( ) );
        }
    }
    
    /**
     * @param Statement $stmt
     * @return KVDdom_DomainObjectCollection
     */
    protected function executeLogFindMany( $stmt )
    {
        $rs = $stmt->executeQuery( );
        $domainObjects =array ( );
        while ( $rs->next( ) ) {
            $logObject = $this->doLogLoad( $rs->getInt( 'id' ), $rs );
            $domainObjects[] = $logObject;
        }
        return new KVDdom_DomainObjectCollection ( $domainObjects );
    }
}
?>
