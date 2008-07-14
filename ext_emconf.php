<?php

########################################################################
# Extension Manager/Repository config file for ext: "dam_filelinks"
#
# Auto generated 14-07-2008 14:07
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
	'version' => '0.3.14',
	'dependencies' => '',
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
	'_md5_values_when_last_written' => 'a:12:{s:9:"ChangeLog";s:4:"ec79";s:10:"README.txt";s:4:"ee2d";s:25:"class.tx_damfilelinks.php";s:4:"3d16";s:21:"ext_conf_template.txt";s:4:"00aa";s:12:"ext_icon.gif";s:4:"f537";s:17:"ext_localconf.php";s:4:"c5ea";s:14:"ext_tables.php";s:4:"b169";s:14:"ext_tables.sql";s:4:"70a7";s:16:"locallang_db.php";s:4:"4afb";s:14:"doc/manual.sxw";s:4:"038f";s:19:"doc/wizard_form.dat";s:4:"4ca0";s:20:"doc/wizard_form.html";s:4:"ae06";}',
);

?>