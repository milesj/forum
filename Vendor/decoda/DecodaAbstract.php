<?php
/**
 * DecodaAbstract
 *
 * Base class for filters and hooks to extend.
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */

abstract class DecodaAbstract {

	/**
	 * Configuration.
	 *
	 * @var array
	 */
	protected $_config = array();

	/**
	 * Parent Decoda object.
	 *
	 * @var Decoda
	 */
	protected $_parser;

	/**
	 * Apply configuration.
	 *
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		$this->_config = $config + $this->_config;
	}

	/**
	 * Return a specific configuration key value.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function config($key) {
		return isset($this->_config[$key]) ? $this->_config[$key] : null;
	}

	/**
	 * Return the Decoda parser.
	 *
	 * @return Decoda
	 */
	public function getParser() {
		return $this->_parser;
	}

	/**
	 * Set the Decoda parser.
	 *
	 * @param Decoda $parser
	 * @return void
	 */
	public function setParser(Decoda $parser) {
		$this->_parser = $parser;
	}

}