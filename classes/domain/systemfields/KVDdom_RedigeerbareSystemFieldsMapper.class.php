<?php
/**
 * @package KVD.dom
 * @package systemfields
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_RedigeerbareSystemFieldsMapper
 *
 * @package KVD.dom
 * @package systemfields
 * @since 9 jul 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdom_RedigeerbareSystemFieldsMapper extends KVDdom_AbstractSystemFieldsMapper
{

    /**
     * De velden die nodig zijn voor het SystemFields object.
     *
     * @var string
     */
    protected $systemFields = "aangemaakt_door, aangemaakt_op, versie, bewerkt_door, bewerkt_op, gecontroleerd_door, gecontroleerd_op";

    /**
     * Laad een SystemFields object op basis van een rij uit de databank
     *
     * @param StdClass $row Een StdClass object dat door PDO wordt afgeleverd via fetchRow. Dit object moet de nodige velden bevatten om een Systemfields object mee samen te kunnen stellen.
     * @param string $prefix Moet er voor zorgen dat bij een join van 2+ tabellen er 2+ systemfields objecten geladen kunnen worden. Standaard wordt er van uitgegaan dat er geen prefix nodig is.
     * @return KVDdom_RedigeerbareSystemFields
     */
    public function doLoadSystemFields( $row , $prefix = null)
    {
        if ($prefix !== null) {
            $prefix = $prefix . '_';
        }
        $aangemaaktDoor = $prefix . 'aangemaakt_door';
        $aangemaaktOp = $prefix . 'aangemaakt_op';
        $versie = $prefix . 'versie';
        $bewerktDoor = $prefix . 'bewerkt_door';
        $bewerktOp = $prefix . 'bewerkt_op';
        $gecontroleerdDoor = $prefix . 'gecontroleerd_door';
        $gecontroleerdOp = $prefix . 'gecontroleerd_op';
        return new KVDdom_RedigeerbareSystemFields( $row->$aangemaaktDoor,
                                                    new DateTime( $row->$aangemaaktOp),
                                                    $row->$versie,
                                                    $row->$bewerktDoor,
                                                    new DateTime( $row->$bewerktOp),
                                                    $row->$gecontroleerdDoor,
                                                    new DateTime( $row->$gecontroleerdOp) );
    }

    /**
     * Stel de waarden van het SystemFields object in in de SQL statement
     *
     * @param PDOStatement $stmt
     * @param KVDdom_DomainObject $domainObject
     * @param integer $startIndex De numerieke index in de PDO Statement van de eerste parameter ( de gebruikersnaam ).
     * @return integer Nummer van de volgende te gebruiken index in het sql statement.
     */
    public function doSetSystemFields($stmt, $domainObject, $startIndex )
    {
        if ( !$domainObject->hasSystemFields( ) ) {
            throw new LogicException ( 'Kan de systemFields van een object dat geen systemFields heeft niet instellen op een statement.');
        }
        $sf = $domainObject->getSystemFields();
        $stmt->bindValue( $startIndex++ , $sf->getAangemaaktDoor( ) , PDO::PARAM_STR );
        $stmt->bindValue( $startIndex++,  $sf->getAangemaaktOp( )->format( DATE_ISO8601), PDO::PARAM_STR);
        $stmt->bindValue( $startIndex++ , $sf->getTargetVersie( ) , PDO::PARAM_INT );
        $stmt->bindValue( $startIndex++ , $sf->getBewerktDoor( ) , PDO::PARAM_STR );
        $stmt->bindValue( $startIndex++ , $sf->getBewerktOp( )->format( DATE_ISO8601), PDO::PARAM_STR);
        $stmt->bindValue( $startIndex++ , $sf->getGecontroleerdDoor( ) , PDO::PARAM_STR );
        $stmt->bindValue( $startIndex++ , $sf->isGecontroleerd( ) ? $systemFields->getGecontroleerdDoor( )->format( DATE_ISO8601 ) : null, PDO::PARAM_STR);
        return $startIndex;
    }

    /**
     * newNull
     *
     * @param integer $versie
     * @return KVDdom_RedigeerbareSystemFields
     */
    public function newNull( $versie = 0 )
    {
        return new KVDdom_RedigeerbareSystemFields( 'ongekend', null, $versie );
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
        $domainObject->getSystemFields()->setUpdated( $gebruiker );
    }

}
?>
