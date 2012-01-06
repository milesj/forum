<?php
/**
 * Type Converter
 *
 * A class that handles the detection and conversion of certain resource formats / content types into other formats.
 * The current formats are supported: XML, JSON, Array, Object, Serialized
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/type-converter
 */

class TypeConverter {

	/**
	 * Current version.
	 *
	 * @access public
	 * @var string
	 */
	public static $version = '1.2';

	/**
	 * Disregard XML attributes and only return the value.
	 */
	const XML_NONE = 0;

	/**
	 * Merge attributes and the value into a single dimension; the values key will be "value".
	 */
	const XML_MERGE = 1;

	/**
	 * Group the attributes into a key "attributes" and the value into a key of "value".
	 */
	const XML_GROUP = 2;

	/**
	 * Attributes will only be returned.
	 */
	const XML_OVERWRITE = 3;

	/**
	 * Returns a string for the detected type.
	 *
	 * @access public
	 * @param mixed $data
	 * @return string
	 * @static
	 */
	public static function is($data) {
		if (self::isArray($data)) {
			return 'array';

		} else if (self::isObject($data)) {
			return 'object';

		} else if (self::isJson($data)) {
			return 'json';

		} else if (self::isSerialized($data)) {
			return 'serialized';

		} else if (self::isXml($data)) {
			return 'xml';
		}

		return 'other';
	}

	/**
	 * Check to see if data passed is an array.
	 *
	 * @access public
	 * @param mixed $data
	 * @return boolean
	 * @static
	 */
	public static function isArray($data) {
		return is_array($data);
	}

	/**
	 * Check to see if data passed is a JSON object.
	 *
	 * @access public
	 * @param mixed $data
	 * @return boolean
	 * @static
	 */
	public static function isJson($data) {
		return (@json_decode($data) !== null);
	}

	/**
	 * Check to see if data passed is an object.
	 *
	 * @access public
	 * @param mixed $data
	 * @return boolean
	 * @static
	 */
	public static function isObject($data) {
		return is_object($data);
	}

	/**
	 * Check to see if data passed has been serialized.
	 *
	 * @access public
	 * @param mixed $data
	 * @return boolean
	 * @static
	 */
	public static function isSerialized($data) {
		$ser = @unserialize($data);
		return ($ser !== false) ? $ser : false;
	}

	/**
	 * Check to see if data passed is an XML document.
	 *
	 * @access public
	 * @param mixed $data
	 * @return boolean
	 * @static
	 */
	public static function isXml($data) {
		$xml = @simplexml_load_string($data);
		return ($xml instanceof SimpleXmlElement) ? $xml : false;
	}

	/**
	 * Transforms a resource into an array.
	 *
	 * @access public
	 * @param mixed $resource
	 * @return array
	 * @static
	 */
	public static function toArray($resource) {
		if (self::isArray($resource)) {
			return $resource;

		} else if (self::isObject($resource)) {
			return self::buildArray($resource);

		} else if (self::isJson($resource)) {
			return json_decode($resource, true);

		} else if ($ser = self::isSerialized($resource)) {
			return self::toArray($ser);

		} else if ($xml = self::isXml($resource)) {
			return self::xmlToArray($xml);
		}

		return $resource;
	}

	/**
	 * Transforms a resource into a JSON object.
	 *
	 * @access public
	 * @param mixed $resource
	 * @return json
	 * @static
	 */
	public static function toJson($resource) {
		if (self::isJson($resource)) {
			return $resource;

		} else {
			if ($xml = self::isXml($resource)) {
				$resource = self::xmlToArray($xml);

			} else if ($ser = self::isSerialized($resource)) {
				$resource = $ser;
			}

			return json_encode($resource);
		}

		return $resource;
	}

	/**
	 * Transforms a resource into an object.
	 *
	 * @access public
	 * @param mixed $resource
	 * @return object
	 * @static
	 */
	public static function toObject($resource) {
		if (self::isObject($resource)) {
			return $resource;

		} else if (self::isArray($resource)) {
			return self::buildObject($resource);

		} else if (self::isJson($resource)) {
			return json_decode($resource);

		} else if ($ser = self::isSerialized($resource)) {
			return self::toObject($ser);

		} else if ($xml = self::isXml($resource)) {
			return $xml;
		}

		return $resource;
	}

	/**
	 * Transforms a resource into a serialized form.
	 *
	 * @access public
	 * @param mixed $resource
	 * @return string
	 * @static
	 */
	public static function toSerialize($resource) {
		if (!self::isArray($resource)) {
			$resource = self::toArray($resource);
		}

		return serialize($resource);
	}

