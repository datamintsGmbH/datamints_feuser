<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:datamints_feuser/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_datamintsfeuser_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_datamintsfeuser_pi1_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/', 'datamints feuser styles');

// Flexform und Flexformfunktionen einbinden.
include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_flexform_getFieldNames.php');
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1','FILE:EXT:' . $_EXTKEY . '/flexform_data_pi1.xml');

?>