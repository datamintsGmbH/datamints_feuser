<?php

class tx_flexform_getFieldNames {

	/**
	 * The getFields method is used to get the 
	 * fe_users FIELDS into the flexform of the plugin
	 *
	 * @param	arr		$config: the fields selected
	 * @return	arr		$config
	 */
	function getFieldNames($config) {
		
		global $TCA; //Damit $TCA hier zur Verfügung steht
		//$TCA-Teil laden. Damit können wir alle Felder durchgehen
		t3lib_div::loadTCA('fe_users');

		$fieldList = array();
		foreach ($TCA['fe_users']['columns'] as $key => $value){
			$fieldList[] = array( $key, $key);
		}

		$config['items'] = array_merge($config['items'],$fieldList);
		return $config;
	}

}

?>