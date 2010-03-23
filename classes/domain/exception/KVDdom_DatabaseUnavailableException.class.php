<?php
/**
 * @package     KVD.dom
 * @subpackage  exception
 * @version     $Id$
 * @copyright   2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_DatabaseUnavailableException 
 * 
 * @package     KVD.dom
 * @subpackage  exception
 * @since       23 okt 2006
 * @copyright   2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_DatabaseUnavailableException extends Exception 
{
    /**
     * databaseException 
     * 
     * @var DatabaseException
     */
    private $databaseException;

    /**
     * databaseName 
     * 
     * @var string
     */
    private $databaseName;
    
    /**
     * @param string $message 
     * @param string $databaseName 
     * @param DatabaseException $databaseException Class binnen het Agavi Framework.
     */
    public function __construct ( $message , $databaseName , $databaseException = null )
    {
        parent::__construct ( $message );
        $this->databaseName = $databaseName;
        $this->databaseException = null;
    }

    /**
     * getDatabaseName 
     * 
     * @return string
     */
    public function getDatabaseName( )
    {
        return $this->databaseName;
    }

    /**
     * getDatabaseException 
     * 
     * @return DatabaseException
     */
    public function getException( )
    {
        return $this->databaseException;
    }
}
?>
