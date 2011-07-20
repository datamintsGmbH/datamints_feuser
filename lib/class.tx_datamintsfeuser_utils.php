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
 *   60: class tx_datamintsfeuser_utils
 *   69:     function getFeUsersTca($feUsersTca)
 *   86:     function getStoragePid($storagePid)
 *  102:     function getTypoLinkUrl($params, $urlParameters = array())
 *  117:     function htmlspecialchars($arrData, $mode)
 *  146:     function generatePassword($password, $arrGenerate = array())
 *  209:     function checkPassword($submitedPassword, $originalPassword)
 *  266:     function userAutoLogin($username, $pageId = 0, $urlParameters = array())
 *  285:     function userRedirect($pageId = 0, $urlParameters = array(), $disableAccessCheck = false)
 *  310:     function escapeBrackets($url)
 *  322:     function cleanHeaderUrlData($data)
 *  334:     function cleanSpecialFieldKey($fieldName)
 *  349:     function getTemplateSubpart($templateFile, $templatePart, $markerArray = array())
 *  372:     function readFlexformTab($flexData, $sTab, &$conf)
 *  406:     function setFlexformConfiguration($key, $value, $conf)
 *  436:     function checkUtf8($str)
 *
 *
 * TOTAL FUNCTIONS: 14
 *
 */

/**
 * Library 'Utils' for the 'datamints_feuser' extension.
 *
 * @author	Bernhard Baumgartl <b.baumgartl@datamints.com>
 * @package	TYPO3
 * @subpackage	tx_datamintsfeuser
 */
class tx_datamintsfeuser_utils {


	/**
	 * Ueberschreibt eventuell vorhandene TCA Konfiguration mit TypoScript Konfiguration.
	 *
	 * @param	array		$feUsersTca
	 * @return	array		$globalFeUsersTca
	 */
	function getFeUsersTca($feUsersTca) {
		$GLOBALS['TSFE']->includeTCA();
		$globalFeUsersTca = $GLOBALS['TCA']['fe_users'];

		if ($feUsersTca) {
			$globalFeUsersTca['columns'] = t3lib_div::array_merge_recursive_overrule((array)$globalFeUsersTca['columns'], (array)t3lib_div::removeDotsFromTS($feUsersTca));
		}

		return $globalFeUsersTca;
	}

	/**
	 * Ermittelt die General Record Storage Pid, falls keine Pid uebergeben wurde.
	 *
	 * @param	integer		$storagePid
	 * @return	integer		$storagePid
	 */
	function getStoragePid($storagePid) {
		if (!$storagePid) {
			$arrayRootPids = $GLOBALS['TSFE']->getStorageSiterootPids();
			$storagePid = $arrayRootPids['_STORAGE_PID'];
		}

		return $storagePid;
	}

	/**
	 * Ermittelt die Url zu einer Seite oder einer Datei.
	 *
	 * @param	string		$params
	 * @param	array		$urlParameters
	 * @return	string		$pageLink
	 */
	function getTypoLinkUrl($params, $urlParameters = array()) {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$pageLink = $cObj->getTypoLink_URL($params, $urlParameters);

		return $pageLink;
	}

	/**
	 * Konvertiert alle Inhalte des uebernenen Arrays um z.B. XSS zu verhindern.
	 * Der Modus gibt an ob das Array encodiert oder decodiert werden soll.
	 *
	 * @param	array		$arrUpdate
	 * @param	boolean		$mode
	 * @return	array		$arrUpdate
	 */
	function htmlspecialchars($arrData, $mode) {
		if ($mode) {
			// Konvertiert alle moeglichen Zeichen der Ausgabe, die stoeren koennten (XSS).
			foreach ($arrData as $key => $val) {
				if (is_array($arrData[$key])) {
					foreach ($arrData[$key] as $subKey => $subVal) {
						$arrData[$key][$subKey] = strip_tags($subVal);
					}
				} else {
					$arrData[$key] = htmlspecialchars($val);
				}
			}
		} else {
			// Konvertiert alle moeglichen Zeichen die fuer die Ausgabe angepasst wurden zurueck.
			foreach ($arrData as $key => $val) {
				$arrData[$key] = htmlspecialchars_decode($val);
			}
		}

		return $arrData;
	}

