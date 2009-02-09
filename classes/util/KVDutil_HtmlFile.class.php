<?php
/**
 * @package KVD.util
 * @subpackage htmlfile
 * @version $Id: KVDutil_HtmlFile.class.php 1 2007-10-05 13:16:16Z standadi $
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_HtmlFile
 *  Klasse die op basis van een url of filename een HTML pagina kan ophalen. Uit dit bestand
 *  kan dan de titel opgevraagd worden.
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_HtmlFile {
	
	/**
	 * @var string contents - de contents of the html file
	 */
	private $contents;
	
	/**
	 * @var string title - title of the html page
	 */
	private $title;
	
	
	/**
	 * __construct
	 * @param string contents of the html page
	 */
	private function __construct( $contents)
	{
		$this->contents = $contents;
		$this->parseTitle();
	}
	
	/**
	 * getTitle
	 * @return string the title of the html page
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * getContents
	 * @return string the contents of the html page
	 */
	public function getContents()
	{
		return $this->contents;
	}
	
	/**
	 * parseTitle
	 * @return void
	 *
	 */
	private function parseTitle()
	{
		if(preg_match("#<title>(.*)</title>#Us", $this->contents, $matches)) {
			$this->title = trim($matches[1]);
		} else {
			$this->title = "";
		}
	}
	
	
	/** 
	 * stripComment
	 * @param string content 
	 * @return string content
	 */
	private static function stripComment($content)
	{
		$result = preg_replace("#<\!\-\-.*\-\->#Us", "", $content);
		return $result;
	}
	
	
	/**
	 * openFile
	 *  Open een bestand (lokaal of remote).
	 * @throws InvalidArgumentException - Indien de file niet bestaat of geen html bestand is.
	 * @return KVDutil_HtmlFile
	 */
	public static function openFile($file)
	{
		$content = file_get_contents($file);
		KVDutil_HtmlFile::openContent($content);		
	}

	
	/**
	 * openContent
	 *  Open een HTML string.
	 * @throws InvalidArgumentException - Indien de file niet bestaat of geen html bestand is.
	 * @return KVDutil_HtmlFile
	 */
	public static function openContent($content)
	{
		$nocomment = KVDutil_HtmlFile::stripComment($content);
		return new KVDutil_HtmlFile($nocomment);			
	}	
	
}
?>