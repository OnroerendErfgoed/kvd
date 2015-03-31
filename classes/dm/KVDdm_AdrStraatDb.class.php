<?php
/**
 * @package     KVD.dm
 * @subpackage  Adr
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @author      Dieter Standaert <dieter.standaert@eds.com>
 */

/**
 * KVDdm_AdrStraatDb
 *
 * @package     KVD.dm
 * @subpackage  Adr
 * @since       augustus 2008
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @author      Dieter Standaert <dieter.standaert@eds.com>
 */
class KVDdm_AdrStraatDb extends KVDdom_PDODataMapper{

	/**
	 * Het soort domain-object dat deze mapper teruggeeft.
	 */
	const RETURNTYPE = "KVDdo_AdrStraat";

  /**
   * initialize
   *
   */
	public function initialize()
	{
	 $this->id = "straat.id";
	 $this->tabel = "kvd_adr.straat";
	 $this->velden =	"naam, label, gemeente_id";
	}

	/**
	 * getFindByGemeenteIdStatement
	 * @return string sql statement
	 */
	public function getFindByGemeenteIdStatement()
	{
		return $this->getSelectStatement()." WHERE straat.gemeente_id = ?";
	}

	/**
	 * getFindByGemeenteAndNaamStatement
	 * @return string sql statement
	 */
	public function getFindByGemeenteAndNaamStatement()
	{
		return $this->getSelectStatement()." WHERE straat.gemeente_id = ? AND UPPER(straat.naam) = UPPER( ? )";
	}

	/**
	 * @param   integer             $id
	 * @param   StdClass            $rs
     * @param   KVDdo_AdrGemeente   $gemeente
	 * @return  KVDdo_AdrStraat
	 */
	public function doLoad( $id , $rs, KVDdo_AdrGemeente $gemeente = null)
	{
        if ( !$rs instanceof stdClass ) {
            throw new InvalidArgumentException( $rs . ' is geen stdClass.' );
        }
		$domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
		if ( $domainObject !== null ) {
			return $domainObject;
		}

		if($gemeente == null) {
			try {
				$gemeente = $this->_sessie->getMapper( 'KVDdo_AdrGemeente')->findById($rs->gemeente_id);
			} catch ( KVDdom_DomainObjectNotFoundException $e ) {
				$gemeente = KVDdo_AdrGemeente::newNull( );
			}
		}
		return new KVDdo_AdrStraat (	$id,
                                        $this->_sessie,
                                        $rs->naam,
                                        $rs->label,
                                        $gemeente,
                                        null
                                        );
	}

	/**
	 * findById
	 * @param integer id
	 * @return MELBAdo_Werkset
	 */
	public function findById($id)
	{
		return $this->abstractFindById(self::RETURNTYPE,$id);
	}

	/**
	 * Zoek alle straten in een bepaalde gemeente.
	 * @param KVDdo_AdrGemeente $gemeente
	 * @return KVDdom_DomainObjectCollection Een verzameling van {@link KVDdo_AdrStraat} objecten
	 */
	public function findByGemeente (KVDdo_AdrGemeente $gemeente )
	{
		$sql = $this->getFindByGemeenteIdStatement();
		//$this->_sessie->getSqlLogger( )->log( $sql );
		$stmt = $this->_conn->prepare($sql);
		$stmt->bindValue(1, $gemeente->getId(), PDO::PARAM_INT);
		return $this->executeFindMany($stmt);
	}


	/**
	 * Zoek alle straten in een bepaalde gemeente.
	 * @param integer $gemeente_id
	 * @return KVDdom_DomainObjectCollection Een verzameling van {@link KVDdo_AdrStraat} objecten
	 */
	public function findByGemeenteId ($gemeente_id )
	{
		$sql = $this->getFindByGemeenteIdStatement();
		//$this->_sessie->getSqlLogger( )->log( $sql );
		$stmt = $this->_conn->prepare($sql);
		$stmt->bindValue(1, $gemeente_id, PDO::PARAM_INT);
		return $this->executeFindMany($stmt);
	}



	/**
	 * Zoek een straat op basis van zijn naam en de gemeente waarin de straat ligt.
     *
	 * @param   KVDdo_AdrGemeente $gemeente
     * @param   string            $naam
	 * @return  KVDdo_AdrStraat
	 * @throws  <b>KVDdom_DomainObjectNotFoundException</b> Indien het object niet geladen kon worden.
	 */
	public function findByNaam ( KVDdo_AdrGemeente $gemeente, $naam )
	{
		$sql = $this->getFindByGemeenteAndNaamStatement();
		//$this->_sessie->getSqlLogger( )->log( $sql );
		$stmt = $this->_conn->prepare($sql);
		$stmt->bindValue(1, $gemeente->getId(), PDO::PARAM_INT);
		$stmt->bindValue(2, $naam, PDO::PARAM_STR);
		$stmt->execute( );
		if ( !$row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			throw new KVDdom_DomainObjectNotFoundException ( 'Kon de straat niet vinden' , 'KVDdo_AdrStraat', null);
		}
		return $this->doLoad($row->id,$row);
	}
	/**
	 * findAll
	 * @return KVDdom_DomainObjectCollection Een verzameling van {@link KVDdo_AdrStraat} objecten
	 */
	public function findAll()
	{
		 return $this->abstractFindAll(self::RETURNTYPE);
	}
}
?>
