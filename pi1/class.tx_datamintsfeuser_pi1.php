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
 *   92: class tx_datamintsfeuser_pi1 extends tslib_pibase
 *  116:     function main($content, $conf)
 *  175:     function sendForm()
 *  477:     function uniqueCheckForm()
 *  504:     function validateForm()
 *  676:     function requireCheckForm()
 *  729:     function generatePassword($fieldName)
 *  790:     function generatePasswordForMail($userId)
 *  809:     function checkPassword($submitedPassword, $originalPassword)
 *  865:     function saveDeleteImage($fieldName, &$arrUpdate)
 *  948:     function showMessageOutputRedirect($mode, $submode = '', $params = array())
 * 1016:     function userAutoLogin($username, $mode = '')
 * 1033:     function userRedirect($pageId = 0)
 * 1050:     function sendActivationMail($userId)
 * 1088:     function makeApprovalCheck($userId)
 * 1161:     function getApprovalTypes()
 * 1173:     function isAdminMail($approvalType)
 * 1183:     function setNotActivatedCookie($userId)
 * 1195:     function getNotActivatedUserArray($arrNotActivated = array())
 * 1231:     function sendMail($userId, $templatePart, $adminMail, $config, $extraMarkers = array(), $extraSuparts = array())
 * 1327:     function getTemplateSubpart($templatePart, $config, $markerArray = array())
 * 1355:     function getChangedForMail($arrNewData, $config)
 * 1394:     function showForm($valueCheck = array())
 * 1612:     function cleanSpecialFieldKey($fieldName)
 * 1627:     function showInput($fieldName, $arrCurrentData, $iItem)
 * 1666:     function showText($fieldName, $arrCurrentData)
 * 1682:     function showCheck($fieldName, $arrCurrentData)
 * 1698:     function showRadio($fieldName, $arrCurrentData)
 * 1728:     function showSelect($fieldName, $arrCurrentData)
 * 1781:     function showGroup($fieldName, $arrCurrentData)
 * 1864:     function makeHiddenFields()
 * 1881:     function makeHiddenParams()
 * 1899:     function cleanHeaderUrlData($data)
 * 1911:     function checkIfRequired($fieldName)
 * 1925:     function getLabel($fieldName)
 * 1960:     function getErrorLabel($fieldName, $valueCheck)
 * 1978:     function getConfiguration()
 * 2023:     function readFlexformTab($flexData, &$conf, $sTab)
 * 2057:     function setFlexformConfiguration($key, $value)
 * 2085:     function setIrreConfiguration()
 * 2214:     function getJSValidationConfiguration()
 * 2328:     function getFeUsersTca()
 * 2342:     function getStoragePid()
 * 2356:     function deletePointInArrayKey($array)
 * 2387:     function checkUtf8($str)
 * 2431:     function cleanArray($array)
 *
 *
 * TOTAL FUNCTIONS: 45
 *
 */

require_once(PATH_tslib . 'class.tslib_pibase.php');

/**
 * Plugin 'Frontend User Management' for the 'datamints_feuser' extension.
 *
 * @author	Bernhard Baumgartl <b.baumgartl@datamints.com>
 * @package	TYPO3
 * @subpackage	tx_datamintsfeuser
 */
class tx_datamintsfeuser_pi1 extends tslib_pibase {
	var $extKey = 'datamints_feuser';
	var $prefixId = 'tx_datamintsfeuser_pi1';
	var $scriptRelPath = 'pi1/class.tx_datamintsfeuser_pi1.php';
	var $pi_checkCHash = true;
	var $feUsersTca = array();
	var $storagePid = 0;
	var $contentUid = 0;
	var $conf = array();
	var $extConf = array();
	var $lang = array();
	var $userId = 0;
	var $arrUsedFields = array();
	var $arrRequiredFields = array();
	var $arrUniqueFields = array();
	var $arrHiddenParams = array();

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	string		The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;

		// Debug.
//		$GLOBALS['TSFE']->set_no_cache();
//		$GLOBALS['TYPO3_DB']->debugOutput = true;

		// ContentId ermitteln.
		$this->contentId = $this->cObj->data['uid'];

		// UserId ermitteln.
		$this->userId = $GLOBALS['TSFE']->fe_user->user['uid'];

		// Flexform und Configurationen laden.
		$this->pi_setPiVarDefaults();
		$this->pi_initPIflexForm();
		$this->getConfiguration();
		$this->getStoragePid();
		$this->getFeUsersTca();
		$this->pi_loadLL();

