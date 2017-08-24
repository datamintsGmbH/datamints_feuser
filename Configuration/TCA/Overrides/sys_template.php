<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'datamints_feuser',
    'pi1/static/',
    'Frontend User Management'
);

// Salesforce
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'datamints_feuser',
    'static/salesforce/',
    'Frontend User Management (Salesforce)'
);