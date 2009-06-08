<?php
/**
 * @package KVD.util
 * @subpackage syndication
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @version $Id$
 */
 
/**
 * KVDUtil_Syndicator
 *
 * @package KVD.util
 * @subpackage syndication
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 26 08 2008
 */
class KVDUtil_Syndicator 
{
	/**
	 * @var DOMDocument het document met RSS/Atom data
	 */
	protected	$rssDoc = null;
	/**
	 * @var DOMElement het element waarin de data van deze feed moet komen.
	 */
	protected	$docElement = null;
	/**
	 * @var DOMElement de root waarin de data van de feed moet komen.
	 */
	protected	$root	= null;
	/**
	 * @var array data van de feed
	 */
	protected	$items = null;
	/**
	 * @var boolean hasChannel bepaald of de data van de feed in een "channel" element moet
	 */
	protected	$hasChannel = true;
	/**
	 * @var array bevat de tags voor bepaalde elementen
	 */
	protected	$tagMap = array(
		'item'	=>	'item',
		'feeddesc'	=>	'description',
		'itemdesc'	=>	'description');
	
	/**
	 * @const integer tag voor items
	 */
	const ITEM	=	0;
	/**
	 * @const integer tag voor feeds
	 */
	const FEED	=	1;

	
	public function __construct($title, $url, $description, $pubDate = null, $id = null)
	{
		try{
			$this->rssDoc = new DOMDocument();
			$this->rssDoc->loadXML($this->SHELL);
			$this->docElement = $this->rssDoc->documentElement;
			if($this->hasChannel) {
				$root = $this->createSyndElement($this->NS, 'channel');
				$this->root = $this->docElement->appendChild($root);
			} else {
				$this->root = $this->docElement;
			}
			$this->createRSSNode(KVDUtil_Syndicator::FEED, $this->root, $title,
					$url, $description, $pubDate, $id);
			return;
		} catch (DOMException $e) {
			throw new Exception($e->getMessage());
		}
		throw new Exception("Unable to Create Object");
	}
	
	/**
	 * createSyndElement
	 *
	 * @param string namespace
	 * @param string name
	 * @param string value (optional)
	 * @return DOMElement
	 */
	protected function createSyndElement($namespace, $name, $value = null)
	{
		if(is_null($namespace)){
			return $this->rssDoc->createElement($name, $value);
		} else {
			return $this->rssDoc->createElementNS($namespace, $name, $value);
		}
	}
	
	/**
	 * createLink
	 *
	 * @param string
	 * @param string
	 * @return void
	 */
	protected function createLink($parent, $url, $attributes = array())
	{
		$link = $this->createSyndElement($this->NS, 'link', $url);
		foreach($attributes as $name=>$value) {
			$link->setAttribute($name, $value);
		}	
		$parent->appendChild($link);
		return $link;
	}

	/**
	 * createRSSNode
	 *  maakt een rss element aan in de 'parent'.
	 * @param integer
	 * @param DOMElement
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param integer
	 * @return void
	 */
	protected function createRSSNode($type, $parent, $title, $url, 
		$description, $pubDate = null, $id=null) 
	{
		$this->createLink($parent, $url);
		
		$atomlink = $this->createSyndElement(null, "atom:link", '');
		$atomlink->setAttribute("rel", "self");
		$atomlink->setAttribute("type", "application/rss+xml");
		$atomlink->setAttribute("href", $url);
		$parent->appendChild($atomlink);
		$title = $this->createSyndElement($this->NS, 'title', $title);
		$parent->appendChild($title);
		if ($type == KVDUtil_Syndicator::ITEM) {
			$titletag = $this->tagMap['itemdesc'];
		} else {
			$titletag = $this->tagMap['feeddesc'];
		}
		$description = $this->createSyndElement($this->NS, $titletag, $description);
		$parent->appendChild($description);
	
		if(!is_null($id)) {
			$idnode = $this->createSyndElement($this->NS, 'guid', $id);
			$parent->appendChild($idnode);
		}
		if(!is_null($pubDate)) {
			$datenode = $this->createSyndElement($this->NS, 'pubDate', $pubDate);
			$parent->appendChild($datenode);
		}
	}
	
	/**
	 * addItem
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param integer
	 */
	public function addItem($title, $link, $description = null, $pubDate = null, $id = null)
	{
		$item = $this->createSyndElement($this->NS, $this->tagMap['item']);
		if($this->docElement->appendChild($item)) {
			$this->createRSSNode(KVDUtil_Syndicator::ITEM, $item, $title, $link, $description, $pubDate, $id);
			return true;
		}
		return false;
	}
	
	/**
	 * addAuthor
	 * @param string
	 * @return boolean
	 */
	public function addAuthor($name)
	{
		trigger_error("Function not implemented");
		return false;
	}
	
