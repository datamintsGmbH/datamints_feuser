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
 *   97: class tx_datamintsfeuser_pi1 extends tslib_pibase
 *  155:     function main($content, $conf)
 *  234:     function doFormSubmit()
 *  421:     function checkValid()
 *  574:     function checkUnique()
 *  607:     function checkRequired()
 *  667:     function checkCaptcha($value)
 *  733:     function cleanPasswordField($fieldName, $fieldConfig, $arrUpdate)
 *  755:     function cleanCheckboxField($fieldName, $fieldConfig, $arrUpdate)
 *  790:     function cleanMultipleSelectboxField($fieldName, $fieldConfig, $arrUpdate)
 *  826:     function cleanGroupAndMultipleCheckboxField($fieldName, $fieldConfig, $arrUpdate)
 *  870:     function cleanUncleanedField($fieldName, $fieldConfig, $arrUpdate)
 *  898:     function saveDeleteFiles($fieldName, $fieldConfig, $arrUpdate, &$error = '')
 * 1012:     function copyFields($arrUpdate)
 * 1068:     function doUserEdit($arrUpdate)
 * 1106:     function doUserRegister($arrUpdate)
 * 1171:     function showOutputRedirect($mode, $submode = '', $params = array())
 * 1276:     function sendActivationMail($userId = 0)
 * 1323:     function doApprovalCheck()
 * 1401:     function getApprovalTypes()
 * 1412:     function setNotActivatedCookie($userId)
 * 1425:     function getNotActivatedUserArray($arrNotActivated = array())
 * 1457:     function sendMail($userId, $templatePart, $adminMail, $config, $extraMarkers = array(), $extraSuparts = array())
 * 1579:     function isAdminMail($approvalType)
 * 1591:     function getTemplateSubpart($templatePart, $markerArray = array(), $config = array())
 * 1612:     function getChangedForMail($arrNewData, $config)
 * 1649:     function getPasswordForMail()
 * 1670:     function showForm($valueCheck = array())
 * 1885:     function showInput($fieldName, $fieldConfig, $arrCurrentData, $valueCheck, $disabledField = '')
 * 1936:     function showText($fieldName, $fieldConfig, $arrCurrentData, $disabledField = '')
 * 1953:     function showCheck($fieldName, $fieldConfig, $arrCurrentData, $disabledField = '')
 * 2007:     function showRadio($fieldName, $fieldConfig, $arrCurrentData, $disabledField = '')
 * 2043:     function showSelect($fieldName, $fieldConfig, $arrCurrentData, $disabledField = '')
 * 2140:     function showGroup($fieldName, $fieldConfig, $arrCurrentData, $disabledField = '')
 * 2255:     function showCaptcha($fieldName, $valueCheck)
 * 2324:     function getFieldId()
 * 2346:     function getFieldName()
 * 2369:     function getLabel($fieldName, $checkRequired = true)
 * 2412:     function getErrorType($fieldName, $valueCheck)
 * 2429:     function getErrorClass($fieldName, $valueCheck)
 * 2447:     function getErrorLabel($fieldName, $valueCheck)
 * 2464:     function isRequiredField($fieldName)
 * 2479:     function getTableLabelFieldName($table)
 * 2494:     function getHiddenParamsArray()
 * 2511:     function getHiddenParamsHiddenFields()
 * 2560:     function getParamArrayFromParamNameParts($arrParamNameParts, &$arrRequest, &$arrParams)
 * 2603:     function determineConfiguration()
 * 2649:     function determineIrreConfiguration()
 * 2801:     function getConfigurationByShowtype($subConfig = '')
 * 2814:     function getJSValidationConfiguration()
 *
 *
 * TOTAL FUNCTIONS: 49
 *
 */

