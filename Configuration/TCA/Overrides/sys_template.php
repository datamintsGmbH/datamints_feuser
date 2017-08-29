<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('datamints_feuser', 'Configuration/TypoScript/', 'Frontend User Management');

// Salesforce
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('datamints_feuser', 'Configuration/TypoScript/Salesforce/', 'Frontend User Management (Salesforce)');