	/**
	 * Erstellt wenn gefordert ein Password, und verschluesselt dieses, oder das uebergebene, wenn es verschluesselt werden soll.
	 *
	 * @param	string		$password
	 * @param	array		$arrGenerate
	 * @return	array		$arrPassword
	 */
	function generatePassword($password, $arrGenerate = array()) {
		$arrPassword = array();

		// Uebergebenes Password setzten.
		// Hier wird kein strip_tags() o.Ae. benoetigt, da beim schreiben in die Datenbank immer "$GLOBALS['TYPO3_DB']->fullQuoteStr()" ausgefuehrt wird!
		$arrPassword['normal'] = trim($password);

		// Erstellt ein Password.
		if ($arrGenerate['mode']) {
			$i = 1;
			$arrPassword['normal'] = '';
			$chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHIKLMNPQRSTUVWXYZ';

			while ($i <= (($arrGenerate['length']) ? $arrGenerate['length'] : 8)) {
				$arrPassword['normal'] .= $chars{mt_rand(0, strlen($chars))};
				$i++;
			}

		}

		// Unverschluesseltes Passwort uebertragen.
		$arrPassword['encrypted'] = $arrPassword['normal'];

		// Wenn "saltedpasswords" installiert ist wird deren Konfiguration geholt, und je nach Einstellung das Password verschluesselt.
		if (t3lib_extMgm::isLoaded('saltedpasswords')) {
			$saltedpasswords = tx_saltedpasswords_div::returnExtConf();

			if ($saltedpasswords['enabled'] == 1) {
				$tx_saltedpasswords = t3lib_div::makeInstance($saltedpasswords['saltedPWHashingMethod']);
				$arrPassword['encrypted'] = $tx_saltedpasswords->getHashedPassword($arrPassword['normal']);
			}
		} else

		// Wenn "md5passwords" installiert ist wird wenn aktiviert, das Password md5 verschluesselt.
		if (t3lib_extMgm::isLoaded('md5passwords')) {
			$arrConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['md5passwords']);

			if ($arrConf['activate'] == 1) {
				$arrPassword['encrypted'] = md5($arrPassword['normal']);
			}
		} else

		// Wenn "t3sec_saltedpw" installiert ist wird wenn aktiviert, das Password gehashed.
		if (t3lib_extMgm::isLoaded('t3sec_saltedpw')) {
			require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/staticlib/class.tx_t3secsaltedpw_div.php';

			if (tx_t3secsaltedpw_div::isUsageEnabled()) {
				require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/lib/class.tx_t3secsaltedpw_phpass.php';
				$tx_t3secsaltedpw_phpass = t3lib_div::makeInstance('tx_t3secsaltedpw_phpass');
				$arrPassword['encrypted'] = $tx_t3secsaltedpw_phpass->getHashedPassword($arrPassword['normal']);
			}
		}

		return $arrPassword;
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
	 * Vollzieht einen Login ohne ein Passwort.
	 *
	 * @param	string		$username
	 * @param	integer		$pageId
	 * @param	array		$urlParameters
	 * @return	void
	 */
	function userAutoLogin($username, $pageId = 0, $urlParameters = array()) {
		// Login vollziehen.
		$GLOBALS['TSFE']->fe_user->checkPid = 0;
		$arrAuthInfo = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
		$userRecord = $GLOBALS['TSFE']->fe_user->fetchUserRecord($arrAuthInfo['db_user'], $username);
		$GLOBALS['TSFE']->fe_user->createUserSession($userRecord);

		// Umleiten, damit der Login wirksam wird.
		self::userRedirect($pageId, $urlParameters, true);
	}

	/**
	 * Vollzieht einen Redirect mit der Seite die benutzt wird, oder auf die aktuelle.
	 *
	 * @param	integer		$pageId
	 * @param	array		$urlParameters
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

		$pageLink = self::getTypoLinkUrl($pageId, $urlParameters);

		header('Location: ' . t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $pageLink);
		exit;
	}

	/**
	 * URL encoded die eckigen Klammern in einem Link.
	 *
	 * @param	string		$url
	 * @return	string
	 */
	function escapeBrackets($url) {
		$replace = array('[' => '%5b', ']' => '%5d');

		return str_replace(array_keys($replace), array_values($replace), $url);
	}

	/**
	 * Konvertiert einen String, um ihn in mit der Funktion header() nutzen zu koennen.
	 *
	 * @param	string		$data
	 * @return	string		$data
	 */
	function cleanHeaderUrlData($data) {
		$data = urlencode(strip_tags(preg_replace("/[\r\n]/", '', $data)));

		return $data;
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
	 * Holt einen Subpart des Standardtemplates und ersetzt uebergeben Marker.
	 *
	 * @param	string		$templateFile
	 * @param	string		$templatePart
	 * @param	array		$markerArray
	 * @return	string		$template
	 */
	function getTemplateSubpart($templateFile, $templatePart, $markerArray = array()) {
		// Template laden.
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$template = $cObj->fileResource($templateFile);
		$template = $cObj->getSubpart($template, '###' . strtoupper($templatePart) . '###');

//		if (!self::checkUtf8($template)) {
//			$template = utf8_encode($template);
//		}

		$template = $cObj->substituteMarkerArray($template, $markerArray, '###|###', 1);

		return $template;
	}

	/**
	 * Parst das Flexform Konfigurations Array und schreibt alle Werte in $conf.
	 *
	 * @param	array		$flexData
	 * @param	string		$sType
	 * @param	array		$conf // Call by reference Array mit allen zu updatenden Daten.
	 * @return	void
	 */
	function readFlexformTab($flexData, $sTab, &$conf) {
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
							 self::readFlexformTab($element, $sTab, $conf[$key][$ekey]);
						 }
					 }
				 } else {
					 self::readFlexformTab($value['el'], $sTab, $conf);
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
	 * @param	array		$conf
	 * @return	array		$conf
	 */
	function setFlexformConfiguration($key, $value, $conf) {
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

			$conf = t3lib_div::array_merge_recursive_overrule($conf, $value);
		} else if ($value) {
			$conf[$key] = $value;
		}

		return $conf;
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


}

?>