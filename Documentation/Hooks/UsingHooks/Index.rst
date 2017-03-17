.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _hooks-using-hooks:

Using Hooks
-----------

The names of the hooks are:

- sendForm
- sendMail
- showOutputRedirect

To use one of these hooks in your extension, define the following in your extensions
"ext_tables.php" file:

::

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['datamints_feuser'][###HOOKNAME###][] = 'EXT:' . $_EXTKEY . '/lib/class.tx_feuserhook_example .php:tx_feuserhook_example->main';

Replace "###HOOKNAME###" with the hook name you want to use.

After that create a class with the name defined in the hook definition.

::

    class tx_feuserhook_example {
        function main($params, $pObj) {
            // What ever you want to do here...
            return;
        }
    }

You always have these two method parameters (example!):

::

    $params = array (
        // These Variables can not be changed!
        'variables' => array(
            'arrUpdate' => $arrUpdate
        ),
        // These Parameters can be used to manipulate the process after the Hook!
        'parameters' => array(
            'mode' => &$mode,
            'submode' => &$submode,
            'params' => &$params
        )
    );

    $pObj->feUsersTca;        // The modified Frontend user TCA.
    $pObj->storagePid;        // The determined storage page ID.
    $pObj->contentUid;        // The TYPO3 content element ID.
    $pObj->conf;              // The extensions TypoScript configuration.
    $pObj->extConf;           // The global extension configuration.
    $pObj->userId;            // The current user ID.
    $pObj->arrUsedFields;     // The fields which are displayed.
    $pObj->arrRequiredFields; // The field which are required.
    $pObj->arrUniqueFields;   // The field which are unique.
    $pObj->arrHiddenParams;   // The hidden params array.
