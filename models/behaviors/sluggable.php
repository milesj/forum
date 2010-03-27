<?php
/* SVN FILE: $Id: sluggable.php 36 2007-11-26 15:10:14Z mgiglesias $ */

/**
 * Sluggable Behavior class file.
 *
 * @filesource
 * @author Mariano Iglesias
 * @link http://cake-syrup.sourceforge.net/ingredients/sluggable-behavior/
 * @version	$Revision: 36 $
 * @license	http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package app
 * @subpackage app.models.behaviors
 */

/**
 * Model behavior to support generation of slugs for models.
 *
 * @package app
 * @subpackage app.models.behaviors
 */
class SluggableBehavior extends ModelBehavior
{
	/**
	 * Contain settings indexed by model name.
	 *
	 * @var array
	 * @access private
	 */
	var $__settings = array();

	/**
	 * Initiate behavior for the model using specified settings. Available settings:
	 *
	 * - label: 	(array | string, optional) set to the field name that contains the
	 * 				string from where to generate the slug, or a set of field names to
	 * 				concatenate for generating the slug. DEFAULTS TO: title
	 *
	 * - slug:		(string, optional) name of the field name that holds generated slugs.
	 * 				DEFAULTS TO: slug
	 *
	 * - separator:	(string, optional) separator character / string to use for replacing
	 * 				non alphabetic characters in generated slug. DEFAULTS TO: -
	 *
	 * - length:	(integer, optional) maximum length the generated slug can have.
	 * 				DEFAULTS TO: 100
	 *
	 * - overwrite: (boolean, optional) set to true if slugs should be re-generated when
	 * 				updating an existing record. DEFAULTS TO: false
	 *
	 * @param object $Model Model using the behaviour
	 * @param array $settings Settings to override for model.
	 * @access public
	 */
	function setup(&$Model, $settings = array())
	{
		$default = array('label' => array('title'), 'slug' => 'slug', 'separator' => '-', 'length' => 100, 'overwrite' => false, 'translation' => null);

		if (!isset($this->__settings[$Model->alias]))
		{
			$this->__settings[$Model->alias] = $default;
		}

		$this->__settings[$Model->alias] = am($this->__settings[$Model->alias], ife(is_array($settings), $settings, array()));
	}

	/**
	 * Run before a model is saved, used to set up slug for model.
	 *
	 * @param object $Model Model about to be saved.
	 * @return boolean true if save should proceed, false otherwise
	 * @access public
	 */
	function beforeSave(&$Model)
	{
		$return = parent::beforeSave($Model);

		// Make label fields an array

		if (!is_array($this->__settings[$Model->alias]['label']))
		{
			$this->__settings[$Model->alias]['label'] = array($this->__settings[$Model->alias]['label']);
		}

		// Make sure all label fields are available

		foreach($this->__settings[$Model->alias]['label'] as $field)
		{
			if (!$Model->hasField($field))
			{
				return $return;
			}
		}

		// See if we should be generating a slug

		if ($Model->hasField($this->__settings[$Model->alias]['slug']) && ($this->__settings[$Model->alias]['overwrite'] || empty($Model->id)))
		{
			// Build label out of data in label fields, if available, or using a default slug otherwise

			$label = '';

			foreach($this->__settings[$Model->alias]['label'] as $field)
			{
				if (!empty($Model->data[$Model->alias][$field]))
				{
					$label .= ife(!empty($label), ' ', '') . $Model->data[$Model->alias][$field];
				}
			}

			// Keep on going only if we've got something to slug

			if (!empty($label))
			{
				// Get the slug

				$slug = $this->__slug($label, $this->__settings[$Model->alias]);

				// Look for slugs that start with the same slug we've just generated

				$conditions = array($Model->alias . '.' . $this->__settings[$Model->alias]['slug'] .' LIKE' => $slug . '%');

				if (!empty($Model->id))
				{
					$conditions[$Model->alias . '.' . $Model->primaryKey] = '!= ' . $Model->id;
				}

				$result = $Model->find('all', array('conditions' => $conditions, 'fields' => array($Model->primaryKey, $this->__settings[$Model->alias]['slug']), 'recursive' => -1));
				$sameUrls = null;

				if (!empty($result))
				{
					$sameUrls = Set::extract($result, '{n}.' . $Model->alias . '.' . $this->__settings[$Model->alias]['slug']);
				}

				// If we have collissions

				if (!empty($sameUrls))
				{
					$begginingSlug = $slug;
					$index = 1;

					// Attach an ending incremental number until we find a free slug

					while($index > 0)
					{
						if (!in_array($begginingSlug . $this->__settings[$Model->alias]['separator'] . $index, $sameUrls))
						{
							$slug = $begginingSlug . $this->__settings[$Model->alias]['separator'] . $index;
							$index = -1;
						}

						$index++;
					}
				}

				// Now set the slug as part of the model data to be saved, making sure that
				// we are on the white list of fields to be saved

				if (!empty($Model->whitelist) && !in_array($this->__settings[$Model->alias]['slug'], $Model->whitelist))
				{
					$Model->whitelist[] = $this->__settings[$Model->alias]['slug'];
				}

				$Model->data[$Model->alias][$this->__settings[$Model->alias]['slug']] = $slug;
			}
		}

		return $return;
	}