require_once PATH_tslib . 'class.tslib_pibase.php';
require_once t3lib_extmgm::extPath('datamints_feuser', 'lib/class.tx_datamintsfeuser_utils.php');

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
	var $conf = array();
	var $lang = array();
	var $extConf = array();
	var $feUsersTca = array();
	var $userId = 0;
	var $contentUid = 0;
	var $storagePid = 0;
	var $arrUsedFields = array();
	var $arrUniqueFields = array();
	var $arrRequiredFields = array();
	var $arrHiddenParams = array();

	const modeKeySend = 'send';
	const modeKeyApprovalcheck = 'approvalcheck';

	const submodeKeySent = 'sent';
	const submodeKeyFailure = 'failure';
	const submodeKeySuccess = 'success';

	const showtypeKeyEdit = 'edit';
	const showtypeKeyRegister = 'register';

	const validationerrorKeySize = 'size';
	const validationerrorKeyType = 'type';
	const validationerrorKeyEqual = 'equal';
	const validationerrorKeyValid = 'valid';
	const validationerrorKeyDelete = 'delete';
	const validationerrorKeyLength = 'length';
	const validationerrorKeyUnique = 'unique';
	const validationerrorKeyUpload = 'upload';
	const validationerrorKeyRequired = 'required';

	const submitparameterKeyHash = 'hash';
	const submitparameterKeyMode = 'submit';
	const submitparameterKeyPage = 'pageid';
	const submitparameterKeyUser = 'userid';
	const submitparameterKeySubmode = 'submitmode';

	const specialfieldKeySubmit = 'submit';
	const specialfieldKeyCaptcha = 'captcha';
	const specialfieldKeyInfoitem = 'infoitem';
	const specialfieldKeySeparator = 'separator';
	const specialfieldKeyUserdelete = 'userdelete';
	const specialfieldKeyResendactivation = 'resendactivation';
	const specialfieldKeyPasswordconfirmation = 'passwordconfirmation';

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content
	 * @param	array		$conf
	 * @return	string		$content
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

		// PiVars und Flexform laden.
		$this->pi_setPiVarDefaults();
		$this->pi_initPIflexForm();

		// Erst die Konfiguration und dann die Labels laden, damit die in der Flexform gesetzten Labels auch beruecksichtigt werden!
		$this->determineConfiguration();
		$this->pi_loadLL();

		// ToDo: Bessere Lösung für das Problem ab 4.6.? finden, dass ein Label nur zum LOCAL_LANG Array hinzugefügt wird, wenn die Sprache bereits im Array vorhanden ist!
		if (t3lib_div::compat_version('4.6')) {
			foreach (t3lib_div::removeDotsFromTS((array)$this->conf['_LOCAL_LANG.']) as $lang => $arrLang) {
				foreach ($arrLang as $langKey => $langValue) {
					$this->LOCAL_LANG[$lang][$langKey]['0'] = $this->LOCAL_LANG['default'][$langKey]['0'];

					$this->LOCAL_LANG[$lang][$langKey]['0']['target'] = $langValue;
				}
			}
		}

		$this->feUsersTca = tx_datamintsfeuser_utils::getFeUsersTca($this->conf['fieldconfig.']);
		$this->storagePid = tx_datamintsfeuser_utils::getStoragePid($this->getConfigurationByShowtype('userfolder'));

		// Stylesheets in den Head einbinden.
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . '[stylesheet]'] = ($this->conf['disablestylesheet']) ? '' : '<link rel="stylesheet" type="text/css" href="' . (($this->conf['stylesheetpath']) ? $this->conf['stylesheetpath'] : tx_datamintsfeuser_utils::getTypoLinkUrl(t3lib_extMgm::siteRelPath($this->extKey) . 'res/datamints_feuser.css')) . '" />';

		// Javascripts in den Head einbinden.
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . '[jsvalidator]'] = ($this->conf['disablejsvalidator']) ? '' : '<script type="text/javascript" src="' . (($this->conf['jsvalidatorpath']) ? $this->conf['jsvalidatorpath'] : tx_datamintsfeuser_utils::getTypoLinkUrl(t3lib_extMgm::siteRelPath($this->extKey) . 'res/validator.min.js')) . '"></script>';
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . '[jsvalidation][' . $this->contentId . ']'] = ($this->conf['disablejsconfig']) ? '' : '<script type="text/javascript">' . "\n/*<![CDATA[*/\n" . $this->getJSValidationConfiguration() . "\n/*]]>*/\n" . '</script>';

		// Wenn nicht eingeloggt kann man auch nicht editieren!
		if ($this->conf['showtype'] == self::showtypeKeyEdit && !$this->userId) {
			return $this->pi_wrapInBaseClass($this->showOutputRedirect('edit_error', 'login'));
		}

		// Wenn ein "userfolder" angegeben ist, der aktuelle User aber nicht in diesem ist, kann man auch nicht editieren!
		if ($this->conf['showtype'] == self::showtypeKeyEdit && $this->getConfigurationByShowtype('userfolder') && $GLOBALS['TSFE']->fe_user->user['pid'] != $this->storagePid) {
			return $this->pi_wrapInBaseClass($this->showOutputRedirect('edit_error', 'storage'));
		}

		switch ($this->piVars[$this->contentId][self::submitparameterKeyMode]) {

			case self::modeKeySend:
				$content = $this->doFormSubmit();
				break;

			case self::modeKeyApprovalcheck:
				// Userid ermitteln und Aktivierung durchfuehren.
				$this->userId = intval($this->piVars[$this->contentId]['uid']);

				$content = $this->doApprovalCheck();
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
	function doFormSubmit() {
		$mode = '';
		$submode = '';
		$params = array();
		$arrUpdate = array();

		// Jedes Element in piVars trimmen.
		array_walk_recursive($this->piVars[$this->contentId], 'tx_datamintsfeuser_utils::trimCallback');

		// Eine Validierung durchfuehren ueber alle Felder die eine gesonderte Konfigurtion bekommen haben.
		$validCheck = $this->checkValid();

		// Ueberpruefen ob Datenbankeintraege mit den uebergebenen Daten uebereinstimmen.
		$uniqueCheck = $this->checkUnique();

		// Ueberpruefen ob in allen benoetigten Feldern etwas drinn steht.
		$requiredCheck = $this->checkRequired();

		// Wenn bei der Validierung ein Feld nicht den Anforderungen entspricht noch einmal die Form anzeigen und entsprechende Felder markieren.
		$valueCheck = array_merge($validCheck, $uniqueCheck, $requiredCheck);

		if (count($valueCheck) > 0) {
			return $this->showForm($valueCheck);
		}

		// Wenn der User eine neue Aktivierungsmail beantragt hat.
		if ($this->piVars[$this->contentId][self::specialfieldKeyResendactivation] && in_array(tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyResendactivation), $this->arrUsedFields)) {
			// Falls der Anzeigetyp "list" ist (Liste der im Cookie gespeicherten User), alle uebergebenen User ermitteln und fuer das erneute zusenden verwenden. Ansonsten die uebergebene E-Mail verwenden.
//			if ($this->conf['shownotactivated'] == 'list') {
//				$arrNotActivated = $this->getNotActivatedUserArray($this->piVars[$this->contentId][$fieldName]);
//				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tx_datamintsfeuser_approval_level', 'fe_users', 'pid = ' . $this->storagePid . ' AND uid IN(' . implode(',', $arrNotActivated) . ') AND disable = 1 AND deleted = 0');
//			} else {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tx_datamintsfeuser_approval_level', 'fe_users', 'pid = ' . $this->storagePid . ' AND email = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(strtolower($this->piVars[$this->contentId][self::specialfieldKeyResendactivation]), 'fe_users') . ' AND disable = 1 AND deleted = 0', '', '', '1');
//			}

			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				// Genehmigungstypen aufsteigend sortiert ermitteln. Das ist noetig um das Level dem richtigen Typ zuordnen zu koennen.
				// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig laesst.
				$arrApprovalTypes = $this->getApprovalTypes();
				$approvalType = $arrApprovalTypes[count($arrApprovalTypes) - $row['tx_datamintsfeuser_approval_level']];

				// Ausgabe vorbereiten.
				$mode = self::specialfieldKeyResendactivation;

				// Fehler anzeigen, falls das naechste aktuelle Genehmigungsverfahren den Admin betrifft.
				$submode = self::submodeKeyFailure;

				// Aktivierungsmail senden und Ausgabe anpassen.
				if ($approvalType && !$this->isAdminMail($approvalType)) {
					$submode = self::submodeKeySent;

					$this->sendActivationMail($row['uid']);
				}
			}

			return $this->showOutputRedirect($mode, $submode);
		}

		// Wenn die Zielseite, der User oder der Bearbeitungsmodus nicht stimmen, dann wird abgebrochen. Andernfalls wird in die Datenbank geschrieben.
		if ($this->piVars[$this->contentId][self::submitparameterKeyPage] != $GLOBALS['TSFE']->id || $this->piVars[$this->contentId][self::submitparameterKeyUser] != $this->userId || $this->piVars[$this->contentId][self::submitparameterKeySubmode] != $this->conf['showtype']) {
			return $this->showOutputRedirect($mode, $submode);
		}

		// Sonderfaelle behandeln!
		foreach ($this->arrUsedFields as $fieldName) {
			if ($this->feUsersTca['columns'][$fieldName]) {
				$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];
				$arrFieldConfigEval = t3lib_div::trimExplode(',', $fieldConfig['eval'], true);

				// Ist das Feld schon gesaeubert worden (MySQL, PHP, HTML, ...).
				$isCleaned = false;

				// Datumsfelder und Datumzeitfelder behandeln.
				if (in_array('date', $arrFieldConfigEval) || in_array('datetime', $arrFieldConfigEval)) {
					$arrUpdate[$fieldName] = strtotime($this->piVars[$this->contentId][$fieldName]);
					$isCleaned = true;
				}

				// Passwordfelder behandeln.
				if (in_array('password', $arrFieldConfigEval)) {
					$arrUpdate = $this->cleanPasswordField($fieldName, $fieldConfig, $arrUpdate);
					$isCleaned = true;
				}

				// Read only behandeln.
				if ($fieldConfig['readOnly']) {
					$isCleaned = true;
				}

				// Checkboxen behandeln.
				if ($fieldConfig['type'] == 'check') {
					$arrUpdate = $this->cleanCheckboxField($fieldName, $fieldConfig, $arrUpdate);
					$isCleaned = true;
				}

				// Multiple Selectboxen.
				if ($fieldConfig['type'] == 'select' && $fieldConfig['size'] > 1) {
					$arrUpdate = $this->cleanMultipleSelectboxField($fieldName, $fieldConfig, $arrUpdate);
					$isCleaned = true;
				}

				// Group, Bildfelder behandeln.
				if ($fieldConfig['type'] == 'group' && $fieldConfig['internal_type'] == 'file') {
					$arrUpdate[$fieldName] = $GLOBALS['TSFE']->fe_user->user[$fieldName];

					// Das Bild hochladen oder loeschen. Gibt einen Fehlerstring per Referenz zurueck falls ein Fehler auftritt!
					$arrUpdate = $this->saveDeleteFiles($fieldName, $fieldConfig, $arrUpdate, $valueCheck[$fieldName]);

					if ($valueCheck[$fieldName]) {
						return $this->showForm($valueCheck);
					}

					$isCleaned = true;
				}

				// Group, Multiple Checkboxen.
				if ($fieldConfig['type'] == 'group' && $fieldConfig['internal_type'] == 'db') {
					$arrUpdate = $this->cleanGroupAndMultipleCheckboxField($fieldName, $fieldConfig, $arrUpdate);
					$isCleaned = true;
				}

				// Wenn noch nicht gesaeubert dann nachholen!
				if (!$isCleaned && isset($this->piVars[$this->contentId][$fieldName])) {
					$arrUpdate = $this->cleanUncleanedField($fieldName, $fieldConfig, $arrUpdate);
				}
			}
		}

		// Konvertiert alle moeglichen Zeichen die fuer die Ausgabe angepasst wurden zurueck.
		$arrUpdate = tx_datamintsfeuser_utils::htmlspecialcharsPostArray($arrUpdate, true);

		// Zusatzfelder setzten, die nicht aus der Form uebergeben wurden.
		$arrUpdate['tstamp'] = time();

		// Wenn der User geloescht werden soll.
		if ($this->piVars[$this->contentId][self::specialfieldKeyUserdelete] && in_array(tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyUserdelete), $this->arrUsedFields)) {
			$arrUpdate['deleted'] = '1';
		}

		// Kopiert den Inhalt eines Feldes in ein anderes Feld.
		$arrUpdate = $this->copyFields($arrUpdate);

		// Der User hat seine Daten editiert.
		if ($this->conf['showtype'] == self::showtypeKeyEdit) {
			$arrMode = $this->doUserEdit($arrUpdate);

			// Ausgabe vorbereiten.
			$mode = $arrMode['mode'];
			$submode = $arrMode['submode'];
		}

		// Ein neuer User hat sich angemeldet.
		if ($this->conf['showtype'] == self::showtypeKeyRegister) {
			$arrMode = $this->doUserRegister($arrUpdate);

			// Ausgabe vorbereiten.
			$mode = $arrMode['mode'];
			$submode = $arrMode['submode'];
			$params = $arrMode['params'];
		}

		// Hook um weiter Userupdates zu machen.
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['sendForm'])) {
			$_params = array(
					'variables' => array(
							'arrUpdate' => $arrUpdate
						),
					'parameters' => array(
							'mode' => &$mode,
							'submode' => &$submode,
							'params' => &$params
						)
				);

			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['sendForm'] as $_funcRef) {
				t3lib_div::callUserFunction($_funcRef, $_params, $this);
			}
		}

		return $this->showOutputRedirect($mode, $submode, $params);
	}

	/**
	 * Ueberprueft ob alle Validierungen eingehalten wurden.
	 *
	 * @return	array		$valueCheck
	 */
	function checkValid() {
		$valueCheck = array();

		// Alle ausgewaehlten Felder durchgehen.
		foreach ($this->arrUsedFields as $fieldName) {
			$fieldName = tx_datamintsfeuser_utils::getSpecialFieldName($fieldName);
			$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

			$value = $this->piVars[$this->contentId][$fieldName];
			$validate = $this->conf['validate.'][$fieldName . '.'];

			// Besonderes Feld das fest in der Extension verbaut ist (passwordconfirmation), und ueberprueft werden soll.
			if ($fieldName == self::specialfieldKeyPasswordconfirmation && $this->conf['showtype'] == self::showtypeKeyEdit) {
				if (!tx_datamintsfeuser_utils::checkPassword($value, $GLOBALS['TSFE']->fe_user->user['password'])) {
					$valueCheck[$fieldName] = self::validationerrorKeyValid;
				}
			}

			// Besonderes Feld das fest in der Extension verbaut ist (resendactivation), und ueberprueft werden soll.
			if ($fieldName == self::specialfieldKeyResendactivation && $value) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid) as count', 'fe_users', 'pid = ' . $this->storagePid . ' AND (uid = ' . intval($value) . ' OR email = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(strtolower($value), 'fe_users') . ') AND disable = 1 AND deleted = 0');
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

				if ($row['count'] < 1) {
					$valueCheck[$fieldName] = self::validationerrorKeyValid;
				}
			}

			// Besonderes Feld das fest in der Extension verbaut ist (captcha), und ueberprueft werden soll.
			// Fuer "jm_recaptcha" darf hier $value nicht ueberprueft werden, wurde aber vorerst entfernt, da das an mehreren Stellen beruecksichtigt werden muesste!
			if ($fieldName == self::specialfieldKeyCaptcha && $value) {
				$captchaCheck = $this->checkCaptcha($value);

				if ($captchaCheck) {
					$valueCheck[$fieldName] = $captchaCheck;
				}
			}

			// Wenn der im TypoScript angegebene Feldname nicht im TCA ist, dann naechstes Feld vornehmen.
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
					$valueRep = $this->piVars[$this->contentId][$fieldName . '_rep'];
					$arrLength[0] = '6';

					if ($value == $valueRep) {
						if ($validate['length']) {
							$arrLength = t3lib_div::trimExplode(',', $validate['length']);
						}

						if (!preg_match('/^.{' . $arrLength[0] . ',' . $arrLength[1] . '}$/', $value)) {
							$valueCheck[$fieldName] = self::validationerrorKeyLength;
						}
					} else {
						$valueCheck[$fieldName] = self::validationerrorKeyEqual;
					}
					break;

				case 'email':
					if (!preg_match('/^[a-zA-Z0-9\._%+-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,6}$/', $value)) {
						$valueCheck[$fieldName] = self::validationerrorKeyValid;
					}
					break;

				case 'username':
					if (!preg_match('/^[^ ]*$/', $value)) {
						$valueCheck[$fieldName] = self::validationerrorKeyValid;
					}
					break;

				case 'zero':
					if ($value == '0') {
						$valueCheck[$fieldName] = self::validationerrorKeyValid;
					}
					break;

				case 'emptystring':
					if ($value == '') {
						$valueCheck[$fieldName] = self::validationerrorKeyValid;
					}
					break;

				case 'custom':
					if ($validate['regexp']) {
						if (is_array($value)) {
							foreach ($value as $subValue) {
								if (!preg_match($validate['regexp'], $subValue)) {
									$valueCheck[$fieldName] = self::validationerrorKeyValid;
								}
							}
						} else {
							if (!preg_match($validate['regexp'], $value)) {
								$valueCheck[$fieldName] = self::validationerrorKeyValid;
							}
						}
					}
					if ($validate['length']) {
						$arrLength = t3lib_div::trimExplode(',', $validate['length']);

						if (is_array($value)) {
							if (($arrLength[0] && count($value) < $arrLength[0]) || ($arrLength[1] && count($value) > $arrLength[1])) {
								$valueCheck[$fieldName] = self::validationerrorKeyLength;
							}
						} else {
							if (!preg_match('/^.{' . $arrLength[0] . ',' . $arrLength[1] . '}$/', $value)) {
								$valueCheck[$fieldName] = self::validationerrorKeyLength;
							}
						}
					}
					break;

			}

		}

		return $valueCheck;
	}

	/**
	 * Ueberprueft die uebergebenen Inhalte, bei bestimmten Feldern, ob diese in der Datenbank schon vorhanden sind.
	 *
	 * @return	array		$valueCheck
	 */
	function checkUnique() {
		$where = '';
		$valueCheck = array();

		// Beim Bearbeiten, den eigenen Datensatz nicht ueberpruefen.
		if ($this->conf['showtype'] == self::showtypeKeyEdit) {
			$where .= ' AND uid <> ' . $this->userId;
		}

		// Wenn beim Bearbeiten keine "userfolder" gesetzt ist, soll global ueberprueft werden, ansonsten nur im Storage!
		if (!$this->conf['uniqueglobal'] && $this->getConfigurationByShowtype('userfolder')) {
			$where .= ' AND pid = ' . $this->storagePid;
		}

		foreach ($this->arrUniqueFields as $fieldName) {
			if ($this->piVars[$this->contentId][$fieldName]) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid) as count', 'fe_users', $fieldName . ' = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->piVars[$this->contentId][$fieldName], 'fe_users') . $where . ' AND deleted = 0');
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

				if ($row['count'] >= 1) {
					$valueCheck[$fieldName] = self::validationerrorKeyUnique;
				}
			}
		}

		return $valueCheck;
	}

	/**
	 * Ueberprueft ob alle benoetigten Felder mit Inhalten uebergeben wurden.
	 *
	 * @return	array		$valueCheck
	 */
	function checkRequired() {
		$valueCheck = array();

		// Geht alle benoetigten Felder durch und ermittelt fehlende.
		foreach ($this->arrRequiredFields as $fieldName) {
			$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];

			// Ueberpruefen, ob das Feld ueberhaupt angezeigt wurde.
			if (!in_array($fieldName, $this->arrUsedFields)) {
				continue;
			}

			$fieldName = tx_datamintsfeuser_utils::getSpecialFieldName($fieldName);
			$fieldValue = $this->piVars[$this->contentId][$fieldName];

			// Arrays zum Ueberpruefen normalisieren, und leere Werte entfernen!
			if (is_array($fieldValue)) {
				$fieldValue = implode(',', t3lib_div::trimExplode(',', implode(',', $fieldValue), true));
			}

			// Dadurch dass die einfache Checkbox ein besonderes verstecktes Feld hat (value="0"), muss dieser Wert erst normalisiert werden!
			if ($fieldConfig['type'] == 'check' && $fieldValue == '0') {
				$fieldValue = '';
			}

			$valueCheck[$fieldName] = self::validationerrorKeyRequired;

			// Fuer Felder vom Typ group (internal_type="file") wird eine Ueberpruefung auf eine vorhandene Datei gemacht.
			if ($fieldConfig['type'] == 'group' && $fieldConfig['internal_type'] == 'file') {
				$arrFieldVars = (array)$this->piVars[$this->contentId][$fieldName];
				$arrFilenames = t3lib_div::trimExplode(',', $GLOBALS['TSFE']->fe_user->user[$fieldName], true);

				foreach ($arrFieldVars['files'] as $sentKey => $filename) {
					$sentKey = intval($sentKey);
					$savedKey = array_search($filename, $arrFilenames);

					// Wenn eine Datei vorhanden (egal ob neu uebergeben oder bereits vorhanden) und diese nicht geloescht wird, wird kein Fehler zurueckgegeben!
					if ($_FILES[$this->prefixId]['name'][$this->contentId][$fieldName]['upload'][$sentKey] || ($savedKey !== false && !$arrFieldVars['delete'][$sentKey])) {
						unset($valueCheck[$fieldName]);
					}
				}

				continue;
			}

			// Durch die versteckten Felder wird immer ein Wert uebergeben, dadurch muss nur ueberprueft werden, ob der Inhalt ungleich einem Leerstring ist!
			if ($fieldValue != '') {
				unset($valueCheck[$fieldName]);
			}
		}

		return $valueCheck;
	}

	/**
	 * Ueberprueft ob das Captcha richtig eingegeben wurde.
	 *
	 * @param	string		$value
	 * @return	string
	 */
	function checkCaptcha($value) {
		if (!t3lib_extMgm::isLoaded($this->conf['captcha.']['use'])) {
			return '';
		}

		switch ($this->conf['captcha.']['use']) {

			case 'captcha':
				session_start();

				$captchaString = $_SESSION['tx_captcha_string'];

				if ($value != $captchaString) {
					return self::validationerrorKeyValid;
				}

				break;

			case 'sr_freecap':
				require_once(t3lib_extMgm::extPath($this->conf['captcha.']['use']) . 'pi2/class.tx_srfreecap_pi2.php');

				$freecap = t3lib_div::makeInstance('tx_srfreecap_pi2');

				if (!$freecap->checkWord($value)) {
					return self::validationerrorKeyValid;
				}

				break;

//			case 'jm_recaptcha':
//				require_once(t3lib_extMgm::extPath($this->conf['captcha.']['use']) . 'class.tx_jmrecaptcha.php');
//
//				$recaptcha = t3lib_div::makeInstance('tx_jmrecaptcha');
//
//				$status = $recaptcha->validateReCaptcha();
//
//				if (!$status['verified']) {
//					 return self::validationerrorKeyValid;
//				}
//
//				break;

			case 'wt_calculating_captcha':
				require_once(t3lib_extMgm::extPath($this->conf['captcha.']['use']) . 'class.tx_wtcalculatingcaptcha.php');

				$calculatingcaptcha = t3lib_div::makeInstance('tx_wtcalculatingcaptcha');

				if (!$calculatingcaptcha->correctCode($value)) {
					return self::validationerrorKeyValid;
				}

				break;

		}

		return '';
	}

	/**
	 * Falls angegebe das Passwort fuer ein Passwortfeld generieren und / oder verschluesseln.
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrUpdate
	 * @return	array		$arrUpdate
	 */
	function cleanPasswordField($fieldName, $fieldConfig, $arrUpdate) {
		// Password generieren und verschluesseln je nach Einstellung.
		$password = tx_datamintsfeuser_utils::generatePassword($this->piVars[$this->contentId][$fieldName], $this->getConfigurationByShowtype('generatepassword.'));
		$arrUpdate[$fieldName] = $password['encrypted'];

		// Wenn kein Password uebergeben wurde auch keins schreiben.
		if (!$arrUpdate[$fieldName]) {
			unset($arrUpdate[$fieldName]);
		}

		return $arrUpdate;
	}

	/**
	 * Saeubert Checkboxfelder, indem die uebergebenen Werte durch 1 oder 0 ausgetauscht werden.
	 * Gilt fuer eine oder mehrere Checkboxen (nicht fuer scrollbare Listen).
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrUpdate
	 * @return	array		$arrUpdate
	 */
	function cleanCheckboxField($fieldName, $fieldConfig, $arrUpdate) {
		$checkItemsCount = count($fieldConfig['items']);

		// Mehrere Checkboxen oder eine Checkbox.
		if ($checkItemsCount > 1) {
			$binString = '';

			for ($key = 0; $key < $checkItemsCount; $key++) {
				if ($this->piVars[$this->contentId][$fieldName][$key]) {
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

		return $arrUpdate;
	}

	/**
	 * Saeubert MultipleSelectboxfelder indem auf jeden uebergebenen Wert intval() angewendet wird.
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrUpdate
	 * @return	array		$arrUpdate
	 */
	function cleanMultipleSelectboxField($fieldName, $fieldConfig, $arrUpdate) {
		$maxItemsCount = 1;
		$arrCleanedValues = array();

		// Wenn nichts ausgewaehlt wurde, wird auch dieser Parameter nicht uebergeben, daher zuerst ueberpruefen, ob etwas vorhanden ist.
		if (!is_array($this->piVars[$this->contentId][$fieldName])) {
			return $arrUpdate;
		}

		foreach ($this->piVars[$this->contentId][$fieldName] as $val) {
			// Einen leeren String als Uebergabewert gibt es nicht, bzw. das ist das versteckte Feld, um alle Werte abwaehlen zu koennen!
			if ($val == '') {
				continue;
			}

			if ($fieldConfig['maxitems'] && $maxItemsCount > $fieldConfig['maxitems']) {
				break;
			}

			$arrCleanedValues[] = intval($val);
			$maxItemsCount++;
		}

		$arrUpdate[$fieldName] = implode(',', $arrCleanedValues);

		return $arrUpdate;
	}

	/**
	 * Saeubert Group- und MultipleCheckboxfelder (scrollbare Liste).
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrUpdate
	 * @return	array		$arrUpdate
	 */
	function cleanGroupAndMultipleCheckboxField($fieldName, $fieldConfig, $arrUpdate) {
		$maxItemsCount = 1;
		$arrCleanedValues = array();

		$arrAllowed = t3lib_div::trimExplode(',', $fieldConfig['allowed'], true);

		// Hier werden absichtlich nur die Erlaubten Tabellen benutzt, da es sonst unmengen an möglichen Optionen geben wuerde!
		foreach ($arrAllowed as $table) {
			if (!$GLOBALS['TCA'][$table] || !is_array($this->piVars[$this->contentId][$fieldName])) {
				continue;
			}

			foreach ($this->piVars[$this->contentId][$fieldName] as $val) {
				if ($fieldConfig['maxitems'] && $maxItemsCount > $fieldConfig['maxitems']) {
					break;
				}

				if (preg_match('/^' . $table . '_[0-9]+$/', $val)) {
					$arrCleanedValues[] = $val;
					$maxItemsCount++;
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

		return $arrUpdate;
	}

	/**
	 * Saeubert die uebrigen Felder (Input, Textarea, ...).
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrUpdate
	 * @return	array		$arrUpdate
	 */
	function cleanUncleanedField($fieldName, $fieldConfig, $arrUpdate) {
		// Wenn eine Selectbox die Ihren Inhalt aus einer anderen Tabelle hat angezeigt wurde, dann darf nur eine Zahl kommen!
		if ($fieldConfig['type'] == 'select' && $fieldConfig['foreign_table']) {
			$arrUpdate[$fieldName] = intval($this->piVars[$this->contentId][$fieldName]);

			return $arrUpdate;
		}

		// Ansonsten Standardsaeuberung.
		$arrUpdate[$fieldName] = strip_tags($this->piVars[$this->contentId][$fieldName]);

		// Wenn E-Mail Feld, alle Zeichen zu kleinen Zeichen konvertieren.
		if ($fieldName == 'email') {
			$arrUpdate[$fieldName] = strtolower($arrUpdate[$fieldName]);
		}

		return $arrUpdate;
	}

	/**
	 * The saveDeleteImage method is used to update or delete an image of an address
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrUpdate
	 * @param	string		$error // Call by reference Gibt den ersten auftretenden Fehler zurueck.
	 * @return	array		$arrUpdate
	 */
	function saveDeleteFiles($fieldName, $fieldConfig, $arrUpdate, &$error = '') {
		$arrFieldVars = (array)$this->piVars[$this->contentId][$fieldName];

		$maxSize = $fieldConfig['max_size'] * 1024;
		$uploadFolder = tx_datamintsfeuser_utils::fixPath($fieldConfig['uploadfolder']);
		$allowedTypes = t3lib_div::trimExplode(',', strtolower(str_replace('*', '', $fieldConfig['allowed'])), true);
		$disallowedTypes = t3lib_div::trimExplode(',', strtolower(str_replace('*', '', $fieldConfig['disallowed'])), true);

		$arrFilenames = t3lib_div::trimExplode(',', $arrUpdate[$fieldName], true);

		$error = '';
		$arrProcessedKeys = array();

		foreach ($arrFieldVars['files'] as $sentKey => $filename) {
			$sentKey = intval($sentKey);
			$savedKey = array_search($filename, $arrFilenames);

			// Falls schon abgearbeitet oder die maximale Anzahl erricht ist, abbrechen!
			if (in_array($sentKey, $arrProcessedKeys) || count($arrProcessedKeys) >= $fieldConfig['maxitems']) {
				continue;
			}

			// Wenn kein Bild hochgeladen wurde und keines geloescht werden kann, abbrechen!
			if (!$_FILES[$this->prefixId]['name'][$this->contentId][$fieldName]['upload'][$sentKey] && !($savedKey !== false && $arrFieldVars['delete'][$sentKey])) {
				continue;
			}

			// Fehlermeldung vorbereiten.
			$error = self::validationerrorKeyUpload;
			$arrProcessedKeys[] = $sentKey;

			// Falls das Bild durch den User geloescht wird, soll bei einem Fehler auch eine entsprechende Fehlermeldung angezeigt werden!
			if ($arrFieldVars['delete'][$sentKey]) {
				$error = self::validationerrorKeyDelete;
			}

			$newFilename = '';

			if ($_FILES[$this->prefixId]['name'][$this->contentId][$fieldName]['upload'][$sentKey]) {
				// Die konfigurierte maximale Dateigroesse wirde ueberschritten.
				if ($maxSize && $_FILES[$this->prefixId]['size'][$this->contentId][$fieldName]['upload'][$sentKey] > $maxSize) {
					$error = self::validationerrorKeySize;

					break;
				}

				// Der Upload war nicht vollstaendig (Datei zu gross => Zeitueberschreitung).
				if ($_FILES[$this->prefixId]['error'][$this->contentId][$fieldName]['upload'][$sentKey] == '2') {
					$error = self::validationerrorKeySize;

					break;
				}

				$newFiletype = pathinfo(strtolower($_FILES[$this->prefixId]['name'][$this->contentId][$fieldName]['upload'][$sentKey]),  PATHINFO_EXTENSION);

				// Wenn nur bestimmte Datei-Typen erlaubt sind, und der aktuelle Typ nicht in den Erlaubten enthalten ist!
				if ($allowedTypes && !in_array($newFiletype, $allowedTypes)) {
					$error = self::validationerrorKeyType;

					break;
				}

				// Wenn bestimmte Datei-Typen nicht erlaubt sind, und der aktuelle Typ in den Unerlaubten enthalten ist!
				if ($disallowedTypes && in_array($newFiletype, $disallowedTypes)) {
					$error = self::validationerrorKeyType;

					break;
				}

				$newFilename = basename(strtolower($_FILES[$this->prefixId]['name'][$this->contentId][$fieldName]['upload'][$sentKey]), '.' . $newFiletype);
				$newFilename = preg_replace('/[^a-z0-9]/', '', $newFilename) . '_' . sprintf('%02d', $sentKey + 1) . '_' . time() . '.' . $newFiletype;

				$filePath = t3lib_div::getFileAbsFileName($uploadFolder . $newFilename);

				// Bild verschieben, und anschliessend den neuen Bildnamen in die Datenbank schreiben.
				if (move_uploaded_file($_FILES[$this->prefixId]['tmp_name'][$this->contentId][$fieldName]['upload'][$sentKey], $filePath)) {
					chmod($filePath, 0644);

					$arrFilenames[] = $newFilename;
					$arrFieldVars['delete'][$sentKey] = true;

					// Wenn Das Bild erfolgreich hochgeladen wurde, Fehlermeldung zuruecksetzten.
					$error = '';
				}
			}

			if ($savedKey !== false && $arrFieldVars['delete'][$sentKey]) {
				$filePath = t3lib_div::getFileAbsFileName($uploadFolder . $arrFilenames[$savedKey]);

				if (file_exists($filePath) && unlink($filePath)) {
					unset($arrFilenames[$savedKey]);

					// Wenn Das Bild erfolgreich geloescht wurde, Fehlermeldung zuruecksetzten.
					$error = '';
				}
			}

			if ($error) {
				break;
			}
		}

		$arrUpdate[$fieldName] = implode(',', $arrFilenames);

		return $arrUpdate;
	}

	/**
	 * Kopiert anhand der angegebenen Konfigurationen Inhalte in dem uebergebenen Array an eine neue oder andere Stelle.
	 * Dabei wird auf jeden kopierten Inhalt die stdWrap Funktionen angewendet.
	 *
	 * @param	array		$arrUpdate
	 * @return	array		$arrUpdate
	 */
	function copyFields($arrUpdate) {
		if (!is_array($this->conf['copyfields.'])) {
			return $arrUpdate;
		}

		// Kopiert den Inhalt eines Feldes in ein anderes Feld.
		$arrCopiedFields = array();

		foreach ($this->conf['copyfields.'] as $fieldToCopy => $arrCopyToFields) {
			$fieldToCopy = rtrim($fieldToCopy, '.');

			// Wenn das Feld nich existiert, ueberspringen.
			if (!array_key_exists($fieldToCopy, $this->feUsersTca['columns'])) {
				continue;
			}

			foreach (array_keys($arrCopyToFields) as $copyToField) {
				$copyToField = rtrim($copyToField, '.');

				// Wenn das Feld nich existiert, ueberspringen.
				if (!array_key_exists($copyToField, $this->feUsersTca['columns'])) {
					continue;
				}

				// Wenn in das Feld bereits kopiert wurde, ueberspringen.
				if (in_array($copyToField, $arrCopiedFields)) {
					continue;
				}

				// Wenn das Feld den Modus "onlyused" hat und nicht im Formular angezeigt wurde, ueberspringen.
				if ($arrCopyToFields[$copyToField] == 'onlyused' && !array_key_exists($fieldToCopy, $this->arrUsedFields)) {
					continue;
				}

				// Wenn aktiviert, stdWrap anwenden.
				if ($arrCopyToFields[$copyToField]) {
					$arrCopiedFields[] = $copyToField;

					// Datenbank Feldinhalt for dem Update des Users dem stdWrap zur Verfuegung stellen.
					$cObj = t3lib_div::makeInstance('tslib_cObj');
					$cObj->data = $GLOBALS['TSFE']->fe_user->user;

					$arrUpdate[$copyToField] = $cObj->stdWrap($arrUpdate[$fieldToCopy], $arrCopyToFields[$copyToField . '.']);
				}
			}
		}

		return $arrUpdate;
	}

	/**
	 * Editiert einen vorhandenen User, anhand des uebergebenen Arrays.
	 *
	 * @param	array		$arrUpdate
	 * @return	array		$arrMode
	 */
	function doUserEdit($arrUpdate) {
		$arrMode = array();

		// Der User hat seine Daten editiert.
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $this->userId , $arrUpdate);

		// User und Admin Benachrichtigung schicken, aber nur wenn etwas geaendert wurde.
		if ($this->getConfigurationByShowtype('sendusermail') || $this->getConfigurationByShowtype('sendadminmail')) {
			$extraMarkers = $this->getChangedForMail($arrUpdate, $this->getConfigurationByShowtype());

			if ($this->getConfigurationByShowtype('sendadminmail') && !isset($extraMarkers['nothing_changed'])) {
				$this->sendMail($this->userId, self::showtypeKeyEdit, true, $this->getConfigurationByShowtype(), $extraMarkers);
			}

			if ($this->getConfigurationByShowtype('sendusermail') && !isset($extraMarkers['nothing_changed'])) {
				// ToDo: Hier vielleicht noch mit Passwort-Generierung?
				$this->sendMail($this->userId, self::showtypeKeyEdit, false, $this->getConfigurationByShowtype(), $extraMarkers);
			}
		}

		// Ausgabe vorbereiten.
		$arrMode['mode'] = $this->conf['showtype'];
		$arrMode['submode'] = self::submodeKeySuccess;

		// Wenn der User geloescht wurde, weiterleiten.
		if ($arrUpdate['deleted']) {
			$arrMode['mode'] = 'userdelete';
		}

		return $arrMode;
	}

	/**
	 * Erstellt einen User, anhand des uebergebenen Arrays.
	 *
	 * @param	array		$arrUpdate
	 * @return	array		$arrMode
	 */
	function doUserRegister($arrUpdate) {
		$arrMode = array();

		// Standartkonfigurationen anwenden.
		$arrUpdate['pid'] = $this->storagePid;
		$arrUpdate['crdate'] = $arrUpdate['tstamp'];
		$arrUpdate['usergroup'] = ($arrUpdate['usergroup']) ? $arrUpdate['usergroup'] : $this->getConfigurationByShowtype('usergroup');

		// Genehmigungstypen aufsteigend sortiert ermitteln. Das ist noetig um das Level dem richtigen Typ zuordnen zu koennen.
		// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig laesst.
		$arrApprovalTypes = $this->getApprovalTypes();

		// Maximales Genehmigungslevel ermitteln (Double Opt In / Admin Approval).
		$arrUpdate['tx_datamintsfeuser_approval_level'] = count($arrApprovalTypes);

		// Wenn ein Genehmigungstyp aktiviert ist, dann den User deaktivieren.
		if ($arrUpdate['tx_datamintsfeuser_approval_level'] > 0) {
			$arrUpdate['disable'] = '1';
		}

		// User erstellen.
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users', $arrUpdate);

		// Userid ermittln un Global definieren!
		$this->userId = $GLOBALS['TYPO3_DB']->sql_insert_id();

		// Wenn nach der Registrierung weitergeleitet werden soll.
		if ($arrUpdate['tx_datamintsfeuser_approval_level'] > 0) {
			// Aktivierungsmail senden.
			$this->sendActivationMail();

			// Ausgabe fuer gemischte Genehmigungstypen erstellen (z.B. erst adminapproval und dann doubleoptin).
			$arrMode['mode'] = array_shift($arrApprovalTypes);
			$arrMode['submode'] = ((count($arrApprovalTypes) > 0) ? implode('_', $arrApprovalTypes) . '_' : '') . self::submodeKeySent;
			$arrMode['params'] = array('mode' => $this->conf['showtype']);
		} else {
			// Registrierungs E-Mail schicken.
			if ($this->getConfigurationByShowtype('sendadminmail')) {
				$this->sendMail($this->userId, 'registration', true, $this->getConfigurationByShowtype());
			}

			if ($this->getConfigurationByShowtype('sendusermail')) {
				// Erstellt ein neues Passwort, falls Passwort generieren eingestellt ist. Das Passwort kannn dann ueber den Marker "###PASSWORD###" mit der Registrierungsmail gesendet werden.
				$extraMarkers = $this->getPasswordForMail();

				$this->sendMail($this->userId, 'registration', false, $this->getConfigurationByShowtype(), $extraMarkers);
			}

			$arrMode['mode'] = $this->conf['showtype'];
			$arrMode['submode'] = self::submodeKeySuccess;
			$arrMode['params'] = array('autologin' => $this->getConfigurationByShowtype('autologin'));
		}

		return $arrMode;
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
	function showOutputRedirect($mode, $submode = '', $params = array()) {
		$redirect = true;
		$autologin = false;

		$labelKey = $mode;
		$redirectKey = $mode;

		if ($submode) {
			$labelKey .= '_' . $submode;

			// Wenn für den Submode eine eigene Weiterleitungsseite definiert ist, diese benutzen!
			if ($this->conf['redirect.'][$redirectKey . '_' . $submode]) {
				$redirectKey .= '_' . $submode;
			}
		}

		// Label ermitteln
		$label = $this->getLabel($labelKey, false);

		// Zusaetzliche Konfigurationen die gesetzt werden, bevor die Ausgabe oder der Redirect ausgefuehrt werden.
		switch ($mode) {

			case 'register':
			case 'doubleoptin':
				// Login vormerken.
				if ($params['autologin']) {
					$autologin = true;
				}

				// WICHTIG: Hier KEIN break, da der nächste Teil von adminapproval auch fuer register und doubleoptin gilt.

			case 'adminapproval':
				// Dieser Modus wird uebergeben, wenn die Registrierung abgeschlossen ist, aber noch Approval Mails versendet werden.
				// Wenn hier dann fuer den uebergebenen Modus ein Weiterleitungsziel angegeben ist, wird dieses verwendet!
				// Dies ist noetig, da man ja nicht die "Registrierung abgeschlossen!" Meldung anzeigen will, sondern "Approval versendet!".
				// Das Weiterleitungsziel soll aber immer noch das Gleich wie das ohne Approval Mail sein!
				if ($params['mode']) {
					if ($this->conf['redirect.'][$params['mode']]) {
						$redirectKey = $params['mode'];
					} else {
						$redirect = false;
					}
				}

				break;

			case 'edit_error':
				$label = '<div class="' . $mode . ' ' . $submode . '">' . $label . '</div>';

				break;

			case 'edit':
				// Einen Refresh der aktuellen Seite am Client ausfuehren, damit nach dem Editieren wieder das Formular angezeigt wird.
				$GLOBALS['TSFE']->additionalHeaderData['refresh'] = '<meta http-equiv="refresh" content="2; url=' . t3lib_div::locationHeaderUrl(tx_datamintsfeuser_utils::getTypoLinkUrl($GLOBALS['TSFE']->id)) . '" />';

				break;

			case 'userdelete':
				// Einen Refresh auf der aktuellen Seite am Client ausfuehren, damit nach dem Loeschen des Users die Startseite angezeigt wird.
				$GLOBALS['TSFE']->additionalHeaderData['refresh'] = '<meta http-equiv="refresh" content="2; url=' . t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '" />';

				break;

		}

		// Hook bevor irgendeine Ausgabe oder eine Weiterleitung stattfindet.
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['showOutputRedirect'])) {
			$_params = array(
					'variables' => array(
							'mode' => $mode,
							'submode' => $submode,
							'params' => $params
						),
					'parameters' => array(
							'label' => &$label,
							'redirect' => &$redirect,
							'autologin' => &$autologin,
							'redirectKey' => &$redirectKey
						)
				);

			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['showOutputRedirect'] as $_funcRef) {
				t3lib_div::callUserFunction($_funcRef, $_params, $this);
			}
		}

		// Login vollziehen, falls eine Redirectseite angegeben ist, wird dorthin automatisch umgeleitet.
		if ($autologin) {
			tx_datamintsfeuser_utils::userAutoLogin($this->userId, $this->conf['redirect.'][$redirectKey], $this->getHiddenParamsArray());
		}

		// Redirect vollziehen, falls angegeben!
		if ($redirect && $this->conf['redirect.'][$redirectKey]) {
			tx_datamintsfeuser_utils::userRedirect($this->conf['redirect.'][$redirectKey], $this->getHiddenParamsArray());
		}

		return $label;
	}

	/**
	 * Sendet die Aktivierungsmail an den uebergebenen User.
	 *
	 * @param	integer		$userId
	 * @return	void
	 */
	function sendActivationMail($userId = 0) {
		$userId = intval($userId);

		if (!$userId) {
			$userId = $this->userId;
		}

		// Neuen Timestamp setzten, damit jede Aktivierungsmail einen anderen Hash hat.
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $userId, array('tstamp' => time()));

		// Userdaten ermitteln.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tstamp, tx_datamintsfeuser_approval_level', 'fe_users', 'uid = ' . $userId, '', '', '1');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		// Genehmigungstypen aufsteigend sortiert ermitteln. Das ist noetig um das Level dem richtigen Typ zuordnen zu koennen.
		// Beispiel: approvalcheck = ,doubleoptin,adminapproval => beim exploden kommt dann ein leeres Arrayelement herraus, das nach dem entfernen einen leeren Platz uebrig laesst.
		$arrApprovalTypes = $this->getApprovalTypes();

		// Aktuellen Genehmigungstyp ermitteln.
		$approvalType = $arrApprovalTypes[count($arrApprovalTypes) - $row['tx_datamintsfeuser_approval_level']];

		// Mail vorbereiten.
		$urlParameters = array($this->prefixId => array($this->contentId => array(self::submitparameterKeyMode => self::modeKeyApprovalcheck, 'uid' => $userId)));
		$approvalParameters = array($this->prefixId => array($this->contentId => array(self::submitparameterKeyHash => md5('approval' . $userId . $row['tstamp'] . $this->extConf['encryptionKey']))));
		$disapprovalParameters = array($this->prefixId => array($this->contentId => array(self::submitparameterKeyHash => md5('disapproval' . $userId . $row['tstamp'] . $this->extConf['encryptionKey']))));

		// Fuegt die hidden Params mit den Approvalcheck Parametern zusammen.
		$approvalParameters = array_merge($this->getHiddenParamsArray(), t3lib_div::array_merge_recursive_overrule($urlParameters, $approvalParameters));
		$disapprovalParameters = array_merge($this->getHiddenParamsArray(), t3lib_div::array_merge_recursive_overrule($urlParameters, $disapprovalParameters));

		$extraMarkers = array(
			'approvallink' => t3lib_div::locationHeaderUrl(tx_datamintsfeuser_utils::escapeBrackets($this->pi_getPageLink($GLOBALS['TSFE']->id, '', $approvalParameters))),
			'disapprovallink' => t3lib_div::locationHeaderUrl(tx_datamintsfeuser_utils::escapeBrackets($this->pi_getPageLink($GLOBALS['TSFE']->id, '', $disapprovalParameters)))
		);

		// E-Mail senden.
		$this->sendMail($userId, $approvalType, $this->isAdminMail($approvalType), $this->getConfigurationByShowtype(), $extraMarkers);

		// Cookie fuer das erneute zusenden des Aktivierungslinks setzten.
		$this->setNotActivatedCookie($userId);
	}

	/**
	 * Ueberprueft ob die Linkbestaetigung gueltig ist und aktiviert gegebenenfalls den User.
	 *
	 * @return	string
	 */
	function doApprovalCheck() {
		// Userdaten ermitteln.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, tstamp, tx_datamintsfeuser_approval_level', 'fe_users', 'uid = ' . $this->userId . ' AND pid = ' . $this->storagePid, '', '', '1');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		// Genehmigungstyp ermitteln um die richtige E-Mail zu senden, bzw. die richtige Ausgabe zu ermitteln.
		$arrApprovalTypes = $this->getApprovalTypes();
		$approvalType = $arrApprovalTypes[count($arrApprovalTypes) - $row['tx_datamintsfeuser_approval_level']];

		// Wenn kein Genehmigungstyp ermittelt werden konnte.
		if (!$approvalType) {
			return $this->showOutputRedirect(self::modeKeyApprovalcheck, self::submodeKeyFailure);
		}

		// Ausgabe vorbereiten.
		$mode = $approvalType;
		$submode = self::submodeKeyFailure;
		$params = array();

		// Daten vorbereiten.
		$hashApproval = md5('approval' . $row['uid'] . $row['tstamp'] . $this->extConf['encryptionKey']);
		$hashDisapproval = md5('disapproval' . $row['uid'] . $row['tstamp'] . $this->extConf['encryptionKey']);

		// Wenn der Approval-Hash richtig ist, des letzte Genehmigungslevel aber noch nicht erreicht ist.
		if ($this->piVars[$this->contentId][self::submitparameterKeyHash] == $hashApproval && $row['tx_datamintsfeuser_approval_level'] > 1) {
			// Genehmigungslevel updaten.
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $this->userId , array('tstamp' => time(), 'tx_datamintsfeuser_approval_level' => $row['tx_datamintsfeuser_approval_level'] - 1));

			// Aktivierungsmail schicken.
			$this->sendActivationMail();

			// Ausgabe vorbereiten.
			$submode = self::submodeKeySuccess;
		}

		// Wenn der Approval-Hash richtig ist, und das letzte Genehmigungslevel erreicht ist.
		if ($this->piVars[$this->contentId][self::submitparameterKeyHash] == $hashApproval && $row['tx_datamintsfeuser_approval_level'] == 1) {
			// User aktivieren.
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $this->userId , array('tstamp' => time(), 'disable' => '0', 'tx_datamintsfeuser_approval_level' => '0'));

			// Registrierungs E-Mail schicken.
			if ($this->getConfigurationByShowtype('sendadminmail')) {
				$this->sendMail($this->userId, 'registration', true, $this->getConfigurationByShowtype());
			}

			if ($this->getConfigurationByShowtype('sendusermail')) {
				// Erstellt ein neues Passwort, falls Passwort generieren eingestellt ist. Das Passwort kannn dann ueber den Marker "###PASSWORD###" mit der Registrierungsmail gesendet werden.
				$extraMarkers = $this->getPasswordForMail();

				$this->sendMail($this->userId, 'registration', false, $this->getConfigurationByShowtype(), $extraMarkers);
			}

			// Ausgabe vorbereiten.
			$submode = self::submodeKeySuccess;
			$params = array('autologin' => $this->getConfigurationByShowtype('autologin'));
		}

		// Wenn der Disapproval-Hash richtig ist.
		if ($this->piVars[$this->contentId][self::submitparameterKeyHash] == $hashDisapproval) {
			// User loeschen.
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $this->userId ,array('tstamp' => time(), 'deleted' => '1'));

			// Eine Account-Abgelehnt Mail senden, wenn User ablehnt an den Administrator, oder andersrum.
			$this->sendMail($this->userId, 'disapproval', !$this->isAdminMail($approvalType), $this->getConfigurationByShowtype());

			// Ausgabe vorbereiten.
			$submode = 'deleted';
		}

		return $this->showOutputRedirect($mode, $submode, $params);
	}

	/**
	 * Ermittelt alle Genehmigungstypen.
	 * Wird benoetigt um das Level dem richtigen Typ zuordnen zu koennen.
	 *
	 * @return	array
	 */
	function getApprovalTypes() {
		// Beispiel: approvalcheck = ,doubleoptin,adminapproval => Beim Exploden kommt dann ein leeres Arrayelement heraus, das nach dem entfernen einen leeren Platz uebrig laesst.
		return array_values(t3lib_div::trimExplode(',', $this->getConfigurationByShowtype(self::modeKeyApprovalcheck), true));
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
			$arrNotActivated = array_map('intval', array_unique(t3lib_div::trimExplode(',', $_COOKIE[$this->prefixId]['not_activated'], true)));
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

		$markerArray = array_merge((array)$config, (array)$row, (array)$extraMarkers);

		foreach ($markerArray as $key => $val) {
			$markerArray['label_' . $key] = $this->getLabel($key, false);

//			if (!tx_datamintsfeuser_utils::checkUtf8($val)) {
//				$markerArray[$key] = utf8_encode($val);
//			}
		}

		// Absender vorbereiten.
		$fromName = $config['sendername'];
		$fromEmail = $config['sendermail'];

		// Wenn die Mail fuer den Admin bestimmt ist.
		if ($adminMail) {
			// Template laden.
			$content = $this->getTemplateSubpart($templatePart . '_admin', $markerArray, $config);

			$toName = $config['adminname'];
			$toEmail = $config['adminmail'];
		} else {
			// Template laden.
			$content = $this->getTemplateSubpart($templatePart, $markerArray, $config);

			$toName = $row['username'];
			$toEmail = $row['email'];
		}

		// Betreff ermitteln und aus dem E-Mail Content entfernen.
		$subject = trim($this->cObj->getSubpart($content, '###SUBJECT###'));
		$content = $this->cObj->substituteSubpart($content, '###SUBJECT###', '');

		// Body zusammensetzen.
		$body = $this->getTemplateSubpart('body', array_merge($markerArray, array('content' => $content)), $config);

		// Header ermitteln und Betreff ersetzten (Title-Tag).
		$header = $this->getTemplateSubpart('header', array_merge($markerArray, array('subject' => $subject)), $config);

		// Extra Subparts ersetzten.
		foreach ($extraSuparts as $key => $val) {
			$body = $this->cObj->substituteSubpart($body, '###' . strtoupper($key) . '###', $val);
		}

		// Hook um die E-Mail zu aendern.
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['sendMail'])) {
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
							'toName' => &$toName,
							'toEmail' => &$toEmail,
							'fromName' => &$fromName,
							'fromEmail' => &$fromEmail
						)
				);

			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['sendMail'] as $_funcRef) {
				t3lib_div::callUserFunction($_funcRef, $_params, $this);
			}
		}

		// Verschicke E-Mail.
		if ($toEmail && $subject && $body) {

			$bodyHtml = '<html>' . $header . $body . '</html>';
			$bodyPlain = trim(strip_tags($body));

			if ($config['mailtype'] == 'html') {
				$bodyPlain = tx_datamintsfeuser_utils::convertHtmlEmailToPlain($bodyHtml);
			}

			if (t3lib_div::compat_version('4.5')) {
				$mail = t3lib_div::makeInstance('t3lib_mail_Message');
				$mail->setSubject($subject);
				$mail->setFrom(array($fromEmail => $fromName));
				$mail->setTo(array($toEmail => $toName));
				$mail->setBody($bodyPlain);
				$mail->setCharset($GLOBALS['TSFE']->metaCharset);

				if ($config['mailtype'] == 'html') {
					$mail->addPart($bodyHtml, 'text/html', $GLOBALS['TSFE']->metaCharset);
				}

				$mail->send();
			} else {
				$mail = t3lib_div::makeInstance('t3lib_htmlmail');
				$mail->start();
				$mail->subject = $subject;
				$mail->from_email = $fromEmail;
				$mail->from_name = $fromName;
				$mail->addPlain($bodyPlain);
				$mail->charset = $GLOBALS['TSFE']->metaCharset;

				if ($config['mailtype'] == 'html') {
					$mail->setHTML($mail->encodeMsg($bodyHtml));
				}

				$mail->send($toEmail);
			}
		}
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
	 * Holt einen Subpart des Standardtemplates und ersetzt uebergeben Marker.
	 *
	 * @param	string		$templatePart
	 * @param	array		$markerArray
	 * @param	array		$config
	 * @return	string		$template
	 */
	function getTemplateSubpart($templatePart, $markerArray = array(), $config = array()) {
		// Template holen.
		$templateFile = $config['emailtemplate'];

		if (!$templateFile) {
			$templateFile = 'EXT:' . $this->extKey . '/res/datamints_feuser_mail.html';
		}

		// Template laden.
		$template = tx_datamintsfeuser_utils::getTemplateSubpart($templateFile, $templatePart, $markerArray);

		return $template;
	}

	/**
	 * Ermittlet alle geaenderten Daten und schreibt sie in ein Markerarray.
	 *
	 * @param	array		$arrNewData
	 * @param	array		$config
	 * @return	array		$extraMarkers
	 */
	function getChangedForMail($arrNewData, $config) {
		$count = 0;
		$template =  $this->getTemplateSubpart('changed_items', array(), $config);
		$extraMarkers = array();

		foreach ($this->arrUsedFields as $fieldName) {
			if ($arrNewData[$fieldName] != $GLOBALS['TSFE']->fe_user->user[$fieldName]) {
				$markerArray = array();
				$markerArray['label'] = $this->getLabel($fieldName, false);
				$markerArray['value_old'] = $GLOBALS['TSFE']->fe_user->user[$fieldName];
				$markerArray['value_new'] = $arrNewData[$fieldName];

				$subpart = $this->cObj->getSubpart($template, '###' . strtoupper($fieldName) . '###');

				if ($subpart) {
					$count++;
					$extraMarkers['changed_item_' . $fieldName] = $this->cObj->substituteMarkerArray($subpart, $markerArray, '###|###', 1);
				} else {
					$extraMarkers['changed_item_' . $fieldName] = '';
				}
			} else {
				$extraMarkers['changed_item_' . $fieldName] = '';
			}
		}

		if (!$count) {
			$extraMarkers['nothing_changed'] = 'nothing_changed';
		}

		return $extraMarkers;
	}

	/**
	 * Erstellt ein neues Passwort, falls Passwort generieren eingestellt ist. Das Passwort kannn dann ueber den Marker "###PASSWORD###" mit der Registrierungsmail gesendet werden.
	 *
	 * @return	array		$extraMarkers
	 */
	function getPasswordForMail() {
		$extraMarkers = array();
		$generatePassword = $this->getConfigurationByShowtype('generatepassword.');

		if ($generatePassword['mode'] && $this->userId) {
			$password = tx_datamintsfeuser_utils::generatePassword($this->piVars[$this->contentId]['password'], $generatePassword);

			$extraMarkers['password'] = $password['normal'];

			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . $this->userId, array('password' => $password['encrypted']));
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
		if ($this->conf['showtype'] == self::showtypeKeyEdit) {
			$arrCurrentData = (array)$GLOBALS['TSFE']->fe_user->user;
		}

		// Wenn das Formular schon einmal abgesendet wurde aber ein Fehler auftrat, dann die bereits vom User uebertragenen Userdaten vorausfuellen.
		if ($this->piVars[$this->contentId]) {
			$arrCurrentData = array_merge($arrCurrentData, (array)$this->piVars[$this->contentId]);
		}

		// Alle moeglichen Zeichen der Ausgabe, die stoeren koennten (XSS) konvertieren / entfernen.
		$arrCurrentData = tx_datamintsfeuser_utils::htmlspecialcharsPostArray($arrCurrentData, false);

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
		$content = '<form name="' . $this->prefixId . '[' . $this->contentId . ']" action="' . $requestLink . '" method="post" enctype="multipart/form-data" id="' . $this->getFieldId('form') . '">';
		$content .= '<fieldset class="group-' . $iFieldset . '">';

		// Wenn eine Lgende fuer das erste Fieldset definiert wurde, diese ausgeben.
		if ($this->conf['legends.'][$iFieldset]) {
			$content .= '<legend>' . $this->conf['legends.'][$iFieldset] . '</legend>';
		}

		// Alle ausgewaehlten Felder durchgehen.
		foreach ($this->arrUsedFields as $fieldName) {
			$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];
			$disabledField = ($fieldConfig['readOnly']) ? ' disabled="disabled"' : '';

			// Standardkonfigurationen laden.
			if (!$arrCurrentData[$fieldName] && $fieldConfig['default']) {
				$arrCurrentData[$fieldName] = $fieldConfig['default'];
			}

			// Wenn das im Flexform ausgewaehlte Feld existiert, dann dieses Feld ausgeben, alle anderen Felder werden ignoriert.
			if ($this->feUsersTca['columns'][$fieldName]) {
				// Form Item Anfang.
				$content .= '<div id="' . $this->getFieldId($fieldName, 'wrapper') . '" class="item item-' . $iItem . ' type-' . $fieldConfig['type'] . (($this->isRequiredField($fieldName)) ? ' required' : '') . $this->getErrorClass($fieldName, $valueCheck) . ' clearfix">';

				// Label schreiben.
				$content .= '<label for="' . $this->getFieldId($fieldName) . '">' . $this->getLabel($fieldName) . '</label>';

				switch ($fieldConfig['type']) {

					case 'input':
						$content .= $this->showInput($fieldName, $fieldConfig, $arrCurrentData, $valueCheck, $disabledField);

						break;

					case 'text':
						$content .= $this->showText($fieldName, $fieldConfig, $arrCurrentData, $disabledField);

						break;

					case 'check':
						$content .= $this->showCheck($fieldName, $fieldConfig, $arrCurrentData, $disabledField);

						break;

					case 'radio':
						$content .= $this->showRadio($fieldName, $fieldConfig, $arrCurrentData, $disabledField);

						break;

					case 'select':
						$content .= $this->showSelect($fieldName, $fieldConfig, $arrCurrentData, $disabledField);

						break;

					case 'group':
						if ($fieldConfig['internal_type'] == 'file') {
							$arrCurrentData[$fieldName] = $GLOBALS['TSFE']->fe_user->user[$fieldName];
						}

						$content .= $this->showGroup($fieldName, $fieldConfig, $arrCurrentData, $disabledField);

						break;

				}

				// Extra Error Label ermitteln.
				$content .= $this->getErrorLabel($fieldName, $valueCheck);

				// Form Item Ende.
				$content .= '</div>';

				$iItem++;
			}

			// Den Feldnamen saeubern und die Spezialfelder anzeigen.
			$fieldName = tx_datamintsfeuser_utils::getSpecialFieldName($fieldName);

			// Submit Button anzeigen.
			if ($fieldName == self::specialfieldKeySubmit) {
				$content .= '<div id="' . $this->getFieldId($fieldName, 'wrapper') . '" class="item item-' . $iItem . ' type-' . $fieldName . '">';
				$content .= '<input type="submit" value="' . $this->getLabel($fieldName . '_' . $this->conf['showtype'], false) . '" id="' . $this->getFieldId($fieldName) . '" />';
				$content .= '</div>';

				$iItem++;
			}

			// Captcha anzeigen.
			if ($fieldName == self::specialfieldKeyCaptcha) {
				$content .= $this->showCaptcha($fieldName, $valueCheck);

				$iItem++;
			}

			// Separator anzeigen.
			if ($fieldName == self::specialfieldKeySeparator) {
				$iFieldset++;

				$content .= '</fieldset><fieldset class="group-' . $iFieldset . '">';

				// Wenn eine Lgende fuer das Fieldset definiert wurde, diese ausgeben.
				if ($this->conf['legends.'][$iFieldset]) {
					$content .= '<legend>' . $this->conf['legends.'][$iFieldset] . '</legend>';
				}
			}

			// Infoitem anzeigen.
			if ($fieldName == self::specialfieldKeyInfoitem) {
				if ($this->conf['infoitems.'][$iInfoItem]) {
					$content .= '<div class="item item-' . $iInfoItem . ' type-info">' . $this->conf['infoitems.'][$iInfoItem] . '</div>';
				}

				$iInfoItem++;
			}

			// Profil loeschen Link anzeigen.
			if ($fieldName == self::specialfieldKeyUserdelete && $this->conf['showtype'] == self::showtypeKeyEdit) {
				$content .= '<div id="' . $this->getFieldId($fieldName, 'wrapper') . '" class="item item-' . $iItem . ' type-' . $fieldName . $this->getErrorClass($fieldName, $valueCheck) . ' clearfix">';
				$content .= '<label for="' . $this->getFieldId($fieldName) . '">' . $this->getLabel($fieldName) . '</label>';
				$content .= '<input type="checkbox" name="' . $this->getFieldName($fieldName) . '" value="1" id="' . $this->getFieldId($fieldName) . '" />';
				$content .= $this->getErrorLabel($fieldName, $valueCheck);
				$content .= '</div>';

				$iItem++;
			}

			// Aktivierung erneut senden anzeigen.
			if ($fieldName == self::specialfieldKeyResendactivation) {
				// Noch nicht fertig gestellte Listenansicht der nicht aktivierten User.
//				if ($this->conf['shownotactivated'] == 'list') {
//					$arrNotActivated = $this->getNotActivatedUserArray();
//					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, username', 'fe_users', 'pid = ' . $this->storagePid . ' AND uid IN(' . implode(',', $arrNotActivated) . ') AND disable = 1 AND deleted = 0');
//
//					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
//						$content .= '<div id="' . $this->getFieldId($fieldName, 'wrapper') . '" class="item item-' . $iItem . ' type-' . $fieldName . ' ' . $this->conf['shownotactivated'] . '">';
//						$content .= '<label for="' . $this->getFieldId($fieldName) . '">' . $this->getLabel($fieldName) . ' ' . $row['username'] . '</label>';
//						$content .= '<input type="checkbox" name="' . $this->getFieldName($fieldName, $row['uid']) . '" value="1" id="' . $this->getFieldId($fieldName) . '" />';
//						$content .= '</div>';
//
//						$iItem++;
//					}
//				} else {
					$content .= '<div id="' . $this->getFieldId($fieldName, 'wrapper') . '" class="item item-' . $iItem . ' type-' . $fieldName . $this->getErrorClass($fieldName, $valueCheck) . ' clearfix">';
					$content .= '<label for="' . $this->getFieldId($fieldName) . '">' . $this->getLabel($fieldName) . '</label>';
					$content .= '<input type="text" name="' . $this->getFieldName($fieldName) . '" value="" id="' . $this->getFieldId($fieldName) . '" />';
					$content .= $this->getErrorLabel($fieldName, $valueCheck);
					$content .= '</div>';

					$iItem++;
//				}
			}

			// Passwortbestaetigung anzeigen.
			if ($fieldName == self::specialfieldKeyPasswordconfirmation && $this->conf['showtype'] == self::showtypeKeyEdit) {
				$content .= '<div id="' . $this->getFieldId($fieldName, 'wrapper') . '" class="item item-' . $iItem . ' type-' . $fieldName . $this->getErrorClass($fieldName, $valueCheck) . ' clearfix">';
				$content .= '<label for="' . $this->getFieldId($fieldName) . '">' . $this->getLabel($fieldName) . '</label>';
				$content .= '<input type="password" name="' . $this->getFieldName($fieldName) . '" value="" id="' . $this->getFieldId($fieldName) . '" />';
				$content .= $this->getErrorLabel($fieldName, $valueCheck);
				$content .= '</div>';

				$iItem++;
			}
		}

		// UserId, PageId und Modus anhaengen.
		$content .= '<input type="hidden" name="' . $this->getFieldName(self::submitparameterKeyMode) . '" value="send" />';
		$content .= '<input type="hidden" name="' . $this->getFieldName(self::submitparameterKeyUser) . '" value="' . $this->userId . '" />';
		$content .= '<input type="hidden" name="' . $this->getFieldName(self::submitparameterKeyPage) . '" value="' . $GLOBALS['TSFE']->id . '" />';
		$content .= '<input type="hidden" name="' . $this->getFieldName(self::submitparameterKeySubmode) . '" value="' . $this->conf['showtype'] . '" />';
		$content .= $this->getHiddenParamsHiddenFields();

		$content .= '</fieldset>';
		$content .= '</form>';

		return $content;
	}

	/**
	 * Rendert Inputfelder.
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrCurrentData
	 * @param	string		$disabledField
	 * @return	string		$content
	 */
	function showInput($fieldName, $fieldConfig, $arrCurrentData, $valueCheck, $disabledField = '') {
		$content = '';

		$arrFieldConfigEval = t3lib_div::trimExplode(',', $fieldConfig['eval'], true);

		// Datumsfeld und Datumzeitfeld.
		if (in_array('date', $arrFieldConfigEval) || in_array('datetime', $arrFieldConfigEval)) {
			$datum = '';

			if ($arrCurrentData[$fieldName] != 0) {
				// Timestamp zu "tt.mm.jjjj" machen.
				if (in_array('date', $arrFieldConfigEval)) {
					$datum = strftime($this->conf['format.']['date'], $arrCurrentData[$fieldName]);
				}

				// Timestamp zu "hh:mm tt.mm.jjjj" machen.
				if (in_array('datetime', $arrFieldConfigEval)) {
					$datum = strftime($this->conf['format.']['datetime'], $arrCurrentData[$fieldName]);
				}
			}

			$content .= '<input type="text" name="' . $this->getFieldName($fieldName) . '" value="' . $datum . '"' . $disabledField . ' id="' . $this->getFieldId($fieldName) . '" />';

			return $content;
		}

		// Passwordfelder.
		if (in_array('password', $arrFieldConfigEval)) {
			$content .= '<input type="password" name="' . $this->getFieldName($fieldName) . '" value=""' . $disabledField . ' id="' . $this->getFieldId($fieldName) . '" />';
			$content .= '</div><div id="' . $this->getFieldId($fieldName, 'rep', 'wrapper') . '" class="item type-' . $fieldConfig['type'] . $this->getErrorClass($fieldName, $valueCheck) . ' clearfix">';
			$content .= '<label for="' . $this->getFieldId($fieldName, 'rep') . '">' . $this->getLabel($fieldName . '_rep', false) . $this->isRequiredField($fieldName) . '</label>';
			$content .= '<input type="password" name="' . $this->prefixId . '[' . $this->contentId . '][' . $fieldName . '_rep]" value=""' . $disabledField . ' id="' . $this->getFieldId($fieldName, 'rep') . '" />';

			return $content;
		}

		// Normales Inputfeld.
		$content .= '<input type="text" name="' . $this->getFieldName($fieldName) . '" value="' . $arrCurrentData[$fieldName] . '"' . $disabledField . ' id="' . $this->getFieldId($fieldName) . '" />';

		return $content;
	}

	/**
	 * Rendert Textareas.
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrCurrentData
	 * @param	string		$disabledField
	 * @return	string		$content
	 */
	function showText($fieldName, $fieldConfig, $arrCurrentData, $disabledField = '') {
		$content = '';

		$content .= '<textarea name="' . $this->getFieldName($fieldName) . '" rows="2" cols="42"' . $disabledField . ' id="' . $this->getFieldId($fieldName) . '">' . $arrCurrentData[$fieldName] . '</textarea>';

		return $content;
	}

	/**
	 * Rendert Checkboxen.
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrCurrentData
	 * @param	string		$disabledField
	 * @return	string		$content
	 */
	function showCheck($fieldName, $fieldConfig, $arrCurrentData, $disabledField = '') {
		$content = '';
		$checkItemsCount = count($fieldConfig['items']);

		if ($checkItemsCount > 1) {
			$checkItems = array_values($fieldConfig['items']);

			// Moeglichkeit das der gespeicherte Wert eine Bitmap ist, daher aufsplitten in ein Array, wie es auch von einem abgesendeten Formular kommen wuerde.
			if (!is_array($arrCurrentData[$fieldName])) {
				$arrCurrentData[$fieldName] = str_split(strrev(decbin($arrCurrentData[$fieldName])));
			}

			$content .= '<input type="hidden" name="' . $this->getFieldName($fieldName, '') . '" value="" />';

			$content .= '<div class="list clearfix">';

			$i = 1;

			// Items, die in der TCA-Konfiguration festgelegt wurden.
			for ($key = 0; $key < $checkItemsCount; $key++) {
				if ($key > 0 && ($key % $fieldConfig['cols']) == 0) {
					$content .= '</div><div class="list clearfix">';
				}

				$checked = ($arrCurrentData[$fieldName][$key]) ? ' checked="checked"' : '';

				$content .= '<div id="' . $this->getFieldId($fieldName, 'item', $i, 'wrapper') . '" class="item item-' . $i . '">';
				$content .= '<input type="checkbox" name="' . $this->getFieldName($fieldName, $key) . '" value="1"' . $checked . $disabledField . ' id="' . $this->getFieldId($fieldName, 'item', $i) . '" />';
				$content .= '<label for="' . $this->getFieldId($fieldName, 'item', $i) . '">' . $this->getLabel($checkItems[$key][0], false) . '</label>';
				$content .= '</div>';

				$i++;
			}

			$content .= '</div>';
		} else {
			$checked = ($arrCurrentData[$fieldName]) ? ' checked="checked"' : '';

			$content .= '<input type="hidden" name="' . $this->getFieldName($fieldName) . '" value="0" />';
			$content .= '<input type="checkbox" name="' . $this->getFieldName($fieldName) . '" value="1"' . $checked . $disabledField . ' id="' . $this->getFieldId($fieldName) . '" />';
		}

		return $content;
	}

	/**
	 * Rendert Radiobuttons.
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrCurrentData
	 * @param	string		$disabledField
	 * @return	string		$content
	 */
	function showRadio($fieldName, $fieldConfig, $arrCurrentData, $disabledField = '') {
		$content = '';
		$radioItems = array_values($fieldConfig['items']);

		$content .= '<div class="list">';

		$i = 1;

		for ($key = 0; $key < count($fieldConfig['items']); $key++) {
			$value = $radioItems[$key][1];
			$checked = ($arrCurrentData[$fieldName] == $value) ? ' checked="checked"' : '';

			$content .= '<div id="' . $this->getFieldId($fieldName, 'item', $i, 'wrapper') . '" class="item item-' . $i . '">';
			$content .= '<input type="radio" name="' . $this->getFieldName($fieldName) . '" value="' . $value . '"' . $checked . $disabledField . ' id="' . $this->getFieldId($fieldName, 'item', $i) . '" />';
			$content .= '<label for="' . $this->getFieldId($fieldName, 'item', $i) . '">';
			$content .= $this->getLabel($radioItems[$key][0], false);
			$content .= '</label>';
			$content .= '</div>';

			$i++;
		}

		$content .= '</div>';

		return $content;
	}

	/**
	 * Rendert Selectfelder.
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrCurrentData
	 * @param	string		$disabledField
	 * @return	string		$content
	 */
	function showSelect($fieldName, $fieldConfig, $arrCurrentData, $disabledField = '') {
		$content = '';
		$optionlist = '';

		// Moeglichkeit das der gespeicherte Wert eine kommseparierte Liste ist, daher aufsplitten in ein Array, wie es auch von einem abgesendeten Formular kommen wuerde.
		if (!is_array($arrCurrentData[$fieldName])) {
			$arrCurrentData[$fieldName] = t3lib_div::trimExplode(',', $arrCurrentData[$fieldName], true);
		}

		// Beim Typ Select gibt es zwei verschidene Rendermodi. Dieser kann "singlebox" (dann ist es eine Selectbox) oder "checkbox" (dann ist es eine Checkboxliste) sein.
		$i = 1;

		// Items, die in der TCA-Konfiguration festgelegt wurden.
		for ($key = 0; $key < count($fieldConfig['items']); $key++) {
			$label = $fieldConfig['items'][$key][0];
			$value = $fieldConfig['items'][$key][1];

			if ($fieldConfig['renderMode'] == 'checkbox') {
				$checked = (in_array($value, $arrCurrentData[$fieldName])) ? ' checked="checked"' : '';

				$optionlist .= '<div id="' . $this->getFieldId($fieldName, 'item', $i, 'wrapper') . '" class="item item-' . $i . '">';
				$optionlist .= '<input type="checkbox"  name="' . $this->getFieldName($fieldName, '') . '" value="' . $value . '"' . $checked . $disabledField . ' id="' . $this->getFieldId($fieldName, 'item', $i) . '" />';
				$optionlist .= '<label for="' . $this->getFieldId($fieldName, 'item', $i) . '">' . $this->getLabel($label, false) . '</label>';
				$optionlist .= '</div>';
			} else {
				$selected = (in_array($value, $arrCurrentData[$fieldName])) ? ' selected="selected"' : '';

				$optionlist .= '<option value="' . $value . '"' . $selected . '>' . $this->getLabel($label, false) . '</option>';
			}

			$i++;
		}

		// Wenn Tabelle angegeben zusaetzlich Items aus Datenbank holen.
		if ($fieldConfig['foreign_table']) {
			$table = $fieldConfig['foreign_table'];

			$labelFieldName = $this->getTableLabelFieldName($table);

			// Select-Items aus DB holen.
			$select = 'uid, ' . $labelFieldName;

			// Wenn AND, OR, GROUP BY, ORDER BY oder LIMIT am Anfang des where steht, eine 1 voranstellen!
			$options = strtolower(substr(trim($fieldConfig['foreign_table_where']), 0, 3));
			$options = '1 ' . $this->cObj->enableFields($table) . ' ' . trim((!$options || $options == 'and' || $options == 'or ' || $options == 'gro' || $options == 'ord' || $options == 'lim') ? $fieldConfig['foreign_table_where'] : 'AND ' . $fieldConfig['foreign_table_where']);

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select , $table, $options);

			$i = 1;

			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				if ($fieldConfig['renderMode'] == 'checkbox') {
					$checked = (in_array($row['uid'], $arrCurrentData[$fieldName])) ? ' checked="checked"' : '';

					$optionlist .= '<div id="' . $this->getFieldId($fieldName, 'item', $i, 'wrapper') . '" class="item item-' . $i . '">';
					$optionlist .= '<input type="checkbox" name="' . $this->getFieldName($fieldName, '') . '" value="' . $row['uid'] . '"' . $checked . $disabledField . ' id="' . $this->getFieldId($fieldName, 'item', $i) . '" />';
					$optionlist .= '<label for="' . $this->getFieldId($fieldName, 'item', $i) . '">' . $row[$labelFieldName] . '</label>';
					$optionlist .= '</div>';
				} else {
					$selected = (in_array($row['uid'], $arrCurrentData[$fieldName])) ? ' selected="selected"' : '';

					$optionlist .= '<option value="' . $row['uid'] . '"' . $selected . '>' . $row[$labelFieldName] . '</option>';
				}

				$i++;
			}
		}

		// Mehrzeiliges oder Einzeiliges Select (Auswahlliste).
		$multiple = ($fieldConfig['size'] > 1) ? ' size="' . $fieldConfig['size'] . '" multiple="multiple"' : '';

		if ($multiple || $fieldConfig['renderMode'] == 'checkbox') {
			$content .= '<input type="hidden" name="' . $this->getFieldName($fieldName, '') . '" value="" />';
		}

		if ($fieldConfig['renderMode'] == 'checkbox') {
			$content .= '<div class="list">';
			$content .= $optionlist;
			$content .= '</div>';
		} else {
			$content .= '<select name="' . $this->getFieldName($fieldName) . '' . (($multiple) ? '[]' : '') . '"' . $multiple . $disabledField . ' id="' . $this->getFieldId($fieldName) . '">';
			$content .= $optionlist;
			$content .= '</select>';
		}

		return $content;
	}

	/**
	 * Rendert Groupfelder.
	 *
	 * @param	string		$fieldName
	 * @param	array		$fieldConfig
	 * @param	array		$arrCurrentData
	 * @param	string		$disabledField
	 * @return	string		$content
	 */
	function showGroup($fieldName, $fieldConfig, $arrCurrentData, $disabledField = '') {
		$content = '';

		// GROUP (z.B. Files oder externe Tabellen).
		// Wenn es sich um den "internal_type" FILE handelt && es ein Bild ist, dann ein Vorschaubild erstellen und ein File-Inputfeld anzeigen.
		if ($fieldConfig['internal_type'] == 'file') {
			// Verzeichniss ermitteln.
			$uploadFolder = tx_datamintsfeuser_utils::fixPath($fieldConfig['uploadfolder']);

			$arrCurrentFieldData = t3lib_div::trimExplode(',', $arrCurrentData[$fieldName], true);

			$content .= '<div class="list">';

			$i = 1;

			for ($key = 0; $key < $fieldConfig['size']; $key++) {
				$filename = $arrCurrentFieldData[$key];

				$content .= '<input type="hidden" name="' . $this->getFieldName($fieldName, 'files', $key) . '" value="' . $filename . '" />';
				$content .= '<div id="' . $this->getFieldId($fieldName, 'item', $i, 'wrapper') . '" class="item item-' . $i . ' clearfix">';

				// Bild anzeigen.
				if ($fieldConfig['show_thumbs'] && $filename) {
					$imgTSConfig = $this->conf['thumb.'];
					$imgTSConfig['file'] = $uploadFolder . $filename;
					$image = $this->cObj->IMAGE($imgTSConfig);

					if ($image) {
						$content .= '<div class="thumb">' . $image . '</div>';
					}
				}

				if ($fieldConfig['show_thumbs'] && !$filename) {
					$content .= '<div class="thumb none"></div>';
				}

				if (!$fieldConfig['show_thumbs'] && $filename) {
					$content .= '<div class="link"><a href="' . tx_datamintsfeuser_utils::getTypoLinkUrl($uploadFolder . $filename) . '" target="_blank" alt="' . $filename . '">' . $filename . '</a></div>';
				}

				// Upload-Feld anzeigen.
				$content .= '<div class="upload">';
				$content .= '<input type="file" name="' . $this->getFieldName($fieldName, 'upload', $key) . '"' . $disabledField . ' id="' . $this->getFieldId($fieldName, 'upload', $i) . '" />';
				$content .= '</div>';

				if ($filename) {
					$content .= '<div class="delete">';
					$content .= '<input type="checkbox" name="' . $this->getFieldName($fieldName, 'delete', $key) . '"' . $disabledField . ' id="' . $this->getFieldId($fieldName, 'delete', $i) . '" />';
					$content .= '<label for="' . $this->getFieldId($fieldName, 'delete', $i) . '">' . $this->getLabel($fieldName . '_delete', false) . '</label>';
					$content .= '</div>';
				}

				$content .= '</div>';

				$i++;
			}

			$content .= '</div>';
		}

		// Wenn es sich um den "internal_type" DB handelt.
		// Hier werden absichtlich nur die Erlaubten Tabellen benutzt, da es sonst unmengen an möglichen Optionen geben wuerde!
		if ($fieldConfig['internal_type'] == 'db') {
			$arrItems = array();
			$arrAllowed = t3lib_div::trimExplode(',', $fieldConfig['allowed'], true);

			foreach ($arrAllowed as $table) {
				if (!$GLOBALS['TCA'][$table]) {
					continue;
				}

				$labelFieldName = $this->getTableLabelFieldName($table);

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, ' . $labelFieldName , $table, '1 ' . $this->cObj->enableFields($table));

				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$arrItems[$table . '_' . $row['uid']] = $row[$labelFieldName];
				}
			}

			$content .= '<input type="hidden" name="' . $this->getFieldName($fieldName, '') . '" value="" />';

			$content .= '<div class="list">';

			$i = 1;

			foreach ($arrItems as $key => $label) {
				// Moeglichkeit das der gespeicherte Wert eine kommseparierte Liste ist, daher aufsplitten in ein Array, wie es auch von einem abgesendeten Formular kommen wuerde.
				if (!is_array($arrCurrentData[$fieldName])) {
					$arrCurrentData[$fieldName] = t3lib_div::trimExplode(',', $arrCurrentData[$fieldName], true);
				}

				$checked = (array_intersect(array($key, substr($key, strripos($key, '_') + 1)), $arrCurrentData[$fieldName])) ? ' checked="checked"' : '';

				$content .= '<div id="' . $this->getFieldId($fieldName, 'item', $i, 'wrapper') . '" class="item item-' . $i . '">';
				$content .= '<input type="checkbox" name="' . $this->getFieldName($fieldName, '') . '" value="' . $key . '"' . $checked . $disabledField . ' id="' . $this->getFieldId($fieldName, 'item', $i) . '" />';
				$content .= '<label for="' . $this->getFieldId($fieldName, 'item', $i) . '">'. $label . '</label>';
				$content .= '</div>';

				$i++;
			}

			$content .= '</div>';
		}

		return $content;
	}

	/**
	 * Rendert ein Captcha.
	 *
	 * @param	string		$fieldName
	 * @param	array		$valueCheck
	 * @return	string		$content
	 */
	function showCaptcha($fieldName, $valueCheck) {
		$content = '';
		$captcha = '';
//		$showInput = true;

		if (!t3lib_extMgm::isLoaded($this->conf['captcha.']['use'])) {
			return $content;
		}

		switch ($this->conf['captcha.']['use']) {

			case 'captcha':
				$captcha = '<img src="' . tx_datamintsfeuser_utils::getTypoLinkUrl(t3lib_extMgm::siteRelPath($this->conf['captcha.']['use']) . 'captcha/captcha.php') . '" alt="Captcha" />';

				break;

			case 'sr_freecap':
				require_once(t3lib_extMgm::extPath($this->conf['captcha.']['use']) . 'pi2/class.tx_srfreecap_pi2.php');

				$freecap = t3lib_div::makeInstance('tx_srfreecap_pi2');
				$arrFreecap = $freecap->makeCaptcha();

				$captcha = $arrFreecap['###SR_FREECAP_IMAGE###'];

				break;

//			case 'jm_recaptcha':
//				require_once(t3lib_extMgm::extPath($this->conf['captcha.']['use']) . 'class.tx_jmrecaptcha.php');
//
//				$recaptcha = t3lib_div::makeInstance('tx_jmrecaptcha');
//
//				$captcha = $recaptcha->getReCaptcha();
//
//				$showInput = false;
//
//				break;

			case 'wt_calculating_captcha':
				require_once(t3lib_extMgm::extPath($this->conf['captcha.']['use']) . 'class.tx_wtcalculatingcaptcha.php');

				$calculatingcaptcha = t3lib_div::makeInstance('tx_wtcalculatingcaptcha');

				$captcha = $calculatingcaptcha->generateCaptcha();

				break;

		}

		if (!$captcha) {
			return $content;
		}

		$content .= '<div id="' . $this->getFieldId($fieldName, 'wrapper') . '" class="item type-' . $fieldName . $this->getErrorClass($fieldName, $valueCheck) . ' clearfix">';
		$content .= '<label for="' . $this->getFieldId($fieldName) . '">' . $this->getLabel($fieldName) . '</label>';
		$content .= '<div class="captcha">' . $captcha . '</div>';
		$content .= '<input type="text" name="' . $this->getFieldName($fieldName) . '" value="" id="' . $this->getFieldId($fieldName) . '" />';
//		$content .= ($showInput) ? '<input type="text" name="' . $this->getFieldName($fieldName) . '" value="" id="' . $this->getFieldId($fieldName) . '" />' : '';
		$content .= $this->getErrorLabel($fieldName, $valueCheck);
		$content .= '</div>';

		return $content;
	}

	/**
	 * Ermittelt die ID fuer das uebergebene Feld.
	 *
	 * @param	string		...
	 * @return	string
	 */
	function getFieldId() {
		if (!func_num_args()) {
			return '';
		}

		$arrParts = array(
			$this->extKey,
			$this->contentId
		);

		// Darf nicht als Methoden-Parameter uebergeben werden, da das vor PHP 5.3 fuer diese Methode nicht unterstuetzt wurde!
		$arrFuncArgs = func_get_args();

		return implode('_', array_merge($arrParts, $arrFuncArgs));
	}

	/**
	 * Ermittelt den Namen fuer das uebergebene Feld.
	 *
	 * @param	string		...
	 * @return	string
	 */
	function getFieldName() {
		if (!func_num_args()) {
			return '';
		}

		$arrParts = array(
			$this->prefixId,
			$this->contentId
		);

		// Darf nicht als Methoden-Parameter uebergeben werden, da das vor PHP 5.3 fuer diese Methode nicht unterstuetzt wurde!
		$arrFuncArgs = func_get_args();

		return array_shift($arrParts) . '[' . implode('][', array_merge($arrParts, $arrFuncArgs)) . ']';
	}

	/**
	 * Ermittelt ein bestimmtes Label aufgrund des im TCA gespeicherten Languagestrings, des Datenbankfeldnamens oder gibt einfach den uebergeben Wert wieder aus, wenn nichts gefunden wurde.
	 *
	 * @param	string		$fieldName / $languageString
	 * @param	boolean		$checkRequired
	 * @return	string
	 */
	function getLabel($fieldName, $checkRequired = true) {
		if (strpos($fieldName, 'LLL:') === false) {
			// Label aus der Konfiguration holen basierend auf dem Datenbankfeldnamen.
			$label = $this->pi_getLL($fieldName);

			// Das Label zurueckliefern, falls vorhanden.
			if ($label) {
				return $label . (($checkRequired) ? $this->isRequiredField($fieldName) : '');
			}

			// LanguageString ermitteln.
			$languageString = $this->feUsersTca['columns'][$fieldName]['label'];
		} else {
			$languageString = $fieldName;
		}

		// Label aus der Konfiguration holen basierend auf dem languageKey.
		$label = $this->pi_getLL(str_replace('.', '-', array_pop(t3lib_div::trimExplode(':', $languageString, true))));

		// Das Label zurueckliefern, falls vorhanden.
		if ($label) {
			return $label . (($checkRequired) ? $this->isRequiredField($fieldName) : '');
		}

		// Das Label zurueckliefern.
		$label = $GLOBALS['TSFE']->sL($languageString);

		// Das Label zurueckliefern, falls vorhanden.
		if ($label) {
			return $label . (($checkRequired) ? $this->isRequiredField($fieldName) : '');
		}

		// Wenn gar nichts gefunden wurde den uebergebenen Wert wieder zurueckliefern.
		return $fieldName . (($checkRequired) ? $this->isRequiredField($fieldName) : '');
	}

	/**
	 * Ermittelt den Fehlertyp aus dem Feldnamen.
	 *
	 * @param	string		$fieldName
	 * @param	array		$valueCheck
	 * @return	string		$type
	 */
	function getErrorType($fieldName, $valueCheck) {
		$type = '';

		if (array_key_exists($fieldName, $valueCheck) && is_string($valueCheck[$fieldName])) {
			$type = $valueCheck[$fieldName];
		}

		return $type;
	}

	/**
	 * Ermittelt die Fehlerklasse aus dem Feldnamen.
	 *
	 * @param	string		$fieldName
	 * @param	array		$valueCheck
	 * @return	string		$class
	 */
	function getErrorClass($fieldName, $valueCheck) {
		$class = '';

		// Extra Error Label ermitteln.
		if (($errorType = $this->getErrorType($fieldName, $valueCheck))) {
			$class = ' error error-' . $errorType;
		}

		return $class;
	}

	/**
	 * Ermittelt das Fehlerlabel aus dem Feldnamen.
	 *
	 * @param	string		$fieldName
	 * @param	array		$valueCheck
	 * @return	string		$label
	 */
	function getErrorLabel($fieldName, $valueCheck) {
		$label = '';

		// Extra Error Label ermitteln.
		if (($errorType = $this->getErrorType($fieldName, $valueCheck))) {
			$label = '<div class="error-label error-' . $fieldName . '">' . $this->getLabel($fieldName . '_error_' . $errorType, false) . '</div>';
		}

		return $label;
	}

	/**
	 * Ueberprueft ob das uebergebene Feld benoetigt wird um erfolgreich zu speichern.
	 *
	 * @param	string		$fieldName
	 * @return	string
	 */
	function isRequiredField($fieldName) {
		if (array_intersect(array($fieldName, tx_datamintsfeuser_utils::getSpecialFieldKey($fieldName)), $this->arrRequiredFields)) {
			return '<span class="star">*</span>';
		} else {
			return '';
		}
	}

	/**
	 * Ueberprüft ob es für die uebergebene Tabelle eine andere Labelkonfiguration gibt.
	 * Dieses LabelField wird dann benutzt, um für Listen Elmente das richtige Label zu holen.
	 *
	 * @param	string		$table
	 * @return	string		$labelFieldName
	 */
	function getTableLabelFieldName($table) {
		$labelFieldName = $GLOBALS['TCA'][$table]['ctrl']['label'];

		if ($this->conf['tablelabelfield.'][$table]) {
			$labelFieldName = $this->conf['tablelabelfield.'][$table];
		}

		return $labelFieldName;
	}

	/**
	 * Erstellt GET-Parameter fuer vordefinierte Parameter die uebergeben wurden.
	 *
	 * @return	array		$arrParams
	 */
	function getHiddenParamsArray() {
		$arrParams = array();

		foreach ($this->arrHiddenParams as $paramName) {
			$arrParamNameParts = t3lib_div::trimExplode('|', $paramName, true);

			$this->getParamArrayFromParamNameParts($arrParamNameParts, $_REQUEST, $arrParams);
		}

		return $arrParams;
	}

	/**
	 * Erstellt Hidden Fields fuer vordefinierte Parameter die uebergeben wurden.
	 *
	 * @return	string		$content
	 */
	function getHiddenParamsHiddenFields() {
		$content = '';

		foreach ($this->arrHiddenParams as $paramName) {
			$arrParams = array();
			$arrParamNameParts = t3lib_div::trimExplode('|', $paramName, true);

			$this->getParamArrayFromParamNameParts($arrParamNameParts, $_REQUEST, $arrParams);

			// Durchlaeuft das gesaeberte Array anhand der Pfad-Teile.
			while (count($arrParamNameParts) > 0) {
				$paramNamePart = array_shift($arrParamNameParts);

				// Abbrechen wenn der aktuelle Pfad-Teil nicht vorhanden ist wurde.
				if (!$arrParams[$paramNamePart]) {
					break;
				}

				// Wenn der letzte Pfad-Teil erreicht ist, das Hidden Field ausgeben.
				if (!$arrParamNameParts) {
					$arrParamNameParts = t3lib_div::trimExplode('|', $paramName, true);

					$hiddenFieldName = array_shift($arrParamNameParts);

					if ($arrParamNameParts) {
						$hiddenFieldName .= '[' . implode('][', $arrParamNameParts) . ']';
					}

					$content .= '<input type="hidden" name="' . $hiddenFieldName . '" value="' . htmlspecialchars($arrParams[$paramNamePart]) . '" />';

					break;
				}

				$arrParams = &$arrParams[$paramNamePart];
			}
		}

		return $content;
	}

	/**
	 * Durchsucht ein mehrdimensionales Array mit dem uebergebenen Pfad-Array, und uebernimmt den gefundenen Wert gesaubert in ein neues mehrdimensionales Array.
	 * Das Pfad-Array ist ein eindimensinales Array, dessen fortlaufende Werte die jeweilige Ebene im durchsuchten und geschriebenen Array repraesentieren!
	 *
	 * @param	array		$arrParamNameParts
	 * @param	array		$arrRequest // Call by reference Das Array in dem gesucht wird.
	 * @param	array		$arrParams // Call by reference das Array in das der Pfad und der Wert geschrieben werden.
	 * @return	void
	 */
	function getParamArrayFromParamNameParts($arrParamNameParts, &$arrRequest, &$arrParams) {
		while (count($arrParamNameParts) > 0) {
			$paramNamePart = array_shift($arrParamNameParts);

			// Abbrechen wenn der aktuelle Pfad-Teil nicht uebergeben wurde.
			if (!isset($arrRequest[$paramNamePart])) {
				break;
			}

			// Wenn der letzte Pfad-Teil erreicht ist, diesen uebertragen und saubern.
			if (!$arrParamNameParts) {
				$arrParams[$paramNamePart] = htmlspecialchars_decode($arrRequest[$paramNamePart]);

				break;
			}

			// Wenn noch nicht der letzte Pfad-Teil erreicht ist, und der aktuelle Pfad-Teil ein Array ist, weiter machen!
			if ($arrParamNameParts && is_array($arrRequest[$paramNamePart])) {
				if (!isset($arrParams[$paramNamePart])) {
					$arrParams[$paramNamePart] = array();
				}

				$arrParams = &$arrParams[$paramNamePart];
				$arrRequest = &$arrRequest[$paramNamePart];

				continue;
			}

			break;
		}
	}

	/**
	 * Holt Konfigurationen aus der Flexform (Tab-bedingt) und ersetzt diese pro Konfiguration in der TypoScript Konfiguration.
	 *
	 * @return	void
	 * @global	$this->conf
	 * @global	$this->extConf
	 * @global	$this->arrUsedFields
	 * @global	$this->arrUniqueFields
	 * @global	$this->arrRequiredFields
	 * @global	$this->arrHiddenParams
	 */
	function determineConfiguration() {
		$flexConf = array();

		// Extension Konfiguration ermitteln.
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

		// Alle Tabs der Flexformkonfiguration durchgehn.
		if (is_array($this->cObj->data['pi_flexform']['data'])) {
			foreach ($this->cObj->data['pi_flexform']['data'] as $tabKey => $_) {
				$flexConf = tx_datamintsfeuser_utils::getFlexformConfigurationFromTab($this->cObj->data['pi_flexform'], $tabKey, $flexConf);
			}
		}

		// Alle gesammelten Konfigurationen in $this->conf uebertragen.
		foreach ($flexConf as $key => $val) {
			if ($this->extConf['enableIrre'] && is_array($val)) {
				// Wenn IRRE Konfiguration uebergeben wurde und in der Extension Konfiguration gesetzt ist...
				$this->conf[$key] = $val;
			} else {
				// Alle anderen Konfigurationen...
				$this->conf = tx_datamintsfeuser_utils::setFlexformConfigurationValue($key, $val, $this->conf);
			}
		}

		// Die IRRE Konfiguration abarbeiten.
		if ($this->extConf['enableIrre'] && $this->conf['databasefields']) {
			$this->determineIrreConfiguration();
		}

		// Konfigurationen, die an mehreren Stellen benoetigt werden, in globales Array schreiben.
		$this->arrUsedFields = t3lib_div::trimExplode(',', $this->conf['usedfields'], true);
		$this->arrUniqueFields = array_unique(t3lib_div::trimExplode(',', $this->conf['uniquefields'], true));
		$this->arrRequiredFields = array_unique(t3lib_div::trimExplode(',', $this->conf['requiredfields'], true));
		$this->arrHiddenParams = array_unique(t3lib_div::trimExplode(',', $this->conf['hiddenparams'], true));

		// Konfigurationen die immer gelten setzten (Feldnamen sind fuer konfigurierte Felder und fuer input Felder).
		$this->arrRequiredFields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyCaptcha);
		$this->arrRequiredFields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyPasswordconfirmation);
	}

	/**
	 * Ueberschreibt eventuell vorhandene TypoScript Konfigurationen oder Flexform Konfigurationen mit den Konfigurationen aus IRRE.
	 *
	 * @return	void
	 * @global	$this->conf
	 */
	function determineIrreConfiguration() {
		if (!is_array($this->conf['databasefields'])) {
			return;
		}

		$infoitems = 1;
		$fieldsets = 2;
		$userdeleteCounter = 0;
		$passwordconfirmationCounter = 0;
		$resendactivationCounter = 0;
		$captchaCounter = 0;
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

			// Submit Button abarbeiten.
			if (isset($field[self::specialfieldKeySubmit])) {
				$usedfields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeySubmit);

				// Label setzten falls angegeben.
				if ($field[self::specialfieldKeySubmit]) {
					$this->conf['_LOCAL_LANG.'][$GLOBALS['TSFE']->lang . '.'][self::specialfieldKeySubmit . '_' . $this->conf['showtype']] = $field[self::specialfieldKeySubmit];
				}
			}

			// Captcha Feld abarbeiten.
			if (isset($field[self::specialfieldKeyCaptcha]) && $captchaCounter < 1) {
				$usedfields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyCaptcha);

				// Requiredfields wird in "determineConfiguration" immer gesetzt!

				// Label setzten falls angegeben.
				if ($field[self::specialfieldKeyCaptcha]) {
					$this->conf['_LOCAL_LANG.'][$GLOBALS['TSFE']->lang . '.'][self::specialfieldKeyCaptcha] = $field[self::specialfieldKeyCaptcha];
				}

				$captchaCounter++;
			}

			// Infoitems abarbeiten.
			if (isset($field[self::specialfieldKeyInfoitem])) {
				$usedfields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyInfoitem);

				// Falls in dem Feld etwas drinn steht.
				if ($field[self::specialfieldKeyInfoitem]) {
					$this->conf['infoitems.'][$infoitems] = $field[self::specialfieldKeyInfoitem];
				}

				$infoitems++;
			}

			// Separators / Legends abarbeiten.
			if (isset($field[self::specialfieldKeySeparator])) {
				// Beim aller ersten Separator / Legend bloss die Legend setzten!
				if ($position == $firstkey) {
					$this->conf['legends.']['1'] = $field[self::specialfieldKeySeparator];
				} else {
					$usedfields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeySeparator);

					// Falls in dem Feld etwas drinn steht.
					if ($field[self::specialfieldKeySeparator]) {
						$this->conf['legends.'][$fieldsets] = $field[self::specialfieldKeySeparator];
					}

					$fieldsets++;
				}
			}

			// Userdelete Checkbox abarbeiten.
			if (isset($field[self::specialfieldKeyUserdelete]) && $userdeleteCounter < 1) {
				$usedfields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyUserdelete);

				// Requiredfields erweitern.
				if ($field['required']) {
					$requiredfields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyUserdelete);
				}

				// Label setzten falls angegeben.
				if ($field[self::specialfieldKeyUserdelete]) {
					$this->conf['_LOCAL_LANG.'][$GLOBALS['TSFE']->lang . '.'][self::specialfieldKeyUserdelete] = $field[self::specialfieldKeyUserdelete];
				}

				$userdeleteCounter++;
			}

			// Resendactivation Feld abarbeiten.
			if (isset($field[self::specialfieldKeyResendactivation]) && $resendactivationCounter < 1) {
				$usedfields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyResendactivation);

				// Requiredfields erweitern.
				if ($field['required']) {
					$requiredfields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyResendactivation);
				}

				// Label setzten falls angegeben.
				if ($field[self::specialfieldKeyResendactivation]) {
					$this->conf['_LOCAL_LANG.'][$GLOBALS['TSFE']->lang . '.'][self::specialfieldKeyResendactivation] = $field[self::specialfieldKeyResendactivation];
				}

				$resendactivationCounter++;
			}

			// Passwordconfirmation Feld abarbeiten.
			if (isset($field[self::specialfieldKeyPasswordconfirmation]) && $passwordconfirmationCounter < 1) {
				$usedfields[] = tx_datamintsfeuser_utils::getSpecialFieldKey(self::specialfieldKeyPasswordconfirmation);

				// Requiredfields wird in "determineConfiguration" immer gesetzt!

				// Label setzten falls angegeben.
				if ($field[self::specialfieldKeyPasswordconfirmation]) {
					$this->conf['_LOCAL_LANG.'][$GLOBALS['TSFE']->lang . '.'][self::specialfieldKeyPasswordconfirmation] = $field[self::specialfieldKeyPasswordconfirmation];
				}

				$passwordconfirmationCounter++;
			}
		}

		// In Konfiguration uebertragen.
		$this->conf['usedfields'] = implode(',', $usedfields);
		$this->conf['uniquefields'] = implode(',', $uniquefields);
		$this->conf['requiredfields'] = implode(',', $requiredfields);
	}

	/**
	 * Ermittelt die komplette oder die uebergebene Unter-Konfiguration des aktuellen Anzeigetyps.
	 *
	 * @param	string		$subConfig
	 * @return	array
	 */
	function getConfigurationByShowtype($subConfig = '') {
		if (!$subConfig) {
			return $this->conf[$this->conf['showtype'] . '.'];
		}

		return $this->conf[$this->conf['showtype'] . '.'][$subConfig];
	}

	/**
	 * Gibt die komplette Validierungskonfiguration fuer die JavaScript Frontendvalidierung zurueck.
	 *
	 * @return	string		$configuration
	 */
	function getJSValidationConfiguration() {
		// Hier eine fertig generierte Konfiguration:
		// datamints_feuser_config[11]=[];
		// datamints_feuser_config[11]["username"]=[];
		// datamints_feuser_config[11]["username"]["validation"]=[];
		// datamints_feuser_config[11]["username"]["validation"]["type"]="username";
		// datamints_feuser_config[11]["username"]["valid"]="Der Benutzername darf keine Leerzeichen beinhalten!";
		// datamints_feuser_config[11]["username"]["required"]="Es muss ein Benutzername eingegeben werden!";
		// datamints_feuser_config[11]["password"]=[];
		// datamints_feuser_config[11]["password"]["validation"]=[];
		// datamints_feuser_config[11]["password"]["validation"]["type"]="password";
		// datamints_feuser_config[11]["password"]["equal"]="Es muss zwei mal das gleiche Passwort eingegeben werden!";
		// datamints_feuser_config[11]["password"]["validation"]["size"]="6";
		// datamints_feuser_config[11]["password"]["size"]="Das Passwort muss mindestens 6 Zeichen lang sein!";
		// datamints_feuser_config[11]["password"]["required"]="Es muss ein Passwort angegeben werden!";
		// datamints_feuser_inputids[11] = new Array("tx_datamintsfeuser_pi1_username", "tx_datamintsfeuser_pi1_password", "tx_datamintsfeuser_pi1_password_rep");

		$arrValidationFields = array();
		$configuration = 'var ' . $this->extKey . '_config=[];var ' . $this->extKey . '_inputids=[];' . $this->extKey . '_config[' . $this->contentId . ']=[];';

		// Bei jedem Durchgang der Schleife wird die Konfiguration fuer ein Datenbankfeld geschrieben. Ausnahmen sind hierbei Passwordfelder.
		// Gleichzeitig werden die ID's der Felder in ein Array geschrieben und am Ende zusammen gesetzt "inputids".
		foreach ($this->arrUsedFields as $fieldName) {
			if (!($this->feUsersTca['columns'][$fieldName] && is_array($this->conf['validate.'][$fieldName . '.']) || in_array($fieldName, $this->arrRequiredFields))) {
				continue;
			}

			$fieldConfig = $this->feUsersTca['columns'][$fieldName]['config'];
			$cleanedFieldName = tx_datamintsfeuser_utils::getSpecialFieldName($fieldName);

			// Die Felder bei denen Aktionen statt finden sollen (Event Listener) ermitteln.
			$itemCount = 0;
			$itemIdSuffix = 'item';

			// Die Anzahl der Felder die ausgegeben wurden (falls mehrere Felder ausgegeben, also kein Select und nur mehr als eine Checkbox).
			if ($fieldConfig['type'] == 'radio'
					|| ($fieldConfig['type'] == 'check' && count($fieldConfig['items']) > 1)
					|| ($fieldConfig['type'] == 'select' && $fieldConfig['renderMode'] == 'checkbox')) {
				$itemCount = count($fieldConfig['items']);
			}

			// Die Anzahl der Felder die ausgegeben wurden, wird beim Typ DB ueber einen Count auf die erlaubten Tabellen ermittelt.
			if ($fieldConfig['type'] == 'group' && $fieldConfig['internal_type'] == 'db') {
				$arrAllowed = t3lib_div::trimExplode(',', $fieldConfig['allowed'], true);

				foreach ($arrAllowed as $table) {
					if (!$GLOBALS['TCA'][$table]) {
						continue;
					}

					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(*) as count' , $table, '1 ' . $this->cObj->enableFields($table));
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

					$itemCount += $row['count'];
				}
			}

			// Fuer den Typ File, gibt es noch einen alternativen item Id suffix!
			if ($fieldConfig['type'] == 'group' && $fieldConfig['internal_type'] == 'file') {
				$itemCount = $fieldConfig['size'];
				$itemIdSuffix = 'upload';
			}

			// Fuer den Typ Passwort, kommt das Passwort wiederholen Feld zum normalen Feld dazu!
			if ($this->conf['validate.'][$fieldName . '.']['type'] == 'password') {
				$arrValidationFields[] = $this->getFieldId($fieldName, 'rep');
			}

			// Gesammelte Items durchlaufen, ansonsten nur eine Feld ID zum Array der zu ueberpruefenden Felder hinzufuegen!
			if ($itemCount > 0) {
				for ($i = 1; $i <= $itemCount; $i++) {
					$arrValidationFields[] = $this->getFieldId($fieldName, $itemIdSuffix, $i);
				}
			} else {
				$arrValidationFields[] = $this->getFieldId($cleanedFieldName);
			}

			// Fuer jedes Feld eine Konfiguration anlegen.
			$configuration .= $this->extKey . '_config[' . $this->contentId . ']["' . $cleanedFieldName . '"]=[];';

			// Validierungs Konfiguration ermitteln.
			if (is_array($this->conf['validate.'][$fieldName . '.'])) {
				$configuration .= $this->extKey . '_config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]=[];';

				// Da es mehrere Validierungskonfiguration pro Feld geben kann, muss hier jede einzeln durchgelaufen werden.
				foreach ($this->conf['validate.'][$fieldName . '.'] as $key => $val) {
					$cleanedVal = str_replace('"', '\\"', $val);

					if ($key == 'length') {
						$configuration .= $this->extKey . '_config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]["size"]="' . $cleanedVal . '";';
						$configuration .= $this->extKey . '_config[' . $this->contentId . ']["' . $fieldName . '"]["size"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_' . self::validationerrorKeyLength, false)) . '";';
					} else if ($key == 'regexp') {
						$configuration .= $this->extKey . '_config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]["' . $key . '"]=new RegExp("' . $cleanedVal . '");';
					} else {
						$configuration .= $this->extKey . '_config[' . $this->contentId . ']["' . $fieldName . '"]["validation"]["' . $key . '"]="' . $cleanedVal . '";';
					}

					if ($key == 'type' && $val == 'password') {
						$configuration .= $this->extKey . '_config[' . $this->contentId . ']["' . $fieldName . '"]["equal"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_' . self::validationerrorKeyEqual, false)) . '";';
					}
				}

				if ($this->conf['validate.'][$fieldName . '.']['type'] != 'password') {
					$configuration .= $this->extKey . '_config[' . $this->contentId . ']["' . $fieldName . '"]["valid"]="' . str_replace('"', '\\"', $this->getLabel($fieldName . '_error_' . self::validationerrorKeyValid, false)) . '";';
				}
			}

			// Required Konfiguration ermitteln.
			if (in_array($fieldName, $this->arrRequiredFields)) {
				$configuration .= $this->extKey . '_config[' . $this->contentId . ']["' . $cleanedFieldName . '"]["required"]="' . str_replace('"', '\\"', $this->getLabel($cleanedFieldName . '_error_' . self::validationerrorKeyRequired, false)) . '";';
			}
		}

		$configuration .= $this->extKey . '_inputids[' . $this->contentId . ']=["' . implode('","', $arrValidationFields) . '"];';

		return $configuration;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/pi1/class.tx_datamintsfeuser_pi1.php']);
}

?>