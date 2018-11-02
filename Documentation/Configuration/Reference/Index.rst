.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _configuration-reference:

Reference
---------

======================================  =============  ======================================================================================  =====================================
Property:                               Data type:     Description:                                                                            Default:
======================================  =============  ======================================================================================  =====================================
showtype                                string         Defines the output type of the plugin (register or                                      register
                                                       edit).
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
usedfields                              string         A list with all the fe_users database fields which are                                  username, password, email, --submit--
                                                       shown. The order of the fields in the Frontend is
                                                       determined by the order in this property.

                                                       Following fields have special meanings:
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
requiredfields                          string         A list of the required fields listed in the userfields                                  username, password, email
                                                       property.

                                                       All fields from "usedfields" are allowed except
                                                       "--passwordconfirmation--" and "--captcha--". This two
                                                       fields are required by default!
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
uniquefields                            string         List of fields which must be unique in the database                                     username, email
                                                       (per pid).

                                                       Only database fields are allowed!
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
uniqueglobal                            boolean        Activate it, if you have set a userfolder and want the                                  false
                                                       extension to check for uniquefields over all users (not
                                                       only the users in the userfolder).
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
requestpid                              integer        Defines the page id where the form is sent to if the
                                                       user clicks register / save. The default is the page id
                                                       where the form is displayed on.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
requestanchor                           boolean        Activate it, if you want to jump to the form after the                                  false
                                                       user clicks register / save and there was e.g. an error.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
legends.[ITEMID]                        string         A text which is shown as html legend for each fieldset.
                                                       Fieldsets are identified by increasing numbers.

                                                       **Example:**

                                                       ::

                                                            legends.1 = Example data:

                                                       This example defines the legend for the first
                                                       "--separator--" element in "usedfields".
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
infoitems.[ITEMID]                      string         A text which is shown as html div for each info item.
                                                       Info items are identified by increasing numbers.

                                                       **Example:**

                                                       ::

                                                            infoitems.1 = Example information:

                                                       This example defines the text for the first
                                                       "--infoitem--" element in "usedfields".
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
hiddenparams                            string         A list of GET- / POST- parameter names which are passed
                                                       through the registration / editing process. These
                                                       parameters are only available as GET parameters on the
                                                       following page.

                                                       **Example:**

                                                       If the URL looks like:

                                                       http://test.com?name1=value1&extkey[name2]=value2

                                                       The configuration looks like this:

                                                       ::

                                                            hiddenparams = name1,extkey|name2
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
copyfields.[FIELDTOCOPY].[COPYTOFIELD]  string / wrap  Copies the value from the [FIELDTOCOPY] field to the
                                                       [COPYTOFIELD] field.

                                                       Set to "1" to always activate the copy process. If you
                                                       only want to copy the value when the field is displayed
                                                       in the Frontend, set to "onlyused"!

                                                       **Example:**

                                                       If you want to set the email as username.

                                                       ::

                                                            copyfields.email.username = 1

                                                       If you want to set a default usergroup ("6"). Assumed
                                                       the user can choose his usergroup.

                                                       ::

                                                            copyfields.usergroup.usergroup = 1
                                                            copyfields.usergroup.usergroup.wrap = 6,|
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.sendusermail                   boolean        Defines if the user receives an E-Mail if he registers.                                 true
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.sendadminmail                  boolean        Defines if the admin receives an E-Mail if the user                                     true
                                                       registers.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.userfolder                     integer        The default page id where the user is stored in.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.usergroup                      integer        The default user group id. If more than one, use comma
                                                       separated list of uids.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.generatepassword.mode          boolean        States whether a password should be generated                                           false
                                                       automatically.

                                                       To send the password to the user, include the marker
                                                       "###PASSWORD###" in the "REGISTRATION" mail template.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.generatepassword.length        integer        The default password length for generated passwords.                                    8
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.autologin                      boolean        States whether the user should be automatically logged
                                                       in after the registration process.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.approvalcheck                  string         A list with all the approval checks which are
                                                       processed. The order of the approval steps is
                                                       determined by the order in this property.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.mailtype                       string         Content type of email (text or html).                                                   html
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.emailtemplate                  string         The path to the email template.

                                                       **Example:**

                                                       ::

                                                            register.emailtemplate =
                                                            fileadmin/templates/feuser_mail.html
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.sendername                     string         The name of the email sender.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.sendermail                     string         The email address of the email sender.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.replytoname                    string         The name of the reply to address.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.replytomail                    string         The email of the reply to address.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.adminname                      string         The name of the admin who receives a notification mail
                                                       for each new user.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
register.adminmail                      string         The email address of the admin who receives a
                                                       notification mail for each new user.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
edit.sendusermail                       boolean        Defines if the user receives an E-Mail if he edits his                                  false
                                                       data.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
edit.sendadminmail                      boolean        Defines if the admin receives an E-Mail if the user                                     false
                                                       edits his data.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
edit.mailtype                           string         Content type of email (text or html).                                                   html
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
edit.emailtemplate                      string         The path to the email template.

                                                       **Example:**

                                                       ::

                                                            edit.emailtemplate = fileadmin/templates/feuser_mail.html
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
edit.sendername                         string         The name of the email sender.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
edit.sendermail                         string         The email address of the email sender.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
edit.replytoname                        string         The name of the reply to address.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
edit.replytomail                        string         The email of the reply to address.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
edit.adminname                          string         The name of the admin who receives a notification mail
                                                       for each new user.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
edit.adminmail                          string         The email address of the admin who receives a
                                                       notification mail for each new user.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
userdelete                              boolean        If this is set the user will be completely deleted from
                                                       the database instead of setting the delete flag. This
                                                       happens if the admin or the user disapproves the
                                                       registration or if the user deletes him self.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