		// Stylesheets in den Head einbinden.
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . '[stylesheet]'] = ($this->conf['disablestylesheet']) ? '' : '<link rel="stylesheet" type="text/css" href="' . (($this->conf['stylesheetpath']) ? $this->conf['stylesheetpath'] : t3lib_extMgm::extRelPath($this->extKey) . 'res/datamints_feuser.css') . '" />';

		// Javascripts in den Head einbinden.
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . '[jsvalidator]'] = ($this->conf['disablejsvalidator']) ? '' : '<script type="text/javascript" src="' . (($this->conf['jsvalidatorpath']) ? $this->conf['jsvalidatorpath'] : t3lib_extMgm::extRelPath($this->extKey) . 'res/validator.min.js') . '"></script>';
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . '[jsvalidation][' . $this->contentId . ']'] = ($this->conf['disablejsconfig']) ? '' : '<script type="text/javascript">' . "\n/*<![CDATA[*/\n" . $this->getJSValidationConfiguration() . "\n/*]]>*/\n" . '</script>';

		// Wenn nicht eingeloggt kann man auch nicht editieren!
		if ($this->conf['showtype'] == 'edit' && !$this->userId) {
			return $this->pi_wrapInBaseClass($this->showMessageOutputRedirect('edit_error_no_login'));
		}

		switch ($this->piVars[$this->contentId]['submit']) {

			case 'send':
				$content = $this->sendForm();
				break;

			case 'approvalcheck':
				// Userid ermittln und Aktivierung durchfuehren.
				$userId = intval($this->piVars[$this->contentId]['uid']);
				$content = $this->makeApprovalCheck($userId);
				break;

			default:
				$content = $this->showForm();
				break;

		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Bereitet die uebergebenen Daten fuer den Import in die Datenbank vor, und fuehrt diesen, wenn es keine Fehler gab, aus.
	 *
	 * @return	string
	 */
	function sendForm() {
		$mode = '';
		$submode = '';
		$params = array();

		// Jedes Element trimmen.
		foreach ($this->piVars[$this->contentId] as $key => $value) {
			if (!is_array($value)) {
				$this->piVars[$this->contentId][$key] = trim($value);
			}
		}

		// Ueberpruefen ob Datenbankeintraege mit den uebergebenen Daten uebereinstimmen.
		$uniqueCheck = $this->uniqueCheckForm();

		// Eine Validierung durchfuehren ueber alle Felder die eine gesonderte Konfigurtion bekommen haben.
		$validCheck = $this->validateForm();

		// Ueberpruefen ob in allen benoetigten Feldern etwas drinn steht.
		$requireCheck = $this->requireCheckForm();

		// Wenn bei der Validierung ein Feld nicht den Anforderungen entspricht noch einmal die Form anzeigen und entsprechende Felder markieren.
		$valueCheck = array_merge((array)$uniqueCheck, (array)$validCheck, (array)$requireCheck);

		if (count($valueCheck) > 0) {
			return $this->showForm($valueCheck);
		}

		// Temporaeren Feldnamen fuer das 'Aktivierungslink zusenden' Feld erstellen.
		$fieldName = '--resendactivation--';

		// Wenn der User eine neue Aktivierungsmail beantragt hat.
		if ($this->piVars[$this->contentId][$this->cleanSpecialFieldKey($fieldName)] && in_array($fieldName, $this->arrUsedFields)) {
			$fieldName = $this->cleanSpecialFieldKey($fieldName);

			// Falls der Anzeigetyp "list" ist (Liste der im Cookie gespeicherten User), alle uebergebenen User ermitteln und fuer das erneute zusenden verwenden. Ansonsten die uebergebene E-Mail verwenden.
//			if ($this->conf['shownotactivated'] == 'list') {
//				$arrNotActivated = $this->getNotActivatedUserArray($this->piVars[$this->contentId][$fieldName]);
//				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tx_datamintsfeuser_approval_level', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND uid IN(' . implode(',', $arrNotActivated) . ') AND disable = 1 AND deleted = 0');
//			} else {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tx_datamintsfeuser_approval_level', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND email = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(strtolower($this->piVars[$this->contentId][$fieldName]), 'fe_users') . ' AND disable = 1 AND deleted = 0', '', '', '1');
//			}

//			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				// Genehmigungstypen aufsteigend sortiert ermitteln. Das ist nötig um das Level dem richtigen Typ zuordnen zu können.
				// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig lässt.
				$arrApprovalTypes = $this->getApprovalTypes();
				$approvalType = $arrApprovalTypes[count($arrApprovalTypes) - $row['tx_datamintsfeuser_approval_level']];

				// Ausgabe vorbereiten.
				$mode = $fieldName;

				// Fehler anzeigen, falls das naechste aktuelle Genehmigungsverfahren den Admin betrifft.
				$submode = 'failure';

				// Aktivierungsmail senden und Ausgabe anpassen.
				if ($approvalType && !$this->isAdminMail($approvalType)) {
					$this->sendActivationMail($row['uid']);
					$submode = 'sent';
				}
//			}

			return $this->showMessageOutputRedirect($mode, $submode);
		}

		// Wenn der Bearbeitungsmodus, die Zielseite, und der User stimmen, dann wird in die Datenbank geschrieben.
		if ($this->piVars[$this->contentId]['submitmode'] == $this->conf['showtype'] && $this->piVars[$this->contentId]['pageid'] == $GLOBALS['TSFE']->id && $this->piVars[$this->contentId]['userid'] == $this->userId) {
			// Sonderfaelle!
			foreach ($this->arrUsedFields as $fieldName) {
				if ($this->feUsersTca['columns'][$fieldName]) {
					$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

					// Ist das Feld schon gesaeubert worden (MySQL, PHP, HTML, ...).
					$isChecked = false;

					// Datumsfelder behandeln.
					if (strpos($fieldConfig['eval'], 'date') !== false) {
						$arrUpdate[$fieldName] = strtotime($this->piVars[$this->contentId][$fieldName]);
						$isChecked = true;
					}

					// Passwordfelder behandeln.
					if (strpos($fieldConfig['eval'], 'password') !== false) {
						// Password generieren und verschluesseln je nach Einstellung.
						$password = $this->generatePassword($fieldName);
						$arrUpdate[$fieldName] = $password['encrypted'];

						// Wenn kein Password uebergeben wurde auch keins schreiben.
						if (!$arrUpdate[$fieldName]) {
							unset($arrUpdate[$fieldName]);
						}

						$isChecked = true;
					}

					// Checkboxen behandeln.
					if ($fieldConfig['type'] == 'check') {
						$countCheckFields = count($fieldConfig['items']);

						// Mehrere Checkboxen oder eine Checkbox.
						if ($countCheckFields > 1) {
							$binString = '';

							for ($i = 0; $i < $countCheckFields; $i++) {
								if (in_array($i, $this->piVars[$this->contentId][$fieldName])) {
									$binString .= '1';
								} else {
									$binString .= '0';
								}
							}

							$arrUpdate[$fieldName] = bindec(strrev($binString));
						} else {
							if ($this->piVars[$this->contentId][$fieldName]) {
								$arrUpdate[$fieldName] = '1';
							} else {
								$arrUpdate[$fieldName] = '0';
							}
						}

						$isChecked = true;
					}

					// Multiple Selectboxen.
					if ($fieldConfig['type'] == 'select' && $fieldConfig['size'] > 1) {
						$arrCleanedValues = array();

						// Wenn nichts ausgewählt wurde, wird auch dieser Parameter nicht übergeben, daher zuerst überprüfen, ob etwas vorhanden ist.
						if($this->piVars[$this->contentId][$fieldName]){
							foreach ($this->piVars[$this->contentId][$fieldName] as $val) {
								$arrCleanedValues[] = intval($val);
							}
						}

						// TODO: maxitems wird noch nicht berücksichtigt!

						$arrUpdate[$fieldName] = implode(',', $arrCleanedValues);
						$isChecked = true;
					}

					// Group, Bildfelder behandeln.
					if ($fieldConfig['type'] == 'group' && $fieldConfig['internal_type'] == 'file' && ($_FILES[$this->prefixId]['type'][$this->contentId][$fieldName] || $this->piVars[$this->contentId][$fieldName . '_delete'])) {
						// Das Bild hochladen oder loeschen. Gibt einen Fehlerstring zurueck falls ein Fehler auftritt. $arrUpdate wird per Referenz uebergeben und innerhalb der Funktion geaendert!
						$valueCheck[$fieldName] = $this->saveDeleteImage($fieldName, $arrUpdate);

						if ($valueCheck[$fieldName]) {
							return $this->showForm($valueCheck);
						}

						$isChecked = true;
					}

					// Group, Multiple Checkboxen.
					if ($fieldConfig['type'] == 'group' && $fieldConfig['internal_type'] == 'db') {
						$arrCleanedValues = array();
						$arrAllowed = t3lib_div::trimExplode(',', $fieldConfig['allowed'], true);

						foreach ($arrAllowed as $table) {
							if ($GLOBALS['TCA'][$table]) {
								foreach ($this->piVars[$this->contentId][$fieldName] as $val) {
									if (preg_match('/^' . $table . '_[0-9]+$/', $val)) {
										$arrCleanedValues[] = $val;
									}
								}
							}
						}

						// Falls nur eine Tabelle im TCA angegeben ist, wird nur die uid gespeichert.
						if (count($arrAllowed) == 1) {
							foreach ($arrCleanedValues as $key => $val) {
								$arrCleanedValues[$key] = substr($val, strripos($val, '_') + 1);
							}
						}

						$arrUpdate[$fieldName] = implode(',', $arrCleanedValues);
						$isChecked = true;
					}

					// Wenn noch nicht gesaeubert dann nachholen!
					if (!$isChecked && isset($this->piVars[$this->contentId][$fieldName])) {
						// Groesse ermitteln und anhand dessen und des Feldtyps das Feld saeubern.
						$size = $fieldConfig['size'];

						// Wenn eine Checkbox oder eine einfache Selectbox, dann darf nur eine Zahl kommen!
						if ($type == 'check' || ($type == 'select' && $size == 1)) {
							$arrUpdate[$fieldName] = intval($this->piVars[$this->contentId][$fieldName]);
						}

						// Ansonsten Standardsaeuberung.
						$arrUpdate[$fieldName] = strip_tags($this->piVars[$this->contentId][$fieldName]);

						// Wenn E-Mail Feld, alle Zeichen zu kleinen Zeichen konvertieren.
						if ($fieldName == 'email') {
							$arrUpdate[$fieldName] = strtolower($arrUpdate[$fieldName]);
						}
					}
				}
			}

			// Zusatzfelder setzten, die nicht aus der Form uebergeben wurden.
			$arrUpdate['tstamp'] = time();

			// Konvertiert alle moeglichen Zeichen die fuer die Ausgabe angepasst wurden zurueck.
			foreach ($arrUpdate as $key => $val) {
				$arrUpdate[$key] = htmlspecialchars_decode($val);
			}

			// Temporaeren Feldnamen fuer das 'User loeschen' Feld erstellen.
			$fieldName = '--userdelete--';

			// Wenn der User geloescht werden soll.
			if ($this->piVars[$this->contentId][$this->cleanSpecialFieldKey($fieldName)] && in_array($fieldName, $this->arrUsedFields)) {
				$arrUpdate['deleted'] = '1';
			}

			// Der User hat seine Daten editiert.
			if ($this->conf['showtype'] == 'edit') {
				// User editieren.
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $this->userId , $arrUpdate);

				// User und Admin Benachrichtigung schicken, aber nur wenn etwas geaendert wurde.
				if ($this->conf['edit.']['sendusermail'] || $this->conf['edit.']['sendadminmail']) {
					$extraMarkers = $this->getChangedForMail($arrUpdate, $this->conf['edit.']);

					if ($this->conf['edit.']['sendadminmail'] && !isset ($extraMarkers['nothing_changed'])) {
						$this->sendMail($this->userId, 'edit', true, $this->conf['edit.'], $extraMarkers);
					}

					if ($this->conf['edit.']['sendusermail'] && !isset ($extraMarkers['nothing_changed'])) {
						$this->sendMail($this->userId, 'edit', false, $this->conf['edit.'], $extraMarkers);
					}
				}

				// Ausgabe vorbereiten.
				$mode = $this->conf['showtype'];
				$submode = 'success';

				// Wenn der User geloescht wurde, weiterleiten.
				if ($arrUpdate['deleted']) {
					$mode = 'userdelete';
				}
			}

			// Kopiert den Inhalt eines Feldes in ein anderes Feld.
			foreach ($this->conf['copyfields.'] as $fieldToCopy => $arrCopyToFields) {
				$fieldToCopy = rtrim($fieldToCopy, '.');

				// Wenn das Feld nich benutzt wird, abbrechen.
				if (!in_array($fieldToCopy, $this->arrUsedFields)) {
					continue;
				}

				foreach ($arrCopyToFields as $copyToField => $stdWrap) {
					$copyToField = rtrim($copyToField, '.');

					if (array_key_exists($copyToField, $this->feUsersTca['columns'])) {
						$cObj = t3lib_div::makeInstance('tslib_cObj');

						// Feldinhalt for dem Update zur Verfuegung stellen.
						$cObj->data = $GLOBALS['TSFE']->fe_user->user;

						$arrUpdate[$copyToField] = $cObj->stdWrap($arrUpdate[$fieldToCopy], $stdWrap);
					}
				}
			}

			// Ein neuer User hat sich angemeldet.
			if ($this->conf['showtype'] == 'register') {
				// Standartkonfigurationen anwenden.
				$arrUpdate['pid'] = $this->storagePid;
				$arrUpdate['usergroup'] = ($arrUpdate['usergroup']) ? $arrUpdate['usergroup'] : $this->conf['register.']['usergroup'];
				$arrUpdate['crdate'] = $arrUpdate['tstamp'];

				// Genehmigungstypen aufsteigend sortiert ermitteln. Das ist nötig um das Level dem richtigen Typ zuordnen zu können.
				// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig lässt.
				$arrApprovalTypes = $this->getApprovalTypes();
				$approvalType = $arrApprovalTypes[0];

				// Maximales Genehmigungslevel ermitteln (Double Opt In / Admin Approval).
				$arrUpdate['tx_datamintsfeuser_approval_level'] = count($arrApprovalTypes);

				// Wenn ein Genehmigungstyp aktiviert ist, dann den User deaktivieren.
				if ($arrUpdate['tx_datamintsfeuser_approval_level'] > 0) {
					$arrUpdate['disable'] = '1';
				}

				// User erstellen.
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users', $arrUpdate);

				// Userid ermittln un Global definieren!
				$userId = $GLOBALS['TYPO3_DB']->sql_insert_id();

				// Wenn nach der Registrierung weitergeleitet werden soll.
				if ($arrUpdate['tx_datamintsfeuser_approval_level'] > 0) {
					// Aktivierungsmail senden.
					$this->sendActivationMail($userId);

					// Ausgabe fuer gemischte Genehmigungstypen erstellen (z.B. erst adminapproval und dann doubleoptin).
					$mode = $approvalType;
					$submode = (array_shift($arrApprovalTypes)) ? implode('_', $arrApprovalTypes) : '';
					$submode .= ($submode) ? '_sent' : 'sent';
					$params = array('mode' => $this->conf['showtype']);
				} else {
					// Erstellt ein neues Passwort, falls Passwort generieren eingestellt ist. Das Passwort kannn dann ueber den Marker "###PASSWORD###" mit der Registrierungsmail gesendet werden.
					$extraMarkers = $this->generatePasswordForMail($userId);

					// Registrierungs E-Mail schicken.
					if ($this->conf['register.']['sendadminmail']) {
						$this->sendMail($userId, 'registration', true, $this->conf['register.']);
					}

					if ($this->conf['register.']['sendusermail']) {
						$this->sendMail($userId, 'registration', false, $this->conf['register.'], $extraMarkers);
					}

					$mode = $this->conf['showtype'];
					$submode = 'success';
					$params = array('username' => $arrUpdate['username']);
				}
			}
		}

		// Hook um weiter Userupdates zu machen.
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['sendForm_return'])) {
			$_params = array(
					'variables' => array(
							'userId' => ($userId) ? $userId : $this->userId,
							'showtype' => $this->conf['showtype'],
							'arrUpdate' => $arrUpdate,
							'arrUserData' => $GLOBALS['TSFE']->fe_user->user
						),
					'parameters' => array(
							'mode' => &$mode,
							'submode' => &$submode,
							'params' => &$params
						)
				);

			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['sendForm_return'] as $_funcRef) {
				t3lib_div::callUserFunction($_funcRef, $_params, $this);
			}
		}

		return $this->showMessageOutputRedirect($mode, $submode, $params);
	}

	/**
	 * Ueberprueft die uebergebenen Inhalte, bei bestimmten Feldern, ob diese in der Datenbank schon vorhanden sind.
	 *
	 * @return	array		$valueCheck
	 */
	function uniqueCheckForm() {
		$valueCheck = array();

		// Wenn User eingeloggt, dann den eigenen Datensatz nicht durchsuchen.
		if ($this->conf['showtype'] == 'edit' && $this->userId) {
			$where = ' AND uid <> ' . $this->userId;
		}

		foreach ($this->arrUniqueFields as $fieldName) {
			if ($this->piVars[$this->contentId][$fieldName]) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid) as count', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND ' . $fieldName . ' = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->piVars[$this->contentId][$fieldName], 'fe_users') . $where . ' AND deleted = 0');
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

				if ($row['count'] >= 1) {
					$valueCheck[$fieldName] = 'unique';
				}
			}
		}

		return $valueCheck;
	}

	/**
	 * Ueberprueft ob alle Validierungen eingehalten wurden.
	 *
	 * @return	array		$valueCheck
	 */
	function validateForm() {
		$valueCheck = array();

		// Alle ausgewaehlten Felder durchgehen.
		foreach ($this->arrUsedFields as $fieldName) {
			$fieldName = $this->cleanSpecialFieldKey($fieldName);
			$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

			$value = trim($this->piVars[$this->contentId][$fieldName]);
			$validate = $this->conf['validate.'][$fieldName . '.'];

			// Besonderes Feld das fest in der Extension verbaut ist (password_confirmation), und ueberprueft werden soll.
			if ($fieldName == 'passwordconfirmation' && $this->conf['showtype'] == 'edit' && $this->userId) {
				if (!$this->checkPassword($value, $GLOBALS['TSFE']->fe_user->user['password'])) {
					$valueCheck[$fieldName] = 'valid';
				}
			}

			// Besonderes Feld das fest in der Extension verbaut ist (resend_activation), und ueberprueft werden soll.
			if ($fieldName == 'resendactivation') {
				if ($value) {
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid) as count', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND (uid = ' . intval($value) . ' OR email = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(strtolower($value), 'fe_users') . ') AND disable = 1 AND deleted = 0');
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

					if ($row['count'] < 1) {
						$valueCheck[$fieldName] = 'valid';
					}
				}
			}

			// Wenn der im TypoScript angegebene Feldname nicht im TCa ist, dann naechstes Feld vornehmen.
			if (!$this->feUsersTca['columns'][$fieldName]) {
				continue;
			}

			// Wenn das Feld ueberhaupt nicht angezeigt wurde, dann naechstes Feld vornehmen.
			if (!in_array($fieldName, $this->arrUsedFields)) {
				continue;
			}

			// Wenn ein Modus fuer dieses Feld konfiguriert wurde, und der Konfigurierte Modus nicht mit dem Anzeigetyp uebereinstimmt, dann naechstes Feld vornehmen.
			if ($validate['mode'] && $validate['mode'] != $this->conf['showtype']) {
				continue;
			}

			// Wenn ueberhaupt kein Wert / Parameter uebergeben wurde, dann naechstes Feld vornehmen.
			if (!$value && !isset($value)) {
				continue;
			}

			// Wenn kein Inhalt im Parameter steht und wenn der Typ des Feldes nicht check, radio oder select ist, dann naechstes Feld vornehmen.
			if (!$value && !in_array($fieldConfig['type'], array('check', 'radio', 'select'))) {
				continue;
			}

			// Wenn ueberhaupt kein Parameter angekommen ist und wenn der Typ des Feldes check, radio oder select ist, dann naechstes Feld vornehmen.
			if (!isset($value) && in_array($fieldConfig['type'], array('check', 'radio', 'select'))) {
				continue;
			}

			// Ansonsten Feldvalidierung anhand des Validierungstyps vornehmen.
			switch ($validate['type']) {

				case 'password':
					$value_rep = $this->piVars[$this->contentId][$fieldName . '_rep'];
					$arrLength[0] = 6;

					if ($value == $value_rep) {
						if ($validate['length']) {
							$arrLength = t3lib_div::trimExplode(',', $validate['length']);

							if ($arrLength[1]) {
								// Wenn eine Maximallaenge festgelegt wurde.
								if (strlen($value) < $arrLength[0] || strlen($value) > $arrLength[1]) {
									$valueCheck[$fieldName] = 'length';
								}
							} else {
								// Wenn nur eine Minimallaenge festgelegt wurde.
								if (strlen($value) < $arrLength[0]) {
									$valueCheck[$fieldName] = 'length';
								}
							}
						} else {
							// Wenn nur eine Minimallaenge festgelegt wurde.
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

				case 'zero':
					if ($value == '0') {
						$valueCheck[$fieldName] = 'valid';
					}
					break;

				case 'emptystring':
					if ($value == '') {
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
						$arrLength = t3lib_div::trimExplode(',', $validate['length']);

						if (is_array($value)) {
							if ($arrLength[1]) {
								// Wenn eine Maximallaenge festgelegt wurde.
								if (count($value) < $arrLength[0] || count($value) > $arrLength[1]) {
									$valueCheck[$fieldName] = 'length';
								}
							} else {
								// Wenn nur eine Minimallaenge festgelegt wurde.
								if (count($value) < $arrLength[0]) {
									$valueCheck[$fieldName] = 'length';
								}
							}
						} else {
							if ($arrLength[1]) {
								// Wenn eine Maximallaenge festgelegt wurde.
								if (strlen($value) < $arrLength[0] || strlen($value) > $arrLength[1]) {
									$valueCheck[$fieldName] = 'length';
								}
							} else {
								// Wenn nur eine Minimallaenge festgelegt wurde.
								if (strlen($value) < $arrLength[0]) {
									$valueCheck[$fieldName] = 'length';
								}
							}
						}
					}
					break;

			}

		}

		return $valueCheck;
	}

	/**
	 * Ueberprueft ob alle benoetigten Felder mit Inhalten uebergeben wurden.
	 *
	 * @return	array		$valueCheck
	 */
	function requireCheckForm() {
		$valueCheck = array();

		// Geht alle benoetigten Felder durch und ermittelt fehlende.
		foreach ($this->arrRequiredFields as $fieldName) {
			$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

			// Ueberpruefen, ob das Feld ueberhaupt benoetigt wird.
			if (!in_array($fieldName, $this->arrUsedFields)) {
				continue;
			}

			// Ueberpruefen, ob ein Wert uebergeben wurde. Hierbei ist es wichtig um welchen Feldtyp es sich handelt.
			// Bei Feldern, die der Browser gar nicht als leere Variable sendet, wenn nichts ausgewaehlt wurde, wird ueberprueft ob ueberhaupt etwas angekommen ist "!isset()".
			// Bei den restlichen Felder schickt der Browser immer eine leere Variable mit, da langt es wenn man ueberprueft, ob ein nicht leerer Wert angekommen ist "!";
			// Eine Sonderstellung haben einfache Selectboxen dort wird von Haus aus der erste Wert vom Browser ausgewählt, somit muss die Default Wert Ueberpruefung hier zusaetzlich per Validierung gemacht werden ("selectzero", "selectemptystring").
			// Fuer group Elemente vom Typ file wird eine Ueberpruefung auf ein vorhandenes File gemacht.
			switch ($fieldConfig['type']) {

				case 'check':
				case 'radio':
				case 'select':
					if (!isset($this->piVars[$this->contentId][$this->cleanSpecialFieldKey($fieldName)])) {
						$valueCheck[$fieldName] = 'required';
					}
					break;

				case 'group';
					if ($fieldConfig['internal_type'] == 'file' && !$_FILES[$this->prefixId]['name'][$this->contentId][$fieldName]) {
						$valueCheck[$fieldName] = 'required';
					}

					if ($fieldConfig['internal_type'] == 'db' && !isset($this->piVars[$this->contentId][$this->cleanSpecialFieldKey($fieldName)])) {
						$valueCheck[$fieldName] = 'required';
					}
					break;

				default:
					if (!$this->piVars[$this->contentId][$this->cleanSpecialFieldKey($fieldName)]) {
						$valueCheck[$fieldName] = 'required';
					}
					break;

			}
		}

		return $valueCheck;
	}

	/**
	 * Erstellt wenn gefordert ein Password, und verschluesselt dieses, oder das uebergebene, wenn es verschluesselt werden soll.
	 *
	 * @param	string		$fieldName
	 * @return	array		$password
	 */
	function generatePassword($fieldName) {
		$password = array();

		// Uebergebenes Password setzten.
		// Hier wird kein strip_tags() o.Ae. benoetigt, da beim schreiben in die Datenbank immer "$GLOBALS['TYPO3_DB']->fullQuoteStr()" ausgefuehrt wird!
		$password['normal'] = trim($this->piVars[$this->contentId][$fieldName]);

		// Erstellt ein Password.
		if ($this->conf['showtype'] == 'register' && $this->conf['register.']['generatepassword.']['mode']) {
			$i = 1;
			$password['normal'] = '';
			$chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHIKLMNPQRSTUVWXYZ';

			while ($i <= (($this->conf['register.']['generatepassword.']['length']) ? $this->conf['register.']['generatepassword.']['length'] : 8)) {
				$password['normal'] .= $chars{mt_rand(0, strlen($chars))};
				$i++;
			}

		}

		// Unverschluesseltes Passwort uebertragen.
		$password['encrypted'] = $password['normal'];

		// Wenn "saltedpasswords" installiert ist wird deren Konfiguration geholt, und je nach Einstellung das Password verschluesselt.
		if (t3lib_extMgm::isLoaded('saltedpasswords')) {
			$saltedpasswords = tx_saltedpasswords_div::returnExtConf();

			if ($saltedpasswords['enabled'] == 1) {
				$tx_saltedpasswords = t3lib_div::makeInstance($saltedpasswords['saltedPWHashingMethod']);
				$password['encrypted'] = $tx_saltedpasswords->getHashedPassword($password['normal']);
			}
		} else

		// Wenn "md5passwords" installiert ist wird wenn aktiviert, das Password md5 verschluesselt.
		if (t3lib_extMgm::isLoaded('md5passwords')) {
			$arrConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['md5passwords']);

			if ($arrConf['activate'] == 1) {
				$password['encrypted'] = md5($password['normal']);
			}
		} else

		// Wenn "t3sec_saltedpw" installiert ist wird wenn aktiviert, das Password gehashed.
		if (t3lib_extMgm::isLoaded('t3sec_saltedpw')) {
			require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/staticlib/class.tx_t3secsaltedpw_div.php';

			if (tx_t3secsaltedpw_div::isUsageEnabled()) {
				require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/lib/class.tx_t3secsaltedpw_phpass.php';
				$tx_t3secsaltedpw_phpass = t3lib_div::makeInstance('tx_t3secsaltedpw_phpass');
				$password['encrypted'] = $tx_t3secsaltedpw_phpass->getHashedPassword($password['normal']);
			}
		}

		return $password;
	}

	/**
	 * Erstellt ein neues Passwort, falls Passwort generieren eingestellt ist. Das Passwort kannn dann ueber den Marker "###PASSWORD###" mit der Registrierungsmail gesendet werden.
	 *
	 * @param	string		$userId
	 * @return	array		$extraMarkers
	 */
	function generatePasswordForMail($userId) {
		$extraMarkers = array();

		if ($this->conf['register.']['generatepassword.']['mode'] && $userId) {
			$password = $this->generatePassword('password');
			$extraMarkers['password'] = $password['normal'];
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $userId , array('password' => $password['encrypted']));
		}

		return $extraMarkers;
	}

	/**
	 * Ueberprueft anhand der aktuellen Verschluesselungsextension, ob das uebergebene unverschluesselte Passwort mit dem uebergebenen verschluesselten Passwort uebereinstimmt.
	 *
	 * @param	string		$submitedPassword
	 * @param	string		$originalPassword
	 * @return	boolean		$check
	 */
	function checkPassword($submitedPassword, $originalPassword) {
		$check = false;

		// Wenn "saltedpasswords" installiert ist wird deren Konfiguration geholt, und je nach Einstellung das Password ueberprueft.
		if (t3lib_extMgm::isLoaded('saltedpasswords')) {
			$saltedpasswords = tx_saltedpasswords_div::returnExtConf();

			if ($saltedpasswords['enabled'] == 1) {
				$tx_saltedpasswords = t3lib_div::makeInstance($saltedpasswords['saltedPWHashingMethod']);
				if ($tx_saltedpasswords->checkPassword($submitedPassword, $originalPassword)) {
					$check = true;
				}
			}
		}

		// Wenn "md5passwords" installiert ist wird wenn aktiviert, das Password ueberprueft.
		else if (t3lib_extMgm::isLoaded('md5passwords')) {
			$arrConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['md5passwords']);

			if ($arrConf['activate'] == 1) {
				if (md5($submitedPassword) == $originalPassword) {
					$check = true;
				}
			}
		}

		// Wenn "t3sec_saltedpw" installiert ist wird wenn aktiviert, das Password ueberprueft.
		else if (t3lib_extMgm::isLoaded('t3sec_saltedpw')) {
			require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/staticlib/class.tx_t3secsaltedpw_div.php';

			if (tx_t3secsaltedpw_div::isUsageEnabled()) {
				require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/lib/class.tx_t3secsaltedpw_phpass.php';
				$tx_t3secsaltedpw_phpass = t3lib_div::makeInstance('tx_t3secsaltedpw_phpass');
				if ($tx_t3secsaltedpw_phpass->checkPassword($submitedPassword, $originalPassword)) {
					$check = true;
				}
			}
		}

		// Wenn keine der oberen Extensions installiert ist (also keine Verschluesselung).
		else {
			if ($submitedPassword == $originalPassword) {
				$check = true;
			}
		}

		return $check;
	}

	/**
	 * The saveDeleteImage method is used to update or delete an image of an address
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrUpdate // Call by reference Array mit allen zu updatenden Daten.
	 * @return	string
	 */
	function saveDeleteImage($fieldName, &$arrUpdate) {
		$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

		// Verzeichniss ermitteln.
		$uploadFolder = $fieldConfig['uploadfolder'];

		if (substr($uploadFolder, -1) != '/') {
			$uploadFolder = $uploadFolder . '/';
		}

		// Bild loeschen und ueberpruefen ob das Bild auch wirklich existiert.
		if ($this->piVars[$this->contentId][$fieldName . '_delete']) {
			$arrUpdate[$fieldName] = '';

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fieldName, 'fe_users', 'uid = ' . $this->userId , '', '', '1');
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

			$imagePath = t3lib_div::getFileAbsFileName($uploadFolder . $row[$fieldName]);

			if ($imagePath && file_exists($imagePath)) {
				unlink($imagePath);
			}

			return '';
		}

		// Konfigurierte Dateigroesse ermitteln.
		$maxSize = $fieldConfig['max_size'] * 1024;

		// Konfigurierte maximale Dateigroesse ueberschritten.
		if ($maxSize && $_FILES[$this->prefixId]['size'][$this->contentId][$fieldName] > $maxSize) {
			return 'size';
		}

		// Der Upload war nicht vollstaendig, da Datei zu gross (Zeitueberschreitung).
		if ($_FILES[$this->prefixId]['error'][$this->contentId][$fieldName] == '2') {
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
		$imageType = $mimeTypes[$_FILES[$this->prefixId]['type'][$this->contentId][$fieldName]];

		// Wenn ein falsche Format hochgeladen wurde.
		if (!$imageType) {
			return 'type';
		}

		// Nur wenn eine Datei ausgewaehlt wurde [image] und diese den obigen mime-typen enstpricht[$type], dann wird die datei gespeichert
		if ($_FILES[$this->prefixId]['name'][$this->contentId][$fieldName]) {
			// Bildname generieren.
			$fileName = preg_replace("/[^a-zA-Z0-9]/", '', $this->piVars[$this->contentId]['username']) . '_' . time() . $imageType;

			// Kompletter Bildpfad.
			$uploadFile = $uploadFolder . $fileName;

			// Bild verschieben, und anschliessend den neuen Bildnamen in die Datenbank schreiben.
			if (move_uploaded_file($_FILES[$this->prefixId]['tmp_name'][$this->contentId][$fieldName], $uploadFile)) {
				chmod($uploadFile, 0644);
				$arrUpdate[$fieldName] = $fileName;

				// Wenn Das Bild erfolgreich hochgeladen wurde, nichts zurueckgeben.
				return '';
			}
		}

		return 'upload';
	}

	/**
	 * Erledigt allen Output der nichts mit dem eigendlichen Formular zu tun hat.
	 * Fuer besondere Faelle kann hier eine Ausnahme, oder zusaetzliche Konfigurationen gesetzt werden.
	 *
	 * @param	string		$mode
	 * @param	string		$submode
	 * @param	array		$params
	 * @return	string		$label
	 */
	function showMessageOutputRedirect($mode, $submode = '', $params = array()) {
		$redirect = true;

		// Label ermitteln
		$label = $this->getLabel($mode . (($submode) ? '_' . $submode : ''));

		// Zusaetzliche Konfigurationen die gesetzt werden, bevor die Ausgabe oder der Redirect ausgefuehrt werden.
		switch ($mode) {

			case 'register':
				// Login vollziehen, falls eine Redirectseite angegeben ist, wird dorthin automatisch umgeleitet.
				if ($params['username'] && $this->conf['register.']['autologin']) {
					$this->userAutoLogin($params['username'], $mode);
				}

				break;

			case 'doubleoptin':
				// Login vollziehen, falls eine Redirectseite angegeben ist, wird dorthin automatisch umgeleitet.
				if ($params['userId'] && $this->conf['register.']['autologin']) {
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('username', 'fe_users', 'uid = ' . $params['userId'], '', '', '1');
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

					$this->userAutoLogin($row['username'], $mode);
				}

			case 'adminapproval':
				// WICHTIG: Kein break beim doubleoptin, da diese Konfiguration auch fuer doubleoptin gilt.
				if ($params['mode']) {
					if ($this->conf['redirect.'][$params['mode']]) {
						$mode = $params['mode'];
					} else {
						$redirect = false;
					}
				}

				break;

			case 'edit_error_no_login':
				$label = '<div class="edit_error_no_login">' . $label . '</div>';
				break;

			case 'edit':
				// Einen Refresh der aktuellen Seite am Client ausfuehren, damit nach dem Editieren wieder das Formular angezeigt wird.
				$GLOBALS['TSFE']->additionalHeaderData['refresh'] = '<meta http-equiv="refresh" content="2; url=' . t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getPageLink($GLOBALS['TSFE']->id) . '" />';
				break;

			case 'doubleoptin':
				// Einen Refresh auf der aktuellen Seite am Client ausfuehren, damit nach dem Loeschen des Users die Startseite angezeigt wird.
				$GLOBALS['TSFE']->additionalHeaderData['refresh'] = '<meta http-equiv="refresh" content="2; url=' . t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '" />';
				break;

		}

		if ($redirect && $this->conf['redirect.'][$mode]) {
			$this->userRedirect($this->conf['redirect.'][$mode]);
		}

		return $label;
	}

	/**
	 * Vollzieht einen Login ohne ein Passwort.
	 *
	 * @param	string		$username
	 * @param	string		$mode
	 * @return	void
	 */
	function userAutoLogin($username, $mode = '') {
		// Login vollziehen.
		$GLOBALS['TSFE']->fe_user->checkPid = 0;
		$arrAuthInfo = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
		$userRecord = $GLOBALS['TSFE']->fe_user->fetchUserRecord($arrAuthInfo['db_user'], $username);
		$GLOBALS['TSFE']->fe_user->createUserSession($userRecord);

		// Umleiten, damit der Login wirksam wird.
		$this->userRedirect($this->conf['redirect.'][$mode], array(), true);
	}

	/**
	 * Vollzieht einen Redirect mit der Seite die benutzt wird, oder auf die aktuelle.
	 *
	 * @param	integer		$pageId
	 * @param	boolean		$disableAccessCheck
	 * @return	void
	 */
	function userRedirect($pageId = 0, $urlParameters = array(), $disableAccessCheck = false) {
		// Normalen Redirect, oder Redirect auf die gewuenschte Seite.
		if (!$pageId) {
			$pageId = $GLOBALS['TSFE']->id;
		}

		// Damit man auch auf Seiten die erst nach dem Login sichtbar sind umleiten kann, wird hier die Gruppen Zugangsüberprüfung vorrübergehend deaktiviert.
		// Das wird aber nur bei einem Autologin benötigt, da sich nur dort der Status des Users während des Abarbeitungsprozesses ändert.
		// WICHTIG: Falls nach dem Login die Seite immer noch unsichtbar (nicht zugänglich) ist, greift die normale Typo3 Umleitung.
		if ($disableAccessCheck) {
			$GLOBALS['TSFE']->config['config']['typolinkLinkAccessRestrictedPages'] = 'NONE';
		}

		$pageLink = $this->pi_getPageLink($pageId, $urlParameters);
		header('Location: ' . t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $pageLink . ((strpos($pageLink, '?') === false) ? '?' : '&') . $this->makeHiddenParams());
		exit;
	}

	/**
	 * Sendet die Aktivierungsmail an den uebergebenen User.
	 *
	 * @param	integer		$userId
	 * @return	void
	 */
	function sendActivationMail($userId) {
		// Neuen Timestamp setzten, damit jede Aktivierungsmail einen anderen Hash hat.
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . intval($userId),array('tstamp' => time()));

		// Userdaten ermitteln.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tstamp, username, email, tx_datamintsfeuser_approval_level', 'fe_users', 'uid = ' . intval($userId), '', '', '1');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		// Genehmigungstypen aufsteigend sortiert ermitteln. Das ist nötig um das Level dem richtigen Typ zuordnen zu können.
		// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig lässt.
		$arrApprovalTypes = $this->getApprovalTypes();

		// Aktuellen Genehmigungstyp ermitteln.
		$approvalType = $arrApprovalTypes[count($arrApprovalTypes) - $row['tx_datamintsfeuser_approval_level']];

		// Mail vorbereiten.
		$approvalHash = md5('approval' . $row['uid'] . $row['tstamp'] . $this->extConf['encryptionKey']);
		$disapprovalHash = md5('disapproval' . $row['uid'] . $row['tstamp'] . $this->extConf['encryptionKey']);
		$pageLink = $this->pi_getPageLink($GLOBALS['TSFE']->id);
		$pageLink = (strpos($pageLink, '?') === false) ? $pageLink . '?' : $pageLink . '&';
		$extraMarkers = array(
			'approvallink' => t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $pageLink . $this->prefixId . '%5B' . $this->contentId . '%5D%5Bsubmit%5D=approvalcheck&' . $this->prefixId . '%5B' . $this->contentId . '%5D%5Buid%5D=' . $row['uid'] . '&' . $this->prefixId . '%5B' . $this->contentId . '%5D%5Bhash%5D=' . $approvalHash . $this->makeHiddenParams(),
			'disapprovallink' => t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $pageLink . $this->prefixId . '%5B' . $this->contentId . '%5D%5Bsubmit%5D=approvalcheck&' . $this->prefixId . '%5B' . $this->contentId . '%5D%5Buid%5D=' . $row['uid'] . '&' . $this->prefixId . '%5B' . $this->contentId . '%5D%5Bhash%5D=' . $disapprovalHash . $this->makeHiddenParams()
		);

		// E-Mail senden.
		$this->sendMail($row['uid'], $approvalType, $this->isAdminMail($approvalType), $this->conf['register.'], $extraMarkers);

		// Cookie fuer das erneute zusenden des Aktivierungslinks setzten.
		$this->setNotActivatedCookie($row['uid']);
	}

	/**
	 * Ueberprueft ob die Linkbestaetigung gueltig ist und aktiviert gegebenenfalls den User.
	 *
	 * @param	integer		$userId
	 * @return	string
	 */
	function makeApprovalCheck($userId) {
		// Userdaten ermitteln.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tstamp, username, email, tx_datamintsfeuser_approval_level', 'fe_users', 'uid = ' . $userId, '', '', '1');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		// Genehmigungstyp ermitteln um die richtige E-Mail zu senden, bzw. die richtige Ausgabe zu ermitteln.
		$arrApprovalTypes = $this->getApprovalTypes();
		$approvalType = $arrApprovalTypes[count($arrApprovalTypes) - $row['tx_datamintsfeuser_approval_level']];

		// Wenn kein Genehmigungstyp ermittelt werden konnte.
		if (!$approvalType) {
			return $this->showMessageOutputRedirect('approvalcheck_failure');
		} else {
			// Ausgabe vorbereiten.
			$mode = $approvalType;
			$submode = 'failure';
			$params = array();
		}

		// Daten vorbereiten.
		$time = time();
		$hashApproval = md5('approval' . $row['uid'] . $row['tstamp'] . $this->extConf['encryptionKey']);
		$hashDisapproval = md5('disapproval' . $row['uid'] . $row['tstamp'] . $this->extConf['encryptionKey']);

		// Wenn der Hash richtig ist, des letzte Genehmigungslevel aber noch nicht erreicht ist.
		if ($this->piVars[$this->contentId]['hash'] == $hashApproval && $row['tx_datamintsfeuser_approval_level'] > 1) {
			// Genehmigungslevel updaten.
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $userId ,array('tstamp' => $time, 'tx_datamintsfeuser_approval_level' => $row['tx_datamintsfeuser_approval_level'] - 1));

			// Aktivierungsmail schicken.
			$this->sendActivationMail($userId);

			// Ausgabe vorbereiten.
			$submode = 'success';
		}

		// Wenn der Hash richtig ist, und das letzte Genehmigungslevel erreicht ist.
		if ($this->piVars[$this->contentId]['hash'] == $hashApproval && $row['tx_datamintsfeuser_approval_level'] == 1) {
			// Erstellt ein neues Passwort, falls Passwort generieren eingestellt ist. Das Passwort kannn dann ueber den Marker "###PASSWORD###" mit der Registrierungsmail gesendet werden.
			$extraMarkers = $this->generatePasswordForMail($userId);

			// User aktivieren.
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $userId ,array('tstamp' => $time, 'disable' => '0', 'tx_datamintsfeuser_approval_level' => '0'));

			// Registrierungs E-Mail schicken.
			if ($this->conf['register.']['sendadminmail']) {
				$this->sendMail($userId, 'registration', true, $this->conf['register.']);
			}

			if ($this->conf['register.']['sendusermail']) {
				$this->sendMail($userId, 'registration', false, $this->conf['register.'], $extraMarkers);
			}

			// Ausgabe vorbereiten.
			$submode = 'success';
			$params = array('userId' => $userId);
		}

		// Wenn der Hash richtig ist, des letzte Genehmigungslevel aber noch nicht erreicht ist.
		if ($this->piVars[$this->contentId]['hash'] == $hashDisapproval) {
			// User loeschen.
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $userId ,array('tstamp' => $time, 'deleted' => '1'));

			// Eine Account-Abgelehnt Mail senden, wenn User ablehnt an den Administrator, oder andersrum.
			$this->sendMail($userId, 'disapproval', !$this->isAdminMail($approvalType), $this->conf['register.']);

			// Ausgabe vorbereiten.
			$submode = 'deleted';
		}

		return $this->showMessageOutputRedirect($mode, $submode, $params);
	}

	/**
	 * Ermittelt alle Genehmigungstypen.
	 *
	 * @return	array
	 */
	function getApprovalTypes() {
		// Genhemigungstypen aufsteigend sortiert ermitteln. Das ist nötig um das Level dem richtigen Typ zuordnen zu können.
		// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig lässt.
		return array_values(t3lib_div::trimExplode(',', $this->conf['register.']['approvalcheck'], true));
	}

	/**
	 * Ueberprueft anhand des Genehmigungstyps ob die Mail eine Adminmail oder eine Usermail ist. Wenn 'admin' im Namen des Genehmigungstyps steht, dann ist die Mail eine Adminmail.
	 *
	 * @param	string		$approvalType
	 * @return	boolean
	 */
	function isAdminMail($approvalType) {
		return (strpos($approvalType, 'admin') === false) ? false : true;
	}

	/**
	 * Setzt einen Cookie fuer den neu angelegten Account, falls dieser aktiviert werden muss.
	 *
	 * @param	integer		$userId
	 * @return	void
	 */
	function setNotActivatedCookie($userId) {
		$arrNotActivated = $this->getNotActivatedUserArray();
		$arrNotActivated[] = intval($userId);
		setcookie($this->prefixId . '[not_activated]', implode(',', $arrNotActivated), time() + 60 * 60 * 24 * 30);
	}

	/**
	 * Ermittelt alle nicht aktivierten Accounts des Users, falls .
	 *
	 * @param	array		$arrNotActivated
	 * @return	array		$arrNotActivatedCleaned
	 */
	function getNotActivatedUserArray($arrNotActivated = array()) {
		$arrNotActivatedCleaned = array();

		// Nicht aktivierte User ueber den Cookie ermitteln, und vor missbrauch schuetzen.
		if (!$arrNotActivated) {
			$arrNotActivated = array_unique(t3lib_div::trimExplode(',', $_COOKIE[$this->prefixId]['not_activated'], true));
		}

		foreach ($arrNotActivated as $key => $val) {
			$arrNotActivated[$key] = intval($val);
		}

		// Wenn nach dem reinigen noch User uebrig bleiben.
		if (count($arrNotActivated) > 0) {
			// Herrausgefundene User ermitteln und ueberpruefen, ob die User mitlerweile schon aktiviert wurden.
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'fe_users', 'uid IN(' . implode(',', $arrNotActivated) . ') AND disable = 1 AND deleted = 0');

			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$arrNotActivatedCleaned[] = $row['uid'];
			}
		}

		return $arrNotActivatedCleaned;
	}

	/**
	 * Sendet die E-Mails mit dem uebergebenen Template und falls angegeben, auch mit den extra Markern.
	 *
	 * @param	integer		$userId
	 * @param	string		$templatePart
	 * @param	boolean		$adminMail
	 * @param	array		$config
	 * @param	array		$extraMarkers
	 * @param	array		$extraSuparts
	 * @return	void
	 */
	function sendMail($userId, $templatePart, $adminMail, $config, $extraMarkers = array(), $extraSuparts = array()) {
		// Userdaten ermitteln.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid = ' . intval($userId), '', '', '1');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		$markerArray = array_merge((array)$row, (array)$extraMarkers);

		foreach ($markerArray as $key => $val) {
			if (!$this->checkUtf8($val)) {
				$markerArray[$key] = utf8_encode($val);
			}
		}

		$markerArray = array_merge((array)$markerArray, (array)$config);

		// Absender vorbereiten.
		$from_name = ($config['sendername']) ? $config['sendername'] : $config['sendermail'];
		$from_email = $config['sendermail'];

		// Wenn die Mail fuer den Admin bestimmt ist.
		if ($adminMail) {
			// Template laden.
			$content = $this->getTemplateSubpart($templatePart . '_admin', $config, $markerArray);

			// Empfaengername vorbereiten.
			$name = ($config['adminname']) ? $config['adminname'] : $config['adminmail'];

			// E-Mail und Name ermitteln.
			$recipient = $name . ' <' . $config['adminmail'] . '>';
		} else {
			// Template laden.
			$content = $this->getTemplateSubpart($templatePart, $config, $markerArray);

			// Empfaengername vorbereiten.
			$name = ($row['username']) ? $row['username'] : $row['email'];

			// E-Mail und Name ermitteln.
			$recipient = $name . ' <' . $row['email'] . '>';
		}

		// Betreff ermitteln und aus dem E-Mail Content entfernen.
		$subject = trim($this->cObj->getSubpart($content, '###SUBJECT###'));
		$content = $this->cObj->substituteSubpart($content, '###SUBJECT###', '');

		// Body zusammensetzen.
		$body = $this->getTemplateSubpart('body', $config, array_merge((array)$markerArray, array('content' => $content)));

		// Header ermitteln und Betreff ersetzten (Title-Tag).
		$header = $this->getTemplateSubpart('header', $config, array_merge((array)$markerArray, array('subject' => $subject)));

		// Extra Subparts ersetzten.
		foreach ($extraSuparts as $key => $val) {
			$body = $this->cObj->substituteSubpart($body, '###' . strtoupper($key) . '###', $val);
		}

		// Hook um die E-Mail zu aendern.
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['sendMail_htmlMail'])) {
			$_params = array(
					'variables' => array(
							'userId' => $userId,
							'templatePart' => $templatePart,
							'adminMail' => $adminMail,
							'config' => $config,
							'markerArray' => $markerArray
						),
					'parameters' => array(
							'body' => &$body,
							'header' => &$header,
							'subject' => &$subject,
							'recipient' => &$recipient,
							'from_name' => &$from_name,
							'from_email' => &$from_email
						)
				);

			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['sendMail_htmlMail'] as $_funcRef) {
				t3lib_div::callUserFunction($_funcRef, $_params, $this);
			}
		}

		// Verschicke E-Mail.
		if ($recipient && $subject && $body) {

			$body_html = '<html>' . $header . $body . '</html>';
			$body_plain = trim(strip_tags($body));

			$htmlMail = t3lib_div::makeInstance('t3lib_htmlmail');
	      	$htmlMail->start();
	      	$htmlMail->recipient = $recipient;
            $htmlMail->replyto_email = $from_email;
            $htmlMail->replyto_name = $from_name;
	      	$htmlMail->subject = $subject;
	      	$htmlMail->from_email = $from_email;
	      	$htmlMail->from_name = $from_name;
            $htmlMail->returnPath = $from_email;
	     	$htmlMail->addPlain($body_plain);

			if ($config['mailtype'] == 'html') {
				$htmlMail->setHTML($htmlMail->encodeMsg($body_html));
			}

			$htmlMail->send($htmlMail->recipient);
		}
	}

	/**
	 * Holt einen Subpart des Standardtemplates und ersetzt uebergeben Marker.
	 *
	 * @param	string		$templatePart
	 * @param	array		$config
	 * @param	array		$markerArray
	 * @return	string		$template
	 */
	function getTemplateSubpart($templatePart, $config, $markerArray = array()) {
		// Template holen.
		$templateFile = $config['emailtemplate'];

		if (!$templateFile) {
			$templateFile = 'EXT:' . $this->extKey . '/res/datamints_feuser_mail.html';
		}

		// Template laden.
		$template = $this->cObj->fileResource($templateFile);
		$template = $this->cObj->getSubpart($template, '###' . strtoupper($templatePart) . '###');

		if (!$this->checkUtf8($template)) {
			$template = utf8_encode($template);
		}

		$template = $this->cObj->substituteMarkerArray($template, $markerArray, '###|###', 1);

		return $template;
	}

	/**
	 * Ermittlet alle geänderten Daten und schreibt sie in ein Markerarray.
	 *
	 * @param	array		$arrNewData
	 * @param	array		$config
	 * @return	array		$extraMarkers
	 */
	function getChangedForMail($arrNewData, $config) {
		$count = 0;
		$extraMarkers = array();
		$template =  $this->getTemplateSubpart('changed_items', $config);

		foreach ($arrNewData as $key => $val) {
			if (in_array($key, $this->arrUsedFields)) {
				if ($val != $GLOBALS['TSFE']->fe_user->user[$key]) {
					$markerArray = array();
					$markerArray['label'] = $this->getLabel($key, false);
					$markerArray['value_old'] = $GLOBALS['TSFE']->fe_user->user[$key];
					$markerArray['value_new'] = $val;

					$subpart = $this->cObj->getSubpart($template, '###' . strtoupper($key) . '###');

					if ($subpart) {
						$count++;
						$extraMarkers['changed_item_' . $key] = $this->cObj->substituteMarkerArray($subpart, $markerArray, '###|###', 1);
					} else {
						$extraMarkers['changed_item_' . $key] = '';
					}
				} else {
					$extraMarkers['changed_item_' . $key] = '';
				}
			}
		}

		if (!$count) {
			$extraMarkers['nothing_changed'] = 'nothing_changed';
		}

		return $extraMarkers;
	}

	/**
	 * Gibt alle im Backend definierten Felder (TypoScipt/Flexform) formatiert und der Anzeigeart entsprechend aus.
	 *
	 * @param	array		$valueCheck
	 * @return	string		$content
	 */
	function showForm($valueCheck = array()) {
		$arrCurrentData = array();

		// Beim editieren der Userdaten, die Felder vorausfuellen.
		if ($this->conf['showtype'] == 'edit' && $this->userId) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', 'uid = ' . $this->userId , '', '', '1');
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

			$arrCurrentData = $row;
		}

		// Wenn das Formular schon einmal abgesendet wurde aber ein Fehler auftrat, dann die bereits vom User uebertragenen Userdaten vorausfuellen.
		if ($this->piVars[$this->contentId]) {
			foreach ($this->piVars[$this->contentId] as $key => $val) {
				if (is_array($this->piVars[$this->contentId][$key])) {
					foreach ($this->piVars[$this->contentId][$key] as $subKey => $subVal) {
						$this->piVars[$this->contentId][$key][$subKey] = strip_tags($subVal);
					}
				} else {
					$this->piVars[$this->contentId][$key] = strip_tags($val);
				}
			}

			$arrCurrentData = array_merge((array)$arrCurrentData, (array)$this->piVars[$this->contentId]);
		}

		// Konvertiert alle moeglichen Zeichen der Ausgabe, die stoeren koennten (XSS).
		if ($arrCurrentData) {
			foreach ($arrCurrentData as $key => $val) {
				if (is_array($arrCurrentData[$key])) {
					foreach ($arrCurrentData[$key] as $subKey => $subVal) {
						$arrCurrentData[$key][$subKey] = strip_tags($subVal);
					}
				} else {
					$arrCurrentData[$key] = htmlspecialchars($val);
				}
			}
		}

		// Seite, die den Request entgegennimmt (TypoLink).
		$requestLink = $this->pi_getPageLink($this->conf['requestpid']);

		// Wenn keine Seite per TypoScript angegeben ist, wird die aktuelle Seite verwendet.
		if (!$this->conf['requestpid']) {
			$requestLink = $this->pi_getPageLink($GLOBALS['TSFE']->id);
		}

		// ID Zaehler fuer Items und Fieldsets.
		$iItem = 1;
		$iFieldset = 1;
		$iInfoItem = 1;

		// Formular start.
		$content = '<form id="' . $this->extKey . '_' . $this->contentId . '_form" name="' . $this->prefixId . '[' . $this->contentId . ']" action="' . $requestLink . '" method="post" enctype="multipart/form-data"><fieldset class="form_fieldset_' . $iFieldset . '">';

		// Wenn eine Lgende fuer das erste Fieldset definiert wurde, diese ausgeben.
		if ($this->conf['legends.'][$iFieldset]) {
			$content .= '<legend class="form_legend_' . $iFieldset . '">' . $this->conf['legends.'][$iFieldset] . '</legend>';
		}

		// Alle ausgewaehlten Felder durchgehen.
		foreach ($this->arrUsedFields as $fieldName) {
			$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

			// Standardkonfigurationen laden.
			if (!$arrCurrentData[$fieldName] && $fieldConfig['default']) {
				$arrCurrentData[$fieldName] = $fieldConfig['default'];
			}

			// Wenn das im Flexform ausgewaehlte Feld existiert, dann dieses Feld ausgeben, alle anderen Felder werden ignoriert.
			if ($this->feUsersTca['columns'][$fieldName]) {
				// Form Item Anfang.
				$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldConfig['type'] . (($this->checkIfRequired($fieldName)) ? ' required_item' : '') . '">';

				// Label schreiben.
				$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '">' . $this->getLabel($fieldName) . '</label>';

				switch ($fieldConfig['type']) {

					case 'input':
						$content .= $this->showInput($fieldName, $arrCurrentData, $iItem);
						break;

					case 'text':
						$content .= $this->showText($fieldName, $arrCurrentData);
						break;

					case 'check':
						$content .= $this->showCheck($fieldName, $arrCurrentData);
						break;

					case 'radio':
						$content .= $this->showRadio($fieldName, $arrCurrentData);
						break;

					case 'select':
						$content .= $this->showSelect($fieldName, $arrCurrentData);
						break;

					case 'group':
						$content .= $this->showGroup($fieldName, $arrCurrentData);
						break;

				}

				// Extra Error Label ermitteln.
				$content .= $this->getErrorLabel($fieldName, $valueCheck);

				// Form Item Ende.
				$content .= '</div>';

				$iItem++;
			}

			// Separator anzeigen.
			if ($fieldName == '--separator--') {
				$iFieldset++;

				$content .= '</fieldset><fieldset class="form_fieldset_' . $iFieldset . '">';

				// Wenn eine Lgende fuer das Fieldset definiert wurde, diese ausgeben.
				if ($this->conf['legends.'][$iFieldset]) {
					$content .= '<legend class="form_legend_' . $iFieldset . '">' . $this->conf['legends.'][$iFieldset] . '</legend>';
				}
			}

			// Infoitem anzeigen.
			if ($fieldName == '--infoitem--') {
				if ($this->conf['infoitems.'][$iInfoItem]) {
					$content .= '<div class="form_infoitem_' . $iInfoItem . '">' . $this->conf['infoitems.'][$iInfoItem] . '</div>';
				}

				$iInfoItem++;
			}

			// Profil loeschen Link anzeigen.
			if ($fieldName == '--userdelete--' && $this->conf['showtype'] == 'edit' && $this->userId) {
				$fieldName = $this->cleanSpecialFieldKey($fieldName);

				$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldName . '">';
				$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '">' . $this->getLabel($fieldName) . '</label>';
				$content .= '<input type="checkbox" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="1" />';
				$content .= $this->getErrorLabel($fieldName, $valueCheck);
				$content .= '</div>';

				$iItem++;
			}

			// Passwortbestaetigung anzeigen.
			if ($fieldName == '--passwordconfirmation--' && $this->conf['showtype'] == 'edit' && $this->userId) {
				$fieldName = $this->cleanSpecialFieldKey($fieldName);

				$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldName . '">';
				$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '">' . $this->getLabel($fieldName) . '</label>';
				$content .= '<input type="password" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="" />';
				$content .= $this->getErrorLabel($fieldName, $valueCheck);
				$content .= '</div>';

				$iItem++;
			}

			// Aktivierung erneut senden anzeigen.
			if ($fieldName == '--resendactivation--') {
				$fieldName = $this->cleanSpecialFieldKey($fieldName);

				// Noch nicht fertig gestellte Listenansicht der nicht aktivierten User.
//				if ($this->conf['shownotactivated'] == 'list') {
//					$arrNotActivated = $this->getNotActivatedUserArray();
//					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, username', 'fe_users', 'pid = ' . intval($this->storagePid) . ' AND uid IN(' . implode(',', $arrNotActivated) . ') AND disable = 1 AND deleted = 0');
//
//					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
//						$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldName . ' ' . $this->conf['shownotactivated'] . '">';
//						$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '">' . $this->getLabel($fieldName) . ' ' . $row['username'] . '</label>';
//						$content .= '<input type="checkbox" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '][' . $row['uid'] . ']" value="1" />';
//						$content .= '</div>';
//
//						$iItem++;
//					}
//				} else {
					$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldName . '">';
					$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '">' . $this->getLabel($fieldName) . '</label>';
					$content .= '<input type="text" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="" />';
					$content .= $this->getErrorLabel($fieldName, $valueCheck);
					$content .= '</div>';

					$iItem++;
//				}
			}

			// Submit Button anzeigen.
			if ($fieldName == '--submit--') {
				$fieldName = $this->cleanSpecialFieldKey($fieldName);

				$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldName . '">';
				$content .= '<input id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" type="submit" value="' . $this->getLabel($fieldName . '_' . $this->conf['showtype']) . '"/>';
				$content .= '</div>';

				$iItem++;
			}
		}

		// UserId, PageId und Modus anhaengen.
		$content .= '<input type="hidden" name="' . $this->prefixId . '[' . $this->contentId . '][submit]" value="send" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[' . $this->contentId . '][userid]" value="' . $this->userId . '" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[' . $this->contentId . '][pageid]" value="' . $GLOBALS['TSFE']->id . '" />';
		$content .= '<input type="hidden" name="' . $this->prefixId . '[' . $this->contentId . '][submitmode]" value="' . $this->conf['showtype'] . '" />';
		$content .= $this->makeHiddenFields();

		$content .= '</fieldset>';
		$content .= '</form>';

		return $content;
	}

	/**
	 * Ersetzt die beim Eingeben angegebenen '--' Zeichen vor und hinter dem eigendlichen Feldnamen, falls vorhanden.
	 *
	 * @param	string		$fieldName
	 * @return	string
	 */
	function cleanSpecialFieldKey($fieldName) {
		if (preg_match('/^--.*--$/', $fieldName)) {
			return preg_replace('/^--(.*)--$/', '\1', $fieldName);
		}
		return $fieldName;
	}

	/**
	 * Rendert Inputfelder.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @param	integer		$iItem
	 * @return	string		$content
	 */
	function showInput($fieldName, $arrCurrentData, $iItem) {
		$content = '';
		$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

		// Datumsfeld.
		if (strpos($fieldConfig['eval'], 'date') !== false) {
			// Timestamp zu "tt.mm.jjjj" machen.
			if ($arrCurrentData[$fieldName] != 0) {
				$datum = strftime('%d.%m.%Y', $arrCurrentData[$fieldName]);
			}

			$content .= '<input type="text" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="' . $datum . '" />';

			return $content;
		}

		// Passwordfelder.
		if (strpos($fieldConfig['eval'], 'password') !== false) {
			$content .= '<input type="password" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="" />';
			$content .= '</div><div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_rep_wrapper" class="form_item form_item_' . $iItem . ' form_type_' . $fieldConfig['type'] . '">';
			$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_rep">' . $this->getLabel($fieldName . '_rep') . $this->checkIfRequired($fieldName) . '</label>';
			$content .= '<input type="password" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_rep" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '_rep]" value="" />';

			return $content;
		}

		// Normales Inputfeld.
		$readOnly = ($fieldConfig['readOnly'] == 1) ? ' readonly="readonly"' : '';
		$content .= '<input type="text" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="' . $arrCurrentData[$fieldName] . '"' . $readOnly . ' />';

		return $content;
	}

	/**
	 * Rendert Textareas.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @return	string		$content
	 */
	function showText($fieldName, $arrCurrentData) {
		$content = '';
		$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

		$readOnly = ($fieldConfig['readOnly'] == 1) ? ' readonly="readonly"' : '';
		$content .= '<textarea id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" rows="2" cols="42"' . $readOnly . '>' . $arrCurrentData[$fieldName] . '</textarea>';

		return $content;
	}

	/**
	 * Rendert Checkboxen.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @return	string		$content
	 */
	function showCheck($fieldName, $arrCurrentData) {
		$content = '';
		$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

		$countCheckFields = count($fieldConfig['items']);

		if ($countCheckFields > 1) {
			// Moeglichkeit das der gespeicherte Wert eine Bitmap ist, daher aufsplitten in ein Array, wie es auch von einem abgesendeten Formular kommen wuerde.
			if (!is_array($arrCurrentData[$fieldName])) {
				$decKeyCheck = '';
				$arrCurrentData[$fieldName] = str_split(strrev(decbin($arrCurrentData[$fieldName])));

				for ($i = 0; $i < $countCheckFields; $i++) {
					if ($arrCurrentData[$fieldName][$i]) {
						$decKeyCheck .= $i;
					}
				}

				$arrCurrentData[$fieldName] = str_split($decKeyCheck);
			}

			$content .= '<div class="check_item_wrapper">';

			// Items, die in der TCA-Konfiguration festgelegt wurden.
			for ($i = 0; $i < $countCheckFields; $i++) {
				if ($i % $fieldConfig['cols'] == 0) {
					$content .= '</div><div class="check_item_wrapper">';
				}

				$checked = (in_array($i, $arrCurrentData[$fieldName])) ? ' checked="checked"' : '';

				$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $i . '_wrapper" class="check_item check_item_' . $i . '">';
				$content .= '<input type="checkbox"  name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '][]" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $i . '" value="' . $i . '"' . $checked . '/>';
				$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $i . '">' . $this->getLabel($fieldConfig['items'][$i][0]) . '</label>';
				$content .= '</div>';
			}

			$content .= '</div>';
		} else {
			$checked = ($arrCurrentData[$fieldName] == 1) ? ' checked="checked"' : '';
			$content .= '<input type="checkbox" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="1"' . $checked . ' />';
		}

		return $content;
	}

	/**
	 * Rendert Radiobuttons.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @return	string		$content
	 */
	function showRadio($fieldName, $arrCurrentData) {
		$content = '';
		$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

		$content .= '<div class="radio_item_wrapper">';

		for ($i = 0; $i < count($fieldConfig['items']); $i++) {
			$label = $fieldConfig['items'][$i][0];
			$value = $fieldConfig['items'][$i][1];
			$checked = ($arrCurrentData[$fieldName] == $value) ? ' checked="checked"' : '';

			$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $value . '_wrapper" class="radio_item radio_item_' . $i . '">';
			$content .= '<input type="radio" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $value . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" value="' . $value . '"' . $checked . ' />';
			$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $i . '">';
			$content .= $this->getLabel($label);
			$content .= '</label>';
			$content .= '</div>';
		}

		$content .= '</div>';

		return $content;
	}

	/**
	 * Rendert Selectfelder.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @return	string		$content
	 */
	function showSelect($fieldName, $arrCurrentData) {
		$content = '';
		$optionlist = '';
		$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];
		$countSelectFields = count($fieldConfig['items']);

		// Moeglichkeit das der gespeicherte Wert eine kommseparierte Liste ist, daher aufsplitten in ein Array, wie es auch von einem abgesendeten Formular kommen wuerde.
		if (!is_array($arrCurrentData[$fieldName])) {
			$arrCurrentData[$fieldName] = t3lib_div::trimExplode(',', $arrCurrentData[$fieldName], true);
		}

		// Bei dem Typ Select gibt es zwei verschidene Rendermodi. Dieser kann "singlebox" (dann ist es eine Selectbox) oder "checkbox" (dann ist es eine Checkboxliste) sein.
		// Checkboxen gehen nur, wenn die Konfiguration "maxItems" > 1 ist (man also auch tatsächlich mehrere auswählen kann).
		// Bei der Ausgabe der einzelnen Einträge wird also immer nach dem Rendermode unterschieden.

		// TODO: funktioniert bei der Anzeige noch nicht richtig!

		// Items, die in der TCA-Konfiguration festgelegt wurden.
		for ($i = 0; $i < $countSelectFields; $i++) {
			$label = $fieldConfig['items'][$i][0];
			$value = $fieldConfig['items'][$i][1];

			if ($fieldConfig['renderMode'] == 'checkbox' && $fieldConfig['maxitems'] > 1) {
				$checked = (in_array($i, $arrCurrentData[$fieldName])) ? ' checked="checked"' : '';

				$optionlist .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $value . '_wrapper" class="check_item check_item_' . $i . '">';
				$optionlist .= '<input type="checkbox"  name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '][]" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $value . '" value="' . $value . '"' . $checked . '/>';
				$optionlist .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $value . '">' . $this->getLabel($label) . '</label>';
				$optionlist .= '</div>';
			} else {
				$selected = (in_array($i, $arrCurrentData[$fieldName])) ? ' selected="selected"' : '';

				$optionlist .= '<option value="' . $value . '"' . $selected . '>' . $this->getLabel($label) . '</option>';
			}
		}

		// Wenn Tabelle angegeben zusaetzlich Items aus Datenbank holen.
		if ($fieldConfig['foreign_table']) {
			// Select-Items aus DB holen.
			$tab = $fieldConfig['foreign_table'];
			$sel = 'uid, ' . $GLOBALS['TCA'][$tab]['ctrl']['label'];

			$whr = strtolower(substr(trim($fieldConfig['foreign_table_where']), 3));
			$whr = trim(($whr == 'and' || $whr == 'or ') ? substr($fieldConfig['foreign_table_where'], 3, strlen($fieldConfig['foreign_table_where'])) : $fieldConfig['foreign_table_where']);

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($sel , $tab, $whr);

			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				if ($fieldConfig['renderMode'] == 'checkbox' && $fieldConfig['maxitems'] > 1) {
					$checked = (in_array($row['uid'], $arrCurrentData[$fieldName])) ? ' checked="checked"' : '';

					$optionlist .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $row['uid'] . '_wrapper" class="check_item check_item_' . $row['uid'] . '">';
					$optionlist .= '<input type="checkbox" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $row['uid'] . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '][]" value="' . $row['uid'] . '"' . $checked . '/>';
					$optionlist .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $row['uid'] . '">' . $row[$GLOBALS['TCA'][$tab]['ctrl']['label']] . '</label>';
					$optionlist .= '</div>';
				} else {
					$selected = (in_array($row['uid'], $arrCurrentData[$fieldName])) ? ' selected="selected"' : '';
					
					$optionlist .= '<option value="' . $row['uid'] . '"' . $selected . '>' . $row[$GLOBALS['TCA'][$tab]['ctrl']['label']] . '</option>';
				}
			}
		}

		// Mehrzeiliges oder Einzeiliges Select (Auswahlliste).
		$multiple = ($fieldConfig['size'] > 1) ? ' size="' . $fieldConfig['size'] . '" multiple="multiple"' : '';

		if ($fieldConfig['renderMode'] == 'checkbox' && $fieldConfig['maxitems'] > 1) {
			$content .= '<div class="check_item_wrapper">';
			$content .= $optionlist;
			$content .= '</div>';
		} else {
			$content .= '<select id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '][]"' . $multiple . '>';
			$content .= $optionlist;
			$content .= '</select>';
		}

		return $content;
	}

	/**
	 * Rendert Groupfelder.
	 *
	 * @param	string		$fieldName
	 * @param	array		$arrCurrentData
	 * @return	string		$content
	 */
	function showGroup($fieldName, $arrCurrentData) {
		$content = '';
		$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

		// GROUP (z.B. Files oder externe Tabellen).
		// Wenn es sich um den "internal_type" FILE handelt && es ein Bild ist, dann ein Vorschaubild erstellen und ein File-Inputfeld anzeigen.
		if ($fieldConfig['internal_type'] == 'file') {
			// Verzeichniss ermitteln.
			$uploadFolder = $fieldConfig['uploadfolder'];

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
			if (!$arrCurrentData[$fieldName]) {
				$content .= '<input type="file" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . ']" />';
			} else {
				$content .= '<div class="image_delete"><input type="checkbox" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_delete" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '_delete]" />';
				$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_delete">' . $this->getLabel('image_delete') . '</label></div>';
			}
		}

		// Wenn es sich um den "internal_type" DB handelt.
		if ($fieldConfig['internal_type'] == 'db') {
			$arrItems = array();
			$arrAllowed = t3lib_div::trimExplode(',', $fieldConfig['allowed'], true);

			foreach ($arrAllowed as $table) {
				if ($GLOBALS['TCA'][$table]) {
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, ' . $GLOBALS['TCA'][$table]['ctrl']['label'] , $table, '1 ' . $this->cObj->enableFields($table));

					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						$arrItems[$table . '_' . $row['uid']] = $row[$GLOBALS['TCA'][$table]['ctrl']['label']];
					}
				}
			}

			$i = 1;
			$content .= '<div class="group_item_wrapper">';

			foreach ($arrItems as $key => $label) {
				// Moeglichkeit das der gespeicherte Wert eine kommseparierte Liste ist, daher aufsplitten in ein Array, wie es auch von einem abgesendeten Formular kommen wuerde.
				if (!is_array($arrCurrentData[$fieldName])) {
					$arrCurrentData[$fieldName] = t3lib_div::trimExplode(',', $arrCurrentData[$fieldName], true);
				}

				$checked = (array_intersect(array($key, substr($key, strripos($key, '_') + 1)), $arrCurrentData[$fieldName])) ? ' checked="checked"' : '';
				
				$content .= '<div id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $i . '_wrapper" class="group_item group_item_' . $i . '">';
				$content .= '<input type="checkbox" id="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $key . '" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '][]" value="' . $key . '"' . $checked . ' />';
				$content .= '<label for="' . $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $key . '">'. $label . '</label></div>';
				$i++;
			}

			$content .= '</div>';
		}

		return $content;
	}

	/**
	 * Erstellt Hidden Fields fuer vordefinierte Parameter die uebergeben wurden.
	 *
	 * @return	string		$content
	 */
	function makeHiddenFields() {
		$content = '';

		foreach ($this->arrHiddenParams as $paramName) {
			if ($_REQUEST[$paramName]) {
				$content .= '<input type="hidden" name="' . $paramName . '" value="' . htmlspecialchars($_REQUEST[$paramName]) . '" />';
			}
		}

		return $content;
	}

	/**
	 * Erstellt GET-Parameter fuer vordefinierte Parameter die uebergeben wurden.
	 *
	 * @return	string		$params
	 */
	function makeHiddenParams() {
		$params = '';

		foreach ($this->arrHiddenParams as $paramName) {
			if ($_REQUEST[$paramName]) {
				$params .= '&' . urlencode($paramName) . '=' . $this->cleanHeaderUrlData($_REQUEST[$paramName]);
			}
		}

		return $params;
	}

	/**
	 * Konvertiert einen String um ihn in der PHP Funktion header nutzen zu koennen.
	 *
	 * @param	string		$data
	 * @return	string		$data
	 */
	function cleanHeaderUrlData($data) {
		$data = urlencode(strip_tags(preg_replace("/[\r\n]/", '', $data)));

		return $data;
	}

	/**
	 * Ueberprueft ob das uebergebene Feld benoetigt wird um erfolgreich zu speichern.
	 *
	 * @param	string		$fieldName
	 * @return	string
	 */
	function checkIfRequired($fieldName) {
		if (array_intersect(array($fieldName, '--' . $fieldName . '--'), $this->arrRequiredFields)) {
			return '<span class="required_item_star">*</span>';
		} else {
			return '';
		}
	}

	/**
	 * Ermittelt ein bestimmtes Label aufgrund des im TCA gespeicherten Languagestrings, des Datenbankfeldnamens oder gibt einfach den uebergeben Wert wieder aus, wenn nichts gefunden wurde.
	 *
	 * @param	string		$fieldName
	 * @param	boolean		$checkRequired
	 * @return	string		$label
	 */
	function getLabel($fieldName, $checkRequired = true) {
		if (strpos($fieldName, 'LLL:') === false) {
			// Label aus der Konfiguration holen basierend auf dem Datenbankfeldnamen.
			$label = $this->pi_getLL($fieldName);

			// Das Label zurueckliefern, falls vorhanden.
			if ($label) {
				return $label . (($checkRequired) ? $this->checkIfRequired($fieldName) : '');
			}

			// LanguageString ermitteln.
			$languageString = $this->feUsersTca['columns'][$fieldName]['label'];
		} else {
			$languageString = $fieldName;
		}

		// Das Label zurueckliefern.
		$label = $GLOBALS['TSFE']->sL($languageString);

		// Das Label zurueckliefern, falls vorhanden.
		if ($label) {
			return $label . (($checkRequired) ? $this->checkIfRequired($fieldName) : '');
		}

		// Wenn gar nichts gefunden wurde den uebergebenen Wert wieder zurueckliefern.
		return $fieldName . (($checkRequired) ? $this->checkIfRequired($fieldName) : '');
	}

	/**
	 * Ermittelt das Error Lebel aus dem feldnamen.
	 *
	 * @param	string		$fieldName
	 * @param	array		$valueCheck
	 * @return	string
	 */
	function getErrorLabel($fieldName, $valueCheck) {
		$label = '';

		// Extra Error Label ermitteln.
		if (array_key_exists($fieldName, $valueCheck) && is_string($valueCheck[$fieldName])) {
			$label = '<div class="form_error ' . $fieldName . '_error">' . $this->getLabel($fieldName . '_error_' . $valueCheck[$fieldName]) . '</div>';
		}

		return $label;
	}

	/**
	 * Holt Konfigurationen aus der Flexform (Tab-bedingt) und ersetzt diese pro Konfiguration in der TypoScript Konfiguration.
	 *
	 * @return	void
	 * @global	$this->extConf
	 * @global	$this->conf
	 */
	function getConfiguration() {
		$conf = array();

		// Extension Konfiguration ermitteln.
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

		// Alle Tabs der Flexformkonfiguration durchgehn.
		foreach ($this->cObj->data['pi_flexform']['data'] as $tabKey => $val) {
			$this->readFlexformTab($this->cObj->data['pi_flexform'], $conf, $tabKey);
		}

		// Alle gesammelten Konfigurationen in $this->conf uebertragen.
		foreach ($conf as $key => $val) {
			if (is_array($val) && $this->extConf['useIRRE']) {
				// Wenn IRRE Konfiguration uebergeben wurde und in der Extension Konfiguration gesetzt ist...
				$this->conf[$key] = $val;
			} else {
				// Alle anderen Konfigurationen...
				$this->setFlexformConfiguration($key, $val);
			}
		}

		// Die IRRE Konfiguration abarbeiten.
		if ($this->extConf['useIRRE'] && $this->conf['databasefields']) {
			$this->setIrreConfiguration();
		}

		// Konfigurationen, die an mehreren Stellen benoetigt werden, in globales Array schreiben.
		$this->arrUsedFields = t3lib_div::trimExplode(',', $this->conf['usedfields'], true);
		$this->arrRequiredFields = array_unique(t3lib_div::trimExplode(',', $this->conf['requiredfields'], true));
		$this->arrUniqueFields = array_unique(t3lib_div::trimExplode(',', $this->conf['uniquefields'], true));
		$this->arrHiddenParams = array_unique(t3lib_div::trimExplode(',', $this->conf['hiddenparams'], true));

		// Konfigurationen die immer gelten setzten (Feldnamen sind fuer konfigurierte Felder und fuer input Felder).
		$this->arrRequiredFields[] = '--passwordconfirmation--';
	}

	/**
	 * Parst das Flexform Konfigurations Array und schreibt alle Werte in $conf.
	 *
	 * @param	array		$flexData
	 * @param	array		$conf // Call by reference Array mit allen zu updatenden Daten.
	 * @param	string		$sType
	 * @return	void
	 */
	function readFlexformTab($flexData, &$conf, $sTab) {
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
							 $this->readFlexformTab($element, $conf[$key][$ekey], $sTab);
						 }
					 }
				 } else {
					 $this->readFlexformTab($value['el'], $conf, $sTab);
				 }

				 if ($value['vDEF']) {
					 $conf[$key] = $value['vDEF'];
				 }
			 }
		 }
	 }

	/**
	 * Ueberschreibt eventuell vorhandene TypoScript Konfigurationen mit den Konfigurationen aus der Flexform.
	 *
	 * @param	string		$key
	 * @param	string		$value
	 * @return	void
	 * @global	$this->conf
	 */
	function setFlexformConfiguration($key, $value) {
		if (strpos($key, '.') !== false && $value) {
			$arrKey = t3lib_div::trimExplode('.', $key);

			for ($i = count($arrKey) - 1; $i >= 0; $i--) {
				$newValue = array();

				if ($i == count($arrKey) - 1) {
					$newValue[$arrKey[$i]] = $value;
				} else {
					$newValue[$arrKey[$i] . '.'] = $value;
				}

				$value = $newValue;
			}

			$this->conf = t3lib_div::array_merge_recursive_overrule($this->conf, $value);
		} else if ($value) {
			$this->conf[$key] = $value;
		}
	}

	/**
	 * Ueberschreibt eventuell vorhandene TypoScript Konfigurationen oder Flexform Konfigurationen mit den Konfigurationen aus IRRE.
	 *
	 * @return	void
	 * @global	$this->conf
	 */
	function setIrreConfiguration() {
		$infoitems = 1;
		$fieldsets = 2;
		$userdeleteCounter = 0;
		$passwordconfirmationCounter = 0;
		$resendactivationCounter = 0;
		$usedfields = array();
		$requiredfields = array();
		$uniquefields = array();
		$firstkey = key($this->conf['databasefields']);

		foreach ($this->conf['databasefields'] as $position => $field) {
			// Datenbankfelder abarbeiten.
			if ($field['field']) {
				$usedfields[] = $field['field'];

				// Requiredfields erweitern.
				if ($field['required']) {
					$requiredfields[] = $field['field'];
				}

				// Uniquefields erweitern.
				if ($field['unique']) {
					$uniquefields[] = $field['field'];
				}

				// Label setzten falls angegeben.
				if ($field['label']) {
					$this->conf['_LOCAL_LANG.'][$GLOBALS['TSFE']->lang . '.'][$field['field']] = $field['label'];
				}
			}

			// Infoitems abarbeiten.
			if (isset($field['infoitem'])) {
				$usedfields[] = '--infoitem--';

				// Falls in dem Feld etwas drinn steht.
				if ($field['infoitem']) {
					$this->conf['infoitems.'][$infoitems] = $field['infoitem'];
				}

				$infoitems++;
			}

			// Separators / Legends abarbeiten.
			if (isset($field['separator'])) {
				// Beim aller ersten Separator / Legend bloss die Legend setzten!
				if ($position == $firstkey) {
					$this->conf['legends.']['1'] = $field['separator'];
				} else {
					$usedfields[] = '--separator--';

					// Falls in dem Feld etwas drinn steht.
					if ($field['separator']) {
						$this->conf['legends.'][$fieldsets] = $field['separator'];
					}

					$fieldsets++;
				}
			}

			// Userdelete Checkbox abarbeiten.
			if (isset($field['userdelete']) && $userdeleteCounter < 1) {
				$usedfields[] = '--userdelete--';

				// Requiredfields erweitern.
				if ($field['required']) {
					$requiredfields[] = '--userdelete--';
				}

				// Label setzten falls angegeben.
				if ($field['userdelete']) {
					$this->conf['_LOCAL_LANG.'][$GLOBALS['TSFE']->lang . '.']['userdelete'] = $field['userdelete'];
				}

				$userdeleteCounter++;
			}

			// Passwordconfirmation Feld abarbeiten.
			if (isset($field['passwordconfirmation']) && $passwordconfirmationCounter < 1) {
				$usedfields[] = '--passwordconfirmation--';

				// Label setzten falls angegeben.
				if ($field['passwordconfirmation']) {
					$this->conf['_LOCAL_LANG.'][$GLOBALS['TSFE']->lang . '.']['passwordconfirmation'] = $field['passwordconfirmation'];
				}

				$passwordconfirmationCounter++;
			}

			// Resendactivation Feld abarbeiten.
			if (isset($field['resendactivation']) && $resendactivationCounter < 1) {
				$usedfields[] = '--resendactivation--';

				// Requiredfields erweitern.
				if ($field['required']) {
					$requiredfields[] = '--resendactivation--';
				}

				// Label setzten falls angegeben.
				if ($field['resendactivation']) {
					$this->conf['_LOCAL_LANG.'][$GLOBALS['TSFE']->lang . '.']['resendactivation'] = $field['resendactivation'];
				}

				$resendactivationCounter++;
			}

			// Submit Button abarbeiten.
			if (isset($field['submit'])) {
				$usedfields[] = '--submit--';

				// Label setzten falls angegeben.
				if ($field['submit']) {
					$this->conf['_LOCAL_LANG.'][$GLOBALS['TSFE']->lang . '.']['submit_' . $this->conf['showtype']] = $field['submit'];
				}
			}
		}

		// In Konfiguration uebertragen.
		$this->conf['usedfields'] = implode(',', $usedfields);
		$this->conf['requiredfields'] = implode(',', $requiredfields);
		$this->conf['uniquefields'] = implode(',', $uniquefields);
	}

	/**
	 * Gibt die komplette Validierungskonfiguration fuer die JavaScript Frontendvalidierung zurueck.
	 *
	 * @return	string		$configuration
	 */
	function getJSValidationConfiguration() {
		// Hier eine fertig generierte Konfiguration:
		// config[11]=[];
		// config[11]["username"]=[];
		// config[11]["username"]["validation"]=[];
		// config[11]["username"]["validation"]["type"]="username";
		// config[11]["username"]["valid"]="Der Benutzername darf keine Leerzeichen beinhalten!";
		// config[11]["username"]["required"]="Es muss ein Benutzername eingegeben werden!";
		// config[11]["password"]=[];
		// config[11]["password"]["validation"]=[];
		// config[11]["password"]["validation"]["type"]="password";
		// config[11]["password"]["equal"]="Es muss zwei mal das gleiche Passwort eingegeben werden!";
		// config[11]["password"]["validation"]["size"]="6";
		// config[11]["password"]["size"]="Das Passwort muss mindestens 6 Zeichen lang sein!";
		// config[11]["password"]["required"]="Es muss ein Passwort angegeben werden!";
		// inputids[11] = new Array("tx_datamintsfeuser_pi1_username", "tx_datamintsfeuser_pi1_password_1", "tx_datamintsfeuser_pi1_password_2");
		// contentid[11] = 11;

		$arrValidationFields = array();
		$configuration = 'var ' . $this->extKey . '_config=[];var ' . $this->extKey . '_inputids=[];var ' . $this->extKey . '_contentids=[];config[' . $this->contentId . ']=[];';

		// Bei jedem Durchgang der Schliefe wird die Konfiguration fuer ein Datenbankfeld geschrieben. Ausnahmen sind hierbei Passwordfelder.
		// Gleichzeitig werden die ID's der Felder in ein Array geschrieben und am Ende zusammen gesetzt "inputids".
		foreach ($this->arrUsedFields as $fieldName) {
			if ($this->feUsersTca['columns'][$fieldName] && (is_array($this->conf['validate.'][$fieldName . '.']) || in_array($fieldName, $this->arrRequiredFields))) {
				$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

				if ($this->conf['validate.'][$fieldName . '.']['type'] == 'password') {
					$arrValidationFields[] = $this->extKey . '_' . $this->contentId . '_' . $fieldName;
					$arrValidationFields[] = $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_rep';
				} else if ($fieldConfig['type'] == 'radio') {
					foreach ($fieldConfig['items'] as $item) {
						$arrValidationFields[] = $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $item['1'];
					}
				} else if ($fieldConfig['type'] == 'group' && $fieldConfig['internal_type'] == 'db') {
					$arrAllowed = t3lib_div::trimExplode(',', $fieldConfig['allowed'], true);

					foreach ($arrAllowed as $table) {
						if ($GLOBALS['TCA'][$table]) {
							$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid' , $table, '1 ' . $this->cObj->enableFields($table));

							while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
								$arrValidationFields[] = $this->extKey . '_' . $this->contentId . '_' . $fieldName . '_item_' . $table . '_' . $row['uid'];
							}
						}
					}
				} else {
					$arrValidationFields[] = $this->extKey . '_' . $this->contentId . '_' . $fieldName;
				}

				$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]=[];';

				if (is_array($this->conf['validate.'][$fieldName . '.'])) {
					$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]=[];';

					// Da es mehrere Validierungskonfiguration pro Feld geben kann, muss hier jede einzeln durchgelaufen werden.
					foreach ($this->conf['validate.'][$fieldName . '.'] as $key => $val) {
						if ($key == 'length') {
							$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]["size"]="' . str_replace('"', '\\"', $val) . '";';
							$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["size"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_length')) . '";';
						} else if ($key == 'regexp') {
							// Da In JavaScript die regulaeren Ausdruecke nicht in einem String vorkommen duerfen diese entsprechen konvertieren (Slash am Anfang und am Ende).
							// Um Fehler im regulaeren Ausdruck zu vermeiden, werden hier alle Slashes entfernt, "\/" wird debei nicht beruecksichtigt!
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

							// Dann alle Slashes aus dem String entfernen, unter beruecksichtigung von "\/"!
							$val = preg_replace('/([^\\\])\//', '$1', $val);
							$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]["' . $key . '"]=' . $regexpStart . $val . $regexpEnd . ';';
						} else {
							$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]["' . $key . '"]="' . str_replace('"', '\\"', $val) . '";';
						}

						if ($key == 'type' && $val == 'password') {
							$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["equal"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_equal')) . '";';
						}
					}

					if ($this->conf['validate.'][$fieldName . '.']['type'] != 'password') {
						$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["valid"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_valid')) . '";';
					}
				}

				if (in_array($fieldName, $this->arrRequiredFields)) {
					$configuration .= 'config[' . $this->contentId . ']["' . $fieldName . '"]["required"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_required')) . '";';
				}
			}
		}

		$configuration .= 'inputids[' . $this->contentId . ']=new Array("' . implode('","', $arrValidationFields) . '");contentids[' . $this->contentId . ']=' . $this->contentId . ';';

		return $configuration;
	}

	/**
	 * Ueberschreibt eventuell vorhandene TCA Konfiguration mit TypoScript Konfiguration.
	 *
	 * @return	void
	 * @global	$this->feUsersTca
	 */
	function getFeUsersTca() {
		$GLOBALS['TSFE']->includeTCA();
		$this->feUsersTca = $GLOBALS['TCA']['fe_users'];

		if ($this->conf['fieldconfig.']) {
			$this->feUsersTca['columns'] = t3lib_div::array_merge_recursive_overrule((array)$this->feUsersTca['columns'], (array)t3lib_div::removeDotsFromTS($this->conf['fieldconfig.']));
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
	 * Checks if a string is utf8 encoded or not.
	 *
	 * @param	string		$str
	 * @return	boolean
	 */
	function checkUtf8($str) {
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++) {
			$c = ord($str[$i]);

			if ($c > 128) {
				if (($c > 247)) {
					return false;
				} else if ($c > 239) {
					$bytes = 4;
				} else if ($c > 223) {
					$bytes = 3;
				} else if ($c > 191) {
					$bytes = 2;
				} else {
					return false;
				}

				if (($i + $bytes) > $len) {
					return false;
				}

				while ($bytes > 1) {
					$i++;
					$b = ord($str[$i]);

					if ($b < 128 || $b > 191) {
						return false;
					}

					$bytes--;
				}
			}
		}

		return true;
	}

	/**
	 * Cleans an array from all empty elements.
	 *
	 * @param	string		$array
	 * @return	string
	 */
	function cleanArray($array) {
		foreach ($array as $key => $val) {
			if (!$val) {
				unset($array[$key]);
			}
		}

		return $array;
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1.php']);
}

?>
