<?php
/**
 * @package KVD.dom
 * @subpackage 
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
/**
 * KVDdom_ChangeableSystemFields 
 * 
 * @package KVD.dom
 * @subpackage 
 * @since 27 jun 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_ChangeableSystemFields
{
    /**
     * versie 
     * 
     * @var integer
     */
    protected $versie;

    /**
     * targetVersie 
     * 
     * @var integer
     */
    protected $targetVersie;

    /**
     * aangemaaktOp 
     * 
     * @var date
     */
    protected $aangemaaktOp;

    /**
     * aangemaaktDoor 
     * 
     * @var string
     */
    protected $aangemaaktDoor;

    /**
     * bewerktop 
     * 
     * @var date
     */
    protected $bewerktop;

    /**
     * bewerktDoor 
     * 
     * @var string
     */
    protected $bewerktDoor;

    /**
     * locked 
     * 
     * @var boolean
     */
    protected $locked;

    /**
     * __construct 
     * 
     * @param string $aangemaaktDoor 
     * @param DateTime $aangemaaktOp 
     * @param int $versie 
     * @param string $bewerktDoor 
     * @param DateTime $bewerktOp 
     * @return void
     */
    public function __construct( $aangemaaktDoor, DateTime $aangemaaktOp = null ,  $versie = 0, $bewerktDoor=null, DateTime $bewerktOp =null)
    {
        $this->aangemaaktDoor = $aangemaaktDoor;
        if ($aangemaaktOp == null) {
            $aangemaaktOp = new DateTime( );
        }
        $this->aangemaaktOp = date(KVDdom_DomainObject::DATETIME_FORMAT , $aangemaaktOp );
        $this->versie = $this->targetVersie = $versie;
        $this->bewerktDoor = null;
        $this->bewerktOp = null;
        $this->locked = false;
    }

    /**
     * updateSystemFields 
     * 
     * @param string $updater 
     * @return void
     */
    public function updateSystemFields( $updater = null )
    {
        if ( !$this->locked ) {
            $this->targetVersie++;
            $this->bewerktOp = date(KVDdom_DomainObject::DATETIME_FORMAT , time());
            $this->bewerktDoor = ( $updater === null ) ? $this->aangemaaktDoor : $updater;
            $this->locked = true;
        }
    }

    /**
     * newNull 
     * 
     * @return KVDdom_ChangeableSystemFields
     */
    public static function newNull( )
    {
        return new KVDdom_ChangeableSystemFields( 'anoniem' );
    }

    /**
     * getVersie 
     * 
     * @return integer
     */
    public function getVersie( )
    {
        return $this->versie;
    }

    /**
     * getTargetVersie 
     * 
     * @return integer
     */
    public function getTargetVersie( )
    {
        return $this->targetVersie;
    }

    /**
     * getAangemaaktOp 
     * 
     * @return DateTime
     */
    public function getAangemaaktOp( )
    {
        return $this->aangemaaktOp;
    }

    /**
     * getAangemaaktDoor 
     * 
     * @return string
     */
    public function getAangemaaktDoor( )
    {
        return $this->aangemaaktDoor;
    }

    /**
     * getBewerktOp 
     * 
     * @return DateTime
     */
    public function getBewerktOp( )
    {
        return $this->bewerktOp;
    }

    /**
     * getBewerktDoor 
     * 
     * @return string
     */
    public function getBewerktDoor( )
    {
        return $this->bewerktDoor;
    }

}


?>
