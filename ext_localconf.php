<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TYPO3_CONF_VARS['EXTCONF']['css_filelinks']['pi1_hooks']['getFilesForCssUploads']="EXT:dam_filelinks/class.tx_damfilelinks.php:&tx_damfilelinks";
$TYPO3_CONF_VARS['EXTCONF']['css_filelinks']['pi1_hooks_more']['fillFileMarkers'][]="EXT:dam_filelinks/class.tx_damfilelinks.php:&tx_damfilelinks";
$TYPO3_CONF_VARS['EXTCONF']['css_filelinks']['pi1_hooks']['getFileUrl']="EXT:dam_filelinks/class.tx_damfilelinks.php:&tx_damfilelinks";

$GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup'] = unserialize($_EXTCONF);

$tempAdditional='additional = none';
$tempEditIcons='stdWrap.editIcons = tt_content: tx_damfilelinks_filelinks, layout, filelink_size';
if($GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['ctype_media_add_orig_field']){
	$tempEditIcons='stdWrap.editIcons = tt_content: media, tx_damfilelinks_filelinks, layout, filelink_size';
};

if ($GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['ctype_media_add_orig_field']) {
	$tempAdditional='additional = yes';	
};

t3lib_extMgm::addTypoScript($_EXTKEY,'setup','
		includeLibs.tx_damfilelinks = EXT:dam_filelinks/class.tx_damfilelinks.php
		
		tt_content.uploads.20{
			dam{
				damIdentField=tx_damfilelinks_filelinks
				damFieldFirst=1
				damTitle=file_name,title
				damSize=file_size
				damFileName=file_name
				damFilePath=file_path
				damDescription=description
				damDescription.overrideOriginalDescription=0
				'.$tempAdditional.'
				additionalField < tt_content.uploads.20.fileList.field
				additionalPath < tt_content.uploads.20.fileList.path
				additionalDescription.field = imagecaption
				additionalDescription.split.token.char = 10
				additionalDescription.split.cObjNum = 1
				additionalDescription.split.1.current = 1
				additionalDescription.split.1.noTrimWrap (
					||
					|
				)
				additionalDescription_ifElementEmpty < tt_content.uploads.20.description_ifElementEmpty
				sys_language_mode=normal
			}
			linkProc.jumpurl{
				damSecure = 1
				damSecure.errorPage=http://www.mypage.com/error.html
			}
			'.$tempEditIcons.'
		}',43);



?>