<?php

########################################################################
# Extension Manager/Repository config file for ext: "dam_filelinks"
#
# Auto generated 05-11-2009 11:44
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Filelinks DAM usage',
	'description' => 'Modifies the content type "Filelinks" for usage of the DAM. Need the CSS styles Filelinks extension.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.3.18',
	'dependencies' => 'cms,dam,css_filelinks',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'tt_content',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Juraj Sulek',
	'author_email' => 'juraj@sulek.sk',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'dam' => '',
			'css_filelinks' => '',
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.5.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:13:{s:9:"ChangeLog";s:4:"8ee7";s:10:"README.txt";s:4:"9fa9";s:25:"class.tx_damfilelinks.php";s:4:"785a";s:21:"ext_conf_template.txt";s:4:"5a7c";s:12:"ext_icon.gif";s:4:"f537";s:17:"ext_localconf.php";s:4:"d43c";s:14:"ext_tables.php";s:4:"95f7";s:14:"ext_tables.sql";s:4:"70a7";s:16:"locallang_db.php";s:4:"90ee";s:16:"locallang_fe.xml";s:4:"8312";s:14:"doc/manual.sxw";s:4:"98be";s:19:"doc/wizard_form.dat";s:4:"4ca0";s:20:"doc/wizard_form.html";s:4:"28f2";}',
	'suggests' => array(
	),
);

?>