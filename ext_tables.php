<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$extensionName = 'datamints_feuser';
if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_datamintsfeuser_pi1_wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionName) . 'pi1/class.tx_datamintsfeuser_pi1_wizicon.php';
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:' . $extensionName . '/locallang_db.xml:tt_content.list_type_pi1', $extensionName . '_pi1', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionName) . 'ext_icon.gif'), 'list_type', $extensionName);

// Flexform anzeigen und die Felder layout, select_key, pages und recursive ausblenden.
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$extensionName . '_pi1'] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$extensionName . '_pi1'] = 'layout, select_key, pages, recursive';

// Extension Konfiguration auslesen.
$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionName]);

// Flexformfunktionen einbinden.
include_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionName) . 'lib/class.tx_datamintsfeuser_flexform.php');

if ($confArray['enableIrre']) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($extensionName . '_pi1', 'FILE:EXT:' . $extensionName . '/flexform/data_pi1_irre.xml');
} else {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($extensionName . '_pi1', 'FILE:EXT:' . $extensionName . '/flexform/data_pi1.xml');
}
