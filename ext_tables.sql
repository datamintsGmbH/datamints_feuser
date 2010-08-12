#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_datamintsfeuser_firstname varchar(255) DEFAULT '' NOT NULL,
	tx_datamintsfeuser_surname varchar(255) DEFAULT '' NOT NULL,
	tx_datamintsfeuser_approval_level int(11) DEFAULT '0' NOT NULL
);