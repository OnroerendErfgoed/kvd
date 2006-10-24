<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Een Query object voor een Creole Statement dat zijn resultaten in stukjes terug haalt.
 *
 * Dit object kan echter alleen reeds gelogde objecten ( = oude versies van records) laden.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 * @deprecated Gebruik de PDO variant.
 */
class KVDdom_ChunkyLogQuery extends KVDdom_ChunkyQuery
{
    /**
     * @see KVDdom_ChunkyQuery::getDomainObjects()
     */
    public function getDomainObjects()
    {
        $this->_stmt->setLimit ( $this->max );
        $this->_stmt->setOffset ( $this->start );
        $rs = $this->_stmt->executeQuery();
        $domainObjects = array();
        while ($rs->next()) {
            $domainObjects[] = $this->_dataMapper->doLogLoad ( $rs->getInt('id') , $rs );
        }
        return $domainObjects;
    }
}

?>
