<?php
/**
 * @package KVD.util
 * @subpackage 
 * @version $Id: KVDutil_GetDeclaredClassesFilter.class.php 1 2007-10-05 13:16:16Z standadi $
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_GetDeclaredClassesFilter 
 * @package KVD.util
 * @subpackage 
 * @since augustus 2008
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDutil_GetDeclaredClassesFilter implements AgaviIActionFilter {

	private $context;

	/**
	 * execute 
	 * 
	 * @param FilterChain $filterChain 
	 * @return void
	 */
	public function execute(AgaviFilterChain $filterChain, AgaviExecutionContainer $container)
	{
		$filterChain->execute($container );
		$this->context->getLoggerManager()->log("Executed", "debug");
	}
	
	public function executeOnce(AgaviFilterChain $filterChain, AgaviExecutionContainer $container)
	{
		$filterChain->execute($container );
		$interfaces = get_declared_interfaces();
		$classes = get_declared_classes();
		$text = "Interfaces ** \n - ". implode("\n - ", $interfaces) . "\n Classes ** \n - ".implode("\n - ", $classes);
		$container->getContext()->getLoggerManager()->getLogger("debug")->log(new AgaviLoggerMessage("$text", AgaviLogger::DEBUG));
	}
	/**
	 * logException 
	 * 
	 * @param string $message 
	 * @param Exception $exception 
	 * @return void
	 */
	private function logException( $message , $exception )
	{
		throw Exception("Wasn't supposed to be called!");
	}
	
	
	public function getContext()
	{
		return $this->context;
	}
	public function initialize(AgaviContext $context, array $parameters = array())
	{
		$this->context = $context;
	}
}