	/**
	 * Generate a slug for the given string using specified settings.
	 *
	 * @param string $string String from where to generate slug
	 * @param array $settings Settings to use (looks for 'separator' and 'length')
	 * @return string Slug for given string
	 * @access private
	 */
	function __slug($string, $settings)
	{
		if (!empty($settings['translation']) && is_array($settings['translation']))
		{
			// Run user-defined translation tables

			if (count($settings['translation']) >= 2 && count($settings['translation']) % 2 == 0)
			{
				for($i=0, $limiti=count($settings['translation']); $i < $limiti; $i+=2)
				{
					$from = $settings['translation'][$i];
					$to = $settings['translation'][$i + 1];

					if (is_string($from) && is_string($to))
					{
						$string = strtr($string, $from, $to);
					}
					else
					{
						$string = r($from, $to, $string);
					}
				}
			}
			else if (count($settings['translation']) == 1)
			{
				$string = strtr($string, $settings['translation'][0]);
			}

			$string = low($string);
		}
		else if (!empty($settings['translation']) && is_string($settings['translation']) && in_array(low($settings['translation']), array('utf-8', 'iso-8859-1')))
		{
			// Run pre-defined translation tables

			$translations = array(
				'iso-8859-1' => array(
					chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
					.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
					.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
					.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
					.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
					.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
					.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
					.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
					.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
					.chr(252).chr(253).chr(255),
					'EfSZsz' . 'YcYuAAA' . 'AAACEEE' . 'EIIIINO' . 'OOOOOUU' . 'UUYaaaa' . 'aaceeee' . 'iiiinoo' . 'oooouuu' . 'uyy',
					array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254)),
					array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th')
				),
				'utf-8' => array(
					array(
						// Decompositions for Latin-1 Supplement
						chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
						chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
						chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
						chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
						chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
						chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
						chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
						chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
						chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
						chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
						chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
						chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
						chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
						chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
						chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
						chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
						chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
						chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
						chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
						chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
						chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
						chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
						chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
						chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
						chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
						chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
						chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
						chr(195).chr(191) => 'y',
						// Decompositions for Latin Extended-A
						chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
						chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
						chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
						chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
						chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
						chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
						chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
						chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
						chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
						chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
						chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
						chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
						chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
						chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
						chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
						chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
						chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
						chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
						chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
						chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
						chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
						chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
						chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
						chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
						chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
						chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
						chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
						chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
						chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
						chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
						chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
						chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
						chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
						chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
						chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
						chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
						chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
						chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
						chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
						chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
						chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
						chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
						chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
						chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
						chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
						chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
						chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
						chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
						chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
						chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
						chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
						chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
						chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
						chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
						chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
						chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
						chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
						chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
						chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
						chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
						chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
						chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
						chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
						chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
						// Euro Sign
						chr(226).chr(130).chr(172) => 'E'
					)
				)
			);

			return $this->__slug($string, am($settings, array('translation' => $translations[$settings['translation']])));
		}

		$string = low($string);
		$string = preg_replace('/[^a-z0-9_]/i', $settings['separator'], $string);
		$string = preg_replace('/' . preg_quote($settings['separator']) . '[' . preg_quote($settings['separator']) . ']*/', $settings['separator'], $string);

		if (strlen($string) > $settings['length'])
		{
			$string = substr($string, 0, $settings['length']);
		}

		$string = preg_replace('/' . preg_quote($settings['separator']) . '$/', '', $string);
		$string = preg_replace('/^' . preg_quote($settings['separator']) . '/', '', $string);

		return $string;
	}
}

?>