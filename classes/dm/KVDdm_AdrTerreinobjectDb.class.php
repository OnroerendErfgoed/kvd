<?php
/**
 * @package KVD.dm
 * @subpackage Adr
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id: KVDdm_AdrTerreinobject.class.php 379 2007-12-04 11:18:16Z vandaeko $
 */

/**
 * KVDdm_AdrTerreinobjectDb 
 * 
 * @package KVD.dm
 * @subpackage Adr
 * @since augustus 2008
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdm_AdrTerreinobjectDb extends KVDdom_PDODataMapper{
    
	/**
	 * Code volgens de EPSG voor de Belge Lambert projectie.
	 * @var integer
	 */
	const EPSG_CODE = 31300;

	/**
	 * Het soort domain-object dat wordt teruggegeven door deze mapper.
	 * @var string 
	 */
	const RETURNTYPE = "KVDdo_AdrTerreinObject";

  /**
   * initialize
   *
   */
	public function initialize()
	{
	 $this->id = "terreinobject.id";
	 $this->tabel = "kvd_adr.terreinobject";
	 $this->velden =	"aard, center_x, center_y, huisnummer_id";
	}

	/**
	 * getFindByHuisnummerIdStatement
	 * @return string sql statement
	 */
	public function getFindByHuisnummerIdStatement()
	{
		return $this->getSelectStatement()." WHERE terreinobject.huisnummer_id = ?";
	}


	/**
	 * @param integer $id
	 * @param
	 * @return KVDdo_AdrTerreinobject
	 */
	public function doLoad( $id , $rs , $huisnummer = null)
	{
		$domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
		if ( $domainObject !== null ) {
			return $domainObject;
		}
		if(is_null($huisnummer)) {
			try{
				$huisnummer = $this->_sessie->getMapper("KVDdo_AdrHuisnummer")->findById($rs->huisnummer_id);
			} catch(KVDdom_DomainObjectNotFoundException $e) {
				$huisnummer = KVDdo_AdrHuisnummer::newNull();
			}
		}
		try {
			$center = new KVDgis_GeomPoint ( self::EPSG_CODE , $rs->center_x, $rs->center_y);
		} catch ( InvalidArgumentException $e ) {
			$center = new KVDgis_GeomPoint ( );
		}
		return new KVDdo_AdrTerreinobject ( $id,
			$this->_sessie,
			$rs->aard,
			$huisnummer,
			$center
		);
	}

	/**
	 * Zoek een terreinobject op basis van zijn id ( identificatorTerreinobject in Crab ).
	 * @param string $id IdentificatorTerreinobject uit Crab.
	 * @return KVDdo_AdrTerreinobjet
	 * @totdo herbekijken hoe het zit met het huisnummer
	 * @throws <b>KVDdom_DomainObjectNotFoundException</b> Indien het object niet geladen kon worden.
	 */ 
	public function findById ( $id )
	{
		return $this->abstractFindById(self::RETURNTYPE,$id);
	}

	/**
	 * Zoek alle terreinobjecten van een bepaald huisnummer.
	 * @param KVDdo_AdrHuisnummer $huisnummer
	 * @return KVDdom_DomainObjectCollection Een verzameling van {@link KVDdo_AdrTerreinobject} objecten
	 */
	public function findByHuisnummer ( $huisnummer )
	{
		$sql = $this->getFindByHuisnummerIdStatement();
		$this->_sessie->getSqlLogger( )->log( $sql );
		$stmt = $this->_conn->prepare($sql);
		$stmt->bindValue(1, $huisnummer->getId(), PDO::PARAM_INT);
		return $this->executeFindMany($stmt);
	}
	/**
	 * Zoek alle terreinobjecten van een bepaald huisnummer.
	 * @param KVDdo_AdrHuisnummer $huisnummer
	 * @return KVDdom_DomainObjectCollection Een verzameling van {@link KVDdo_AdrTerreinobject} objecten
	 */
	public function findByHuisnummerId ( $huisnummer_id )
	{
		$sql = $this->getFindByHuisnummerIdStatement();
		$this->_sessie->getSqlLogger( )->log( $sql );
		$stmt = $this->_conn->prepare($sql);
		$stmt->bindValue(1, $huisnummer_id, PDO::PARAM_INT);
		return $this->executeFindMany($stmt);
	}

}
?>
