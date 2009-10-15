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
	 * @var string encoding for output strings
	 */
	private $encoding = "UTF-8";
	
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
		return KVDutil_HtmlFile::ensureEncoding($this->title, $this->encoding);
	}
	
	/**
	 * getContents
	 * @return string the contents of the html page
	 */
	public function getContents()
	{
		return KVDutil_HtmlFile::ensureEncoding($this->contents, $this->encoding);
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
		} elseif(preg_match("#<TITLE>(.*)</TITLE>#Us", $this->contents, $matches)) {
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
	public static function openFile($file, $flags = false, $context = null)
	{	
		if (!$content = file_get_contents($file, $flags, $context)){
			throw new Exception("Bestand niet gevonden");
		}
		$htmlfile = KVDutil_HtmlFile::openContent($content);	
		return $htmlfile;
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
	

	/**
	 * ensureEncoding
	 * 
	 * @param string to be encoded
	 * @return string in proper encoding.
	 */
	public static function ensureEncoding($string, $encoding)
	{
		$source_encoding = mb_detect_encoding($string, 'UTF-8, ISO-8859-1');
		if($source_encoding == $encoding) {
			return $string;
		} else {
			return mb_convert_encoding($string, $encoding, $source_encoding);
		}
	}
	
	
	/**
	 * setEncoding
	 *  sets the encoding for this class.
	 * @param string encoding
	 */	
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}
	
	/**
	 * getSourceEncoding
	 *  returns the encoding of the original document.
	 * @return string encoding name
	 */
	public function getSourceEncoding()
	{
		return mb_detect_encoding($this->contents,  'UTF-8, ISO-8859-1');
	}	
	/**
	 * getTitleEncoding
	 *  returns the encoding of the original document.
	 * @return string encoding name
	 */
	public function getTitleEncoding()
	{
		return mb_detect_encoding($this->title,  'UTF-8, ISO-8859-1');
	}
	
	/**
	 * getTargetEncoding
	 *  returns the encoding from the get-functions
	 * @return string encoding name
	 */
	public function getTargetEncoding()
	{
		return $this->encoding;
	}
}
?>