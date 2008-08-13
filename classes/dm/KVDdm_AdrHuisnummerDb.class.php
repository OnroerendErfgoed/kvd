<?php
/**
 * @package KVD.dm
 * @subpackage Adr
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id: KVDdm_AdrHuisnummer.class.php 304 2007-05-24 14:23:03Z vandaeko $
 */

/**
 * KVDdm_AdrHuisnummerDb
 * 
 * @package KVD.dm
 * @subpackage Adr
 * @since maart 2008
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdm_AdrHuisnummerDb extends KVDdom_PDODataMapper{
    
	/**
	 * Het soort domain-objects dat deze mapper teruggeeft. 
	 */
	const RETURNTYPE = "KVDdo_AdrHuisnummer";
	
  /**
   * initialize
   *
   */
	public function initialize()
	{
	 $this->id = "huisnummer.id";
	 $this->tabel = "kvd_adr.huisnummer";
	 $this->velden =	"nummer, straat_id";
	}
	
	/**
	 * getFindByHuisnummerStatement
	 * @return string sql statement
	 */
	public function getFindByHuisnummerStatement()
	{
		return $this->getSelectStatement()." WHERE huisnummer.nummer = ? AND huisnummer.straat_id = ?";
	}	
	
	/**
	 * getFindByStraatStatement
	 * @return string sql statement
	 */
	public function getFindByStraatIdStatement()
	{
		return $this->getSelectStatement()." WHERE huisnummer.straat_id = ?";
	}	

	/**
	 * @param integer $id
	 * @param array $crabData   Een associatieve array met minimaal de sleutel huisnummer en huisnummerId. 
	 *                          Indien er geen KVDdo_AdrStraat wordt meegegeven als derde parameter moet er ook een sleutel straatnaamId aanwezig zijn.
	 * @param KVDdo_AdrStraat
	 * @return KVDdo_AdrHuisnummer
	 */
	public function doLoad( $id , $rs , $straat = null)
	{
		$domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
		if ( $domainObject !== null ) {
			return $domainObject;
		}

		if ( is_null( $straat ) ) {
			try {
				$straatMapper = $this->_sessie->getMapper( 'KVDdo_AdrStraat');
				$straat = $straatMapper->findById( $rs->straat_id);
			} catch (KVDdom_DomainObjectNotFoundException $e) {
				$straat = KVDdo_AdrStraat::newNull();
			}
		}
		return new KVDdo_AdrHuisnummer (    $id,
			$this->_sessie,
			$straat,
			$rs->nummer
			);
	}

	/**
	 * Zoek een huisnummer op basis van zijn id.
	 * @param integer $id Id van het huisnummer, dit is een nummer dat toegewezen werd door crab.
	 * @return KVDdo_AdrHuisnummer
	 * @throws <b>KVDdom_DomainObjectNotFoundException</b> Indien het object niet geladen kon worden.
	 */ 
	public function findById ( $id )
	{
		return $this->abstractFindById(self::RETURNTYPE,$id);
	}

	/**
	 * Zoek een huisnummer op basis van zijn huisnummer. Dit is een tekstuele voorstelling van het huisnummer met inbegrip van eventuele bis-waarden. Tevens is het crabId van de straat nodig.
	 * @param string $huisnummer Het huisnummer volgens Crab.
	 * @param integer $straatId Het crabId van de straat.
	 * @return KVDdo_AdrHuisnummer
	 * @throws <b>KVDdom_DomainObjectNotFoundException</b> Indien het object niet geladen kon worden.
	 */ 
	public function findByHuisnummer ( $huisnummer , $straatId )
	{
	
		$sql = $this->getFindByHuisnummerStatement();
		$this->_sessie->getSqlLogger( )->log( $sql );
		$stmt = $this->_conn->prepare($sql);
		$stmt->bindValue(1, $huisnummer , PDO::PARAM_STR);
		$stmt->bindValue(2, $straatId, PDO::PARAM_INT);
		$stmt->execute( );
		if ( !$row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			throw new KVDdom_DomainObjectNotFoundException ( 'Kon het huisnummer niet vinden' , self::RETURNTYPE, null);
		}
		return $this->doLoad($row, $row->id);
	}

	/**
	 * Zoek alle huisnummers in een bepaalde straat.
	 * @param KVDdo_AdrStraat $straat
	 * @return KVDdom_DomainObjectCollection Een verzameling van {@link KVDdo_AdrHuisnummer} objecten.
	 */
	public function findByStraat ( $straat )
	{
		$sql = $this->getFindByStraatIdStatement();
		$this->_sessie->getSqlLogger( )->log( $sql );
		$stmt = $this->_conn->prepare($sql);
		$stmt->bindValue(1, $straat->getId(), PDO::PARAM_INT);
		return $this->executeFindMany($stmt);
	}
		

	/**
	 * Zoek alle huisnummers in een bepaalde straat.
	 * @param KVDdo_AdrStraat $straat
	 * @return KVDdom_DomainObjectCollection Een verzameling van {@link KVDdo_AdrHuisnummer} objecten.
	 */
	public function findByStraatId ( $straat_id )
	{
		$sql = $this->getFindByStraatIdStatement();
		$this->_sessie->getSqlLogger( )->log( $sql );
		$stmt = $this->_conn->prepare($sql);
		$stmt->bindValue(1, $straat_id, PDO::PARAM_INT);
		return $this->executeFindMany($stmt);
	}

  /**
	 * findPostCodeByHuisnummer 
	 * 
	 * @param KVDdo_AdrHuisnummer $huisnummer 
	 * @return integer De postkantonCode van het huisnummer.
	 */
	public function findPostCodeByHuisnummer( $huisnummer )
	{
		return 'Onbepaald';
	} 

	/**
	 * findAll
	 * @return KVDdom_Collection
	 */	
	public function findAll()
	{
		 return $this->abstractFindAll(self::RETURNTYPE);
	}

}
?>
