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
     * @param string    $domainObject       Naam van het domain object dat de thesaurus kan leveren. 
     *                                      Er zal altijd afgedwongen worden dat deze door de xml-mapper wordt geleverd.
     * @return string                       Een string die alle sql statements bevat nodig om deze thesaurus in een databank op te vragen. 
     */
    public function generateSqlStraten( $domainObject )
    {
			$this->sessie->setDefaultMapper( $domainObject, 'xml');
			$sql = "--Straten.\n";
			
			$straten = $this->sessie->getMapper( $domainObject )->findAll( );
			
			foreach ( $straten as $straat) {
				$sql .= sprintf( "INSERT INTO kvd_adr.straat VALUES ( %d, '%s', '%s', %d);\n", 
					$thesaurus_id, 
					$straat->getId( ),
					$straat->getNaam( ), 
					$straat->getLabel( ), 
					$straat->getGemeenteId()
				);
			}
			return $sql;
    }
}