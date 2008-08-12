<?php
/**
 * @package     KVD.gis
 * @subpackage  Util
 * @version     $Id$
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author      Dieter Standaert <dieter.standaert@eds.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDgis_CrabSqlGenerator 
 * @package     KVD.gis
 * @subpackage  Util
 * @since       12 jul 2008
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author      Dieter Standaert <dieter.standaert@eds.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDgis_CrabSqlGenerator
{
    /**
     * sessie 
     * 
     * @var KVDdom_ISessie
     */
    protected $sessie;

    /**
     * __construct 
     * 
     * @param KVDdom_IReadSessie $sessie 
     * @return void
     */
    public function __construct( KVDdom_IReadSessie $sessie )
    {
        $this->sessie = $sessie;

    }

		/**
     * generateSqlStraten 
     * 
     * @param KVDdo_AdrGemeente	$gemeente gemeente voor welke de straten moeten opgezocht worden
     * @return string	Een string die alle sql statements bevat nodig om de strateb in een databank op te slaan. 
     */
    public function generateSqlStraten( KVDdo_AdrGemeente $gemeente )
    {
			$mapper = $this->sessie->getMapper( "KVDdo_AdrStraat" , 'soap');
			
			$startTime = microtime();
			$straten = $mapper->findByGemeente( $gemeente );
			$endTime = microtime();
			$sql = "--Straten voor ".$gemeente->getGemeenteNaam()." (geladen in ".($endTime - $startTime)." secs)\n";
			
			if($straten->count() == 0) {
				$sql.= "--Geen uit mapper ".get_class($mapper)."\n";
			}
			foreach ( $straten as $straat) {
				$sql .= sprintf( "INSERT INTO kvd_adr.straat VALUES ( %d, '%s', '%s', %d);\n", 
					$straat->getId( ),
					$straat->getStraatnaam( ), 
					$straat->getStraatLabel( ), 
					$straat->getGemeente()->getId()
				);
			}
			return $sql;
    }


		/**
     * generateSqlHuisnummers 
     * 
     * @param KVDdo_AdrStraat	$straat straat voor welke de huisnummers moeten opgezocht worden
     * @return string	Een string die alle sql statements bevat nodig om de strateb in een databank op te slaan. 
     */
    public function generateSqlHuisnummers( KVDdo_AdrStraat $straat )
    {
			$mapper = $this->sessie->getMapper( "KVDdo_AdrHuisnummer");
			
			$startTime = microtime();
			$huizen = $mapper->findByStraat( $straat);
			$endTime = microtime();
			$sql = "--Huizen voor ".$straat->getStraatnaam()." (geladen in ".($endTime - $startTime)." secs)\n";
			
			if($huizen->count() == 0) {
				$sql.= "--Geen uit mapper ".get_class($mapper)."\n";
			}
			foreach ( $huizen as $huis) {
				$sql .= sprintf( "INSERT INTO kvd_adr.huisnummer VALUES ( %d, '%s', %d);\n", 
					$huis->getId( ),
					$huis->getHuisnummer( ),
					$straat->getId()
				);
			}
			return $sql;
    }

}