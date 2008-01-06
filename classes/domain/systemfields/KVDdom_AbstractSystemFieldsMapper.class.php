<?php
/**
 * @package KVD.dom
 * @subpackage systemfields
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_AbstractSystemFieldsMapper 
 * 
 * @package KVD.dom
 * @subpackage systemfields
 * @since 27 jun 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDdom_AbstractSystemFieldsMapper
{
    /**
     * systemFields 
     * 
     * @var string
     */
    protected $systemFields = "";

    /**
     * getSystemFields 
     * 
     * @return string
     */
    public function getSystemFields( )
    {
        return $this->systemFields;
    }

    /**
     * getSystemFieldsString 
     * 
     * Stel de string samen om de system fields uit een bepaalde tabel te halen op basis van de tabelnaam en 
     * het al dan niet zijn van een logtabel.
     * @param string    $tabelNaam 
     * @param boolean   $logTabel 
     * @param string    $systemFields 
     * @return string
     */
    public function getSystemFieldsString ( $tabelNaam , $logTabel = false , $systemFields = null )
    {
        $systemFields = ( $systemFields === null ) ? $this->systemFields : $systemFields;
        if ( $systemFields == '' ) {
            return '';
        }
        $fields = explode ( ', ' , $systemFields );
        $tabel = ( $logTabel === false ) ? $tabelNaam : 'log_' . $tabelNaam;
        foreach ( $fields as &$field ) {
            $field = "$tabel.$field AS " . $tabelNaam . "_" . $field;
        }
        return implode ( ', ', $fields );
    }

    /**
     * getUpdateSystemFieldsString 
     * 
     * Een sql string die kan gebruikt worden in een update statement om de systemfields te updaten ( bv. versie = ?, gebruiker = ? ).
     * @return string
     */
    public function getUpdateSystemFieldsString( )
    {
        $fields = explode ( ', ' , $this->systemFields );
        foreach ( $fields as &$field ) {
            $field = "$field = ?";
        }
        return implode ( ', ' , $fields);
    }

    /**
     * getInsertSystemFieldsString 
     * 
     * Een sql string die kan gebruikt worden in een insert statement om de systemfields aan te maken ( bv. ?, ? ). Komt neer op een string met
     * voor elk SystemField een placeholder.
     * @return string
     */
    public function getInsertSystemFieldsString( )
    {
        $fields = explode ( ', ' , $this->systemFields );
        return implode ( ', ' , array_fill( 0 , count( $fields ) , '?' ) );
    }

    /**
     * doLoadSystemFields 
     * 
     * @param StdClass  $row 
     * @param string    $prefix 
     * @return KVDdom_ISystemFields
     */
    abstract public function doLoadSystemFields( $row , $prefix = null );

    /**
     * doSetSytemFields 
     * 
     * Stel de sytemfields in op het pdo statement.
     * @param PDOStatement          $stmt
     * @param KVDdom_DomainIbject   $sf             DomainObject waarvan de systemFields moeten ingesteld worden.
     * @param integer               $startIndex     Volgende te gebruiken index op het statement
     * @return void
     */
    public function doSetSystemFields( $stmt, $domainObject, $startIndex)
    {
        return $startIndex;
    }

    /**
     * newNull
     * 
     * @param integer $versie 
     * @return KVDdom_ISystemFields
     */
    abstract public function newNull( $versie = null );

    /**
     * create 
     * 
     * @param string $mapper 
     * @return KVDdom_AbstractSystemFieldsMapper
     */
    public static function create( $mapper )
    {
        switch ( $mapper ) {
            case 'changeable':
                return new KVDdom_ChangeableSystemFieldsMapper( );
            case 'dependent':
                return new KVDdom_DependentSystemFieldsMapper( );
            case 'redigeerbaar':
                return new KVDdom_RedigeerbareSystemFieldsMapper( );
            case 'legacy':
                return new KVDdom_LegacySystemFieldsMapper( );
            case 'legacyDependent':
                return new KVDdom_LegacyDependentSystemFieldsMapper( );
            case 'geen':
            default:
                return new KVDdom_NullSystemFieldsMapper( );
        }
    }

    /**
     * updateSystemFields 
     * 
     * @param KVDdom_DomainObject $domainObject
     * @param string $gebruiker 
     * @return void
     */
    abstract public function updateSystemFields( KVDdom_DomainObject $domainObject , $gebruiker=null);
}
?>
