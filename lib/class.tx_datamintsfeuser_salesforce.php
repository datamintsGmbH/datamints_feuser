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
 *   47: class tx_datamintsfeuser_salesforce
 *   59:     public function main($params, $pObj)
 *  101:     public function getMappingFields($mappings, $variables)
 *
 *
 * TOTAL FUNCTIONS: 2
 *
 */

/**
 * Library 'Salesforce' for the 'datamints_feuser' extension.
 *
 * @author	Bernhard Baumgartl <b.baumgartl@datamints.com>
 * @package	TYPO3
 * @subpackage	tx_datamintsfeuser
 */
class tx_datamintsfeuser_salesforce {

	const alreadyExecuted = 'userAlreadyAddedToSalesforceInThisSession';

	/**
	 * Maps the typo3 fields to the salesforce fields and submits it to salesforce.
	 * This will only be done if the user is completely activated!
	 *
	 * @param	array		$params
	 * @param	object		$pObj
	 * @return	null
	 */
	public function main($params, $pObj) {
		if ($GLOBALS[self::alreadyExecuted]) {
			return;
		}

		if (!$pObj->conf['salesforce.'] || !$pObj->conf['salesforce.']['enable']) {
			return;
		}

		if ($params['variables']['markerArray']['tx_datamintsfeuser_approval_level'] > 0) {
			return;
		}

		if (!$pObj->conf['salesforce.']['target'] || !$pObj->conf['salesforce.']['oid']) {
			return;
		}

		$fields = array(
			'oid' => $pObj->conf['salesforce.']['oid']
		);

		$mappingFields = self::getMappingFields($pObj->conf['salesforce.']['mapping.'], $params['variables']['markerArray']);

		$resource = curl_init();

		curl_setopt_array($resource, array(
			CURLOPT_URL => $pObj->conf['salesforce.']['target'],
			CURLOPT_POST => TRUE,
			CURLOPT_POSTFIELDS => http_build_query(array_merge($mappingFields, $fields)),
			CURLOPT_FAILONERROR => TRUE
		));

		$GLOBALS[self::alreadyExecuted] = curl_exec($resource);

		curl_close($resource);

		return;
	}

	/**
	 * Gets the mapped fields.
	 *
	 * @param	array		$mappings
	 * @param	array		$variables
	 * @return	array		$fields
	 */
	public function getMappingFields($mappings, $variables) {
		$fields = array();

		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->data = $variables;

		foreach ($mappings as $field => $stdWrapConfig) {
			$fields[rtrim($field, '.')] = $cObj->stdWrap('', $stdWrapConfig);
		}

		return $fields;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/lib/class.tx_datamintsfeuser_salesforce.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/lib/class.tx_datamintsfeuser_salesforce.php']);
}

?>