disablestylesheet                       boolean        Here you can disable the local (default or manual set)
                                                       CSS stylesheet.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
stylesheetpath                          string         Defines the path to your own CSS stylesheet. The
                                                       default is in the extension dir.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
disablejsvalidator                      boolean        Here you can disable the local (default or manual set)
                                                       JavaScript vaildator.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
jsvalidatorpath                         string         Defines the path to your own JavaScript validator. The
                                                       default is in the extension dir.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
disablejsconfig                         boolean        Here you can disable the local (default) JavaScript
                                                       configuration, which is used by the JavaScript
                                                       validator.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
redirect.[EVENT]                        integer        All redirect events are now documented in the
                                                       "Documentation/LabelAndRedirectDefinitionOverview.pdf" file
                                                       located within the "doc" folder of the extension!
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
validate.[FIELDNAME].type               string         This is the configuration of the validation for each
                                                       database field in fe_users. For [FIELDNAME] you have to
                                                       enter the database field name. There are some default
                                                       validation methods:

                                                       Custom validation methods are described later.

                                                       The password, email, username, zero and emptystring
                                                       options are preconfigured types of validation.

                                                       password: Compares the two password input fields and
                                                       checks for valid password length.

                                                       email: Checks for valid email address.

                                                       username: Checks if the username contains whitespaces.

                                                       zero: Checks for a zero ("0") in the string. All other
                                                       values are allowed.

                                                       emptystring: Checks for an empty string (""). All other
                                                       values are allowed.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
validate.[FIELDNAME].mode               string         The mode defines in which showtype the field is
                                                       validated. Possible options are:

                                                       Leave empty to validate the field in both showtypes.
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
validate.[FIELDNAME].length             string         You can validate the length of the given value. This
                                                       string defines the minimum and the maximum length.

                                                       **Example:**

                                                       Defining only a minimum length.

                                                       ::

                                                            validate.myfield_passw.type = password

                                                            validate.myfield_passw.length = 6

                                                       Defining a minimum length of 6 characters and a maximum
                                                       length of 12 characters.

                                                       ::

                                                            validate.myfield_passw.type = password

                                                            validate.myfield_passw.length = 6,12
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
validate.[FIELDNAME].regexp             string         If "validate.[FIELDNAME].type" is set to "custom" you
                                                       can perform a regular expression.

                                                       **Example:**

                                                       ::

                                                            validate.username.type = custom

                                                            validate.username.regexp = /^[^ ]\*$/

                                                       This regular expression states that the username must
                                                       not contain any spaces.

                                                       You can only use delemiters and modifiers which are
                                                       support by PHP and JavaScript e.g. delemiter "/",
                                                       modifiers "im"!
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
captcha.use                             string         Which captcha extension to use for the captcha field:                                   captcha

                                                       The extension which you want to use must be installed
                                                       and configured!
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
format.date                             string         The format of the date fields. Uses the php function                                    d.m.Y
                                                       "date".
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
format.datetime                         string         The format of the datetime fields. Uses the php                                         H:i d.m.Y
                                                       function "date".
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
thumb                                   image          The image configuration array for generated thumbs.

                                                       Reference: http://wiki.typo3.org/TSref/IMAGE
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
fieldconfig.[FIELDNAME]                 tca            With this configuration you can extend or overwrite any
                                                       existing TCA configuration for the given field to
                                                       modify it for the Frontend output.

                                                       **Example:**

                                                       ::

                                                            fieldconfig {
                                                                 usergroup.config {
                                                                      size = 1
                                                                      foreign_table = fe_groups
                                                                      foreign_table_where = uid IN(1,2,3,4,5)

                                                                      items {
                                                                           0 {
                                                                                0 = --- Please choose ---
                                                                                1 = 0
                                                                           }
                                                                      }
                                                                 }
                                                            }

                                                       This example shows how to make a
                                                       single-row-select-field from a foreign table.

                                                       ATTENTION: Not all options from TCA are supported /
                                                       implemented!

                                                       For more options and how to use fieldconfig visit the
                                                       official TCA documentation (Section "Column types",
                                                       subsections "Common column properties", "input",
                                                       "text", "check", "radio", "select", "group").

                                                       Reference:
                                                       http://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Index.html#columns-types
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
tablelabelfield.[FIELDNAME]             string         If you use the configuration options "foreign_table"
                                                       (type = select) or "allowed" (internal_type = db)
                                                       within the "fieldconfig" you can change the label field
                                                       of the table with this option. The items of the field
                                                       displayed in the Frontend will now use the value of
                                                       this table column.

                                                       **Example:**

                                                       ::

                                                            tablelabelfield.fe_groups = description
--------------------------------------  -------------  --------------------------------------------------------------------------------------  -------------------------------------
_LOCAL_LANG.[LANGUAGE]                  language       In this section you can overwrite any default label,
                                                       and any error output generated by the extension.

                                                       [LANGUAGE] must be replaced by the language you want to
                                                       set.

                                                       Special redirect event labels are now documented in the
                                                       "Documentation/LabelAndRedirectDefinitionOverview.pdf" file
                                                       located within the "doc" folder of the extension!

                                                       **Example:**

                                                       ::

                                                            _LOCAL_LANG.default.username = Community name:

                                                       For error output there is always the same syntax:

                                                       [DATABASEFIELDNAME]_error_[ERRORTYPE]

                                                       [DATABASEFIELDNAME] = the field name

                                                       [ERRORTYPE] = type of error

                                                       There are four default error types:

                                                       **Example:**

                                                       ::

                                                            _LOCAL_LANG.default.username_error_unique = Please choose another username!
======================================  =============  ======================================================================================  =====================================

[tsref:plugin.tx_datamintsfeuser_pi1]
