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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'datamints feuser' for the 'datamints_feuser' extension.
 *
 * @author	Bernhard baumgartl <b.baumgartl@datamints.com>
 * @package	TYPO3
 * @subpackage	tx_datamintsfeuser
 */
class tx_datamintsfeuser_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_datamintsfeuser_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_datamintsfeuser_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'datamints_feuser';	// The extension key.
	var $pi_checkCHash = true;
	var $confTypes = Array('showType', 'usedFields', 'requiredFields');
	var $conf = Array();
	var $lang = Array();
	var $userId = 0;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		// Debug.
		$GLOBALS['TSFE']->set_no_cache();
		$GLOBALS['TYPO3_DB']->debugOutput = true;

		// Flexform und Configurationen laden.
		$this->pi_initPIflexForm();
		$this->getConfiguration();
		//print_r($GLOBALS['TCA']['fe_users']['columns']);

		// Userid ermitteln.
		$this->userId = $GLOBALS['TSFE']->fe_user->user['uid'];

		switch ($this->piVars['submitmode']) {
			case 'register':
				$content = '';
				break;
			case 'edit':
				$content = '';
				break;
			default:
				$content = $this->showForm();
				break;
		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Gibt alle im Backend definierten Felder (TypoScipt/Flexform) formatiert und der Anzeigeart entsprechend aus.
	 * @return	String	$content
	 */
	function showForm() {
		if ($this->conf['showType'] == 'edit') {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid = ' . $this->userId , '', '');
            $arrCurrentData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}

		// Ein Array erzeugen, das "Assoziative Keys" hat.
		$arrUsedFields = explode(',', $this->conf['usedFields']);

		// Seite, die dden Request entgegennimmt (TypoLink).
		$requestLink = $this->pi_getPageLink($this->conf['requestPid']); // Link aus FF
		if (!$this->conf['requestPid']) {
			// wenn keine Seite per Flexform angegeben ist, wird die aktuelle Seite verwendet.
			$requestLink = $this->pi_getPageLink($GLOBALS['TSFE']->id);
		}

		// Formular start.
		$content = '<form name="' . $this->extKey . '" action="' . $requestLink . '" method="post" enctype="multipart/form-data"><fieldset class="form_part_1">';

		// ID zähler für Items und Fieldsets.
		$iItem = 1;
		$iFieldset = 1;

		// Alle ausgewählten $a_fields durchgehen
		foreach ($arrUsedFields as $fieldName) {

			// Wenn das im Flexform ausgewählte Feld existiert, dann dieses Feld ausgeben...
			if ($GLOBALS['TCA']['fe_users']['columns'][$fieldName]) {
				// Form Item Anfang.
				$content .= '<div class="form_item form_item_' . $iItem . ' form_type_' . $GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['type'] . '">';

				// Label schreiben.
				$label = $this->getLabel($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['label']);
				$content .= '<label for="' . $this->extKey . '_' . $fieldName . '">' . $label . $this->checkIfRequired($fieldName) . '</label> ';

				switch ($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['type']) {

					case 'input':
						if (strstr($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['eval'], 'date')) {
							// Datumsfeld.
							if ($arrCurrentData[$fieldName] != 0) {
								// Timestamp zu "tt.mm.jjjj" machen.
								$datum = strftime('%d.%m.%Y', $arrCurrentData[$fieldName]);
                            }
							$content .= '<input type="text" id="' . $this->extKey . '_' . $fieldName . '" name="' . $this->extKey . '[' . $fieldName . ']" value="' . $datum . '" />';
							break;
						}
						if (strstr($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['eval'], 'password')) {
							// Passwordfeld.
							$content .= '<input type="password" id="' . $this->extKey . '_' . $fieldName . '_1" name="' . $this->extKey . '[' . $fieldName . '][1]" value="" />';
							$content .= '<input type="password" id="' . $this->extKey . '_' . $fieldName . '_2" name="' . $this->extKey . '[' . $fieldName . '][2]" value="" />';
							break;
						}
						// Normales Inputfeld.
						$content .= '<input type="text" id="' . $this->extKey . '_' . $fieldName . '" name="' . $this->extKey . '[' . $fieldName . ']" value="' . $arrCurrentData[$fieldName] . '" />';
						break;

					case 'text':
						// Textarea.
						$content .= '<textarea id="' . $this->extKey . '_' . $fieldName . '" name="' . $this->extKey . '[' . $fieldName . ']">' . $arrCurrentData[$fieldName] . '</textarea>';
						break;

					case 'check':
						$checked = ($arrCurrentData[$fieldName] == 1) ? ' checked="checked"' : '';
						$content .= '<div class="check_item"><input type="checkbox" id="' . $this->extKey . '_' . $fieldName . '" name="' . $this->extKey . '[' . $fieldName . ']" value="1"' . $checked . ' /></div>';
						break;
/*
					case 'radio':
						for ($j = 0; $j < count($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items']); $j++) {
							$checked = ($arrCurrentData[$fieldName] == $GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items'][$j][1]) ? ' checked="checked"' : '';
							$content .= '<input type="radio" id="' . $this->extKey . '_' . $fieldName . '_' . $j . '" name="' . $this->extKey . '[' . $fieldName . ']" value="' . $GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items'][$j][1] . '"' . $checked . ' class="radiobutton" />';
							$content .= '<label class="radio_label" for="' . $this->extKey . '_' . $fieldName . '_' . $j . '">';
							$content .= $this->getLabel($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items'][$j][0]);
							$content .= '</label>';
						}
						break;
*/
					case 'select':
						$content .= '<div class="select_item">';
						// Anzahl der Select-Items
						$countSelectfields = count($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items']);
						// Einzeiliges Select (Dropdown).
						if ($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['size'] == 1 && !$GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['foreign_table']) {
							$content .= '<select id="' . $this->extKey . '_' . $fieldName . '" name=' . $this->extKey . '[' . $fieldName . ']">';
							for ($j = 0; $j < $countSelectfields; $j++) {
								$selected = ($arrCurrentData[$fieldName] == $j) ? ' selected="selected"' : '';
								$content .= '<option value="' . $GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items'][$j][1] . '"' . $selected . '>' . $this->getLabel($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items'][$j][0]) . '</option>';
							}
							$content .= '</select>';
						}
						$content .= '</div>';
						break;

					case 'group':
						// GROUP (z.B. externe Tabellen).
						$content .= '<div class="group_item">';

						// Wenn es sich um den "internal_type" FILE handelt && es ein Bild ist, dann ein Vorschaubild erstellen und ein Fiel-Inputfeld anzeigen.
						if ($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['internal_type'] == 'file') {
							// Verzeichniss ermitteln.
							$uploadFolder = $GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['uploadfolder'];
							if (substr($uploadFolder, -1) != '/') {
								$uploadFolder = $uploadFolder . '/';
							}
							// Breite ermitteln.
							$imageWidth = $this->conf['image.']['maxW'];
							if (!$imageWidth) {
								$imageWidth = 100;
							}
							$imgTSConfig = $this->conf['image.'];
							$imgTSConfig['file'] = $uploadFolder . $arrCurrentData[$fieldName];
							$imgTSConfig['file.']['maxW'] = $imageWidth;
							$imgTSConfig['altText'] = 'Bild';
							$imgTSConfig['titleText'] = 'Bild';
							$image = $this->cObj->IMAGE($imgTSConfig);
							// Bild anzeigen.
							$content .= '<div class="image_preview">' . $image . '</div>';

							// Wenn kein Bild vorhanden ist, das Upload-Feld anzeigen.
							if ($arrCurrentData[$fieldName] == '') {
								$content .= '<input type="file" id="' . $this->extKey . '_' . $fieldName . '" name="' . $this->extKey . '[' . $fieldName . ']" />';
							} else {
								$content .= '<div class="image_delete"><input type="checkbox" name="' . $this->extKey . '[delete_image]" value="' . $uploadFolder . $arrCurrentData[$fieldName] . '" />' . $this->pi_getLL('delete_image') . '</div>';
							}

						}

						$content .= '</div>';
						break;

				}
				// Form Item Ende.
				$content .= '</div>';
				$iItem++;
			} elseif ($fieldName == '--Separator--') {
				$iFieldset++;
				$content .= '</fieldset><fieldset class="fieldset_' . $iFieldset . '">';
			}

		}

		// UserId, PageId und Modus anhängen.
		$content .= '<input type="hidden" name="' . $this->extKey . '[userid]" value="' . $this->userId . '" />';
		$content .= '<input type="hidden" name="' . $this->extKey . '[pageid]" value="' . $GLOBALS['TSFE']->id . '" />';
		$content .= '<input type="hidden" name="' . $this->extKey . '[submitmode]" value="' . $this->conf['showType'] . '" />';
		// Submitbutton.
		$content .= '<div class="submit_item"><input type="submit" value="' . $this->pi_getLL('submit_button_' . $this->conf['showType']) . '"/></div>';

		$content .= '</fieldset>';
		$content .= '</form>';

		return $content;

	}

	/**
	 * Überprüft ob das übergebene Feld benötigt wird um erfolgreich zu speichern.
	 * @param	String	$fieldName
	 * @return	String
	 */
	function checkIfRequired($fieldName) {
		$arrRequiredFields = explode(',', $this->conf['requiredFields']);
		if (in_array($fieldName, $arrRequiredFields)) {
			return ' *';
		} else {
			return '';
		}
	}

	/**
	 * Ermittelt ein bestimmtes Label aufgrund des im TCA gespeicherten Languagestrings.
	 * @param	String	$languageString
	 * @return	String
	 */
	function getLabel($languageString) {
		// Standard Sprache.
		$defaultLanguage = 'default';
		$defaultLanguage = $GLOBALS['TSFE']->config['config']['language'];
		// Languagekey ermitteln ("LLL:EXT:lang/locallang_general.php:LGL.starttime" => "LGL.starttime").
		$languageKey = substr($languageString, strripos($languageString, ':') + 1);
		// Label aus der Cinfiguration holen.
		$confLabel = $this->pi_getLL($languageKey);
		if ($confLabel) {
			// Das Label zurückliefern.
			return $confLabel;
		} else {
			// Languagefile ermitteln ("LLL:EXT:lang/locallang_general.php:LGL.starttime" => "EXT:lang/locallang_general.php").
			$languageFilePath = substr($languageString, 4, strripos($languageString, ':') - 4);
			// LanguageFile laden.
			$languageFile = $GLOBALS['TSFE']->readLLfile($languageFilePath);
			// Das Label zurückliefern.
			return $languageFile[$defaultLanguage][$languageKey];
		}
	}

	/**
	 * Überschribt eventuell vorhandene TypoScript Konfigurationen mit den Konfigurationen aus der Flexform.
	 * @global	$this->conf
	 */
	function getConfiguration() {
		foreach ($this->confTypes as $key) {
			$value = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $key, 'sDEF');
			if ($value) {
				$this->conf[$key] = $value;
			}
		}
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1.php']);
}

?>