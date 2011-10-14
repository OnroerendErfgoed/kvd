<?php
/**
 * @package    KVD.gis
 * @subpackage util
 * @version    $Id$
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author     Dieter Standaert <dieter.standaert@eds.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Generator die sql bestanden kan aanmaken om tabellen te vullen met crab 
 * data.
 *
 * @package    KVD.gis
 * @subpackage util
 * @since      12 jul 2008
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author     Dieter Standaert <dieter.standaert@eds.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDgis_CrabSqlGenerator
{
    /**
     * sessie 
     * 
     * @var KVDdom_IReadSessie
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
     * generateSqlTableStraten 
     * 
     * @param KVDdo_AdrGemeente	$gemeente Gemeente waarvoor de straten 
     *                                    moeten opgezocht worden
     * @return string Een string die alle sql statements bevat nodig om 
     *                de straten in een databank op te slaan. 
     */
    public function generateStratenTable( KVDdo_AdrGemeente $gemeente)
    {
        $this->sessie->setDefaultMapper(  "KVDdo_AdrStraat", 'soap');
        $mapper = $this->sessie->getMapper( "KVDdo_AdrStraat", 'soap');
        $sql = "";
        $startTime = microtime();
        $straten = $mapper->findByGemeente( $gemeente );
        $endTime = microtime();
        foreach ( $straten as $straat) {
            $sql .= 
                $straat->getId( ) ."\t".
                $straat->getStraatnaam( ) ."\t".
                $straat->getStraatLabel( ) ."\t".
                $straat->getGemeente()->getId() ."\n";
        }
        return $sql;
    }

    /**
     * generateSqlStraten 
     * 
     * @param KVDdo_AdrGemeente	$gemeente Gemeente waarvoor de straten 
     *                                    moeten opgezocht worden
     * @param boolean $log geeft weer of de laadtijden gelogd moeten worden
     * @return string Een string die alle sql statements bevat nodig om 
     *                de straten in een databank op te slaan. 
     */
    public function generateSqlStraten( KVDdo_AdrGemeente $gemeente, $log = false )
    {
        $this->sessie->setDefaultMapper(  "KVDdo_AdrStraat", 'soap');
        $mapper = $this->sessie->getMapper( "KVDdo_AdrStraat", 'soap');
        $sql = "";
        $startTime = microtime();
        $straten = $mapper->findByGemeente( $gemeente );
        $endTime = microtime();
        if($log) {
            $sql .= "--Straten voor ".$gemeente->getGemeenteNaam().
                    " (geladen in ".($endTime - $startTime)." secs)\n";
        }
        
        if(($straten->count() == 0) && $log) {
            $sql.= "--Geen uit mapper ".get_class($mapper)."\n";
        }
        foreach ( $straten as $straat) {
            $sql .= sprintf( "INSERT INTO kvd_adr.straat VALUES ( %d, '%s', '%s', %d);\n", 
                $straat->getId( ),
                addslashes($straat->getStraatnaam( )), 
                addslashes($straat->getStraatLabel( )), 
                $straat->getGemeente()->getId()
            );
        }
        return $sql;
    }

    /**
     * generateHuisnummersTable
     * 
     * @param KVDdo_AdrStraat $straat Straat waarvoor de huisnummers 
     *                                moeten opgezocht worden
     * @return string Een string die alle sql statements bevat nodig om 
     *                de huisnummers in een databank op te slaan. 
     */
    public function generateHuisnummersTable( KVDdo_AdrStraat $straat)
    {
        $this->sessie->setDefaultMapper(  "KVDdo_AdrHuisnummer", 'soap');
        $mapper = $this->sessie->getMapper( "KVDdo_AdrHuisnummer");
        
        $startTime = microtime();
        $huizen = $mapper->findByStraat( $straat);
        $endTime = microtime();
        $sql = "";
        foreach ( $huizen as $huis) {
            $sql .= 
                $huis->getId( )."\t".
                addslashes($huis->getHuisnummer( ))."\t".
                $straat->getId()."\n";
        }
        return $sql;
    }

    /**
     * generateSqlHuisnummers 
     * 
     * @param KVDdo_AdrStraat $straat Straat waarvoor de huisnummers moeten 
     *                                opgezocht worden
     * @param boolean $log Geeft weer of de laadtijden gelogd moeten worden
     * @return string Een string die alle sql statements bevat nodig om 
     *                de straten in een databank op te slaan. 
     */
    public function generateSqlHuisnummers( KVDdo_AdrStraat $straat , $log = false)
    {
        $this->sessie->setDefaultMapper( "KVDdo_AdrHuisnummer", 'soap');
        $mapper = $this->sessie->getMapper( "KVDdo_AdrHuisnummer");
        
        $startTime = microtime();
        $huizen = $mapper->findByStraat( $straat);
        $endTime = microtime();
        $sql = "";
        if($log) {
            $sql .= "--Huizen voor ".$straat->getStraatnaam().
                    " (geladen in ".($endTime - $startTime)." secs)\n";
        }
        
        if(($huizen->count() == 0) && $log){
            $sql.= "--Geen uit mapper ".get_class($mapper)."\n";
        }
        foreach ( $huizen as $huis) {
            $sql .= sprintf( 
                "INSERT INTO kvd_adr.huisnummer VALUES ( %d, '%s', %d);\n", 
                $huis->getId( ),
                addslashes($huis->getHuisnummer( )),
                $straat->getId()
            );
        }
        return $sql;
    }

    /**
     * generateTerreinobjectenTable 
     * 
     * @param KVDdo_AdrHuisnummer $huis Gebouw waarvoor de terreinobjecten 
     *                                  moeten opgezocht worden
     * @return string Een string die alle sql statements bevat nodig om de 
     *                straten in een databank op te slaan. 
     */
    public function generateTerreinobjectenTable( KVDdo_AdrHuisnummer $huis)
    {
        $this->sessie->setDefaultMapper( "KVDdo_AdrTerreinobject", 'soap');
        $mapper = $this->sessie->getMapper( "KVDdo_AdrTerreinobject");
        
        $startTime = microtime();
        $objecten = $mapper->findByHuisnummer( $huis);
        $endTime = microtime();
        $sql = "";
        foreach ( $objecten as $object) {
            $sql .=
                $object->getId( )."\t".
                $object->getAardTerreinObject( )."\t".
                $object->getCenter()->getX()."\t".
                $object->getCenter()->getY()."\t".
                $huis->getId()."\n";
        }
        return $sql;
    }

    /**
     * generateSqlTerreinobjecten 
     * 
     * @param KVDdo_AdrHuisnummer $huis Gebouw waarvoor de terreinobjecten 
     *                                  moeten opgezocht worden
     * @return string Een string die alle sql statements bevat nodig om de 
     *                straten in een databank op te slaan. 
     */
    public function generateSqlTerreinobjecten( KVDdo_AdrHuisnummer $huis, $log = false )
    {
        $this->sessie->setDefaultMapper( "KVDdo_AdrTerreinobject", 'soap');
        $mapper = $this->sessie->getMapper( "KVDdo_AdrTerreinobject");
        
        $startTime = microtime();
        $objecten = $mapper->findByHuisnummer( $huis);
        $endTime = microtime();
        $sql = "";
        if($log) {
            $sql .= "--Terreinobjecten voor nr ".$huis->getHuisnummer().
                    " (geladen in ".($endTime - $startTime)." secs)\n";
        }
        
        if(($objecten->count() == 0) && $log) {
            $sql.= "--Geen uit mapper ".get_class($mapper)."\n";
        }
        foreach ( $objecten as $object) {
            $sql .= sprintf( 
                "INSERT INTO kvd_adr.terreinobject VALUES ( '%s', '%s', %d, %d, %d);\n", 
                addSlashes($object->getId( )),
                addslashes($object->getAardTerreinObject( )),
                $object->getCenter()->getX(),
                $object->getCenter()->getY(),
                $huis->getId()
            );
        }
        return $sql;
    }
}
