<?php
/**
 * DecodaPhpEngine
 *
 * Renders tags by using PHP as template engine.
 *
 * @author      Miles Johnson - http://milesj.me
 * @author      Sean C. Koop - sean.koop@icans-gmbh.com
 * @copyright   Copyright 2006-2012, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */

class DecodaPhpEngine implements DecodaTemplateEngineInterface {

	/**
	 * Current path.
	 *
	 * @var string
	 */
	protected $_path;

	/**
	 * Current filter.
	 *
	 * @var DecodaFilter
	 */
	protected $_filter;

	/**
	 * Return the current filter.
	 *
	 * @return DecodaFilter
	 */
	public function getFilter() {
		return $this->_filter;
	}

	/**
	 * Return the template path. If no path has been set, set it.
	 *
	 * @return string
	 */
	public function getPath() {
		if (empty($this->_path)) {
			$this->setPath(DECODA . '/templates/');
		}

		return $this->_path;
	}

	/**
	 * Renders the tag by using php templates.
	 *
	 * @param array $tag
	 * @param string $content
	 * @return string
	 * @throws Exception
	 */
	public function render(array $tag, $content) {
		$setup = $this->getFilter()->tag($tag['tag']);
		$path = $this->getPath() . $setup['template'] . '.php';

		if (!file_exists($path)) {
			throw new Exception(sprintf('Template file %s does not exist.', $setup['template']));
		}

		$vars = array();

		foreach ($tag['attributes'] as $key => $value) {
			if (isset($setup['map'][$key])) {
				$key = $setup['map'][$key];
			}

			$vars[$key] = $value;
		}

		extract($vars, EXTR_SKIP);
		ob_start();

		include $path;

		return ob_get_clean();
	}

	/**
	 * Sets the current filter.
	 *
	 * @param DecodaFilter $filter
	 * @return DecodaTemplateEngineInterface
	 */
	public function setFilter(DecodaFilter $filter) {
		$this->_filter = $filter;

		return $this;
	}

	/**
	 * Sets the path to the tag templates.
	 *
	 * @param string $path
	 * @return DecodaTemplateEngineInterface
	 */
	public function setPath($path) {
		if (substr($path, -1) !== '/') {
			$path .= '/';
		}

		$this->_path = $path;

		return $this;
	}

}
