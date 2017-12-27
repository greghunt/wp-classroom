<?php

/**
 * A class to create simple custom routes.
 *
 * Example usage:
 *
 *     $theme_routes = new CustomRoutes();
 *     $theme_routes->addRoute(
 *
 *         // required, regex to match the route
 *         '^api/([^/]*)/([^/]*)/?',
 *
 *         // required, a callback function name or
 *         // callable array like `array($object, 'method')`
 *         'api_callback',
 *
 *         // optional template path or array of template path candidates
 *         get_template_directory() . '/api-template.php',
 *
 *         // query vars based on regex matches
 *         // will be passed to the callback function in the same order
 *         array($param1 => 1, $param2 => 2),
 *     );
 *
 *     function api_callback($param1, $param2) {
 *         // called before the optional template is invoked
 *     }
 *
 * Also:
 *
 *     // Force flush rewrite rules.
 *     $theme_routes->forceFlush();
 *
 * @author Sam Hernandez (hernandez@happycog.com)
 */
class CustomRoutes {

	/**
	 * An array of route data indexed by regex.
	 *
	 * Example value:
	 *
	 * 	   $routes['^widgets/([^/]*)/([^/]*)/?'] = array(
	 *         'callback' => 'some_function', // or array($object, 'someMethod')
	 *         'template' => '/template/path/to/file.php',
	 *         'query_vars' => array('param1' => 1, 'param2' => 2)
	 *     );
	 *
	 * @var array
	 */
	protected $routes = array();

	/**
	 * Flag to force flushing existing rewrite rules.
	 * @var boolean
	 */
	protected $force_flush = false;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		add_action('parse_request',       array($this, 'parseRequestAction'));
		add_filter('query_vars',          array($this, 'queryVarsFilter'));
		add_filter('rewrite_rules_array', array($this, 'rewriteRulesArrayFilter'));
		add_action('wp_loaded',           array($this, 'wpLoadedAction'));
	}

	/*
	 *
	 * Public methods
	 *
	 */

	/**
	 * Force flush existing rewrite rules.
	 */
	public function forceFlush() {
		$this->force_flush = true;
	}

	/**
	 * Action callback for 'parse_request'. Handles the execution
	 * of custom routes by invoking the optional callback and optional
	 * template file if they are given for the matched route.
	 *
	 * @param  WP $query
	 */
	public function parseRequestAction($query)
	{
		if ($query->matched_rule and isset($this->routes[$query->matched_rule]))
		{
			$route = $this->routes[$query->matched_rule];

			$this->doCallback($route, $query);

			if ($route['template']) {
				$html = $this->doTemplate($route);
			}

			exit;
		}
	}

	/**
	 * Add a route with params.
	 *
	 * @param string $match      A regular expression for the url match
	 * @param string $callback   Callback; function name or callable array such as `array($object, 'method')`
	 * @param string $template   Optional template path
	 * @param array  $query_vars An array of url query vars indexed by the var name, value being the regex match number.
	 */
	public function addRoute($match, $callback, $template = null, $query_vars = array())
	{
		$this->routes[$match] = compact('callback', 'template', 'query_vars');
	}

	/*
	 *
	 * Action and filter callbacks
	 *
	 */

	/**
	 * Action callback for 'wp_loaded'.
	 * Flushes the current rewrite rules if a newly defined rule
	 * is missing or if the `$force_flush` flag is raised.
	 */
	public function wpLoadedAction()
	{
		$rules = get_option('rewrite_rules');
		$missing_routes = false;

		foreach ($this->routes as $key => $value) {
			$missing_routes += !isset($rules[$key]);
		}

		if ($missing_routes || $this->force_flush) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}
	}

	/**
	 * Filter callback for 'rewrite_rules_array'.
	 * Adds new rules for newly defined routes.
	 *
	 * @param  array $rules
	 * @return array
	 */
	public function rewriteRulesArrayFilter($rules)
	{
		$newrules = array();
		foreach ($this->routes as $match => $route) {
			$newrules[$match] = $this->makeRuleUrl($route);
		}

		return $newrules + $rules;
	}

	/**
	 * Filter callback for 'query_vars'.
	 * @param  array $vars
	 * @return array
	 */
	public function queryVarsFilter($vars)
	{
		foreach($this->routes as $route)
		{
			foreach($route['query_vars'] as $key => $value) {
				$vars[] = $key;
			}
		}

		return $vars;
	}

	/*
	 *
	 * Protected methods
	 *
	 */

	/**
	 * Invoke the callback for a given route.
	 *
	 * @param  array $route An item from $this->routes
	 * @param  WP    $query
	 */
	protected function doCallback($route, $query)
	{
		$params = array();

		// params are in the same order as given in the array
		foreach($route['query_vars'] as $name => $match) {
			$params[] = $query->query_vars[$name];
		}

		call_user_func_array($route['callback'], $params);
	}

	/**
	 * Includes a template for a given route if one is found.
	 * @param  array $route   An item from $this->routes
	 */
	protected function doTemplate($route)
	{
		$candidates = (array) $route['template'];

		foreach($candidates as $candidate)
		{
			if (file_exists($candidate))
			{
				include $candidate;
				break;
			}
		}
	}

	/**
	 * Returns a url with query string key/value pairs as
	 * needed for rewrite rules.
	 *
	 * @param  array $route  An item from $this->routes
	 * @return string
	 */
	protected function makeRuleUrl($route)
	{
		$q_vars = array();

		foreach($route['query_vars'] as $name => $match) {
			$q_vars[] = $name . '=$matches[' . $match . ']';
		}

		return 'index.php?' . implode('&', $q_vars);
	}
}
