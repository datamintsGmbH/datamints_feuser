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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class that adds the wizard icon.
 *
 * @author	Bernhard baumgartl <b.baumgartl@datamints.com>
 * @package	TYPO3
 * @subpackage	tx_datamintsfeuser
 */
class tx_datamintsfeuser_pi1_wizicon {

	/**
	 * Processing the wizard items array
	 *
	 * @param	array		$wizardItems: The wizard items.
	 * @return	array		$wizardItems: Modified array with wizard items.
	 */
	public function proc($wizardItems) {
		$wizardItems['plugins_tx_datamintsfeuser_pi1'] = array(
			'icon' => ExtensionManagementUtility::extPath('datamints_feuser') . 'pi1/ce_wiz.gif',
			'title' => $GLOBALS['LANG']->sL('LLL:EXT:datamints_feuser/locallang.xml:pi1_title'),
			'description' => $GLOBALS['LANG']->sL('LLL:EXT:datamints_feuser/locallang.xml:pi1_plus_wiz_description'),
			'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=datamints_feuser_pi1'
		);

		return $wizardItems;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1_wizicon.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1_wizicon.php']);
}
