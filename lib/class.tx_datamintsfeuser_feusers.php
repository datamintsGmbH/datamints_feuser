<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Bernhard Baumgartl <b.baumgartl@datamints.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *   54: class tx_datamintsfeuser_feusers
 *   62:     public function __construct()
 *   74:     public function mergeFeUsersTca($feUsersTca)
 *   84:     public function getFieldConfig($fieldName)
 *  101:     public function cleanFieldValue($fieldName, $value)
 *  153:     public function cleanInputField($fieldConfig, $value)
 *  186:     public function cleanCheckField($fieldConfig, $value)
 *  218:     public function cleanSelectField($fieldConfig, $value)
 *  277:     public function cleanGroupField($fieldConfig, $value)
 *  323:     public function cleanUncleanedField($fieldConfig, $value)
 *
 *
 * TOTAL FUNCTIONS: 9
 *
 */

/**
 * Library 'FeUsers' for the 'datamints_feuser' extension.
 *
 * @author	Bernhard Baumgartl <b.baumgartl@datamints.com>
 * @package	TYPO3
 * @subpackage	tx_datamintsfeuser
 */
class tx_datamintsfeuser_feusers {
	private $feUsersTca = array();

	/**
	 * Initialization.
	 *
	 * @return void
	 */
	public function __construct() {
		$GLOBALS['TSFE']->includeTCA();

		$this->feUsersTca = (array)$GLOBALS['TCA']['fe_users'];
	}

	/**
	 * Fuehrt die vorhandene TCA Konfiguration mit einer neuen Konfiguration zusammen.
	 *
	 * @param array $feUsersTca
	 * @return void
	 */
	public function mergeFeUsersTca($feUsersTca) {
		$this->feUsersTca = t3lib_div::array_merge_recursive_overrule($this->feUsersTca, (array)$feUsersTca);
	}

	/**
	 * Ermittelt die Konfiguration fuer ein Feld.
	 *
	 * @param string $fieldName
	 * @return array $fieldConfig
	 */
	public function getFieldConfig($fieldName) {
		$fieldConfig = array();

		if (is_array($this->feUsersTca['columns'][$fieldName])) {
			$fieldConfig = (array)$this->feUsersTca['columns'][$fieldName]['config'];
		}

		return $fieldConfig;
	}

	/**
	 * Saeubert ein Feld.
	 *
	 * @param string $fieldName
	 * @param mixed $value
	 * @return mixed $value
	 */
	public function cleanFieldValue($fieldName, $value) {
		$fieldConfig = $this->getFieldConfig($fieldName);

		switch ($fieldConfig['type']) {
			case 'input':
				$value = $this->cleanInputField($fieldConfig, $value);

				break;

			case 'check':
				$value = $this->cleanCheckField($fieldConfig, $value);

				break;

			case 'select':
				$value = $this->cleanSelectField($fieldConfig, $value);

				break;

			case 'group':
				$value = $this->cleanGroupField($fieldConfig, $value);

				break;

			default:
				$value = $this->cleanUncleanedField($fieldConfig, $value);

				break;
		}

		return $value;

		// ToDo: Dateifeld behandeln
		if ($fieldConfig['type'] == 'group' && $fieldConfig['internal_type'] == 'file') {
			$arrUpdate[$fieldName] = $GLOBALS['TSFE']->fe_user->user[$fieldName];

			// Das Bild hochladen oder loeschen. Gibt einen Fehlerstring per Referenz zurueck falls ein Fehler auftritt!
			$valueCheck[$fieldName] = $this->saveDeleteFiles($arrUpdate, $fieldName, $fieldConfig);

			if ($valueCheck[$fieldName]) {
				return $this->showForm($valueCheck);
			}
		}
	}

	/**
	 * Saeubert ein Input.
	 *
	 * @param array $fieldConfig
	 * @param mixed $value
	 * @return mixed
	 */
	public function cleanInputField($fieldConfig, $value) {
		$arrFieldConfigEval = t3lib_div::trimExplode(',', $fieldConfig['eval'], TRUE);

		// Datumsfeld behandeln.
		if (in_array('date', $arrFieldConfigEval)) {
			// ToDo: Conf auslagern
			return date_timestamp_get(date_create_from_format($this->conf['format.']['date'], $value));
		}

		// Datumzeitfeld behandeln.
		if (in_array('datetime', $arrFieldConfigEval)) {
			// ToDo: Conf auslagern
			return date_timestamp_get(date_create_from_format($this->conf['format.']['datetime'], $value));
		}

		// Passwortfeld behandeln.
		if (in_array('password', $arrFieldConfigEval)) {
			return $value;
		}

		// Read only behandeln.
		if ($fieldConfig['readOnly']) {
			return FALSE;
		}

		return $this->cleanUncleanedField($fieldConfig, $value);
	}

