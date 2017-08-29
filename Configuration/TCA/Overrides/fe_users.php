<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$tempColumns = array (
	'gender' => array (
		'exclude' => '1',
		'label' => 'LLL:EXT:datamints_feuser/locallang_db.xml:fe_users.gender',
		'config' => array (
			'type' => 'radio',
			'items' => array (
				array('LLL:EXT:datamints_feuser/locallang_db.xml:fe_users.gender.I.0', '0'),
				array('LLL:EXT:datamints_feuser/locallang_db.xml:fe_users.gender.I.1', '1')
			),
		)
	),
	'tx_datamintsfeuser_approval_level' => array (
		'exclude' => '1',
		'label' => 'LLL:EXT:datamints_feuser/locallang_db.xml:fe_users.tx_datamintsfeuser_approval_level',
		'config' => array (
			'type' => 'input',
			'size' => '2',
			'eval' => 'int',
			'range' => array (
				'upper' => '2',
				'lower' => '0'
			),
			'default' => '0'
		)
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'gender', '', 'before:name');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', '--div--;LLL:EXT:datamints_feuser/locallang_db.xml:tt_content.list_type_pi1, tx_datamintsfeuser_approval_level');
