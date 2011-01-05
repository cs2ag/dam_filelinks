<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Juraj Sulek (juraj@sulek.sk)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin dam_filelinks.
 *
 * $Id: class.tx_damfilelinks.php,v 0.2.0 2005/28/12 20:02:15 typo3 Exp $
 *
 * @author	Juraj Sulek <juraj@sulek.sk>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   61: class tx_damfilelinks extends tslib_pibase
 *   70:     function fetchFileList ($content, $conf)
 *   87:     function fillFileMarkers($fileFileMarkers,$fileLayout,$file,$fileCount,$fileext)
 *  111:     function getDamSql($field,$defaultField='')
 *  139:     function getDamResult($field,$row,$defaultField='',$defaultValue='')
 *  167:     function getDamFromDatabase($contentUid,$addField,$ident='tx_damfilelinks_filelinks')
 *  258:     function getFilesForCssUploads($conf)
 *  473:     function getFileUrl($url,$conf,$record)
 *  503:     function checkDownload()
 *  539:     function showDownloadError()
 *  555:     function getDownload($record,$url)
 *  574:     function df_array_union($array1,$array2)
 *  589:     function &hookRequest($functionName)
 *  609:     function hookRequestMore($functionName)
 *
 * TOTAL FUNCTIONS: 13
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

	require_once(PATH_tslib."class.tslib_pibase.php");

	class tx_damfilelinks extends tslib_pibase {

	/**
	 * Return files from dam reference field (this is used for generating filelist field which TCA was overriden by CSS MULTIMEDIA)
	 *
	 * @param	mixed		$content: ...
	 * @param	array		$conf: ...
	 * @return	string		comma list of files with path
	 */
		function fetchFileList ($content, $conf) {
			$uid = ($this->pObj->cObj->data['_LOCALIZED_UID'] > 0) ? $this->pObj->cObj->data['_LOCALIZED_UID'] : $this->pObj->cObj->data['uid'];
            $refField = trim($this->pObj->cObj->stdWrap($conf['refField'],$conf['refField.']));
            $damFiles = tx_dam_db::getReferencedFiles('tt_content', $uid, $refField);
            return implode(',',$damFiles['files']);
		}

	/**
	 * return layout with filled file markers
	 *
	 * @param	array		$fileFileMarkers: TypoScript configuration for 'layout.userMarker.'
	 * @param	string		$fileLayout: Layout with markers
	 * @param	array		$file: array obtained from dam
	 * @param	array		$fileCount: filecounter
	 * @param	array		$fileext: file extension
	 * @return	strin		layout with filled file markers
	 */
		function fillFileMarkers($fileFileMarkers,$fileLayout,$file,$fileCount,$fileext){
			if ($hookObj = &$this->hookRequest('fillFileMarkers'))	{
				return $hookObj->fillFileMarkers($fileFileMarkers,$fileLayout,$file,$fileCount,$fileext);
			} else {
				$_fileFileMarkers=$fileFileMarkers['dam.'];
				if(count($_fileFileMarkers)>0){
					$userMarkersArray=$file['userMarker'];
					if(count($userMarkersArray)>0){
						while(list($key,$val)=each($userMarkersArray)){
							$fileLayout=str_replace('###'.$key.'###',$val,$fileLayout);
						}
					};
				};
				return $fileLayout;
			};
		}

	/**
	 * Return a string for sql select:
	 *
	 * @param	string		$field: string with database fields separated by ',' (e.g. autor,crdate,height)
	 * @param	string		$defaultField: this will be used if the $field is empty
	 * @return	array		string for select (e.g. 'tx_dam.field1, tx_dam.field2')
	 */
		function getDamSql($field,$defaultField=''){
			if ($hookObj = &$this->hookRequest('getDamSql'))	{
				return $hookObj->getDamSql($field,$defaultField);
			} else {
				$field_sql='';
				$field=trim($field);
				$field=trim($field,',');
				$field=trim($field);
				if($field!=''){
					$field_array=t3lib_div::trimExplode(',',$field);
					foreach($field_array as $fieldFor){
						$field_sql.='tx_dam.'.$fieldFor.' ,';
					};
				};
				if(($defaultField!='')&&($field_sql=='')){$field_sql='tx_dam.'.$defaultField.' ,';};
				return $field_sql;
			};
		}

	/**
	 * Return a value from query result and field definition:
	 *
	 * @param	string		$field: string with database fields separated by ',' (e.g. autor,crdate,height)
	 * @param	array		$row: myslq result
	 * @param	string		$defaultField: this will be used if the $field is empty
	 * @param	string		$defaultValue: this will be used if the $field and the $defaultField is empty
	 * @return	array		value from mysql result
	 */
		function getDamResult($field,$row,$defaultField='',$defaultValue=''){
			if ($hookObj = &$this->hookRequest('getDamResult'))	{
				return $hookObj->getDamResult($field,$row,$defaultField,$defaultValue);
			} else {
				$field_return='';
				$string=trim($field);
				$string=trim($field,',');
				$field=trim($field);
				if($field!=''){
					$field_array=t3lib_div::trimExplode(',',$field);
					foreach($field_array as $fieldFor){
						if(trim($row[$fieldFor])!=''){$field_return=trim($row[$fieldFor]);};
					};
				};
				if(($defaultField!='')&&($field_return=='')){$field_return=trim($row[$defaultField]);};
				if(($defaultValue!='')&&($field_return=='')){$field_return=trim($defaultValue);};
				return $field_return;
			};
		}

	/**
	 * Return dams from database:
	 *
	 * @param	string		$contentUid: string width content uids from which the dam should be obtained
	 * @param	array		$addField: additional field that should be obtained
	 * @param	array		$addField: additional field that should be obtained
	 * @return	object		sql result
	 */
		function getDamFromDatabase($contentUid,$addField,$ident='tx_damfilelinks_filelinks'){
			if ($hookObj = &$this->hookRequest('getDamFromDatabase'))	{
				return $hookObj->getDamFromDatabase($contentUid,$addField,$ident);
			} else {
				$select = 'tx_dam.sys_language_uid, tx_dam.uid, '.$addField.' tx_dam.file_type';
				if($this->pObj->cObj->data['select_key']!='' && ($GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['readFromPathDam']==1 || $GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['ctype_media_add_orig_field']==0)){
					$orderBy = 'tx_dam.sorting';
					$limit=1000;
					$configArray=t3lib_div::trimExplode('|',$this->pObj->cObj->data['select_key']);
					$c_directory='"'.$GLOBALS['TYPO3_DB']->quoteStr($configArray[0],'tx_dam').'"';
					if(strpos('*',$c_directory)!=-1){
						$c_directory='tx_dam.file_path LIKE '.str_replace('*','%',$c_directory);
					}else{
						$c_directory='tx_dam.file_path='.$c_directory;
					};
					$global_extension_array = $GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['allowedExtReadFromPath'];
					$c_global_extension_array = t3lib_div::trimExplode(',',$global_extension_array);
					$c_extension='';
					if($configArray[1]!=''){
						$c_extension_array=t3lib_div::trimExplode(',',$configArray[1]);
						if(count($c_extension_array)>0){
							foreach($c_extension_array as $c_arr){
								if (in_array($c_arr, $c_global_extension_array)) {
									$c_extension.=',"'.$GLOBALS['TYPO3_DB']->quoteStr($c_arr,'tx_dam').'"';
								};
							};
							$c_extension=trim($c_extension,',');
							if($c_extension!=''){
								$c_extension=' AND tx_dam.file_type IN('.$c_extension.') ';
							};
						};
					} else {
						if($global_extension_array !=''){
							foreach($c_global_extension_array as $c_arr){
								$c_extension.=',"'.$GLOBALS['TYPO3_DB']->quoteStr($c_arr,'tx_dam').'"';
							};
							$c_extension=trim($c_extension,',');
							if($c_extension!=''){
								$c_extension=' AND tx_dam.file_type IN('.$c_extension.') ';
							};
						};

					};
					$c_sorting_arr['name']='tx_dam.file_name';
					$c_sorting_arr['size']='tx_dam.file_size';
					$c_sorting_arr['ext']='tx_dam.file_type';
					$c_sorting_arr['date']='tx_dam.file_mtime';
					if($configArray[2]=='name' || $configArray[2]=='size' || $configArray[2]=='ext' || $configArray[2]=='date'){
						$orderBy=$c_sorting_arr[$configArray[2]];
					};
					if($configArray[3]=='r'){
						$orderBy.=' DESC';
					};
					$whereClause = $c_directory.' AND tx_dam.sys_language_uid < 1 '.$GLOBALS['TSFE']->sys_page->enableFields('tx_dam').$c_extension;
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						$select,
						'tx_dam',
						$whereClause,
						'',
						$orderBy,
						$limit
					);
				}else{
				 	$whereClause = ' AND tx_dam_mm_ref.ident="'.$GLOBALS['TYPO3_DB']->quoteStr($ident,'tx_dam_mm_ref').'" AND tx_dam_mm_ref.tablenames="'.$GLOBALS['TYPO3_DB']->quoteStr('tt_content','tx_dam_mm_ref').'" AND tx_dam.sys_language_uid < 1';
					$orderBy = 'tx_dam_mm_ref.sorting';
					if($GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['dam_1_0_9']==1){
						$orderBy = 'tx_dam_mm_ref.sorting_foreign';
					}
					/*$groupBy='';*/
					$limit=10000;
					$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						$select,
						'tx_dam',
						'tx_dam_mm_ref',
						'tt_content',
						'AND tt_content.uid IN ('.$contentUid.')  '.$GLOBALS['TSFE']->sys_page->enableFields('tx_dam').' '.$whereClause,
						$groupBy,
						$orderBy,
						$limit
					);
				};
				return $res;
			};
		}

	/**
	 * Returns a serialized array from files inserted in media field and dam field used by css_filelinks
	 *
	 * @param	array		TypoScript configuration
	 * @return	string		serialized array used by css_filelinks.
	 */
		function getFilesForCssUploads($conf){
			if ($hookObj = &$this->hookRequest('getFilesForCssUploads'))	{
				return $hookObj->getFilesForCssUploads($conf);
			} else {
				global $TSFE;
				$this->conf=$conf;
				$this->checkDownload();
				$files_all=array();
				/* old files begin */
				$description_ts['cObject.']=$conf['dam.']['additionalDescription.'];
				$descriptionField = $this->pObj->cObj->stdWrap($description_ts['cObject'],$description_ts['cObject.']);
				$description_ifElementEmpty=$this->pObj->cObj->stdWrap($conf['dam.']['additionalDescription_ifElementEmpty'],$conf['dam.']['additionalDescription_ifElementEmpty.']);
				if($descriptionField!=''){
					$descriptionArray=t3lib_div::trimExplode(chr(10),$descriptionField);
				}else{
					$descriptionArray=array();
				};
				$i=0;

				if($conf['dam.']['additional']=='yes'){
					/* get the standard field begin */
					$path=$this->pObj->cObj->stdWrap($conf['dam.']['additionalPath'],$conf['dam.']['additionalPath.']);
					$file_ts['cObject.']['field']=$conf['dam.']['additionalField'];
					if(is_array($conf['fileList.']['override.'])){
						$file_ts['cObject.']['override.']=$conf['fileList.']['override.'];
					};
					if($GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['readFromPathDam']==1){
						unset($file_ts['cObject.']['override.']);
					};
					$files_get = trim($this->pObj->cObj->stdWrap($file_ts['cObject'],$file_ts['cObject.']),',');
					$separatePathFromFile=false;
					if(strpos($files_get,"\\")!==false || strpos($files_get,"/")!==false){
						$separatePathFromFile=true;
					};
					/* get the standard field end */
					/* If the css_multimedia is installet it turns the media field to dam field.
					 * Therefore i must check if this is the case and if yes then i must return the DAM reference */
						if($GLOBALS['T3_VAR']['ext']['css_filelinks']['setup']['default_dam']==1 && t3lib_extMgm::isLoaded('dam') && t3lib_extMgm::isLoaded('css_styled_multimedia')){
							$files_get=$this->fetchFileList('',array('refField'=>'media'));
							if(is_array($conf['fileList.']['override.'])){
								$files_get2=$this->pObj->cObj->stdWrap($files_get,array('override.'=>$conf['fileList.']['override.']));
							};
							$separatePathFromFile=true;
							//if the overide has get some ressults
							if($files_get!=$files_get2){
								$separatePathFromFile=false;
								$files_get=$files_get2;
							};
						};
					/* dam reference end */
					$files_arr=t3lib_div::trimExplode(',',$files_get);
					if(count($files_arr)!=0){
						foreach($files_arr as $filetemp){
							if($separatePathFromFile){
								$file=str_replace("\\","/",$filetemp);
								$lastPos=strrpos($filetemp,"/");
								if($lastPos!==false){
									$path=substr($file,0,$lastPos+1);
									$file=substr($file,$lastPos+1);
								};
							}else{
								$file=$filetemp;
							};
							if(@is_file(trim($path).trim($file))){
								if ($conf['linkProc.']['removePrependedNumbers']){$title=preg_replace('_[0-9][0-9](\.[[:alnum:]]*)$','\1',$file);}else{$title=$file;}
								$description=$descriptionArray[$i]!=''?$descriptionArray[$i]:$description_ifElementEmpty;
								$files_all[]=array('dam'=>'0','url'=>trim($path).trim($file),'title'=>trim($title),'size'=>filesize(trim($path).trim($file)),'filename'=>trim($file),'description'=>$description);
								$i++;
							};
						};
					};
				};
				/* old files end */
				$defaultTitle='title';
				$defaultSize='file_size';
				$defaultFileName='file_name';
				$defaulFilePath='file_path';
				/**/
				$titleFieldsSQL=$this->getDamSql($conf['dam.']['damTitle'],$defaultTitle);
				$sizeFieldsSQL=$this->getDamSql($conf['dam.']['damSize'],$defaultSize);
				$fileNameFieldsSQL=$this->getDamSql($conf['dam.']['damFileName'],$defaultFileName);
				$filePathFieldsSQL=$this->getDamSql($conf['dam.']['damFilePath'],$defaulFilePath);
				$descriptionFieldSQL=$this->getDamSql($conf['dam.']['damDescription']);
				/**/
				/* userMarker_sql begin */
				$userMarker=$conf['layout.']['userMarker.']['dam.'];
				$userMarker_sql='';
				if(count($userMarker)>0){
					foreach($userMarker as $um_key=>$um){
						if(substr($um_key,-1)!='.'){
							$tempUserMarker_sql=$this->getDamSql($um);
							if($tempUserMarker_sql!=''){$userMarker_sql.=$tempUserMarker_sql;};
						};
					};
				};
				/* userMarker_sql end */
				/* small hack for templavoila */
				$dam_selectfields=$sizeFieldsSQL.$titleFieldsSQL.$fileNameFieldsSQL.$filePathFieldsSQL.$userMarker_sql.$descriptionFieldSQL;
                $_id = isset($this->pObj->cObj->data['uid']) ? $this->pObj->cObj->data['uid'] : $this->pObj->cObj->parentRecord['data']['uid'];
				if (isset($this->pObj->cObj->data['_ORIG_uid']) && ($this->pObj->cObj->data['_ORIG_uid'] > 0)) {
					$_id = $this->pObj->cObj->data['_ORIG_uid'];
				}
				$localized_id = isset($this->pObj->cObj->data['_LOCALIZED_UID']) ? $this->pObj->cObj->data['_LOCALIZED_UID'] : $this->pObj->cObj->parentRecord['data']['_LOCALIZED_UID'];
				if ($localized_id) {$_id = $localized_id;}
				$res=$this->getDamFromDatabase($_id,$dam_selectfields,$conf['dam.']['damIdentField']);
                $files_all_dam=array();

				/* select ende */
				if(intval($conf['dam.']['damFieldFirst'])==1){
					$i=0;
				};
				if($GLOBALS['TYPO3_DB']->sql_num_rows($res)){
					while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						/**/
						/* language handling begin */
						if($GLOBALS['TSFE']->sys_language_uid==0 || $conf['dam.']['sys_language_mode']=='disabled' || $row['sys_language_uid']=='-1'){
							$row=&$row2;
						}else{
							$res_lang = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
								trim($dam_selectfields,','),
								'tx_dam',
								'(tx_dam.sys_language_uid='.$GLOBALS['TSFE']->sys_language_uid.' OR tx_dam.sys_language_uid=-1) AND tx_dam.l18n_parent='.$row2['uid'].' '.$GLOBALS['TSFE']->sys_page->enableFields('tx_dam')
							);
							if($GLOBALS['TYPO3_DB']->sql_num_rows($res_lang)>0){
								$row3=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res_lang);
								$row=$this->df_array_union($row3,$row2);
							}else{
								if($conf['dam.']['sys_language_mode']!='strict'){
									$row=&$row2;
								}else{
									continue;
								};
							};
						};
						/* language handling end */


						$titleAdd=$this->getDamResult($conf['dam.']['damTitle'],$row,$defaultTitle);
						$fileNameAdd=$this->getDamResult($conf['dam.']['damFileName'],$row,$defaultFileName);
						$filePathAdd=$this->getDamResult($conf['dam.']['damFilePath'],$row,$defaulFilePath);
						$filesizeTemp=0;
						if(file_exists($filePathAdd.$fileNameAdd)){$filesizeTemp=filesize($filePathAdd.$fileNameAdd);};
						$sizeAdd=$this->getDamResult($conf['dam.']['damSize'],$row,$defaultSize,$filesizeTemp);
						/* description begin */
						$descriptionAdd=trim($descriptionArray[$i]);
						$descriptionDamAdd=$this->pObj->cObj->stdWrap($this->getDamResult($conf['dam.']['damDescription'],$row,''),$conf['dam.']['damDescription.']);
						if($descriptionAdd==''){$descriptionAdd=$descriptionDamAdd;};
						if(intval($conf['dam.']['damDescription.']['overrideOriginalDescription'])==1){
							if(trim($descriptionDamAdd)!=''){
								$descriptionAdd=$descriptionDamAdd;
							};
						};
						if(trim($descriptionAdd)==''){$descriptionAdd=$description_ifElementEmpty;};
						/* description end */
						/* userMarker begin */
						if(count($userMarker)>0){
							reset($userMarker);
							$fileUserMarker=array();
							while(list($key,$val)=each($userMarker)){
								if(substr($key,-1)!='.'){
									$fileUserMarker[$key]=$this->getDamResult($val,$row);
									if($userMarker[$key.'.']['stdWrap']=='1'){
										$fileUserMarker[$key]=$this->pObj->cObj->stdWrap($fileUserMarker[$key],$userMarker[$key.'.']['stdWrap.']);
									}
								};

							};
						};
						/* userMarker end */
						$hookMarkers=array();
						/* hook for additional markers by other extensions begin */
						$hookObjs=$this->hookRequestMore('tx_damfilelinks_addHookMarkers');
						if((is_array($hookObjs))&&(count($hookObjs)>0)){
							foreach($hookObjs as $hObjs){
								$hookMarkers=$hObjs->tx_damfilelinks_addHookMarkers($row,$hookMarkers);
							};
						};
						/* hook for additional markers by other extensions end */
						if(@is_file(trim($filePathAdd).trim($fileNameAdd))){
							$files_all_dam[]=array('dam'=>$row['uid'],'url'=>trim($filePathAdd).trim($fileNameAdd),'title'=>trim($titleAdd),'size'=>trim($sizeAdd),'filename'=>trim($fileNameAdd),'userMarker'=>$fileUserMarker,'description'=>$descriptionAdd,'hookMarkers'=>$hookMarkers);
							$i++;
						};
					};
				};
				if($conf['dam.']['additional']=='yes'){
					if(intval($conf['dam.']['damFieldFirst'])==1){
						$file_all_return=$files_all_dam;
						reset($files_all);
						foreach($files_all as $file_add){
							if(trim($descriptionArray[$i])!=''){
								$file_add['description']=$descriptionArray[$i];
							}else{
								$file_add['description']=$description_ifElementEmpty;
							}
							$file_all_return[]=$file_add;
							$i++;
						};
					}else{
						$file_all_return=$files_all;
						reset($files_all);
						foreach($files_all_dam as $file_add){$file_all_return[]=$file_add;};
					}
				}else{
					$file_all_return=$files_all_dam;
				};

				return $file_all_return;
			};
		}

	/**
	 * return url from file
	 *
	 * @param	string		$url: file url
	 * @param	array		$conf: typoscript configuration
	 * @param	array		$record: record with all informations about the file
	 * @return	string		url
	 */
	function getFileUrl($url,$conf,$record){
		if ($hookObj = &$this->hookRequest('getFileUrl'))	{
				return $hookObj->getFileUrl($url,$conf,$record);
		} else {
			$output = '';
			$initP = '?id='.$GLOBALS['TSFE']->id.'&type='.$GLOBALS['TSFE']->type;
			if (@is_file($url))	{
				if($conf['jumpurl.']['damSecure']){
					return $this->pObj->cObj->typolink('',array(
						'returnLast'=>'url',
						'parameter'=>$GLOBALS['TSFE']->page['uid'],
						'additionalParams'=>'&cid='.$this->pObj->cObj->data['uid'].'&did='.$record['dam'].'&sechash='.substr(md5($this->pObj->cObj->data['uid'].$record['dam'].$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']),0,8),
						'no_cache'=>1
					));
				}else{
					$urlEnc = str_replace('%2F', '/', rawurlencode($url));
					$locDataAdd = $conf['jumpurl.']['secure'] ? $this->pObj->cObj->locDataJU($urlEnc,$conf['jumpurl.']['secure.']) : '';
					$retUrl = ($conf['jumpurl']) ? $GLOBALS['TSFE']->config['mainScript'].$initP.'&jumpurl='.rawurlencode($urlEnc).$locDataAdd.$GLOBALS['TSFE']->getMethodUrlIdToken : $urlEnc;		// && $GLOBALS['TSFE']->config['config']['jumpurl_enable']
					return htmlspecialchars($GLOBALS['TSFE']->absRefPrefix.$retUrl);
				}
			};
			return '';
		};
	}

	/**
	 * if the damSecure is set this function return the file
	 *
	 * @return	void
	 */
	function checkDownload(){
		if ($hookObj = &$this->hookRequest('checkDownload'))	{
				return $hookObj->checkDownload();
		} else {
			$cid=intval(t3lib_div::_GP('cid'));
			$did=intval(t3lib_div::_GP('did'));
			$securehash=strip_tags(t3lib_div::_GP('sechash'));
			if($cid==0 && $did==0 && $securehash=='') return ''; //if there are no these parameters i had nothing to do
			if(substr(md5($cid.$did.$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']),0,8)!= $securehash) return $this->showDownloadError();
			if($cid==0 || $did==0) return $this->showDownloadError();
			// check if the content element exists
			$res_content = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'tt_content.*',
				'tt_content',
				'tt_content.uid='.$cid.' '.$GLOBALS['TSFE']->sys_page->enableFields('tt_content')
			);
			if($GLOBALS['TYPO3_DB']->sql_num_rows($res_content)==0) return $this->showDownloadError();
			// check if the dam element exists
			$res_dam = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'tx_dam.*',
				'tx_dam',
				'tx_dam.uid='.$did.' '.$GLOBALS['TSFE']->sys_page->enableFields('tx_dam')
			);
			if($GLOBALS['TYPO3_DB']->sql_num_rows($res_dam)==0) return $this->showDownloadError();
			$row_dam = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res_dam);
			$url=$row_dam['file_path'].$row_dam['file_name'];

			$this->getDownload($row_dam,$url);
		};
	}

	/**
	 * if the file doesn't exist or the user has no right to access it this function return the no access screen
	 *
	 * @return	void
	 */
	function showDownloadError(){
		if($this->conf['linkProc.']['jumpurl.']['damSecure.']['errorPage']==''){
			echo $GLOBALS['TSFE']->sL('LLL:EXT:dam_filelinks/locallang_fe.xml:noaccess');
		}else{
			if($this->conf['linkProc.']['jumpurl.']['damSecure.']['errorPage']==''){
				$GLOBALS['TSFE']->pageErrorHandler('http://'.$_SERVER['HTTP_HOST'],'',$GLOBALS['TSFE']->sL('LLL:EXT:dam_filelinks/locallang_fe.xml:noaccess_404'));
			}else{
				$GLOBALS['TSFE']->pageErrorHandler($this->conf['linkProc.']['jumpurl.']['damSecure.']['errorPage'],'',$GLOBALS['TSFE']->sL('LLL:EXT:dam_filelinks/locallang_fe.xml:noaccess_404'));
			}
		}
		exit();
	}

	/**
	 * download the file
	 *
	 * @param	array		$record: record with all informations about the file
	 * @param	[type]		$url: ...
	 * @return	void
	 */
	function getDownload($record,$url){
		$url=PATH_site.$url;
		if(!file_exists($url)) return $this->showDownloadError();
		$fp=fopen($url,'rb');
		$file_content=fread($fp,filesize($url));
		fclose($fp);
		header("Pragma: private");
		header("Cache-control: private, must-revalidate");
		header("Content-type: application/octet-stream");
		header('Content-disposition: attachment; filename="'.$record['file_dl_name'].'"');
		echo $file_content;
		exit();
	}

		/**
		 * Unite 2 arrays to one
		 *
		 * @param	array		array with language values
		 * @param	array		array with original values
		 * @return	array		united arrays
		 */
		function df_array_union($array1,$array2)	{
			foreach($array1 as $key=>$val){
				if($val!='' && $val!='0'){
					$array2[$key]=$val;
				};
			};
			return $array2;
		}

		/**
		 * Returns an object reference to the hook object if any
		 *
		 * @param	string		Name of the function you want to call / hook key
		 * @return	object		Hook object, if any. Otherwise null.
		 */
		function &hookRequest($functionName)	{
			global $TYPO3_CONF_VARS;

				// Hook: menuConfig_preProcessModMenu
			if ($TYPO3_CONF_VARS['EXTCONF']['dam_filelinks']['pi1_hooks'][$functionName]) {
				$hookObj = &t3lib_div::getUserObj($TYPO3_CONF_VARS['EXTCONF']['dam_filelinks']['pi1_hooks'][$functionName]);
				if (method_exists ($hookObj, $functionName)) {
					$hookObj->pObj = &$this;
					return $hookObj;
				}
			}
		}


		/**
		 * Returns an array of object reference to the hook object if any
		 *
		 * @param	string		Name of the function you want to call / hook key
		 * @return	array		Array of Hook objects or empty array.
		 */
		function hookRequestMore($functionName)	{
			global $TYPO3_CONF_VARS;

			$hookObjectsArr=array();
			$i=0;
			if (is_array($TYPO3_CONF_VARS['EXTCONF']['dam_filelinks']['pi1_hooks_more'][$functionName])){
				foreach ($TYPO3_CONF_VARS['EXTCONF']['dam_filelinks']['pi1_hooks_more'][$functionName] as $classRef){
	        		$hookObjectsArr[$i] = &t3lib_div::getUserObj($classRef);
	        		$hookObjectsArr[$i]->pObj = &$this;
	        		$i++;
	    		}
	    		return $hookObjectsArr;
			}
		}
	}

	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_filelinks/class.tx_damfilelinks.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_filelinks/class.tx_damfilelinks.php']);
}
?>