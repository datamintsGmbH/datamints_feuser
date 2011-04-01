<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_datamintsfeuser_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_datamintsfeuser_pi1_wizicon.php';
}

t3lib_extMgm::addPlugin(array('LLL:EXT:datamints_feuser/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY . '_pi1', t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'), 'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY, 'pi1/static/', 'Frontend User Management');

// TCA von tt_content laden, Flexform anzeigen und die Felder layout, select_key, pages und recursive ausblenden.
t3lib_div::loadTCA('tt_content');

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout, select_key, pages, recursive';

// Extension Konfiguration auslesen.
$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);

// Flexformfunktionen einbinden.
include_once(t3lib_extMgm::extPath($_EXTKEY) . 'class.tx_flexform_getFieldNames.php');

if ($confArray['useIRRE']) {
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1','FILE:EXT:' . $_EXTKEY . '/flexform_data_pi1_irre.xml');
} else {
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1','FILE:EXT:' . $_EXTKEY . '/flexform_data_pi1.xml');
}

$tempColumns = array (
	'tx_datamintsfeuser_firstname' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:datamints_feuser/locallang_db.xml:fe_users.tx_datamintsfeuser_firstname',		
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'eval' => 'trim',
		)
	),
	'tx_datamintsfeuser_surname' => array (		
		'exclude' => 0,
		'label' => 'LLL:EXT:datamints_feuser/locallang_db.xml:fe_users.tx_datamintsfeuser_surname',
		'config' => array (
			'type' => 'input',
			'size' => '30',
			'eval' => 'trim',
		)
	),
	'tx_datamintsfeuser_approval_level' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:datamints_feuser/locallang_db.xml:fe_users.tx_datamintsfeuser_approval_level',
		'config' => Array (
			'type'     => 'input',
			'size'     => '2',
			'eval'     => 'int',
			'range'    => array (
				'upper' => '2',
				'lower' => '0'
			),
			'default' => 0
		)
	),
);

t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users', $tempColumns, 1);

//$TCA['fe_users']['types'][0]['showitem'] = str_replace('name;;1;;1-1-1,', 'name;;1;;1-1-1, tx_datamintsfeuser_firstname, tx_datamintsfeuser_surname,', $TCA['fe_users']['types'][0]['showitem']);
//t3lib_extMgm::addFieldsToAllPalettesOfField('fe_users', 'name', 'tx_datamintsfeuser_firstname, tx_datamintsfeuser_surname, --linebreak--', 'before:title,before:first_name');
t3lib_extMgm::addToAllTCAtypes('fe_users', 'tx_datamintsfeuser_firstname, tx_datamintsfeuser_surname', '', 'after:name');
t3lib_extMgm::addToAllTCAtypes( 'fe_users', '--div--;LLL:EXT:datamints_feuser/locallang_db.xml:tt_content.list_type_pi1, tx_datamintsfeuser_approval_level;;;;1-1-1');

?>