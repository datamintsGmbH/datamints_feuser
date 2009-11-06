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
	var $confTypes = Array('showtype', 'usedfields', 'requiredfields');		// Konfigurationen, die von Flexformkonfiguration überschrieben werden können.
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

		switch ($this->piVars['submit']) {
			case 'send':
				$content = $this->sendForm();
				break;
			case 'redirect':
				// Wenn Weiterleitung mit Login, dann wird erst eingeloggt und dann weitergeleitet.
				if ($this->conf['register.']['redirect']) {
					header('Location: ' . $this->pi_getPageLink($this->conf['register.']['redirect']));
				} else {
					header('Location: ' . $this->pi_getPageLink($GLOBALS['TSFE']->id));
				}
				break;
			default:
				$content = $this->showForm();
				break;
		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Bereitet die übergeben Daten für den Import in die Datenbank vor, und führt diesen, wenn es keine Fehler gab, aus.
	 * @return	String	$content
	 */
	function sendForm() {
		// Jedes Element trimmen.
		foreach ($this->piVars as $key => $value) {
			$this->piVars[$key] = trim($value);
		}

		// Überprüfen ob Datenbankeinträge mit den übergebenen Daten übereinstimmen.
		$uniqueCheck = $this->uniqueCheckForm();
		// Eine Validierung durchführen über alle Felder die eine gesonderte Konfigurtion bekommen haben.
		$validCheck = $this->validateForm();
		// Überprüfen ob in allen benötigten Feldern etwas drinn steht.
		$requireCheck = $this->requireCheckForm();
		// Wenn bei der Validierung ein Feld nicht den Anforderungen entspricht noch einmal die Form anzeigen und entsprechende Felder markieren.
		$valueCheck = array_merge((Array)$uniqueCheck, (Array)$validCheck, (Array)$requireCheck);
		if (in_array(0, $valueCheck)) {
			$content = $this->showForm($valueCheck);
			return $content;
		}

		// Wenn der Bearbeitungsmodus, die Zielseite, und der User stimmen, dann wird in die Datenbank schreiben.
		if ($this->piVars['submitmode'] == $this->conf['showtype'] && intval($this->piVars['pageid']) == $GLOBALS['TSFE']->id && intval($this->piVars['userid']) == $this->userId) {
			// Übergebene Felder auslagern um eventuell später noch einmal darauf zugreifen zu können.
			$arrUpdate = $this->piVars;

			// Zusatzfelder setzten, die nicht aus der Form übergeben wurden.
			$arrUpdate['tstamp'] = time();

			// Alle nicht in der Datenbank vorhandenen Felder aus dem Array löschen.
			unset($arrUpdate['submit'], $arrUpdate['submitmode'], $arrUpdate['pageid'], $arrUpdate['userid']);

			// Sonderfälle!
			$usedFields = explode(',', str_replace(' ', '', $this->conf['usedfields']));
			foreach ($usedFields as $fieldName) {
				// Passwordfelder behandeln.
				if (strstr($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['eval'], 'password')) {
					unset($arrUpdate[$fieldName . '_rep']);
					// Password generieren und verschlüsseln je nach Einstellung.
					$arrUpdate[$fieldName] = $this->generatePassword($arrUpdate[$fieldName]);
					// Wenn kein Password übergeben wurde auch keins schreiben.
					if ($arrUpdate[$fieldName] == '') {
						unset($arrUpdate[$fieldName]);
					}
				}
				// Bildfelder behandeln.
				if ($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['internal_type'] == 'file' && ($_FILES[$this->prefixId]['type'][$fieldName] || $this->piVars[$fieldName . '_delete'])) {
					unset($arrUpdate[$fieldName . '_delete']);
					// Das Bild hochladen oder löschen. Gibt einen Fehlerstring zurück falls ein Fehler auftritt.
					$valueCheck[$fieldName] = $this->saveDeleteImage($fieldName, $arrUpdate);
					if ($valueCheck[$fieldName]) {
						$content = $this->showForm($valueCheck);
						return $content;
					}
				}
				// Checkboxen behandeln.
				if ($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['type'] == 'check' && !$this->piVars[$fieldName]) {
					$arrUpdate[$fieldName] = '0';
				}
				// Datumsfelder behandeln.
				if (strstr($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['eval'], 'date')) {
					$arrUpdate[$fieldName] = strtotime($arrUpdate[$fieldName]);
				}
			}

			// Der User hat seine Daten editiert.
			if ($this->conf['showtype'] == 'edit') {
				// User editieren.
				$error = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $this->userId , $arrUpdate);
				if ($error == 1) {
					$content = 'Alle Daten erfolgreich geupdated! Sie werden in wenigen Sekunden weitergeleitet!';
					$GLOBALS['TSFE']->additionalHeaderData['refresh'] = '<meta http-equiv="refresh" content="2; url=' . $this->pi_getPageLink($GLOBALS['TSFE']->id) . '" />';
				}
			}

			// Ein neuer User hat sich angemeldet.
			if ($this->conf['showtype'] == 'register') {
				// Standartkonfigurationen anwenden.
				$arrUpdate['pid'] = $this->conf['register.']['userfolder'];
				$arrUpdate['usergroup'] = $this->conf['register.']['usergroup'];
				$arrUpdate['crdate'] = $arrUpdate['tstamp'];
				// User erstellen.
				$error = $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users', $arrUpdate);
				if ($error == 1) {
					$content = 'Alle Daten erfolgreich eingetragen! Sie werden in wenigen Sekunden weitergeleitet!';
					// Wenn nach der Registrierung weitergeleitet werden soll.
					if ($this->conf['register.']['login']) {
						// Weiterleitung mit Login. Zuerst auf die eigene Seite mit Login Parametern und dann auf das Weiterleitungsziel.
						header('Location: ' . $this->pi_getPageLink($GLOBALS['TSFE']->id) . '?' . $this->prefixId . '[submit]=redirect&logintype=login&pid=' . $this->conf['register.']['userfolder'] . '&user=' . $this->piVars['username'] . '&pass=' . $this->piVars['password']);
					} elseif ($this->conf['register.']['redirect']) {
						// Weiterleitung ohne Login.
						header('Location: ' . $this->pi_getPageLink($this->conf['register.']['redirect']));
					}
				}
			}
		}

		return $content;
	}

	/**
	 * Erstellt wenn gefordert ein Password, und verschlüsselt dieses, oder das übergebene, wenn es verschlüsselt werden soll.
	 * @param	String	$password
	 */
	function generatePassword($password) {
		// Erstellt ein Password.
		if ($this->conf['register.']['generatepassword.']['mode']) {
			$chars = '234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$i = 1;
			$password = '';
			while ($i <= $this->conf['register.']['generatepassword.']['length']) {
				$password .= $chars{mt_rand(0, strlen($chars))};
				$i++;
			}
			// Unverschlüsseltes Password aufheben.
			$this->piVars['password'] = $password;
		}
		// Wenn "saltedpasswords" installiert ist wird deren Konfiguration geholt, und je nach Einstellung das Password verschlüsselt.
		if (t3lib_extMgm::isLoaded('saltedpasswords')) {
			$saltedpasswords = tx_saltedpasswords_div::returnExtConf();
			if ($saltedpasswords['enabled'] == 1) {
				$tx_saltedpasswords = new $saltedpasswords['saltedPWHashingMethod']();
				$password = $tx_saltedpasswords->getHashedPassword($password);
			}
		}
		return $password;
	}

	/**
	 * Überprüft ob alle benötigten Felder mit Inhalten übergeben wurden.
	 * @return	Array	$valueCheck
	 */
	function requireCheckForm() {
		// Alle ausgewählten Felder durchgehen.
		$requiredFields = explode(',', str_replace(' ', '', $this->conf['requiredfields']));
		foreach ($requiredFields as $fieldName) {
			if ($this->piVars[$fieldName] == '') {
				$valueCheck[$fieldName] = 'required';
			}
		}
		return $valueCheck;
	}

	/**
	 * Überprüft ob alle Validierungen eingehalten wurden.
	 * @return	Array	$valueCheck
	 */
	function validateForm() {
		// Alle ausgewählten Felder durchgehen.
		foreach ($this->conf['validate.'] as $fieldName => $validate) {
			$fieldName = trim($fieldName, '.');
			// Wenn der im TypoScript angegebene Feldname existiert und ein Wert übergeben wurde, dann validieren.
			if ($GLOBALS['TCA']['fe_users']['columns'][$fieldName] && $this->piVars[$fieldName]) {
				$value = $this->piVars[$fieldName];

				switch ($validate['type']) {

					case 'password':
						$value_rep = $this->piVars[$fieldName . '_rep'];
						$arrLength[0] = 6;
						if ($value == $value_rep) {
							if ($validate['length']) {
								$arrLength = explode(',', str_replace(' ', '', $validate['length']));
								if ($arrLength[1]) {
									// Wenn eine Maximallänge festgelegt wurde.
									if (strlen($value) < $arrLength[0] && strlen($value) > $arrLength[1]) {
										$valueCheck[$fieldName] = 'length';
									}
								} else {
									// Wenn nur eine Minimallänge festgelegt wurde.
									if (strlen($value) < $arrLength[0]) {
										$valueCheck[$fieldName] = 'length';
									}
								}
							} else {
								// Wenn nur eine Minimallänge festgelegt wurde.
								if (strlen($value) < $arrLength[0]) {
									$valueCheck[$fieldName] = 'length';
								}
							}
						} else {
							$valueCheck[$fieldName] = 'equal';
						}
						break;

					case 'email':
						if (!preg_match('/^[a-zA-Z0-9\._%+-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,6}$/', $value)) {
							$valueCheck[$fieldName] = 'valid';
						}
						break;

					case 'custom':
						if ($validate['regexp']) {
							if (!preg_match($validate['regexp'], $value)) {
								$valueCheck[$fieldName] = 'valid';
							}
						}
						if ($validate['length']) {
							$arrLength = explode(',', str_replace(' ', '', $validate['length']));
							if ($arrLength[1]) {
								// Wenn eine Maximallänge festgelegt wurde.
								if (strlen($value) < $arrLength[0] && strlen($value) > $arrLength[1]) {
									$valueCheck[$fieldName] = 'length';
								}
							} else {
								// Wenn nur eine Minimallänge festgelegt wurde.
								if (strlen($value) < $arrLength[0]) {
									$valueCheck[$fieldName] = 'length';
								}
							}
						}
						break;

				}
			}
		}
		return $valueCheck;
	}

	/**
	 * Überprüft die übergeben Inhalte, bei bestimmten Feldern, in der Datenbank schon vorhanden sind.
	 * @return	Array	$valueCheck
	 */
	function uniqueCheckForm() {
		// Check unique Fields.
		$uniqueFields = explode(',', str_replace(' ', '', $this->conf['register.']['uniquefields']));
		// Wenn User eingeloggt, dann den eigenen Datensatz nicht durchsuchen.
		if ($this->userId) {
			$where = ' uid <> ' . $this->userId;
		}
		foreach ($uniqueFields as $fieldName) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid) as count', 'fe_users', 'pid = ' . $this->conf['register.']['userfolder'] . ' AND ' . $fieldName . ' = "' . $this->piVars[$fieldName] . '"' . $where, '', '');
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if ($row['count'] >= 1) {
				$valueCheck[$fieldName] = 'unique';
			}
		}
		return $valueCheck;
	}

	/**
	 * Gibt alle im Backend definierten Felder (TypoScipt/Flexform) formatiert und der Anzeigeart entsprechend aus.
	 * @return	String	$content
	 */
	function showForm($valueCheck = Array()) {
		// Beim editieren der Userdaten, die Felder vorausfüllen.
		if ($this->conf['showtype'] == 'edit') {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid = ' . $this->userId , '', '');
            $arrCurrentData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}

		// Wenn das Formular schon einmal abgesendet wurde aber ein Fehler auftrat, dann die bereits vom User übertragenen Userdaten vorausfüllen.
		if ($this->piVars) {
			$arrCurrentData = array_merge((Array)$arrCurrentData, (Array)$this->piVars);
		}

		// Ein Array erzeugen, mit allen zu benutztenden Feldern.
		$arrUsedFields = explode(',', str_replace(' ', '', $this->conf['usedfields']));

		// Seite, die den Request entgegennimmt (TypoLink).
		$requestLink = $this->pi_getPageLink($this->conf['requestPid']);
		if (!$this->conf['requestPid']) {
			// Wenn keine Seite per TypoScript angegeben ist, wird die aktuelle Seite verwendet.
			$requestLink = $this->pi_getPageLink($GLOBALS['TSFE']->id);
		}

		// Formular start.
		$content = '<form name="' . $this->prefixId . '" action="' . $requestLink . '" method="post" enctype="multipart/form-data"><fieldset class="form_part_1">';

		// ID zähler für Items und Fieldsets.
		$iItem = 1;
		$iFieldset = 1;

		// Alle ausgewählten Felder durchgehen.
		foreach ($arrUsedFields as $fieldName) {
			// Wenn das im Flexform ausgewählte Feld existiert, dann dieses Feld ausgeben.
			if ($GLOBALS['TCA']['fe_users']['columns'][$fieldName]) {
				// Form Item Anfang.
				$content .= '<div class="form_item form_item_' . $iItem . ' form_type_' . $GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['type'] . '">';

				// Label schreiben.
				$label = $this->getLabel($fieldName);
				$content .= '<label for="' . $this->prefixId . '_' . $fieldName . '">' . $label . $this->checkIfRequired($fieldName) . '</label> ';

				switch ($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['type']) {

					case 'input':
						if (strstr($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['eval'], 'date')) {
							// Datumsfeld.
							if ($arrCurrentData[$fieldName] != 0) {
								// Timestamp zu "tt.mm.jjjj" machen.
								$datum = strftime('%d.%m.%Y', $arrCurrentData[$fieldName]);
                            }
							$content .= '<input type="text" id="' . $this->prefixId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']" value="' . $datum . '" />';
							break;
						}
						if (strstr($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['eval'], 'password')) {
							// Passwordfeld.
							$content .= '<input type="password" id="' . $this->prefixId . '_' . $fieldName . '_1" name="' . $this->prefixId . '[' . $fieldName . ']" value="" />';
							$content .= '<input type="password" id="' . $this->prefixId . '_' . $fieldName . '_2" name="' . $this->prefixId . '[' . $fieldName . '_rep]" value="" />';
							break;
						}
						// Normales Inputfeld.
						$content .= '<input type="text" id="' . $this->prefixId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']" value="' . $arrCurrentData[$fieldName] . '" />';
						break;

					case 'text':
						// Textarea.
						$content .= '<textarea id="' . $this->prefixId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']">' . $arrCurrentData[$fieldName] . '</textarea>';
						break;

					case 'check':
						$checked = ($arrCurrentData[$fieldName] == 1) ? ' checked="checked"' : '';
						$content .= '<div class="check_item"><input type="checkbox" id="' . $this->prefixId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']" value="1"' . $checked . ' /></div>';
						break;
/*
					case 'radio':
						for ($j = 0; $j < count($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items']); $j++) {
							$checked = ($arrCurrentData[$fieldName] == $GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items'][$j][1]) ? ' checked="checked"' : '';
							$content .= '<input type="radio" id="' . $this->prefixId . '_' . $fieldName . '_' . $j . '" name="' . $this->prefixId . '[' . $fieldName . ']" value="' . $GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items'][$j][1] . '"' . $checked . ' class="radiobutton" />';
							$content .= '<label class="radio_label" for="' . $this->prefixId . '_' . $fieldName . '_' . $j . '">';
							$content .= $this->getLabel($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items'][$j][0]);
							$content .= '</label>';
						}
						break;
*/
					case 'select':
						$content .= '<div class="select_item">';
						// Einzeiliges Select (Dropdown).
						if ($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['size'] == 1 && !$GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['foreign_table']) {
							$content .= '<select id="' . $this->prefixId . '_' . $fieldName . '" name=' . $this->prefixId . '[' . $fieldName . ']">';
							// Anzahl der Select-Items.
							$countSelectfields = count($GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['items']);
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
								$content .= '<input type="file" id="' . $this->prefixId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']" />';
							} else {
								$content .= '<div class="image_delete"><input type="checkbox" name="' . $this->prefixId . '[' . $fieldName . '_delete]" value="' . $uploadFolder . $arrCurrentData[$fieldName] . '" />' . $this->pi_getLL('image_delete') . '</div>';
							}

						}

						$content .= '</div>';
						break;

				}
				// Form Item Ende.
				if (array_key_exists($fieldName, $valueCheck) && is_string($valueCheck[$fieldName])) {
					// Extra Error Label ermitteln.
					$content .= '<div class="form_error ' . $fieldName . '_error">' . $this->getLabel($fieldName . '_error_' . $valueCheck[$fieldName]) . '</div>';
				}
				$content .= '</div>';
				$iItem++;
			} elseif ($fieldName == '--separator--') {
				$iFieldset++;
				$content .= '</fieldset><fieldset class="fieldset_' . $iFieldset . '">';
			}

		}

		// UserId, PageId und Modus anhängen.
		$content .= '<input type="hidden" name="' . $this->prefixId . '[submit]" value="send" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[userid]" value="' . $this->userId . '" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[pageid]" value="' . $GLOBALS['TSFE']->id . '" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[submitmode]" value="' . $this->conf['showtype'] . '" />';
		// Submitbutton.
		$content .= '<div class="submit_item"><input type="submit" value="' . $this->pi_getLL('submit_' . $this->conf['showtype']) . '"/></div>';

		$content .= '</fieldset>';
		$content .= '</form>';

		return $content;

	}

	/**
	 * The saveDeleteImage method is used to update or delete an image of an address
	 * @param	String	$fieldName
	 * @param	Array	$arrUpdate // Call by reference Array mit allen zu updatenden Daten.
	 * @return	String	$content
	 */
	function saveDeleteImage($fieldName, &$arrUpdate) {
		// Bild löschen.
		if ($this->piVars[$fieldName . '_delete']) {
			$arrUpdate[$fieldName] = '';
			// Bild aus dem Filesystem löschen, wenn vorhanden.
			if (file_exists($this->piVars[$fieldName . '_delete'])) {
				unlink($this->piVars[$fieldName . '_delete']);
			}
			return '';
		}

		// Wenn die Datei zu groß ist.
		if ($_FILES[$this->prefixId]['error'][$fieldName] == '2') {
			return 'size';
		}

		// Die erlaubten MIME-Typen.
		$mimeTypes = array();
		$mimeTypes['image/jpeg'] = '.jpg';
		$mimeTypes['image/gif'] = '.gif';
		$mimeTypes['image/bmp'] = '.bmp';
		$mimeTypes['image/tiff'] = '.tif';
		$mimeTypes['image/png'] = '.png';
		// Den Format-Typ ermitteln.
		$imageType = $mimeTypes[$_FILES[$this->prefixId]['type'][$fieldName]];
		// Wenn ein falsche Format hochgeladen wurde.
		if (!$imageType) {
			return 'type';
		}

		// Nur wenn eine Datei ausgewählt wurde [image] und diese den obigen mime-typen enstpricht[$type], dann wird die datei gespeichert
		if ($_FILES[$this->prefixId]['name'][$fieldName]) {
			// Verzeichniss ermitteln.
			$uploadFolder = $GLOBALS['TCA']['fe_users']['columns'][$fieldName]['config']['uploadfolder'];
			if (substr($uploadFolder, -1) != '/') {
				$uploadFolder = $uploadFolder . '/';
			}

			// Bildname generieren.
			$fileName = $arrUpdate['username'] . '_' . time() . $imageType;
			// Kompletter Bildpfad.
			$uploadFile = $uploadFolder . $fileName;

			// Bild verschieben, und anschließend den neuen Bildnamen in die Datenbank schreiben.
			if (move_uploaded_file($_FILES[$this->prefixId]['tmp_name'][$fieldName], $uploadFile)) {
				chmod($uploadFile, 0644);
				$arrUpdate['image'] = $fileName;
				// Wenn Das Bild erfolgreich hochgeladen wurde, nichts zurückgeben.
				return '';
			}
		}

		return 'upload';
	}

	/**
	 * Überprüft ob das übergebene Feld benötigt wird um erfolgreich zu speichern.
	 * @param	String	$fieldName
	 * @return	String
	 */
	function checkIfRequired($fieldName) {
		$arrRequiredFields = explode(',', str_replace(' ', '', $this->conf['requiredfields']));
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
	function getLabel($fieldName) {
		// Label aus der Konfiguration holen basierend auf dem Datenbankfeldnamen.
		$confLabel = $this->pi_getLL($fieldName);
		if ($confLabel) {
			// Das Label zurückliefern.
			return $confLabel;
		} else {
			// Standard Sprache.
			$defaultLanguage = $GLOBALS['TSFE']->config['config']['language'];
			if (!$defaultLanguage) {
				$defaultLanguage = 'default';
			}
			// LanguageString ermitteln.
			$languageString = $GLOBALS['TCA']['fe_users']['columns'][$fieldName]['label'];
			// Languagekey ermitteln z.B. ("LLL:EXT:lang/locallang_general.php:LGL.starttime" => "LGL.starttime").
			$languageKey = substr($languageString, strripos($languageString, ':') + 1);
			// Languagefile ermitteln z.B. ("LLL:EXT:lang/locallang_general.php:LGL.starttime" => "EXT:lang/locallang_general.php").
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