	/**
	 * dump
	 * @return string the XML data of this document
	 */
	public function dump()
	{
		if($this->rssDoc) {
			$this->rssDoc->formatOutput = true;
			return $this->rssDoc->saveXML();
		}
		return "";
	}
}
 
/**
 * KVDUtil_RSS1
 *
 * @package KVD.util
 * @subpackage syndication
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 26 08 2008
 */
class KVDUtil_RSS1 extends KVDUtil_Syndicator
{
	/**
	 * @const string
	 */
	const RDFNS = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
	/**
	 * @var string
	 */
	protected $NS = 'http://purl.org/rss/1.0/';
	/**
	 * @var string
	 */
	protected $SHELL = '<rdf:RDF	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns="http://purl.org/rss/1.0/"/>';
	
	/**
	 * addToItems
	 *  voeg een url resource aan de lijst van items
	 * @param string
	 * @return void
	 */
	private function addToItems($url)
	{
		if(is_null($this->items)){
			$container = $this->createSyndElement($this->NS, 'items');
			$this->root->appendChild($container);
			$this->items = $this->rssDoc->createElementNS(self::RDFNS, 'Seq');
			$container->appendChild($this->items);
		}
		$item = $this->rssDoc->createElementNS(self::RDFNS, 'li');
		$this->items->appendChild($item);
		$item->setAttribute("resource", $url);
	}
	
	/**
	 * addItem
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param integer
	 * @return boolean
	 */
	public function addItem($title, $link, $description = null, $pubDate = null, $id = null)
	{
		if(parent::addItem($title, $link, $description, $pubDate, $id)) {
			$this->addToItems($link);
			return true;
		}
		return false;
	}
	/**
	 * createRSSNode
	 * @param integer
	 * @param DOMElement
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return void
	 */
	protected function createRSSNode($type, $parent, $title, $url, $description, $pubDate = null)
	{
		$parent->setAttributeNS(self::RDFNS, 'rdf:about', $url);
		parent::createRSSNode($type, $parent, $title, $url, $description, $pubDate);	
	}
}

 
/**
 * KVDUtil_RSS2 
 *
 * @package KVD.util
 * @subpackage syndication
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 26 08 2008
 */
class KVDUtil_RSS2 extends KVDUtil_Syndicator
{
	/**
	 * @var string
	 */
	protected $NS = null;
	/**
	 * @var string
	 */
	protected $SHELL = "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\"/>";
	
	/**
	 * __construct
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param integer
	 */
	public function __construct($title, $url, $description, $pubDate = null, $id = null)
	{
		try {
			parent::__construct($title, $url, $description, $pubDate, $id);
			$this->docElement = $this->root;
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
}

 
/**
 * KVDUtil_Atom
 *
 * @package KVD.util
 * @subpackage syndication
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 26 08 2008
 */
class KVDUtil_Atom extends KVDUtil_Syndicator
{	
	/**
	 * @var string
	 */
	protected $NS = 'http://www.w3.org/2005/Atom';
	/**
	 * @var string
	 */
	protected $SHELL = '<feed xmnls="http://www.w3.org/2005/Atom"/>';
	/**
	 * @var boolean
	 */
	protected $hasChannel = false;
	/**
	 * @var array
	 */
	protected $tagMap = array(
		'item' => 'entry',
		'feeddesc' => 'subtitle',
		'itemdesc' => 'content');

	/**
	 * __construct
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param integer
	 */
	public function __construct($title, $url, $description, $pubDate = null, $id = null)
	{
		try{
			if(empty($id)) {
				$id = $url;
			}
			if(empty($pubDate)) {
				$pubDate = date('c');
			}
			parent::__construct($title, $url, $description, $pubDate, $id);
		} catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}
	/**
	 * createLink
	 * @param string
	 * @param string
	 */
	protected function createLink($parent, $url)
	{
		$link = $this->rssDoc->createElementNS($this->NS, 'link');
		$parent->appendChild($link);
		$link->setAttribute('href', $url);
	}
	/**
	 * addAuthor
	 * @param string
	 * @return boolean
	 */
	public function addAuthor($name)
	{
		$author = $this->rssDoc->createElementNS($this->NS, 'author');
		if($this->docElement->appendChile($author)){
			$namenode = $this->rssDoc->createElementNS($this->NS, 'name', $name);
			if($author->appendChild($namenode)) {
				return true;
			}
		}
		return false;
	}	
	/**
	 * addItem
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param integer
	 * @return boolean
	 */
	public function addItem($title, $link, $description = null, $pubDate = null, $id = null)
	{
		if(empty($id)) {
			$id = $link;
		}
		if(empty($pubDate)){
			$pubDate = date('c');
		}
		return parent::addItem($title, $link, $description, $pubDate, $id);
	}	
}

?>