<?php


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
		preg_match("#<title>(.*)</title>#Us", $this->contents, $matches);
		print_r($matches);
		$this->title = trim($matches[1]);
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
	 * Open een bestand (lokaal of remote).
	 * @throws InvalidArgumentException - Indien de file niet bestaat of geen html bestand is.
	 * @return KVDutil_HtmlFile
	 */
	public static function openFile($file)
	{
		$content = file_get_contents($file);
		echo $content;
		$nocomment = KVDutil_HtmlFile::stripComment($content);
		echo $nocomment;
		return new KVDutil_HtmlFile($nocomment);			
	}
}
?>