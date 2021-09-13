<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_datamintsfeuser_pi1_wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('datamints_feuser') . 'pi1/class.tx_datamintsfeuser_pi1_wizicon.php';
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:datamints_feuser/locallang_db.xml:tt_content.list_type_pi1', 'datamints_feuser_pi1', 'EXT:datamints_feuser/Resources/Public/Icons/Extension.gif'), 'list_type', 'datamints_feuser');

// Flexform anzeigen und die Felder layout, select_key, pages und recursive ausblenden.
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['datamints_feuser_pi1'] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['datamints_feuser_pi1'] = 'layout, select_key, pages, recursive';

// Extension Konfiguration auslesen.
$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['datamints_feuser']) ?: \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Configuration\\ExtensionConfiguration')->get('datamints_feuser');

// Flexformfunktionen einbinden.
include_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('datamints_feuser') . 'lib/class.tx_datamintsfeuser_flexform.php');

if ($confArray['enableIrre']) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('datamints_feuser_pi1', 'FILE:EXT:datamints_feuser/flexform/data_pi1_irre.xml');
} else {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('datamints_feuser_pi1', 'FILE:EXT:datamints_feuser/flexform/data_pi1.xml');
}
