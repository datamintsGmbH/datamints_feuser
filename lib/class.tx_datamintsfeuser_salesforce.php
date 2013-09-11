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
 *   46: class tx_datamintsfeuser_salesforce
 *   56:     public function main($params, $pObj)
 *
 *
 * TOTAL FUNCTIONS: 1
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

	/**
	 * Maps the typo3 fields to the salesforce fields and submits it to salesforce.
	 * This will only done if the user is completely activated!
	 *
	 * @param	array		$params
	 * @param	object		$pObj
	 * @return	null
	 */
	public function main($params, $pObj) {
		if ($GLOBALS['userAlreadyAddedToSalesforceInThisSession']) {
			return;
		}

		if (!$pObj->conf['salesforce.']['enable'] || !$pObj->conf['salesforce.']['oid']) {
			return;
		}

		if ($params['variables']['markerArray']['tx_datamintsfeuser_approval_level'] > 0) {
			return;
		}

		$fields = array(
			'oid' => $pObj->conf['salesforce.']['oid']
		);

		foreach ($pObj->conf['salesforce.']['mapping.'] as $field => $stdWrapConfig) {
			$cObj = t3lib_div::makeInstance('tslib_cObj');
			$cObj->data = $params['variables']['markerArray'];

			$fields[rtrim($field, '.')] = $cObj->stdWrap('', $stdWrapConfig);
		}

		$curlOptions = array(
			CURLOPT_URL => $pObj->conf['salesforce.']['target'],
			CURLOPT_POST => TRUE,
			CURLOPT_POSTFIELDS => http_build_query($fields),
			CURLOPT_FAILONERROR => TRUE
		);

		$resource = curl_init();

		curl_setopt_array($resource, $curlOptions);

		$GLOBALS['userAlreadyAddedToSalesforceInThisSession'] = curl_exec($resource);

		curl_close($resource);

		return;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/lib/class.tx_datamintsfeuser_salesforce.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/lib/class.tx_datamintsfeuser_salesforce.php']);
}

?>