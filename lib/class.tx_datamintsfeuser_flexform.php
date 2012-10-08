<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2009 Bernhard baumgartl <b.baumgartl@datamints.com>
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
 *   46: class tx_datamintsfeuser_flexform
 *   54:     function getFieldNames($config)
 *
 *
 * TOTAL FUNCTIONS: 1
 *
 */

/**
 * Class 'tx_datamintsfeuser_flexform' which gets all field names from the "fe_users".
 *
 * @author	Bernhard baumgartl <b.baumgartl@datamints.com>
 * @package	TYPO3
 * @subpackage	tx_datamintsfeuser
 */
class tx_datamintsfeuser_flexform {

	/**
	 * The getFields method is used to get the "fe_users" field names into the flexform of the plugin.
	 *
	 * @param	array		$config: The fields selected.
	 * @return	array		$config
	 */
	function getFieldNames($config) {
		// Damit $TCA hier zur Verfuegung steht.
		global $TCA;

		// $TCA-Teil laden. Damit koennen wir alle Felder durchgehen.
		t3lib_div::loadTCA('fe_users');

		$fieldList = array();

		foreach ($TCA['fe_users']['columns'] as $key => $_){
			$fieldList[] = array($key, $key);
		}

		$config['items'] = array_merge($config['items'], $fieldList);

		return $config;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/lib/class.tx_datamintsfeuser_flexform.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/lib/class.tx_datamintsfeuser_flexform.php']);
}

?>