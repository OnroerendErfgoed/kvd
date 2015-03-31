<?php
/**
 * @package KVD.dom
 * @subpackage systemfields
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_DependentSystemFieldsMapper
 *
 * Deze mapper verzorgt de mapping op het gebied van SystemFields wanneer het gaat om een object dat eigelijk afhankelijk is van een ander object
 * en dus de SystemFields van dat object gebruikt. Het is echter wel belangrijk dat het dependent object weet bij welke versie van een object het
 * hoort. Dat is dan ook de enige info die wordt bijgehouden.
 *
 * @package KVD.dom
 * @subpackage systemfields
 * @since 27 jun 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdom_DependentSystemFieldsMapper extends KVDdom_AbstractSystemFieldsMapper
{
    /**
     * systemFields
     *
     * @var string
     */
    protected $systemFields = "versie";

    /**
     * doLoadSystemFields
     *
     * @param StdClass  $row
     * @param string    $prefix
     * @return KVDdom_ChangeableSystemFields
     */
    public function doLoadSystemFields( $row , $prefix = null )
    {
        throw new LogicException ( 'Een object dat dependent is op een ander voor systemFields kan nooit zelf systemFields laden.');
    }

    /**
     * doSetSystemFields
     *
     * @param PDOStatement                  $stmt
     * @param KVDdom_DomainObject           $domainObject
     * @param integer                       $startIndex
     * @return integer      Volgende te gebruiken index.
     */
    public function doSetSystemFields( $stmt , $domainObject , $startIndex )
    {
        $stmt->bindValue( $startIndex++, $domainObject->getSystemFields( )->getTargetVersie( ), PDO::PARAM_INT);
        return $startIndex;
    }

    /**
     * newNull
     *
     * @param integer $versie
     * @return KVDdom_ChangeableSystemFields
     */
    public function newNull( $versie = 0 )
    {
        throw new LogicException ( 'Een object dat dependent is op een ander voor systemFields kan nooit zelf systemFields laden.');
    }

    /**
     * updateSystemFields
     *
     * @param KVDdom_DomainObject $domainObject
     * @param string $gebruiker
     * @return void
     */
    public function updateSystemFields( KVDdom_DomainObject $domainObject , $gebruiker = null )
    {
        if ( !$domainObject->hasSystemFields( ) ) {
            throw new LogicException ( 'Kan de systemFields van een object dat geen systemFields heeft niet updaten.');
        }
        $domainObject->getSystemFields( )->setUpdated( $gebruiker );
    }
}
?>
