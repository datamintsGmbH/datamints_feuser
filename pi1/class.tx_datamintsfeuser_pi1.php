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
 *   67: class tx_datamintsfeuser_pi1 extends tslib_pibase
 *  109:     function main($content, $conf)
 *  186:     function sendForm()
 *  350:     function generatePassword($password)
 *  386:     function requireCheckForm()
 *  402:     function validateForm()
 *  515:     function uniqueCheckForm()
 *  541:     function saveDeleteImage($fieldName, &$arrUpdate)
 *  605:     function sendMail($templatePart, $extraMarkers = Array())
 *  670:     function makeDoubleOptIn()
 *  689:     function showForm($valueCheck = Array())
 *  909:     function makeHiddenFields()
 *  925:     function makeHiddenParams()
 *  946:     function cleanHeaderUrlData($data)
 *  957:     function checkIfRequired($fieldName)
 *  972:     function getLabel($fieldName)
 * 1013:     function getConfiguration()
 * 1034:     function setFlexformConfiguration($key, $value)
 * 1058:     function getJSValidationConfiguration()
 * 1104:     function getFeUsersTca()
 * 1118:     function getStoragePid()
 * 1132:     function deletePoint($array)
 * 1163:     function array_merge_replace_recursive($array1)
 * 1195:     function check_utf8($str)
 *
 * TOTAL FUNCTIONS: 23
 *
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
	var $feUsersTca = Array();
	var $storagePid = 0;
	var $contentUid = 0;
	var $conf = Array();
	var $extConf = Array();
	var $lang = Array();
	var $userId = 0;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	string		The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->contentUid = $this->cObj->data['uid'];
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		// Debug.
		//$GLOBALS['TSFE']->set_no_cache();
		//$GLOBALS['TYPO3_DB']->debugOutput = true;

		// Flexform und Configurationen laden.
		$this->pi_initPIflexForm();
		$this->getConfiguration();
		$this->getFeUsersTca();
		$this->getStoragePid();

		// Javascripts in den Head einbinden.
		$GLOBALS['TSFE']->setJS($this->extKey, $this->getJSValidationConfiguration());
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] .= '<script type="text/javascript" src="' . (($this->conf['jsvalidatorpath']) ? $this->conf['jsvalidatorpath'] : t3lib_extMgm::extRelPath($this->extKey) . 'res/validator.js') . '"></script>' . "\n";

		// Stylesheets in den Head einbinden.
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] .= ($this->conf['disablestylesheet']) ? '' : '<link rel="stylesheet" type="text/css" href="' . (($this->conf['stylesheetpath']) ? $this->conf['stylesheetpath'] : t3lib_extMgm::extRelPath($this->extKey) . 'res/datamints_feuser.css') . '" />' . "\n";

		// Userid ermitteln.
		$this->userId = $GLOBALS['TSFE']->fe_user->user['uid'];

		// Wenn nicht eingeloggt kann man auch nicht editieren!
		if ($this->conf['showtype'] == 'edit' && !$this->userId) return $this->pi_wrapInBaseClass('<div class="edit_error_no_login">' . $this->pi_getLL('edit_error_no_login') . '</div>');

		switch ($this->piVars['submit']) {
			case 'send':
				$content = $this->sendForm();
				break;
			case 'redirect':
				// Wenn Weiterleitung mit Login, dann wird erst eingeloggt und dann weitergeleitet.
				if ($this->conf['register.']['redirect']) {
					header('Location: ' . $this->pi_getPageLink($this->conf['register.']['redirect']) . '?' . $this->makeHiddenParams());
					exit;
				} else {
					header('Location: ' . $this->pi_getPageLink($GLOBALS['TSFE']->id) . '?' . $this->makeHiddenParams());
					exit;
				}
				break;
			case 'doubleoptin':
				if ($this->makeDoubleOptIn()) {
					// Userid ermittln un Global definieren!
					$this->userId = intval($this->piVars['uid']);
					// Registrierungsemail schicken.
					$this->sendMail('registration');

					//if ($this->conf['register.']['autologin']) {
					//	// Weiterleitung mit Login. Zuerst auf die eigene Seite mit Login Parametern und dann auf das Weiterleitungsziel.
					//	header('Location: ' . $this->pi_getPageLink($GLOBALS['TSFE']->id) . '?' . $this->prefixId . '[submit]=redirect&logintype=login&pid=' . $this->storagePid . '&user=' . $this->piVars['username'] . '&pass=' . $this->piVars['password'] . $this->makeHiddenParams());
					//	exit;
					//}
					if ($this->conf['register.']['redirect']) {
						// Weiterleitung ohne Login.
						header('Location: ' . $this->pi_getPageLink($this->conf['register.']['redirect']) . '?' . $this->makeHiddenParams());
						exit;
					}
					$content = $this->pi_getLL('doubleoptin_success');
				} else {
					$content = $this->pi_getLL('doubleoptin_failure');
				}
				break;
			default:
				$content = $this->showForm();
				break;
		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Bereitet die übergebenen Daten für den Import in die Datenbank vor, und führt diesen, wenn es keine Fehler gab, aus.
	 *
	 * @return	string		$content
	 */
	function sendForm() {
		// Jedes Element trimmen.
		foreach ($this->piVars as $key => $value) {
			if (!is_array($value)) {
				$this->piVars[$key] = trim($value);
			}
		}

		// Überprüfen ob Datenbankeinträge mit den übergebenen Daten übereinstimmen.
		$uniqueCheck = $this->uniqueCheckForm();
		// Eine Validierung durchführen über alle Felder die eine gesonderte Konfigurtion bekommen haben.
		$validCheck = $this->validateForm();
		// Überprüfen ob in allen benötigten Feldern etwas drinn steht.
		$requireCheck = $this->requireCheckForm();
		// Wenn bei der Validierung ein Feld nicht den Anforderungen entspricht noch einmal die Form anzeigen und entsprechende Felder markieren.
		$valueCheck = array_merge((Array)$uniqueCheck, (Array)$validCheck, (Array)$requireCheck);
		if (count($valueCheck) > 0) {
			$content = $this->showForm($valueCheck);
			return $content;
		}

		// Wenn der Bearbeitungsmodus, die Zielseite, und der User stimmen, dann wird in die Datenbank geschrieben.
		if ($this->piVars['submitmode'] == $this->conf['showtype'] && intval($this->piVars['pageid']) == $GLOBALS['TSFE']->id && intval($this->piVars['userid']) == $this->userId) {
			// Sonderfälle!
			$usedFields = explode(',', str_replace(' ', '', $this->conf['usedfields']));
			foreach ($usedFields as $fieldName) {
				if ($this->feUsersTca['columns'][$fieldName]) {
					// Ist das Feld schon gesäubert worden (MySQL, PHP, HTML, ...).
					$isChecked = false;

					// Passwordfelder behandeln.
					if (strpos($this->feUsersTca['columns'][$fieldName]['config']['eval'], 'password') !== false) {
						// Password generieren und verschlüsseln je nach Einstellung.
						$arrUpdate[$fieldName] = $this->generatePassword($this->piVars[$fieldName]);
						// Wenn kein Password übergeben wurde auch keins schreiben.
						if ($arrUpdate[$fieldName] == '') {
							unset($arrUpdate[$fieldName]);
						}
						$isChecked = true;
					}
					// Bildfelder behandeln.
					if ($this->feUsersTca['columns'][$fieldName]['config']['internal_type'] == 'file' && ($_FILES[$this->prefixId]['type'][$fieldName] || $this->piVars[$fieldName . '_delete'])) {
						// Das Bild hochladen oder löschen. Gibt einen Fehlerstring zurück falls ein Fehler auftritt. $arrUpdate wird per Referenz übergeben und innerhalb der Funktion geändert!
						$valueCheck[$fieldName] = $this->saveDeleteImage($fieldName, $arrUpdate);
						if ($valueCheck[$fieldName]) {
							$content = $this->showForm($valueCheck);
							return $content;
						}
						$isChecked = true;
					}
					// Checkboxen behandeln.
					if ($this->feUsersTca['columns'][$fieldName]['config']['type'] == 'check' && !$this->piVars[$fieldName]) {
						$arrUpdate[$fieldName] = '0';
					}
					// Datumsfelder behandeln.
					if (strpos($this->feUsersTca['columns'][$fieldName]['config']['eval'], 'date') !== false) {
						$arrUpdate[$fieldName] = strtotime($this->piVars[$fieldName]);
						$isChecked = true;
					}
					// Multiple Selectboxen.
					if ($this->feUsersTca['columns'][$fieldName]['config']['type'] == 'select' && $this->feUsersTca['columns'][$fieldName]['config']['size'] > 1) {
						foreach ($this->piVars[$fieldName] as $key => $val) {
							$this->piVars[$fieldName][$key] = intval($val);
						}
						$arrUpdate[$fieldName] = implode(',', $this->piVars[$fieldName]);
						$isChecked = true;
					}

					// Wenn noch nicht gesäubert dann nachholen!
					if (!$isChecked && isset($this->piVars[$fieldName])) {
						// Typ ermitteln und anhand dessen das Feld säubern.
						$type = $this->feUsersTca['columns'][$fieldName]['config']['type'];
						$size = $this->feUsersTca['columns'][$fieldName]['config']['size'];
						if ($type == 'check' || ($type == 'select' && $size == 1)) {
							// Wenn eine Checkbox oder eine einfache Selectbox, dann darf nur eine Zahl kommen!
							$arrUpdate[$fieldName] = intval($this->piVars[$fieldName]);
						}
						// Ansonsten Standardsäuberung.
						$arrUpdate[$fieldName] = strip_tags($this->piVars[$fieldName]);
					}
				}
			}

			// Zusatzfelder setzten, die nicht aus der Form übergeben wurden.
			$arrUpdate['tstamp'] = time();

			// Konvertiert alle möglichen Zeichen die für die Ausgabe angepasst wurden zurück.
			foreach ($arrUpdate as $key => $val) {
				$arrUpdate[$key] = htmlspecialchars_decode($val);
			}

			// Der User hat seine Daten editiert.
			if ($this->conf['showtype'] == 'edit') {
				// User editieren.
				$error = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $this->userId , $arrUpdate);
				if ($error == 1) {
					$GLOBALS['TSFE']->additionalHeaderData['refresh'] = '<meta http-equiv="refresh" content="2; url=/' . $this->pi_getPageLink($GLOBALS['TSFE']->id) . '" />';
					$content = $this->pi_getLL('edit_success');
				}
			}

			// Ein neuer User hat sich angemeldet.
			if ($this->conf['showtype'] == 'register') {
				// Wenn Double-Opt-In aktiviert ist, dann den User deaktivieren.
				if ($this->conf['register.']['doubleoptin']) {
					$arrUpdate['disable'] = '1';
				}
				// Standartkonfigurationen anwenden.
				$arrUpdate['pid'] = $this->storagePid;
				$arrUpdate['usergroup'] = ($arrUpdate['usergroup']) ? $arrUpdate['usergroup'] : $this->conf['register.']['usergroup'];
				$arrUpdate['crdate'] = $arrUpdate['tstamp'];

				// Extra Erstellungsdatumsfelder hinzufügen.
				foreach (explode(',', str_replace(' ', '', $this->conf['register.']['crdatefields'])) as $val) {
					if (trim($val)) {
						$arrUpdate[trim($val)] = $arrUpdate['crdate'];
					}
				}

				// User erstellen.
				$success = $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users', $arrUpdate);
				if ($success == 1) {
					// Userid ermittln un Global definieren!
					$this->userId = $GLOBALS['TYPO3_DB']->sql_insert_id();

					// Wenn nach der Registrierung weitergeleitet werden soll.
					if ($this->conf['register.']['doubleoptin']) {
						$hash = md5($this->userId . $arrUpdate['username'] . $arrUpdate['email'] . $arrUpdate['tstamp']);
						$pageLink = (strpos($this->pi_getPageLink($GLOBALS['TSFE']->id), '?') === false) ? $this->pi_getPageLink($GLOBALS['TSFE']->id) . '?' : $this->pi_getPageLink($GLOBALS['TSFE']->id) . '&';
						$extraMarkers = Array(
							'registerlink' => t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $pageLink . $this->prefixId . '%5Bsubmit%5D=doubleoptin&' . $this->prefixId . '%5Buid%5D=' . $this->userId . '&' . $this->prefixId . '%5Bhash%5D=' . $hash . $this->makeHiddenParams()
						);
						$this->sendMail('doubleoptin', $extraMarkers);
						$content = $this->pi_getLL('doubleoptin_sent');
						return $content;
					}

					// Registrierungsemail schicken.
					$this->sendMail('registration');

					if ($this->conf['register.']['autologin']) {
						// Weiterleitung mit Login. Zuerst auf die eigene Seite mit Login Parametern und dann auf das Weiterleitungsziel. Username wird per $arrUpdate übergeben, weil dieser Wert schon bereinigt ist.
						header('Location: ' . $this->pi_getPageLink($GLOBALS['TSFE']->id) . '?' . $this->prefixId . '[submit]=redirect&logintype=login&pid=' . $this->storagePid . '&user=' . $this->cleanHeaderUrlData($arrUpdate['username']) . '&pass=' . $this->cleanHeaderUrlData($this->piVars['password']) . $this->makeHiddenParams());
						exit;
					}
					if ($this->conf['register.']['redirect']) {
						// Weiterleitung ohne Login.
						header('Location: ' . $this->pi_getPageLink($this->conf['register.']['redirect']) . '?' . $this->makeHiddenParams());
						exit;
					}
					$content = $this->pi_getLL('register_success');
				}
			}
		}

		return $content;
	}

	/**
	 * Erstellt wenn gefordert ein Password, und verschlüsselt dieses, oder das übergebene, wenn es verschlüsselt werden soll.
	 *
	 * @param	string		$password
	 * @return	string		$password
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
		// Wenn "md5passwords" installiert ist wird wenn aktiviert, das Password md5 verschlüsselt.
		if (t3lib_extMgm::isLoaded('md5passwords')) {
			$arrConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['md5passwords']);
			if ($arrConf['activate'] == 1) {
				$password = md5($password);
			}
		}
		return $password;
	}

	/**
	 * Überprüft ob alle benötigten Felder mit Inhalten übergeben wurden.
	 *
	 * @return	array		$valueCheck
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
	 *
	 * @return	array		$valueCheck
	 */
	function validateForm() {
		// Alle ausgewählten Felder durchgehen.
		foreach ($this->conf['validate.'] as $fieldName => $validate) {
			$fieldName = trim($fieldName, '.');

			// Wenn der im TypoScript angegebene Feldname existiert,
			if ($this->feUsersTca['columns'][$fieldName]
					// ein Wert übergeben wurde,
					&& $this->piVars[$fieldName] !== ''
					// der Konfigurierte Modus stimmt,
					&& (!$validate['mode'] || $validate['mode'] == $this->conf['showtype'])
					// und das Feld überhaupt angezeigt wurde, dann validieren.
					&& in_array($fieldName, explode(',', str_replace(' ', '', $this->conf['usedfields'])))) {

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
									if (strlen($value) < $arrLength[0] || strlen($value) > $arrLength[1]) {
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

					case 'username':
						if (!preg_match('/^[^ ]*$/', $value)) {
							$valueCheck[$fieldName] = 'valid';
						}
						break;

					case 'custom':
						if ($validate['regexp']) {
							if (is_array($value)) {
								foreach ($value as $subValue) {
									if (!preg_match($validate['regexp'], $subValue)) {
										$valueCheck[$fieldName] = 'valid';
									}
								}
							} else {
								if (!preg_match($validate['regexp'], $value)) {
									$valueCheck[$fieldName] = 'valid';
								}
							}
						}
						if ($validate['length']) {
							$arrLength = explode(',', str_replace(' ', '', $validate['length']));
							if (is_array($value)) {
								if ($arrLength[1]) {
									// Wenn eine Maximallänge festgelegt wurde.
									if (count($value) < $arrLength[0] || count($value) > $arrLength[1]) {
										$valueCheck[$fieldName] = 'length';
									}
								} else {
									// Wenn nur eine Minimallänge festgelegt wurde.
									if (count($value) < $arrLength[0]) {
										$valueCheck[$fieldName] = 'length';
									}
								}
							} else {
								if ($arrLength[1]) {
									// Wenn eine Maximallänge festgelegt wurde.
									if (strlen($value) < $arrLength[0] || strlen($value) > $arrLength[1]) {
										$valueCheck[$fieldName] = 'length';
									}
								} else {
									// Wenn nur eine Minimallänge festgelegt wurde.
									if (strlen($value) < $arrLength[0]) {
										$valueCheck[$fieldName] = 'length';
									}
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
	 * Überprüft die übergebenen Inhalte, bei bestimmten Feldern, ob diese in der Datenbank schon vorhanden sind.
	 *
	 * @return	array		$valueCheck
	 */
	function uniqueCheckForm() {
		// Check unique Fields.
		$uniqueFields = explode(',', str_replace(' ', '', $this->conf['uniquefields']));
		// Wenn User eingeloggt, dann den eigenen Datensatz nicht durchsuchen.
		if ($this->conf['showtype'] == 'edit' && $this->userId) {
			$where = ' AND uid <> ' . $this->userId;
		}
		foreach ($uniqueFields as $fieldName) {
			if (trim(strip_tags($this->piVars[$fieldName]))) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid) as count', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND ' . $fieldName . ' = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(strip_tags($this->piVars[$fieldName], 'fe_users')) . $where . ' AND deleted = 0', '', '');
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				if ($row['count'] >= 1) {
					$valueCheck[$fieldName] = 'unique';
				}
			}
		}
		return $valueCheck;
	}

	/**
	 * The saveDeleteImage method is used to update or delete an image of an address
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrUpdate // Call by reference Array mit allen zu updatenden Daten.
	 * @return	string		$content
	 */
	function saveDeleteImage($fieldName, &$arrUpdate) {
		// Verzeichniss ermitteln.
		$uploadFolder = $this->feUsersTca['columns'][$fieldName]['config']['uploadfolder'];
		if (substr($uploadFolder, -1) != '/') {
			$uploadFolder = $uploadFolder . '/';
		}

		// Bild löschen und überprüfen ob das Bild auch wirklich existiert.
		if ($this->piVars[$fieldName . '_delete']) {
			$arrUpdate[$fieldName] = '';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fieldName, 'fe_users', 'uid = ' . $this->userId , '', '');
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$imagePath = t3lib_div::getFileAbsFileName($uploadFolder . $row[$fieldName]);
			if ($imagePath && file_exists($imagePath)) {
				unlink($imagePath);
			}
			return '';
		}

		// Wenn die Datei zu groß ist.
		$maxSize = $this->feUsersTca['columns'][$fieldName]['config']['max_size'];
		if ($maxSize && $_FILES[$this->prefixId]['size'][$fieldName] > $maxSize) {
			// Konfigurierte maximale Dateigröße überschritten.
			return 'size';
		} else if ($_FILES[$this->prefixId]['error'][$fieldName] == '2') {
			// Der Upload war nicht vollständig, da Datei zu groß (Zeitüberschreitung).
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
			// Bildname generieren.
			$fileName = preg_replace("/[^a-zA-Z0-9]/", '', $this->piVars['username']) . '_' . time() . $imageType;
			// Kompletter Bildpfad.
			$uploadFile = $uploadFolder . $fileName;

			// Bild verschieben, und anschließend den neuen Bildnamen in die Datenbank schreiben.
			if (move_uploaded_file($_FILES[$this->prefixId]['tmp_name'][$fieldName], $uploadFile)) {
				chmod($uploadFile, 0644);
				$arrUpdate[$fieldName] = $fileName;
				// Wenn Das Bild erfolgreich hochgeladen wurde, nichts zurückgeben.
				return '';
			}
		}

		return 'upload';
	}

	/**
	 * Sendet die E-Mails mit dem übergebenen Template und falls angegeben, auch mit den extra Markern.
	 *
	 * @param	string		$templatePart
	 * @param	array		$extraMarkers
	 * @return	void
	 */
	function sendMail($templatePart, $extraMarkers = Array()) {
		// Userdaten holen.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid = ' . $this->userId , '', '');
		$dataArray = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$markerArray = array_merge((Array)$dataArray, (Array)$extraMarkers);
		foreach ($markerArray as $key => $val) {
			if (!$this->check_utf8($val)) {
				$markerArray[$key] = utf8_encode($val);
			}
		}
		// Template holen.
		if ($this->conf['register.']['emailtemplate']) {
			$templateFile = $this->conf['register.']['emailtemplate'];
		} else {
			$templateFile = 'typo3conf/ext/datamints_feuser/res/datamints_feuser_mail.html';
		}
		// Template laden.
		$template = utf8_encode($this->cObj->fileResource($templateFile));
		$template = $this->cObj->getSubpart($template, '###' . strtoupper($templatePart) . '###');
		$template = $this->cObj->substituteMarkerArray($template, $markerArray, '###|###', 1);
		// Betreff ermitteln und aus dem E-Mail Content entfernen.
		$subject = trim($this->cObj->getSubpart($template, '###SUBJECT###'));
		$template = $this->cObj->substituteSubpart($template, '###SUBJECT###', '');

		// Restlichen Content wieder zusammenfügen.
		if ($this->conf['register.']['mailtype'] == 'html') {
			$mailtype = 'text/html';
		} else {
			$mailtype = 'text/plain';
			$template = trim(strip_tags($template));
		}

		// Zusätzliche Header User-Mail.
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: ' . $mailtype . '; charset=utf-8' . "\r\n";
		$header .= 'From: ' . $this->conf['register.']['sendername'] . ' <' . $this->conf['register.']['sendermail'] . '>' . "\r\n";
		$header .= 'X-Mailer: PHP/' . phpversion();
		// Verschicke User-Mail.
		mail($dataArray['name'] . ' <' . $dataArray['email'] . '>', $subject, $template, $header);
		// Verschicke Admin-Mail.
		if ($this->conf['register.']['adminname'] && $this->conf['register.']['adminmail'] && $templatePart != 'doubleoptin') {
			// Template laden.
			$template = $this->cObj->fileResource($templateFile);
			$template = $this->cObj->getSubpart($template, '###ADMINNOTIFICATION###');
			$template = $this->cObj->substituteMarkerArray($template, $markerArray, '###|###', 1);
			// Betreff ermitteln und aus dem E-Mail Content entfernen.
			$subject = trim($this->cObj->getSubpart($template, '###SUBJECT###'));
			$template = $this->cObj->substituteSubpart($template, '###SUBJECT###', '');

			// Restlichen Content wieder zusammenfügen.
			if ($this->conf['register.']['mailtype'] == 'html') {
				$mailtype = 'text/html';
			} else {
				$mailtype = 'text/plain';
				$template = trim(strip_tags($template));
			}
			mail($this->conf['register.']['adminname'] . ' <' . $this->conf['register.']['adminmail'] . '>', $subject, $template, $header);
		}
	}

	/**
	 * Überprüft ob der Double-Opt-In gültig ist und aktiviert den User.
	 *
	 * @return	boolean
	 */
	function makeDoubleOptIn() {
		// Userdaten holen.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, username, email, tstamp', 'fe_users', 'uid = ' . intval($this->piVars['uid']) , '', '');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$hash = md5($row['uid'] . $row['username'] . $row['email'] . $row['tstamp']);
		if ($this->piVars['hash'] == $hash) {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . intval($this->piVars['uid']) , Array('tstamp' => time(), 'disable' => '0'));
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Gibt alle im Backend definierten Felder (TypoScipt/Flexform) formatiert und der Anzeigeart entsprechend aus.
	 *
	 * @param	array		$valueCheck
	 * @return	string		$content
	 */
	function showForm($valueCheck = Array()) {
		// Beim editieren der Userdaten, die Felder vorausfüllen.
		if ($this->conf['showtype'] == 'edit') {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid = ' . $this->userId , '', '');
            $arrCurrentData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}

		// Wenn das Formular schon einmal abgesendet wurde aber ein Fehler auftrat, dann die bereits vom User übertragenen Userdaten vorausfüllen.
		if ($this->piVars) {
			foreach ($this->piVars as $key => $val) {
				$this->piVars[$key] = strip_tags($val);
			}
			$arrCurrentData = array_merge((Array)$arrCurrentData, (Array)$this->piVars);
		}

		// Konvertiert alle möglichen Zeichen der Ausgabe, die stören könnten (XSS).
		foreach ($arrCurrentData as $key => $val) {
			$arrCurrentData[$key] = htmlspecialchars($val);
		}

		// Ein Array erzeugen, mit allen zu benutztenden Feldern.
		$arrUsedFields = explode(',', str_replace(' ', '', $this->conf['usedfields']));

		// Seite, die den Request entgegennimmt (TypoLink).
		$requestLink = $this->pi_getPageLink($this->conf['requestpid']);
		if (!$this->conf['requestpid']) {
			// Wenn keine Seite per TypoScript angegeben ist, wird die aktuelle Seite verwendet.
			$requestLink = $this->pi_getPageLink($GLOBALS['TSFE']->id);
		}

		// ID zähler für Items und Fieldsets.
		$iItem = 1;
		$iFieldset = 1;
		$iInfoItem = 1;

		// Formular start.
		$content = '<form id="' . $this->extKey . '_' . $this->contentUid . '_form" name="' . $this->prefixId . '" action="' . $requestLink . '" method="post" enctype="multipart/form-data"><fieldset class="form_fieldset_' . $iFieldset . '">';

		// Wenn eine Lgende für das erste Fieldset definiert wurde, diese ausgeben.
		if ($this->conf['separatorheads.'][$iFieldset]) {
			$content .= '<legend class="form_legend_' . $iFieldset . '">' . $this->conf['separatorheads.'][$iFieldset] . '</legend>';
		}

		// Alle ausgewählten Felder durchgehen.
		foreach ($arrUsedFields as $fieldName) {
			// Wenn das im Flexform ausgewählte Feld existiert, dann dieses Feld ausgeben.
			if ($this->feUsersTca['columns'][$fieldName]) {
				// Form Item Anfang.
				$content .= '<div id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $this->feUsersTca['columns'][$fieldName]['config']['type'] . '">';

				// Label schreiben.
				$label = $this->getLabel($fieldName);
				$content .= '<label for="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '">' . $label . $this->checkIfRequired($fieldName) . '</label>';

				switch ($this->feUsersTca['columns'][$fieldName]['config']['type']) {

					case 'input':
						if (strpos($this->feUsersTca['columns'][$fieldName]['config']['eval'], 'date') !== false) {
							// Datumsfeld.
							if ($arrCurrentData[$fieldName] != 0) {
								// Timestamp zu "tt.mm.jjjj" machen.
								$datum = strftime('%d.%m.%Y', $arrCurrentData[$fieldName]);
                            }
							$content .= '<input type="text" id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']" value="' . $datum . '" />';
							break;
						}
						if (strpos($this->feUsersTca['columns'][$fieldName]['config']['eval'], 'password') !== false) {
							// Passwordfeld.
							$content .= '<input type="password" id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']" value="" />';
							$content .= '<input type="password" id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '_rep" name="' . $this->prefixId . '[' . $fieldName . '_rep]" value="" />';
							break;
						}
						$readOnly = ($this->feUsersTca['columns'][$fieldName]['config']['readOnly'] == 1) ? ' readonly="readonly"' : '';
						// Normales Inputfeld.
						$content .= '<input type="text" id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']" value="' . $arrCurrentData[$fieldName] . '"' . $readOnly . ' />';
						break;

					case 'text':
						$readOnly = ($this->feUsersTca['columns'][$fieldName]['config']['readOnly'] == 1) ? ' readonly="readonly"' : '';
						// Textarea.
						$content .= '<textarea id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']" rows="2" cols="42"' . $readOnly . '>' . $arrCurrentData[$fieldName] . '</textarea>';
						break;

					case 'check':
						$checked = ($arrCurrentData[$fieldName] == 1) ? ' checked="checked"' : '';
						$content .= '<input type="checkbox" id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']" value="1"' . $checked . ' />';
						break;
/*
					case 'radio':
						for ($j = 0; $j < count($this->feUsersTca['columns'][$fieldName]['config']['items']); $j++) {
							$checked = ($arrCurrentData[$fieldName] == $this->feUsersTca['columns'][$fieldName]['config']['items'][$j][1]) ? ' checked="checked"' : '';
							$content .= '<input type="radio" id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '_' . $j . '" name="' . $this->prefixId . '[' . $fieldName . ']" value="' . $this->feUsersTca['columns'][$fieldName]['config']['items'][$j][1] . '"' . $checked . ' class="radiobutton" />';
							$content .= '<label class="radio_label" for="' . $this->prefixId . '_' . $fieldName . '_' . $j . '">';
							$content .= $this->getLabel($this->feUsersTca['columns'][$fieldName]['config']['items'][$j][0]);
							$content .= '</label>';
						}
						break;
*/
					case 'select':
						// Optionlist erstellen.
						$optionlist = '';
						// Select-Item aus Konfigurtion holen.
						$countSelectfields = count($this->feUsersTca['columns'][$fieldName]['config']['items']);
						for ($j = 0; $j < $countSelectfields; $j++) {
							//$selected = ($arrCurrentData[$fieldName] == $j) ? ' selected="selected"' : '';
							$selected = (strpos($arrCurrentData[$fieldName], $j) !== false || in_array($j, $arrCurrentData[$fieldName])) ? ' selected="selected"' : '';
							$optionlist .= '<option value="' . $this->feUsersTca['columns'][$fieldName]['config']['items'][$j][1] . '"' . $selected . '>' . $this->getLabel($this->feUsersTca['columns'][$fieldName]['config']['items'][$j][0]) . '</option>';
						}
						// Wenn Tabelle angegeben zusätzlich aus DB holen.
						if ($this->feUsersTca['columns'][$fieldName]['config']['foreign_table']) {
							// Select-Items aus DB holen.
							$tab = $this->feUsersTca['columns'][$fieldName]['config']['foreign_table'];
							$sel = 'uid, ' . $GLOBALS['TCA'][$tab]['ctrl']['label'];
							$whr = $this->feUsersTca['columns'][$fieldName]['config']['foreign_table_where'];
							// Wenn OrderBy ganz vorne in $whr steht, dann muss eine 1 davor plaziert werden, da sonst die Abfrage ungültig ist.
							$whr = (strtolower(substr(trim($whr), 0, 8)) == 'order by') ? '1 ' . $whr : $whr;
							$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($sel , $tab, $whr);
							while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
								//$selected = ($arrCurrentData[$fieldName] == $row['uid']) ? ' selected="selected"' : '';
								$selected = (strpos($arrCurrentData[$fieldName], $row['uid']) !== false || in_array($row['uid'], $arrCurrentData[$fieldName])) ? ' selected="selected"' : '';
								$optionlist .= '<option value="' . $row['uid'] . '"' . $selected . '>' . $row[$GLOBALS['TCA'][$tab]['ctrl']['label']] . '</option>';
							}
						}
						// Einzeiliges Select (Dropdown).
						if ($this->feUsersTca['columns'][$fieldName]['config']['size'] == 1) {
							$content .= '<select id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '" name=' . $this->prefixId . '[' . $fieldName . ']">';
							$content .= $optionlist;
							$content .= '</select>';
						} else {
							$content .= '<select id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '" name=' . $this->prefixId . '[' . $fieldName . '][]" size="' . $this->feUsersTca['columns'][$fieldName]['config']['size'] . '" multiple="multiple">';
							$content .= $optionlist;
							$content .= '</select>';
						}
						break;

					case 'group':
						// GROUP (z.B. externe Tabellen).
						// Wenn es sich um den "internal_type" FILE handelt && es ein Bild ist, dann ein Vorschaubild erstellen und ein Fiel-Inputfeld anzeigen.
						if ($this->feUsersTca['columns'][$fieldName]['config']['internal_type'] == 'file') {
							// Verzeichniss ermitteln.
							$uploadFolder = $this->feUsersTca['columns'][$fieldName]['config']['uploadfolder'];
							if (substr($uploadFolder, -1) != '/') {
								$uploadFolder = $uploadFolder . '/';
							}
							// Breite ermitteln.
							$imageWidth = $this->conf['image.']['maxwidth'];
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
							if ($image) {
								$content .= '<div class="image_preview">' . $image . '</div>';
							}

							// Wenn kein Bild vorhanden ist, das Upload-Feld anzeigen.
							if ($arrCurrentData[$fieldName] == '') {
								$content .= '<input type="file" id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . ']" />';
							} else {
								$content .= '<div class="image_delete"><input type="checkbox" id="' . $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $fieldName . '_delete]" />' . $this->pi_getLL('image_delete') . '</div>';
							}

						}
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
				$content .= '</fieldset><fieldset class="form_fieldset_' . $iFieldset . '">';
				// Wenn eine Lgende für das Fieldset definiert wurde, diese ausgeben.
				if ($this->conf['separatorheads.'][$iFieldset]) {
					$content .= '<legend class="form_legend_' . $iFieldset . '">' . $this->conf['separatorheads.'][$iFieldset] . '</legend>';
				}
			} elseif ($fieldName == '--infoitem--') {
				if ($this->conf['infoitems.'][$iInfoItem]) {
					$content .= '<div class="form_infoitem_' . $iInfoItem . '">' . $this->conf['infoitems.'][$iInfoItem] . '</div>';
				}
				$iInfoItem++;
			}

		}

		// UserId, PageId und Modus anhängen.
		$content .= '<input type="hidden" name="' . $this->prefixId . '[submit]" value="send" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[userid]" value="' . $this->userId . '" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[pageid]" value="' . $GLOBALS['TSFE']->id . '" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[submitmode]" value="' . $this->conf['showtype'] . '" />';
		$content .= $this->makeHiddenFields();
		// Submitbutton.
		$content .= '<div id="' . $this->extKey . '_' . $this->contentUid . '_submit_wrapper" class="form_item form_item_' . $iItem . ' form_type_submit"><input id="' . $this->extKey . '_' . $this->contentUid . '_submit" type="submit" value="' . $this->pi_getLL('submit_' . $this->conf['showtype']) . '"/></div>';

		$content .= '</fieldset>';
		$content .= '</form>';

		return $content;

	}

	/**
	 * Erstellt Hidden Fields für vordefinierte Parameter die übergeben wurden.
	 *
	 * @return	string		$content
	 */
	function makeHiddenFields() {
		$content = '';
		$hiddenParams = explode(',', str_replace(' ', '', $this->conf['hiddenparams']));
		foreach ($hiddenParams as $paramName) {
			if ($_REQUEST[$paramName]) {
				$content .= '<input type="hidden" name="' . $paramName . '" value="' . strip_tags($_REQUEST[$paramName]) . '" />';
			}
		}
		return $content;
	}

	/**
	 * Erstellt GET-Parameter für vordefinierte Parameter die übergeben wurden.
	 *
	 * @return	string		$content
	 */
	function makeHiddenParams() {
		$content = '';
		$hiddenParams = explode(',', str_replace(' ', '', $this->conf['hiddenparams']));
		foreach ($hiddenParams as $paramName) {
			//if (strpos($paramName, '[') !== false) {
			//	$paramName = strstr($paramName, '[');
			//	$paramName = substr($paramName, 0, strpos($paramName, ']'));
			//}
			if ($_REQUEST[$paramName]) {
				$content .= '&' . urlencode($paramName) . '=' . $this->cleanHeaderUrlData($_REQUEST[$paramName]);
			}
		}
		return $content;
	}

	/**
	 * Konvertiert einen String um ihn in der PHP Funktion header nutzen zu können.
	 *
	 * @param	string		$data
	 * @return	string		$data
	 */
	function cleanHeaderUrlData($data) {
		$data = urlencode(strip_tags(preg_replace("/[\r\n]/", '', $data)));
		return $data;
	}

	/**
	 * Überprüft ob das übergebene Feld benötigt wird um erfolgreich zu speichern.
	 *
	 * @param	string		$fieldName
	 * @return	string
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
	 * Ermittelt ein bestimmtes Label aufgrund des im TCA gespeicherten Languagestrings, des Datenbankfeldnamens oder gibt einfach den übergeben Wert wieder aus, wenn nichts gefunden wurde.
	 *
	 * @param	string		$fieldName
	 * @return	string		$label
	 */
	function getLabel($fieldName) {
		if (strpos($fieldName, 'LLL:') === false) {
			// Label aus der Konfiguration holen basierend auf dem Datenbankfeldnamen.
			$label = $this->pi_getLL($fieldName);
			if ($label) {
				// Das Label zurückliefern.
				return $label;
			}
			// LanguageString ermitteln.
			$languageString = $this->feUsersTca['columns'][$fieldName]['label'];
		} else {
			$languageString = $fieldName;
		}
		// Standard Sprache.
		$defaultLanguage = $GLOBALS['TSFE']->config['config']['language'];
		if (!$defaultLanguage) {
			$defaultLanguage = 'default';
		}
		// Languagekey ermitteln z.B. ("LLL:EXT:lang/locallang_general.php:LGL.starttime" => "LGL.starttime").
		$languageKey = substr($languageString, strripos($languageString, ':') + 1);
		// Languagefile ermitteln z.B. ("LLL:EXT:lang/locallang_general.php:LGL.starttime" => "EXT:lang/locallang_general.php").
		$languageFilePath = substr($languageString, 4, strripos($languageString, ':') - 4);
		// LanguageFile laden.
		$languageFile = $GLOBALS['TSFE']->readLLfile($languageFilePath);
		// Das Label zurückliefern.
		$label = $languageFile[$defaultLanguage][$languageKey];

		if ($label) {
			// Das Label zurückliefern.
			return $label;
		}
		// Wenn gar nichts gefunden wurde den übergebenen Wert wieder zurückliefern.
		return $fieldName;
	}

	/**
	 * Holt Konfigurationen aus der Flexform (Tab-bedingt) und ersetzt diese pro Konfiguration in der TypoScript Konfiguration.
	 *
	 * @return	void
	 * @global	$this->conf
	 */
	function getConfiguration() {
		// Extension configuration holen.
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		// Alle Tabs durchgehn.
		foreach ($this->cObj->data['pi_flexform']['data'] as $tabKey => $val) {
			$this->readFlexformMainTab($this->cObj->data['pi_flexform'], $conf, $tabKey);
		}
		// Alle gesammelten Konfigurationen in $this->conf übertragen.
		foreach ($conf as $key => $val) {
			if (is_array($val) && $this->extConf['useIRRE']) {
				// Wenn IRRE Konfiguration übergeben wurde und in der Extension Konfiguration gesetzt ist...
				$this->conf[$key] = $this->array_merge_replace_recursive($this->conf[$key], $val);
			} else {
				// Alle anderen Konfigurationen...
				$this->setFlexformConfiguration($key, $val);
			}
		}
	}

	/**
	 * Parsd das Flexform Konfigurations Array und schreibt alle Werte in $conf.
	 *
	 * @param	array		$flexData
	 * @param	array		$conf
	 * @param	string		$sType
	 */
	function readFlexformMainTab($flexData, &$conf, $sTab) {
		 if (is_array($flexData)) {
			 if (isset($flexData['data'][$sTab]['lDEF'])) {
				 $flexData = $flexData['data'][$sTab]['lDEF'];
			 }

			 foreach ($flexData as $key => $value) {
				 if (is_array($value['el']) && count($value['el']) > 0) {
					 foreach ($value['el'] as $ekey => $element) {
						 if (isset($element['vDEF'])) {
							 $conf[$ekey] = $element['vDEF'];
						 } else {
							 $this->readFlexformMainTab($element, $conf[$key][$ekey], $sTab);
						 }
					 }
				 } else {
					 $this->readFlexformMainTab($value['el'], $conf, $sTab);
				 }
				 if ($value['vDEF']) {
					 $conf[$key] = $value['vDEF'];
				 }
			 }
		 }
	 }

	/**
	 * Überschreibt eventuell vorhandene TypoScript Konfigurationen mit den Konfigurationen aus der Flexform.
	 *
	 * @param	string		$key
	 * @param	string		$value
	 * @return	void
	 */
	function setFlexformConfiguration($key, $value) {
		if (strpos($key, '.') !== false && $value) {
			$arrKey = explode('.', $key);
			for ($i = count($arrKey) - 1; $i >= 0; $i--) {
				$newValue = array();
				if ($i == count($arrKey) - 1) {
					$newValue[$arrKey[$i]] = $value;
				} else {
					$newValue[$arrKey[$i] . '.'] = $value;
				}
				$value = $newValue;
			}
			$this->conf = $this->array_merge_replace_recursive($this->conf, $value);
		} elseif ($value) {
			$this->conf[$key] = $value;
		}
	}

	/**
	 * Gibt die komplette Validierungskonfiguration für die JavaScript Frontendvalidierung zurück.
	 *
	 * @return	string		$configuration
	 */
	function getJSValidationConfiguration() {
		// Hier eine fertig generierte Konfiguration:
		// var config = new Array();
		// config['username'] = new Array();
		// config['username']['validation'] = new Array();
		// config['username']['validation']['type'] = 'username';
		// config['username']['valid'] = 'Der Benutzername darf keine Leerzeichen beinhalten!';
		// config['username']['required'] = 'Es muss ein Benutzername eingegeben werden!';
		// config['password'] = new Array();
		// config['password']['validation'] = new Array();
		// config['password']['validation']['type'] = 'password';
		// config['password']['equal'] = 'Es muss zwei mal das gleiche Passwort eingegeben werden!';
		// config['password']['validation']['size'] = '6';
		// config['password']['size'] = 'Das Passwort muss mindestens 6 Zeichen lang sein!';
		// config['password']['required'] = 'Es muss ein Passwort angegeben werden!';
		// var inputids = new Array('tx_datamintsfeuser_pi1_username', 'tx_datamintsfeuser_pi1_password_1', 'tx_datamintsfeuser_pi1_password_2');

		$configuration = "var config = new Array(); ";
		$arrValidationFields = Array();
		$usedFields = explode(',', str_replace(' ', '', $this->conf['usedfields']));
		$requiredFields = explode(',', str_replace(' ', '', $this->conf['requiredfields']));#
		// Bei jedem Durchgang der Schliefe wird die Konfiguration für ein Datenbankfeld geschrieben. Ausnahmen sind hierbei Passwordfelder.
		// Gleichzeitig werden die ID's der Felder in ein Array geschrieben und am Ende zusammen gesetzt "inputids".
		foreach ($usedFields as $fieldName) {
			if ($this->feUsersTca['columns'][$fieldName] && (is_array($this->conf['validate.'][$fieldName . '.']) || in_array($fieldName, $requiredFields))) {
					if ($this->conf['validate.'][$fieldName . '.']['type'] == 'password') {
						$arrValidationFields[] = $this->extKey . '_' . $this->contentUid . '_' . $fieldName;
						$arrValidationFields[] = $this->extKey . '_' . $this->contentUid . '_' . $fieldName . '_rep';
					} else {
						$arrValidationFields[] = $this->extKey . '_' . $this->contentUid . '_' . $fieldName;
					}
					$configuration .= "config['" . $fieldName . "'] = new Array(); ";
					if (is_array($this->conf['validate.'][$fieldName . '.'])) {
						$configuration .= "config['" . $fieldName . "']['validation'] = new Array(); ";
						// Da es mehrere Validierungskonfiguration pro Feld geben kann, muss hier jede einzeln durchgelaufen werden.
						foreach ($this->conf['validate.'][$fieldName . '.'] as $key => $val) {
							if ($key == 'length') {
								$configuration .= "config['" . $fieldName . "']['validation']['size'] = '" . str_replace("'", "\\'", $val) . "'; ";
								$configuration .= "config['" . $fieldName . "']['size'] = '" . str_replace("'", "\\'", $this->getLabel($fieldName . '_error_length')) . "'; ";
							} elseif ($key == 'regexp') {
								// Da In JavaScript die Regulären Ausdrücke nicht in einem String vorkommen dürfen diese entsprechen konvertieren (Slash am Anfang und am Ende).
								// Um Fehler im Regulären Ausdruck zu vermeiden, werden hier alle Slashes entfernt, "\/" wird debei nicht berücksichtigt!
								// Als erstes den hinteren Slash entfernen und den eventuell vorhandenen Modifier merken.
								$matches = array();
								if (preg_match("/\/[a-z]*$/", $val, $matches)) {
									$regexpEnd = substr($val, - strlen($matches[0]));
									$val = substr($val, 0, strlen($val) - strlen($matches[0]));
								} else {
									$regexpEnd = '/';
								}
								// Einen eventuell vorhandenen Slash am Anfang ebenfalls entfernen.
								$regexpStart = '/';
								if (preg_match("/^\//", $val)) {
									$val = substr($val, 1);
								}
								// Dann alle Slashes aus dem String entfernen, unter berücksichtigung von "\/"!
								$val = preg_replace('/([^\\\])\//', '$1', $val);
								$configuration .= "config['" . $fieldName . "']['validation']['" . $key . "'] = " . $regexpStart . $val . $regexpEnd . "; ";
							} else {
								$configuration .= "config['" . $fieldName . "']['validation']['" . $key . "'] = '" . str_replace("'", "\\'", $val) . "'; ";
							}
							if ($key == 'type' && $val == 'password') {
								$configuration .= "config['" . $fieldName . "']['equal'] = '" . str_replace("'", "\\'", $this->getLabel($fieldName . '_error_equal')) . "'; ";
							}
						}
						if ($this->conf['validate.'][$fieldName . '.']['type'] != 'password') {
							$configuration .= "config['" . $fieldName . "']['valid'] = '" . str_replace("'", "\\'", $this->getLabel($fieldName . '_error_valid')) . "'; ";
						}
					}
					if (in_array($fieldName, $requiredFields)) {
						$configuration .= "config['" . $fieldName . "']['required'] = '" . str_replace('\'', '\\\'', $this->getLabel($fieldName . '_error_required')) . "'; ";
					}
			}
		}
		$configuration .= "var inputids = new Array('" . implode("', '", $arrValidationFields) . "'); var contentid = " . $this->contentUid . ";";
		return $configuration;
	}

	/**
	 * Überschreibt eventuell vorhandene TCA Konfiguration mit TypoScript Konfiguration.
	 *
	 * @return	void
	 * @global	$this->feUsersTca
	 */
	function getFeUsersTca() {
		$GLOBALS['TSFE']->includeTCA();
		$this->feUsersTca = $GLOBALS['TCA']['fe_users'];
		if ($this->conf['fieldconfig.']) {
			$this->feUsersTca['columns'] = $this->array_merge_replace_recursive((Array)$this->feUsersTca['columns'], (Array)$this->deletePoint($this->conf['fieldconfig.']));
		}
	}

	/**
	 * Ermittelt die General Record Storage Pid bzw. den vom User festgelegten Userfolder.
	 *
	 * @return	void
	 * @global	$this->storagePid
	 */
	function getStoragePid() {
		$this->storagePid = $this->conf['register.']['userfolder'];
		if (!$this->storagePid) {
			$arrayRootPids = $GLOBALS['TSFE']->getStorageSiterootPids();
			$this->storagePid = $arrayRootPids['_STORAGE_PID'];
		}
	}

	/**
	 * Löscht den Punkt den Typo3 bei TypoScript-Variablen (Arrays) hinzufügt.
	 *
	 * @param	array		$array
	 * @return	array		$newArray
	 */
	function deletePoint($array) {
		// Neues Array erstellen um das alte Array nicht zu überschreiben.
		$newArray = Array();
		// Alle Elemente des Arrays durchgehen.
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				// Wenn der Inhalt des Elements ein Array ist, letztes Zeichen entfernen (Punkt).
				$newKey = substr($key, 0, -1);
				// Da das Array recursiv sein kann Funktion erneut ausführen.
				$newVal = $this->deletePoint($val);
			} else {
				// Wenn Element kein Array ist, dann einfach Key und Value übernehmen.
				$newKey = $key;
				$newVal = $val;
			}
			// Neues Array füllen.
			$newArray[$newKey] = $newVal;
		}
		return $newArray;
	}

	/**
	 * Merges any number of arrays of any dimensions, the later overwriting
	 * previous keys, unless the key is numeric, in whitch case, duplicated
	 * values will not be added.
	 *
	 * The arrays to be merged are passed as arguments to the function.
	 *
	 * @param	array		$array1
	 * @return	array		Resulting array, once all have been merged
	 */
	function array_merge_replace_recursive($array1) {
		// Holds all the arrays passed.
		$params = & func_get_args();
		// Merge all arrays on the first array.
		foreach ($params as $array) {
			foreach ($array as $key => $value) {
				// Numeric keyed values are added (unless already there).
				if (is_numeric($key) && !in_array($value, $array1)) {
					if (is_array($value)) {
						$array1[] = $this->array_merge_replace_recursive($array1[$key], $value);
					} else {
						$array1[] = $value;
					}
				// String keyed values are replaced.
				} else {
					if (isset($array1[$key]) && is_array($value) && is_array($array1[$key])) {
						$array1[$key] = $this->array_merge_replace_recursive($array1[$key], $value);
					} else {
						$array1[$key] = $value;
					}
				}
			}
		}
		return $array1;
	}

	/**
	 * Checks if a string is utf8 encoded or not.
	 *
	 * @param	string		$str
	 * @return	boolean
	 */
	function check_utf8($str) {
		$len = strlen($str);
		for($i = 0; $i < $len; $i++){
			$c = ord($str[$i]);
			if ($c > 128) {
				if (($c > 247)) return false;
				elseif ($c > 239) $bytes = 4;
				elseif ($c > 223) $bytes = 3;
				elseif ($c > 191) $bytes = 2;
				else return false;
				if (($i + $bytes) > $len) return false;
				while ($bytes > 1) {
					$i++;
					$b = ord($str[$i]);
					if ($b < 128 || $b > 191) return false;
					$bytes--;
				}
			}
		}
		return true;
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1.php']);
}

?>