	/**
	 * Saeubert ein Check.
	 *
	 * @param array $fieldConfig
	 * @param mixed $value
	 * @return mixed
	 */
	public function cleanCheckField($fieldConfig, $value) {
		$itemsCount = count($fieldConfig['items']);

		// Mehrere Checkboxen oder eine Checkbox.
		if ($itemsCount > 1) {
			$binString = '';

			for ($key = 0; $key < $itemsCount; $key++) {
				if ($value[$key]) {
					$binString .= '1';
				} else {
					$binString .= '0';
				}
			}

			return bindec(strrev($binString));
		}

		if ($value) {
			return '1';
		}

		return '0';
	}

	/**
	 * Saeubert ein Select.
	 *
	 * @param array $fieldConfig
	 * @param mixed $value
	 * @return mixed
	 */
	public function cleanSelectField($fieldConfig, $value) {
		$arrItemValues = array();

		if (is_array($fieldConfig['items'])) {
			foreach ($fieldConfig['items'] as $item) {
				$arrItemValues[] = $item[1];
			}
		}

		if ($fieldConfig['size'] > 1) {
			$arrCleanedValues = array();

			if (!is_array($value)) {
				return FALSE;
			}

			foreach ($arrItemValues as $itemValue) {
				if (in_array($itemValue, $value)) {
					$arrCleanedValues[] = $itemValue;
				}
			}

			if ($fieldConfig['foreign_table']) {
				foreach ($value as $val) {
					if (is_numeric($val)) {
						$arrCleanedValues[] = intval($val);
					}
				}
			}

			$arrCleanedValues = array_unique($arrCleanedValues);

			if ($fieldConfig['maxitems']) {
				$arrCleanedValues = array_slice($arrCleanedValues, 0, $fieldConfig['maxitems']);
			}

			return implode(',', $arrCleanedValues);
		}

		foreach ($arrItemValues as $itemValue) {
			if ($itemValue == $value) {
				return $itemValue;
			}
		}

		if ($fieldConfig['foreign_table']) {
			return intval($value);
		}

		return FALSE;
	}

	/**
	 * Saeubert ein Group.
	 *
	 * @param array $fieldConfig
	 * @param mixed $value
	 * @return mixed
	 */
	public function cleanGroupField($fieldConfig, $value) {
		if ($fieldConfig['internal_type'] == 'db') {
			$arrCleanedValues = array();

			if (!is_array($value)) {
				return FALSE;
			}

			$arrAllowed = t3lib_div::trimExplode(',', $fieldConfig['allowed'], TRUE);

			if (count($arrAllowed) > 1) {
				$fieldConfig['prepend_tname'] = TRUE;
			}

			// Hier werden absichtlich nur die erlaubten Tabellen verwendet, da es sonst Unmengen an möglichen Optionen geben wuerde!
			foreach ($arrAllowed as $table) {
				if (!$GLOBALS['TCA'][$table]) {
					continue;
				}

				foreach ($value as $val) {
					if (!preg_match('/^' . $table . '_([0-9]+)$/', $val, $matches)) {
						$arrCleanedValues[] = (($fieldConfig['prepend_tname']) ? $table . '_' : '') . intval($matches[1]);
					}
				}
			}

			$arrCleanedValues = array_unique($arrCleanedValues);

			if ($fieldConfig['maxitems']) {
				$arrCleanedValues = array_slice($arrCleanedValues, 0, $fieldConfig['maxitems']);
			}

			return implode(',', $arrCleanedValues);
		}

		return FALSE;
	}

	/**
	 * Standardsaeuberung.
	 *
	 * @param array $fieldConfig
	 * @param mixed $value
	 * @return mixed
	 */
	public function cleanUncleanedField($fieldConfig, $value) {
		return strip_tags($value);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/lib/class.tx_datamintsfeuser_feusers.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/lib/class.tx_datamintsfeuser_feusers.php']);
}

?>