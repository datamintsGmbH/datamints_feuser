<?php

namespace Datamints\Feuser\Hook;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Bernhard Baumgartl <b.baumgartl@datamints.com>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook for removing deprecated flexform fields.
 */
class FlexFormHook {
	const deprecatedFlexFormSheetFields = [
		'sRED' => ['redirect.register', 'redirect.resendactivation', 'redirect.edit', 'redirect.userdelete']
	];

	/**
	  * Checks if a flexform field is deprecated and removes it.
	  *
	  * @param string $status
	  * @param string $table
	  * @param string $id
	  * @param array $fieldArray
	  * @param \TYPO3\CMS\Core\DataHandling\DataHandler $reference
	  *
	  * @return void
	  */
	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$reference) {
		if ($status !== 'update' || $table !== 'tt_content' || !$fieldArray['pi_flexform']) {
			return;
		}

		$flexFormData = GeneralUtility::xml2array($fieldArray['pi_flexform']);

		foreach (self::deprecatedFlexFormSheetFields as $sheet => $fields) {
			foreach($fields as $field) {
				if (isset($flexFormData['data'][$sheet]['lDEF'][$field]['vDEF'])) {
					unset($flexFormData['data'][$sheet]['lDEF'][$field]);
				}
			}

			// If remaining sheet does not contain fields, then remove the sheet.
			if (empty($flexFormData['data'][$sheet]['lDEF'])) {
				unset($flexFormData['data'][$sheet]);
			}
		}

		$flexFormTools = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class);

		$fieldArray['pi_flexform'] = $flexFormTools->flexArray2Xml($flexFormData, TRUE);
	}

}
