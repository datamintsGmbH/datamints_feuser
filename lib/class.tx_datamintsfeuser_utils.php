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
 *   66: class tx_datamintsfeuser_utils
 *   74:     public static function getFeUsersTca($feUsersTca)
 *   91:     public static function getStoragePageId($storagePageId)
 *  107:     public static function getTypoLinkUrl($params, $urlParameters = array())
 *  121:     public static function currentUserWrap($content, $stdWrap)
 *  135:     public static function fixPath($path)
 *  147:     public static function htmlspecialcharsPostArray(&$arrPost, $decode)
 *  178:     public static function shiftEmptyArrayValuePostArray(&$arrPost)
 *  197:     public static function generatePassword($password, $arrGenerate = array())
 *  261:     public static function checkPassword($submittedPassword, $originalPassword)
 *  321:     public static function userAutoLogin($userId, $pageId = 0, $urlParameters = array())
 *  341:     public static function userRedirect($pageId = 0, $urlParameters = array(), $disableAccessCheck = FALSE)
 *  366:     public static function escapeBrackets($url)
 *  378:     public static function getSpecialFieldKey($fieldName)
 *  388:     public static function getSpecialFieldName($fieldName)
 *  402:     public static function convertHtmlEmailToPlain($content)
 *  443:     public static function getTemplateSubpart($templateFile, $templatePart, $markerArray = array())
 *  466:     public static function getFlexformConfigurationFromTab($flexData, $sTab, $conf = array())
 *  512:     public static function setFlexformConfigurationValue($key, $value, $conf)
 *  542:     public static function trimCallback(&$string)
 *  552:     public static function stripTagsCallback(&$string)
 *  562:     public static function checkUtf8($str)
 *
 *
 * TOTAL FUNCTIONS: 21
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
	public static function getFeUsersTca($feUsersTca) {
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
	 * @param	integer		$storagePageId
	 * @return	integer		$storagePid
	 */
	public static function getStoragePageId($storagePageId) {
		if (!$storagePageId) {
			$arrayRootPids = $GLOBALS['TSFE']->getStorageSiterootPids();
			$storagePageId = $arrayRootPids['_STORAGE_PID'];
		}

		return intval($storagePageId);
	}

	/**
	 * Ermittelt die Url zu einer Seite oder einer Datei.
	 *
	 * @param	string		$params
	 * @param	array		$urlParameters
	 * @return	string		$pageLink
	 */
	public static function getTypoLinkUrl($params, $urlParameters = array()) {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$pageLink = $cObj->getTypoLink_URL($params, $urlParameters);

		return $pageLink;
	}

	/**
	 * Fuehrt einen stdWrap mit den aktuellen Benutzerdaten aus.
	 *
	 * @param	string		$content
	 * @param	array		$stdWrap
	 * @return	string		$content
	 */
	public static function currentUserWrap($content, $stdWrap) {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->data = $GLOBALS['TSFE']->fe_user->user;

		return $cObj->stdWrap($content, $stdWrap);
	}

	/**
	 * Wird verwendet, um doppelte Schraegstriche zu vermeiden.
	 * Der Pfad wird mit einem abschliessenden Schraegstrich zurueckgegeben.
	 *
	 * @param	string		$path
	 * @return	string		$path
	 */
	public static function fixPath($path) {
		return dirname($path . '/.') . '/';
	}

	/**
	 * Konvertiert alle Werte des uebergebenen Post Arrays um z.B. XSS zu verhindern.
	 * Der Modus gibt an ob die Werte encodiert oder decodiert werden soll.
	 *
	 * @param	array		$arrPost // Call by reference: Das Post Array dessen Werte konvertiert werden.
	 * @param	boolean		$decode
	 * @return	boolean
	 */
	public static function htmlspecialcharsPostArray(&$arrPost, $decode) {
		if ($decode) {
			// Konvertiert alle moeglichen Zeichen die fuer die Ausgabe angepasst wurden zurueck.
			foreach ($arrPost as $key => $val) {
				if (!is_array($arrPost[$key])) {
					$arrPost[$key] = htmlspecialchars_decode($val);
				}
			}
		} else {
			// Konvertiert alle moeglichen Zeichen der Ausgabe, die stoeren koennten (XSS).
			foreach ($arrPost as $key => $val) {
				// Falls es kein Array ist, darf auch HTML enthalten sein, deshalb nur htmlspecialchars() anwenden!
				if (!is_array($arrPost[$key])) {
					$arrPost[$key] = htmlspecialchars($val);
				} else {
					// Wenn es ein Array ist, dann auf alle Elemente strip_tags() anwenden!
					array_walk_recursive($arrPost[$key], 'tx_datamintsfeuser_utils::stripTagsCallback');
				}
			}
		}

		return TRUE;
	}

	/**
	 * Es wird jeder Wert im Post Array ueberprueft ob er ein Array ist.
	 * Wenn dass der Fall ist, wird der erste Wert in diesem Array entfernt, falls dieser ein Leerstring ist.
	 *
	 * @param	array		$arrPost // Call by reference: Das Post Array
	 * @return	boolean
	 */
	public static function shiftEmptyArrayValuePostArray(&$arrPost) {
		foreach($arrPost as $key => $value) {
			if (is_array($value) && $value[0] === '') {
				array_shift($value);

				$arrPost[$key] = $value;
			}
		}

		return TRUE;
	}

	/**
	 * Erstellt wenn gefordert ein Password, und verschluesselt dieses, oder das uebergebene, wenn es verschluesselt werden soll.
	 *
	 * @param	string		$password
	 * @param	array		$arrGenerate
	 * @return	array		$arrPassword
	 */
	public static function generatePassword($password, $arrGenerate = array()) {
		$arrPassword = array();

		// Uebergebenes Password setzten.
		// Hier wird kein strip_tags() o.Ae. benoetigt, da beim schreiben in die Datenbank immer "$GLOBALS['TYPO3_DB']->fullQuoteStr()" ausgefuehrt wird!
		$arrPassword['normal'] = trim($password);

		// Erstellt ein Password.
		if ($arrGenerate['mode']) {
			$chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHIKLMNPQRSTUVWXYZ';

			$arrPassword['normal'] = '';

			for ($i = 0; $i < (($arrGenerate['length']) ? $arrGenerate['length'] : 8); $i++) {
				$arrPassword['normal'] .= $chars{mt_rand(0, strlen($chars))};
			}
		}

		// Unverschluesseltes Passwort uebertragen.
		$arrPassword['encrypted'] = $arrPassword['normal'];

		// Wenn "saltedpasswords" installiert ist wird deren Konfiguration geholt, und je nach Einstellung das Password verschluesselt.
		if (t3lib_extMgm::isLoaded('saltedpasswords') && $GLOBALS['TYPO3_CONF_VARS']['FE']['loginSecurityLevel']) {
			$saltedpasswords = tx_saltedpasswords_div::returnExtConf();

			if ($saltedpasswords['enabled']) {
				$tx_saltedpasswords = t3lib_div::makeInstance($saltedpasswords['saltedPWHashingMethod']);

				$arrPassword['encrypted'] = $tx_saltedpasswords->getHashedPassword($arrPassword['normal']);
			}
		} else

		// Wenn "md5passwords" installiert ist wird wenn aktiviert, das Password md5 verschluesselt.
		if (t3lib_extMgm::isLoaded('md5passwords')) {
			$arrConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['md5passwords']);

			if ($arrConf['activate']) {
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
	 * @param	string		$submittedPassword
	 * @param	string		$originalPassword
	 * @return	boolean		$check
	 */
	public static function checkPassword($submittedPassword, $originalPassword) {
		$check = FALSE;

		// Wenn "saltedpasswords" installiert ist wird deren Konfiguration geholt, und je nach Einstellung das Password ueberprueft.
		if (t3lib_extMgm::isLoaded('saltedpasswords') && $GLOBALS['TYPO3_CONF_VARS']['FE']['loginSecurityLevel']) {
			$saltedpasswords = tx_saltedpasswords_div::returnExtConf();

			if ($saltedpasswords['enabled']) {
				$tx_saltedpasswords = t3lib_div::makeInstance($saltedpasswords['saltedPWHashingMethod']);

				if ($tx_saltedpasswords->checkPassword($submittedPassword, $originalPassword)) {
					$check = TRUE;
				}
			}
		}

		// Wenn "md5passwords" installiert ist wird wenn aktiviert, das Password ueberprueft.
		else if (t3lib_extMgm::isLoaded('md5passwords')) {
			$arrConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['md5passwords']);

			if ($arrConf['activate']) {
				if (md5($submittedPassword) == $originalPassword) {
					$check = TRUE;
				}
			}
		}

		// Wenn "t3sec_saltedpw" installiert ist wird wenn aktiviert, das Password ueberprueft.
		else if (t3lib_extMgm::isLoaded('t3sec_saltedpw')) {
			require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/staticlib/class.tx_t3secsaltedpw_div.php';

			if (tx_t3secsaltedpw_div::isUsageEnabled()) {
				require_once t3lib_extMgm::extPath('t3sec_saltedpw') . 'res/lib/class.tx_t3secsaltedpw_phpass.php';

				$tx_t3secsaltedpw_phpass = t3lib_div::makeInstance('tx_t3secsaltedpw_phpass');

				if ($tx_t3secsaltedpw_phpass->checkPassword($submittedPassword, $originalPassword)) {
					$check = TRUE;
				}
			}
		}

		// Wenn keine der oberen Extensions installiert ist (also keine Verschluesselung).
		else {
			if ($submittedPassword == $originalPassword) {
				$check = TRUE;
			}
		}

		return $check;
	}

	/**
	 * Vollzieht einen Login ohne ein Passwort.
	 *
	 * @param	integer		$userId
	 * @param	integer		$pageId
	 * @param	array		$urlParameters
	 * @return	void
	 */
	public static function userAutoLogin($userId, $pageId = 0, $urlParameters = array()) {
		// Login vollziehen.
		$GLOBALS['TSFE']->fe_user->checkPid = 0;

		$userRecord = $GLOBALS['TSFE']->fe_user->getRawUserByUid($userId);

		$GLOBALS['TSFE']->fe_user->createUserSession($userRecord);

		// Umleiten, damit der Login wirksam wird.
		self::userRedirect($pageId, $urlParameters, TRUE);
	}

	/**
	 * Vollzieht einen Redirect mit der Seite die benutzt wird, oder auf die aktuelle.
	 *
	 * @param	integer		$pageId
	 * @param	array		$urlParameters
	 * @param	boolean		$disableAccessCheck
	 * @return	void
	 */
	public static function userRedirect($pageId = 0, $urlParameters = array(), $disableAccessCheck = FALSE) {
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

		header('Location: ' . t3lib_div::locationHeaderUrl($pageLink));
		exit;
	}

	/**
	 * URL encoded die eckigen Klammern in einem Link.
	 *
	 * @param	string		$url
	 * @return	string
	 */
	public static function escapeBrackets($url) {
		$replace = array('[' => '%5b', ']' => '%5d');

		return str_replace(array_keys($replace), array_values($replace), $url);
	}

	/**
	 * Fuegt die '--' Zeichen vor und hinter dem eigendlichen Feldnamen, hinzu um den eindeutigen Key zu bekommen.
	 *
	 * @param	string		$fieldName
	 * @return	string
	 */
	public static function getSpecialFieldKey($fieldName) {
		return '--' . $fieldName . '--';
	}

	/**
	 * Ersetzt die beim Eingeben angegebenen '--' Zeichen vor und hinter dem eigendlichen Feldnamen, falls vorhanden.
	 *
	 * @param	string		$fieldName
	 * @return	string
	 */
	public static function getSpecialFieldName($fieldName) {
		if (preg_match('/^--.*--$/', $fieldName)) {
			return preg_replace('/^--(.*)--$/', '\1', $fieldName);
		}

		return $fieldName;
	}

	/**
	 * Convertiert eine HTML E-Mail zu einer Plain Text E-Mail.
	 *
	 * @param	string		$content
	 * @return	string		$content
	 */
	public static function convertHtmlEmailToPlain($content) {
		$newLine = chr(13) . chr(10);

		// Den Head entfernen.
		$content = preg_replace('/<head>.*?<\/head>/s', '', $content, 1);

		// Links auflösen (A-Tag entfernen und Href extrahieren).
		$content = preg_replace('/<a[^>]*href="([^"]*)"[^>]*>[^<]*<\/a>/i', ' $1 ', $content);

		// Nach jedem schliessenden Tag eine Leerzeile einfuegen.
		$content = preg_replace('/>/i', '>' . $newLine, $content);

		// HTML Sonderzeichen in Textzeichen umwandeln.
		$content = html_entity_decode($content);

		// Alle HTML Tags entfernen und allgemein trimmen.
		$content = trim(strip_tags($content));

		// Jede Zeile trimmen.
		$arrContent = preg_split('/\r?\n/', $content);

		foreach ($arrContent as $key => $val) {
			$arrContent[$key] = trim($val);
		}

		$content = implode($newLine, $arrContent);

		// Wenn mehr als 2 Zeilenumbrueche hintereinander kommen, 2 daraus machen.
		$content = preg_replace('/(' . $newLine . '){2,}/', $newLine . $newLine, $content);

		return $content;
	}

	/**
	 * Holt einen Subpart des Standardtemplates und ersetzt uebergeben Marker.
	 *
	 * @param	string		$templateFile
	 * @param	string		$templatePart
	 * @param	array		$markerArray
	 * @return	string		$template
	 */
	public static function getTemplateSubpart($templateFile, $templatePart, $markerArray = array()) {
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
	 * @param	string		$sTab
	 * @param	array		$conf
	 * @return	array		$conf
	 */
	public static function getFlexformConfigurationFromTab($flexData, $sTab, $conf = array()) {
		if (isset($flexData['data'][$sTab]['lDEF'])) {
			$flexData = $flexData['data'][$sTab]['lDEF'];
		}

		if (!is_array($flexData)) {
			return $conf;
		}

		foreach ($flexData as $key => $value) {
			if (!is_array($value)) {
				continue;
			}

			if (is_array($value['el']) && count($value['el']) > 0) {
				foreach ($value['el'] as $ekey => $element) {
					if (!is_array($element)) {
						continue;
					}

					if (isset($element['vDEF'])) {
						$conf[$ekey] = $element['vDEF'];
					} else {
						$conf[$key][$ekey] = self::getFlexformConfigurationFromTab($element, $sTab, $conf[$key][$ekey]);
					}
				}
			} else {
				$conf = self::getFlexformConfigurationFromTab($value['el'], $sTab, $conf);
			}

			if ($value['vDEF']) {
				$conf[$key] = $value['vDEF'];
			}
		}

		return $conf;
	}

	/**
	 * Ueberschreibt eventuell vorhandene TypoScript Konfigurationen mit den Konfigurationen aus der Flexform.
	 *
	 * @param	string		$key
	 * @param	string		$value
	 * @param	array		$conf
	 * @return	array		$conf
	 */
	public static function setFlexformConfigurationValue($key, $value, $conf) {
		if (strpos($key, '.') !== FALSE && $value) {
			$arrKey = t3lib_div::trimExplode('.', $key, TRUE);

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
	 * Nimmt einen String entgegen um auf diesen ein trim() anzuwenden.
	 *
	 * @param	string		$string // Call by reference: Der String der getrimmt wird.
	 * @return	void
	 */
	public static function trimCallback(&$string) {
		$string = trim($string);
	}

	/**
	 * Nimmt einen String entgegen um auf diesen ein strip_tags() anzuwenden.
	 *
	 * @param	string		$string // Call by reference: Der String der gesaubert wird.
	 * @return	void
	 */
	public static function stripTagsCallback(&$string) {
		$string = strip_tags($string);
	}

	/**
	 * Checks if a string is utf8 encoded or not.
	 *
	 * @param	string		$str
	 * @return	boolean
	 */
	public static function checkUtf8($str) {
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++) {
			$c = ord($str[$i]);

			if ($c > 128) {
				if (($c > 247)) {
					return FALSE;
				} else if ($c > 239) {
					$bytes = 4;
				} else if ($c > 223) {
					$bytes = 3;
				} else if ($c > 191) {
					$bytes = 2;
				} else {
					return FALSE;
				}

				if (($i + $bytes) > $len) {
					return FALSE;
				}

				while ($bytes > 1) {
					$i++;
					$b = ord($str[$i]);

					if ($b < 128 || $b > 191) {
						return FALSE;
					}

					$bytes--;
				}
			}
		}

		return TRUE;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/lib/class.tx_datamintsfeuser_utils.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/datamints_feuser/lib/class.tx_datamintsfeuser_utils.php']);
}

?>