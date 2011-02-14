<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$tempSetup = $GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup'];
$defaultMaxSize=10485760;
$tempColumns = array (
	'tx_damfilelinks_filelinks' => txdam_getMediaTCA('media_field', 'tx_damfilelinks_filelinks')
);

t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

if ($tempSetup['ctype_media_add_orig_field']) {
	t3lib_extMgm::addToAllTCAtypes('tt_content','tx_damfilelinks_filelinks','uploads','after:select_key');
} else {
	$TCA['tt_content']['types']['uploads']['showitem'] = str_replace(', media;', ', tx_damfilelinks_filelinks;', $TCA['tt_content']['types']['uploads']['showitem']);
	$TCA['tt_content']['palettes']['uploads']['showitem'] = str_replace(', media;', ', tx_damfilelinks_filelinks;', $TCA['tt_content']['palettes']['uploads']['showitem']);
}

$TCA['tt_content']['columns']['tx_damfilelinks_filelinks']['config']['allowed_types'] = $tempSetup['allowedExt'];
$TCA['tt_content']['columns']['tx_damfilelinks_filelinks']['config']['disallowed_types'] = $TCA['tt_content']['columns']['media']['config']['disallowed_types'];
$TCA['tt_content']['columns']['tx_damfilelinks_filelinks']['config']['size']= $TCA['tt_content']['columns']['media']['config']['size'];
$TCA['tt_content']['columns']['tx_damfilelinks_filelinks']['config']['maxitems']=$tempSetup['maxElements'];
$TCA['tt_content']['columns']['tx_damfilelinks_filelinks']['config']['minitems']=$tempSetup['minElements'];
if($TCA['tt_content']['columns']['media']['config']['max_size']>$defaultMaxSize){
	$TCA['tt_content']['columns']['tx_damfilelinks_filelinks']['config']['max_size']=$TCA['tt_content']['columns']['media']['config']['max_size'];
}else{
	$TCA['tt_content']['columns']['tx_damfilelinks_filelinks']['config']['max_size']=$defaultMaxSize;
};
if($TCA['tt_content']['columns']['tx_damfilelinks_filelinks']['config']['autoSizeMax']<$tempSetup['maxElements']){
	$TCA['tt_content']['columns']['tx_damfilelinks_filelinks']['config']['autoSizeMax']=$tempSetup['maxElements'];
};

?>