<?php
/**
 * @version $Id$
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version $Id$
 */

/**
 * KVDdom_DataMapper
 *
 * Een basis class die de mapping-functies die alle DataMappers gebruiken groepeert. Dit komt neer op een DataMapper voor Read-Only objecten
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 2005
 */
abstract class KVDdom_PDODataMapper {
    /**
     * parameters 
     * 
     * @var array
     */
    protected $parameters;

    /**
     * Een KVDdom_Sessie object, nodig voor de Unit Of Work en de Identity Map
     *
     * @var KVDdom_Sessie
     */
    protected $_sessie;
    /**
     * De connectie met de databank
     * @var Connection
     */
    protected $_conn;

    /**
     * systemFieldsMapper 
     * 
     * @var KVDdom_AbstractSystemFieldsMapper
     */
    protected $systemFieldsMapper;

    /**
     * Velden uit de databank waarin het DomainObject wordt opgeslagen.
     */
    const VELDEN = "";
    /**
     * Tabel uit de databank waarin het Domainobject wordt opgeslagen.
     */
    const TABEL = "";
    /**
     * Het veld dat het Id nummer van het DomainObject bevat.
     */
    const ID = "id";
    /**
     * Soort DomainObject dat deze DataMappers teruggeeft.
     */
    const RETURNTYPE = "";

    /**
     * tabel 
     * 
     * @var string
     */
    protected $tabel;
    /**
     * velden 
     * 
     * @var string
     */
    protected $velden;
    /**
     * id 
     * 
     * @var string
     */
    protected $id;

    /**
     * Maak de mapper aan en stel een connectie in
     * 
     * @param KVDdom_Sessie $sessie 
     * @param array $parameters 
     * @return void
     */
    public function __construct( $sessie , $parameters = array( ) )
    {
        $this->_sessie = $sessie;
        $this->_conn = $sessie->getDatabaseConnection( get_class($this) );
        $this->parameters = $parameters;
        $this->initialize( );
    }

    /**
     * initialize 
     * 
     * @return void
     */
    protected function initialize( )
    {
        $this->determineSystemFieldsMapper( );
        $this->id = 'id';
        
    }

    /**
     * determineSystemFieldsMapper 
     * 
     * @return void
     */
    protected function determineSystemFieldsMapper( )
    {
        if ( !isset( $this->parameters['systemFieldsMapper'] ) ) {
            $this->parameters['systemFieldsMapper'] = 'geen';
        }
        $this->systemFieldsMapper = KVDdom_AbstractSystemFieldsMapper::create( $this->parameters['systemFieldsMapper'] );
    }

    /**
     * getSelectStatement 
     * 
     * @return string SQL Statement
     */
    protected function getSelectStatement( )
    {
        return  "SELECT " . $this->id . " AS id, " . $this->velden . 
                " FROM " . $this->tabel;
    }

    /**
     * getFindByIdStatement 
     * Het SQL statement dat nodig is om een DomainObject aan de hand van zijn Id terug te vinden in de databank
     * @return string SQL Statement
     */
    protected function getFindByIdStatement()
    {
        return  $this->getSelectStatement( ) . 
                " WHERE " . $this->id . " = ?";
    }

    /**
     * getFindAllStatement 
     * 
     * De sql om alle DomainObjects van dit type te zoeken
     * @return string SQL Statement
     */
    protected function getFindAllStatement()
    {
        return  $this->getSelectStatement( ) . 
                " ORDER BY " . $this->id . " ASC";
    }
   
    /**
     * Een abstract functie die het grootste deel van het opzoekwerk naar een DomainObject met een specifieke Id uitvoert
     *
     * @param string $returnType Het soort DomainObject dat gevraagd wordt, nodig om de IdentityMap te controleren.
     * @param integer $id Het id nummer van het gevraagd DomainObject.
     * @return KVDdom_DomainObject Een DomainObject van het type dat gevraagd werd met de parameter $returnType.
     * @throws KVDdom_DomainObjectNotFoundException - Indien het gevraagde DomainObject niet werd gevonden.
     */
    protected function abstractFindById ( $returnType , $id )
    {
        $domainObject = $this->_sessie->getIdentityMap()->getDomainObject( $returnType , $id);
        if ($domainObject != null) {
            return $domainObject;
        }
        $sql = $this->getFindByIdStatement( );
        $this->_sessie->getSqlLogger( )->log( $sql );
        $stmt = $this->_conn->prepare($sql);
        $stmt->bindValue(1, $id , PDO::PARAM_INT );
        $stmt->execute();
        if (!$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $msg = "$returnType met id $id kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException ( $msg , $returnType , $id );
        }
        return $this->doLoad($id,$row);
    }

    /**
     * Functie om een DomainObject terug te vinden op basis van zijn id.
     * @param integer $id Het id nummer van het gevraagd DomainObject.
     * @return KVDdom_DomainObject Een DomainObject van het type waarvoor deze class een DataMapper is.
     */
    abstract public function findById ( $id );

    /**
     * @return KVDdom_LazyDomainObjectCollection
     */
    protected function abstractFindAll ()
    {
        $sql = $this->getFindAllStatement( );
        $this->_sessie->getSqlLogger( )->log( $sql );
        $stmt = $this->_conn->prepare ( $sql );
        return $this->executeFindMany( $stmt );
    }

    abstract public function findAll ();

    /**
     * Laad een DomainObject van het type waarvoor deze class een DataMapper is
     *
     * @param integer $id Id nummer van het DomainObject dat geladen moet worden.
     * @param ResultSet $rs Een ResultSet object waarin de gegevens om het DomainObject te laden gevonden kunnen worden.
     * @return KVDdom_DomainObject Het geladen DomainObject.
     */
    abstract public function doLoad( $id , $rs );

    /**
     * Voor een query uit en geef de resultaten terug als een collectie van domainobjects.
     * @param PDOStatement $stmt
     * @return KVDdom_DomainObjectCollection
     */
    protected function executeFindMany ( $stmt )
    {
        $stmt->execute( );
        $domainObjects = array ( );
        while ( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
            $domainObjects[] = $this->doLoad ( $row->id , $row );
        }
        return new KVDdom_DomainObjectCollection ( $domainObjects );
    }
    /**
     * Voor een query uit en geef de resultaten terug als een luie collectie van domainobjects.
     * @param string $sql
     * @param string $idField
     * @return KVDdom_LazyDomainObjectCollection
     */
    protected function executeLazyFindMany ( $sql , $idField = 'id' )
    {
        $query = new KVDdom_PDOChunkyQuery( $this->_conn, $this, $sql, $idField , 1, 50, $this->_sessie->getSqlLogger( ));
        return new KVDdom_LazyDomainObjectCollection ( $query );
    }
    
}
?>