	/**
	 * Transforms a resource into an XML document.
	 *
	 * @access public
	 * @param mixed $resource
	 * @param string $root
	 * @return string
	 * @static
	 */
	public static function toXml($resource, $root = 'root') {
		if (self::isXml($resource)) {
			return $resource;
		}

		$array = self::toArray($resource);

		if (!empty($array)) {
			$xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><'. $root .'></'. $root .'>');
			$response = self::buildXml($xml, $array);

			return $response->asXML();
		}

		return $resource;
	}

	/**
	 * Turn an object into an array. Alternative to array_map magic.
	 *
	 * @access public
	 * @param object $object
	 * @return array
	 */
	public static function buildArray($object) {
		$array = array();

		foreach ($object as $key => $value) {
			if (is_object($value)) {
				$array[$key] = self::buildArray($value);
			} else {
				$array[$key] = $value;
			}
		}

		return $array;
	}

	/**
	 * Turn an array into an object. Alternative to array_map magic.
	 *
	 * @access public
	 * @param array $array
	 * @return object
	 */
	public static function buildObject($array) {
		$obj = new stdClass();

		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$obj->{$key} = self::buildObject($value);
			} else {
				$obj->{$key} = $value;
			}
		}

		return $obj;
	}

	/**
	 * Turn an array into an XML document. Alternative to array_map magic.
	 *
	 * @access public
	 * @param object $xml
	 * @param array $array
	 * @return object
	 */
	public static function buildXml(&$xml, $array) {
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				// XML_NONE
				if (!is_array($value)) {
					$xml->addChild($key, $value);
					continue;
				}

				// Multiple nodes of the same name
				if (isset($value[0])) {
					foreach ($value as $kValue) {
						if (is_array($kValue)) {
							self::buildXml($xml, array($key => $kValue));
						} else {
							$xml->addChild($key, $kValue);
						}
					}

				// XML_GROUP
				} else if (isset($value['attributes'])) {
					if (is_array($value['value'])) {
						$node = $xml->addChild($key);
						self::buildXml($node, $value['value']);
					} else {
						$node = $xml->addChild($key, $value['value']);
					}

					if (!empty($value['attributes'])) {
						foreach ($value['attributes'] as $aKey => $aValue) {
							$node->addAttribute($aKey, $aValue);
						}
					}

				// XML_MERGE
				} else if (isset($value['value'])) {
					$node = $xml->addChild($key, $value['value']);
					unset($value['value']);

					if (!empty($value)) {
						foreach ($value as $aKey => $aValue) {
							if (is_array($aValue)) {
								self::buildXml($node, array($aKey => $aValue));
							} else {
								$node->addAttribute($aKey, $aValue);
							}
						}
					}

				// XML_OVERWRITE
				} else {
					$node = $xml->addChild($key);

					if (!empty($value)) {
						foreach ($value as $aKey => $aValue) {
							if (is_array($aValue)) {
								self::buildXml($node, array($aKey => $aValue));
							} else {
								$node->addChild($aKey, $aValue);
							}
						}
					}
				}
			}
		}

		return $xml;
	}

	/**
	 * Convert a SimpleXML object into an array.
	 *
	 * @access public
	 * @param object $xml
	 * @param int $format
	 * @return array
	 */
	public static function xmlToArray($xml, $format = self::XML_GROUP) {
		if (is_string($xml)) {
			$xml = @simplexml_load_string($xml);
		}

		if ($xml->count() <= 0) {
			return (string)$xml;
		}

		$array = array();

		foreach ($xml->children() as $element => $node) {
			$data = array();

			if (!isset($array[$element])) {
				$array[$element] = "";
			}

			if (!$node->attributes() || $format == self::XML_NONE) {
				$data = self::xmlToArray($node, $format);

			} else {
				switch ($format) {
					case self::XML_GROUP:
						$data = array(
							'attributes' => array(),
							'value' => (string)$node
						);

						if ($node->count() > 0) {
							$data['value'] = self::xmlToArray($node, $format);
						}

						foreach ($node->attributes() as $attr => $value) {
							$data['attributes'][$attr] = (string)$value;
						}
					break;

					case self::XML_MERGE:
					case self::XML_OVERWRITE:
						if ($format == self::XML_MERGE) {
							if ($node->count() > 0) {
								$data = $data + self::xmlToArray($node, $format);
							} else {
								$data['value'] = (string)$node;
							}
						}

						foreach ($node->attributes() as $attr => $value) {
							$data[$attr] = (string)$value;
						}
					break;
				}
			}

			if (count($xml->{$element}) > 1) {
				$array[$element][] = $data;
			} else {
				$array[$element] = $data;
			}
		}

		return $array;
	}

}
