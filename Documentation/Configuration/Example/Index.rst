.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _configuration-reference-example:

Example
^^^^^^^

::

    plugin.tx_datamintsfeuser_pi1 {
        showtype = register
        // Displayed fields.
        usedfields = username,password,image,--separator--,usergroup,--submit--
        // Required fields.
        requiredfields = username,password,usergroup
        // Unique fields.
        uniquefields = username,email

        register {
            // The pid in which the user are saved, and the default usergroup.
            userfolder = 12
            usergroup = 4
            // Generate a password with the given length.
            generatepassword.mode = 1
            generatepassword.length = 8
            // Perform a double-opt-in.
            approvalcheck = doubleoptin
            // The content type of the email (text or html).
            mailtype = text
            // The path to the template file.
            emailtemplate = fileadmin/templates/datamints_feuser_mail.html
            // The name und email address of the sender.
            sendername = mein-seite.com
            sendermail = info@my-site.com
            // The name and email address of the admin.
            adminname = Admin Armin
            adminmail = admin.armin@my-site.com
        }

        redirect {
            // The page id to which the user is redirected after registration.
            register_success = 13
        }

        validate {
            // Validate the password field with the predefined password type.
            password.type = password
            password.length = 6
            // Validate the email field with the predefined email type.
            email.type = email
            // Validate the username field with the predefined username type.
            username.type = usernames
            // States which input values are valid.
            usergroup.type = custom
            usergroup.regexp = /^(1|2|3|4|5)$/
        }

        fieldconfig {
            usergroup.config {
                // Make a single select box.
                size = 1
                // Show only the selected entries.
                foreign_table = fe_groups
                foreign_table_where = uid IN(1,2,3,4,5)
                // Add a item to the select box with the value 0.
                items {
                    0 {
                        0 = --- Please choose ---
                        1 = 0
                    }
                }
            }
        }

        _LOCAL_LANG.default {
            // Change the label of the usergroup field.
            usergroup = Your group:
            // This error appears if no value is entered from the user.
            usergroup_error_required = You have to choose a group!
            // This error appears if the entered value does not match the defined validation.
            usergroup_error_valid = An invalid group has been chosen!
        }
    }
