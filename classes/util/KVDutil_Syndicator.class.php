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
	protected	$rssDoc = null;
	protected	$docElement = null;
	protected	$root	= null;
	protected	$items = null;
	protected	$hasChannel = true;
	protected	$tagMap = array(
		'item'	=>	'item',
		'feeddesc'	=>	'description',
		'itemdesc'	=>	'description');
	
	const ITEM	=	0;
	const FEED	=	1;
	
	protected function createSyndElement($namespace, $name, $value = null)
	{
		if(is_null($namespace)){
			return $this->rssDoc->createElement($name, $value);
		} else {
			return $this->rssDoc->createElementNS($namespace, $name, $value);
		}
	}
	
	protected function createLink($parent, $url)
	{
		$link = $this->createSyndElement($this->NS, 'link', $url);
		$parent->appendChild($link);
	}

	protected function createRSSNode($type, $parent, $title, $url, 
		$description, $pubDate = null, $id=null) 
	{
		$this->createLink($parent, $url);
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
			$idnode = $this->createSyndElement($this->NS, 'id', $id);
			$parent->appendChild($idnode);
		}
		if(!is_null($pubDate)) {
			$datenode = $this->createSyndElement($this->NS, 'updated', $pubDate);
			$parent->appendChild($datenode);
		}
	}
	
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
	
	public function addItem($title, $link, $description = null, $pubDate = null, $id = null)
	{
		$item = $this->createSyndElement($this->NS, $this->tagMap['item']);
		if($this->docElement->appendChild($item)) {
			$this->createRSSNode(KVDUtil_Syndicator::ITEM, $item, $title, $link, $description, $pubDate, $id);
			return true;
		}
		return false;
	}
	
	public function addAuthor($name)
	{
		trigger_error("Function not implemented");
		return false;
	}
	
	public function dump()
	{
		if($this->rssDoc) {
			$this->rssDoc->formatOutput = true;
			return $this->rssDoc->saveXML();
		}
		return "";
	}
}

class KVDUtil_RSS1 extends KVDUtil_Syndicator
{
	const RDFNS = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
	protected $NS = 'http://purl.org/rss/1.0/';
	
	protected $SHELL = '<rdf:RDF	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns="http://purl.org/rss/1.0/"/>';
	
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
	
	public function addItem($title, $link, $description = null, $pubDate = null, $id = null)
	{
		if(parent::addItem($title, $link, $description, $pubDate, $id)) {
			$this->addToItems($link);
			return true;
		}
		return false;
	}

	protected function createRSSNode($type, $parent, $title, $url, $description, $pubDate = null)
	{
		$parent->setAttributeNS(self::RDFNS, 'rdf:about', $url);
		parent::createRSSNode($type, $parent, $title, $url, $description, $pubDate);	
	}
}

class KVDUtil_RSS2 extends KVDUtil_Syndicator
{
	protected $NS = null;
	protected $SHELL = "<rss version=\"2.0\"/>";
	
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

class KVDUtil_Atom extends KVDUtil_Syndicator
{
	protected $NS = 'http://www.w3.org/2005/Atom';
	protected $SHELL = '<feed xmnls="http://www.w3.org/2005/Atom"/>';
	protected $hasChannel = false;
	protected $tagMap = array(
		'item' => 'entry',
		'feeddesc' => 'subtitle',
		'itemdesc' => 'content');
	
	protected function createLink($parent, $url)
	{
		$link = $this->rssDoc->createElementNS($this->NS, 'link');
		$parent->appendChild($link);
		$link->setAttribute('href', $url);
	}
	
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