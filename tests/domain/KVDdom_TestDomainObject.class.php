<?php
/**
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_SimpleTestDomainObject implements KVDdom_DomainObject
{
    protected $id;

    protected $titel;

    public function __construct( $id, $titel = null ) {
        $this->id = $id;
        $this->titel = $titel;
    }

    public function getId( ) {
        return $this->id;
    }

    public function getTitel( ) {
        return $this->titel;
    }

    public function getOmschrijving(  ) {
        return $this->titel;
    }

    public function getClass( ) 
    {
        return get_class( $this );
    }
}

/**
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_TestValueDomainObject extends KVDdom_ValueDomainObject
{
    protected $titel;

    public function __construct( $id, $titel )
    {
        parent::__construct( $id );
        $this->titel = $titel;
    }

    public function getTitel( ) {
        return $this->titel;
    }

    public function getOmschrijving( ) {
        return $this->titel;
    }
}
?>
