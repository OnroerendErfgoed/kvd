<?php
/**
 * @package KVD.util
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDutil_PDOTransaction
 *
 * @package KVD.util
 * @since 31 okt 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDutil_PDOTransaction
{
	/**
	 * @var array
	 */
	private static $connectionsMap = array();

	/**
	 * @param PDO $conn
	 * @return string
	 */
	private static function getConnectionKey( $conn )
	{
		return (string ) $conn;
	}

	/**
	 * @param PDO $conn
	 * @return integer
	 */
	private static function getOpCount ($conn )
	{
		$connKey = self::getConnectionKey($conn);
		if ( !isset( self::$connectionsMap[$connKey] ) ) {
			self::$connectionsMap[$connKey] = 0;
		}
		return self::$connectionsMap[$connKey];
	}

	/**
	 * @param PDO $conn
	 */
	private static function incrementOpCount ( $conn )
	{
		self::$connectionsMap[self::getConnectionKey($conn)]++;
	}

	/**
	 * @param PDO $conn
	 */
	private static function decrementOpCount ( $conn )
	{
		self::$connectionsMap[self::getConnectionKey($conn)]--;
	}

	/**
	 * @param PDO $conn
	 * @return boolean
	 */
	public static function isInTransaction ( $conn )
	{
		return (self::getOpCount( $conn ) > 0);
	}

	/**
	 * @param PDO $conn
	 * @throws KVDutil_TransactionException
	 */
	public static function beginTransaction ( $conn )
	{
		if ( self::getOpCount( $conn ) === 0) {
		    $conn->beginTransaction();
		}
		self::incrementOpCount( $conn );
	}

	/**
	 * @param PDO $conn
	 * @throws KVDutil_TransactionException
	 */
	public static function commit ( $conn )
	{
		if ( self::getOpCount( $conn ) > 0 ) {
			if (self::getOpCount( $conn ) === 1 ) {
				$conn->commit();
			}
			self::decrementOpCount( $conn );
		}
	}

	/**
	 * @param PDO $conn
	 * @throws KVDutil_TransactionException
	 */
	public static function rollBack ( $conn )
	{
		if ( self::getOpCount( $conn ) > 0 ) {
			if (self::getOpCount( $conn ) === 1 ) {
				try {
					$conn->rollBack();
				} catch ( PDOException $e ) {
					throw new KVDutil_TransactionException ( 'Kon de transaction niet terug draaien.' , $e );
				}
			}
			self::decrementOpCount ( $conn );
		}
	}

}

/**
 * KVDutil_TransactionException
 *
 * @package KVD.util
 * @since 31 okt 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_TransactionException extends Exception
{
	/**
	 * @param PDOException
	 */
	private $exception;

	/*
	 * @param string $msg
	 * @param PDOException $exception
	 */
	public function __construct( $msg , $exception = null )
	{
		parent::__construct( $msg );
		$this->exception = $exception;
	}

	/**
	 * @return PDOException
	 */
	public function getException ()
	{
		return $this->exception;
	}
}
?>

