<?php

if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43('datamints_feuser', 'pi1/class.tx_datamintsfeuser_pi1.php', '_pi1', 'list_type', 0);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['datamints_feuser'] = 'Datamints\\Feuser\\Hook\\FlexFormHook';

// Extension Konfiguration auslesen.
$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['datamints_feuser']) ?: \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Configuration\\ExtensionConfiguration')->get('datamints_feuser');

// Wenn gewünscht Salesforce verwenden.
if ($confArray['enableSalesforce']) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['datamints_feuser']['sendMail']['salesforce'] = 'EXT:datamints_feuser/lib/class.tx_datamintsfeuser_salesforce.php:tx_datamintsfeuser_salesforce->main';
}
