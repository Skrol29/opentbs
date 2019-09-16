<?php

/**
 * @file
 * OpenTBS
 *
 * This TBS plug-in can open a zip file, read the central directory,
 * and retrieve the content of a zipped file which is not compressed.
 *
 * @version 1.10.0
 * @date 2019-09-16
 * @see     http://www.tinybutstrong.com/plugins.php
 * @author  Skrol29 http://www.tinybutstrong.com/onlyyou.html
 * @license LGPL-3.0
 */

/**
 * Constants to drive the plugin.
 */
define('OPENTBS_PLUGIN','clsOpenTBS');
define('OPENTBS_DOWNLOAD',1);   // download (default) = TBS_OUTPUT
define('OPENTBS_NOHEADER',4);   // option to use with DOWNLOAD: no header is sent
define('OPENTBS_FILE',8);       // output to file   = TBSZIP_FILE
define('OPENTBS_DEBUG_XML',16); // display the result of the current subfile
define('OPENTBS_STRING',32);    // output to string = TBSZIP_STRING
define('OPENTBS_DEBUG_AVOIDAUTOFIELDS',64); // avoit auto field merging during the Show() method
define('OPENTBS_INFO','clsOpenTBS.Info');       // command to display the archive info
define('OPENTBS_RESET','clsOpenTBS.Reset');      // command to reset the changes in the current archive
define('OPENTBS_ADDFILE','clsOpenTBS.AddFile');    // command to add a new file in the archive
define('OPENTBS_DELETEFILE','clsOpenTBS.DeleteFile'); // command to delete a file in the archive
define('OPENTBS_REPLACEFILE','clsOpenTBS.ReplaceFile'); // command to replace a file in the archive
define('OPENTBS_EDIT_ENTITY','clsOpenTBS.EditEntity'); // command to force an attribute
define('OPENTBS_FILEEXISTS','clsOpenTBS.FileExists');
define('OPENTBS_GET_FILES','clsOpenTBS.GetFiles');
define('OPENTBS_GET_OPENED_FILES','clsOpenTBS.GetOpenedFiles');
define('OPENTBS_WALK_OPENED_FILES','clsOpenTBS.WalkOpenedFiles');
define('OPENTBS_CHART','clsOpenTBS.Chart');
define('OPENTBS_CHART_INFO','clsOpenTBS.ChartInfo');
define('OPENTBS_CHART_DELETE_CATEGORY','clsOpenTBS.ChartDeleteCategory');
define('OPENTBS_DEFAULT','');   // Charset
define('OPENTBS_ALREADY_XML',false);
define('OPENTBS_ALREADY_UTF8','already_utf8');
define('OPENTBS_DEBUG_XML_SHOW','clsOpenTBS.DebugXmlShow');
define('OPENTBS_DEBUG_XML_CURRENT','clsOpenTBS.DebugXmlCurrent');
define('OPENTBS_DEBUG_INFO','clsOpenTBS.DebugInfo');
define('OPENTBS_DEBUG_CHART_LIST','clsOpenTBS.DebugInfo'); // deprecated
define('OPENTBS_FORCE_DOCTYPE','clsOpenTBS.ForceDocType');
define('OPENTBS_DELETE_ELEMENTS','clsOpenTBS.DeleteElements');
define('OPENTBS_SELECT_SHEET','clsOpenTBS.SelectSheet');
define('OPENTBS_SELECT_SLIDE','clsOpenTBS.SelectSlide');
define('OPENTBS_SELECT_MAIN','clsOpenTBS.SelectMain');
define('OPENTBS_DISPLAY_SHEETS','clsOpenTBS.DisplaySheets');
define('OPENTBS_DELETE_SHEETS','clsOpenTBS.DeleteSheets');
define('OPENTBS_DELETE_COMMENTS','clsOpenTBS.DeleteComments');
define('OPENTBS_MERGE_SPECIAL_ITEMS','clsOpenTBS.MergeSpecialItems');
define('OPENTBS_CHANGE_PICTURE','clsOpenTBS.ChangePicture');
define('OPENTBS_COUNT_SLIDES','clsOpenTBS.CountSlides');
define('OPENTBS_COUNT_SHEETS','clsOpenTBS.CountSheets');
define('OPENTBS_SEARCH_IN_SLIDES','clsOpenTBS.SearchInSlides');
define('OPENTBS_DISPLAY_SLIDES','clsOpenTBS.DisplaySlides');
define('OPENTBS_DELETE_SLIDES','clsOpenTBS.DeleteSlides');
define('OPENTBS_SELECT_FILE','clsOpenTBS.SelectFile');
define('OPENTBS_ADD_CREDIT','clsOpenTBS.AddCredit');
define('OPENTBS_SYSTEM_CREDIT','clsOpenTBS.SystemCredit');
define('OPENTBS_RELATIVE_CELLS','clsOpenTBS.RelativeCells');
define('OPENTBS_MAKE_OPTIMIZED_TEMPLATE','clsOpenTBS.MakeOptimizedTemplate');
define('OPENTBS_GET_CELLS','clsOpenTBS.GetCells');
define('OPENTBS_SET_CELLS','clsOpenTBS.SetCells');
define('OPENTBS_FIRST',1); // 
define('OPENTBS_GO',2);    // = TBS_GO
define('OPENTBS_ALL',4);   // = TBS_ALL
// Types of file to select
define('OPENTBS_GET_HEADERS_FOOTERS','clsOpenTBS.SelectHeaderFooter');
define('OPENTBS_SELECT_HEADER','clsOpenTBS.SelectHeader');
define('OPENTBS_SELECT_FOOTER','clsOpenTBS.SelectFooter');
// Sub-types of file
define('OPENTBS_EVEN',128);

/**
 * Main class which is a TinyButStrong plug-in.
 * It is also a extension of clsTbsZip so it can directly manage the archive underlying the template.
 */
class clsOpenTBS extends clsTbsZip {

	function OnInstall() {
		$TBS =& $this->TBS;

		if (!isset($TBS->OtbsAutoLoad))             $TBS->OtbsAutoLoad = true; // TBS will load the subfile regarding to the extension of the archive
		if (!isset($TBS->OtbsConvBr))               $TBS->OtbsConvBr = false;  // string for NewLine conversion
		if (!isset($TBS->OtbsAutoUncompress))       $TBS->OtbsAutoUncompress = $this->Meth8Ok;
		if (!isset($TBS->OtbsConvertApostrophes))   $TBS->OtbsConvertApostrophes = true;
		if (!isset($TBS->OtbsSpacePreserve))        $TBS->OtbsSpacePreserve = true;
		if (!isset($TBS->OtbsClearWriter))          $TBS->OtbsClearWriter = true;
		if (!isset($TBS->OtbsClearMsWord))          $TBS->OtbsClearMsWord = true;
		if (!isset($TBS->OtbsMsExcelConsistent))    $TBS->OtbsMsExcelConsistent = true;
		if (!isset($TBS->OtbsMsExcelExplicitRef))   $TBS->OtbsMsExcelExplicitRef = true;
		if (!isset($TBS->OtbsClearMsPowerpoint))    $TBS->OtbsClearMsPowerpoint = true;
		if (!isset($TBS->OtbsGarbageCollector))     $TBS->OtbsGarbageCollector = true;
		if (!isset($TBS->OtbsMsExcelCompatibility)) $TBS->OtbsMsExcelCompatibility = true;
		$this->Version = '1.10.0';
		$this->DebugLst = false; // deactivate the debug mode
		$this->ExtInfo = false;
		$TBS->TbsZip = &$this; // a shortcut
		return array('BeforeLoadTemplate','BeforeShow', 'OnCommand', 'OnOperation', 'OnCacheField');
	}

	function BeforeLoadTemplate(&$File,&$Charset) {

		$TBS =& $this->TBS;
		if ($TBS->_Mode!=0) return; // If we are in subtemplate mode, the we use the TBS default process

		if ($File === false) {
			// Close the current template if any
			@$this->Close();
			// Save memory space
			$this->TbsInitArchive();
			return false;
		}
		
		// Decompose the file path. The syntaxe is 'Archive.ext#subfile', or 'Archive.ext', or '#subfile'
		$FilePath = $File;
		$SubFileLst = false;
		if (is_string($File)) {
			$p = strpos($File, '#');
			if ($p!==false) {
				$FilePath = substr($File,0,$p);
				$SubFileLst = substr($File,$p+1);
			}
		}

		// Open the archive
		if ($FilePath!=='') {
			$ok = @$this->Open($FilePath);  // Open the archive
			if (!$ok) {
				if ($this->ArchHnd===false) {
					return $this->RaiseError("The template '".$this->ArchFile."' cannot be found.");
				} else {
					return false;
				}
			}
			$this->TbsInitArchive(); // Initialize other archive informations
			if ($TBS->OtbsAutoLoad && ($this->ExtInfo!==false) && ($SubFileLst===false)) {
				// auto load files from the archive
				$SubFileLst = $this->ExtInfo['load'];
				$TBS->OtbsConvBr = $this->ExtInfo['br'];
			}
			$TBS->OtbsSubFileLst = $SubFileLst;
		} elseif ($this->ArchFile==='') {
			$this->RaiseError('Cannot read file(s) "'.$SubFileLst.'" because no archive is opened.');
		}

		// Change the Charset if a new archive is opended, or if LoadTemplate is called explicitely for that
		if (($FilePath!=='') || ($File==='')) {
			if ($Charset===OPENTBS_ALREADY_XML) {
				$TBS->LoadTemplate('', false);                       // Define the function for string conversion
			} elseif ($Charset===OPENTBS_ALREADY_UTF8) {
				$TBS->LoadTemplate('', array(&$this,'ConvXmlOnly')); // Define the function for string conversion
			} else {
				$TBS->LoadTemplate('', array(&$this,'ConvXmlUtf8')); // Define the function for string conversion
			}
		}

		// Load the subfile(s)
		if (($SubFileLst!=='') && ($SubFileLst!==false)) {
			if (is_string($SubFileLst)) $SubFileLst = explode(';',$SubFileLst);
			$this->TbsLoadSubFileAsTemplate($SubFileLst);
		}

		if ($FilePath!=='') $TBS->_LastFile = $this->ArchFile;

		return false; // default LoadTemplate() process is not executed

	}

	function BeforeShow(&$Render, $File='') {

		$TBS =& $this->TBS;

		if ($this->ArchFile==='') {
			return $this->RaiseError('Command Show() cannot be processed because no archive is opened.');
		}

		if ($TBS->_Mode!=0) return; // If we are in subtemplate mode, the we use the TBS default process

		if ($this->TbsSystemCredits) {
			$this->Misc_EditCredits("OpenTBS " . $this->Version, true, true);
		}
		
		$this->TbsStorePark(); // Save the current loaded subfile if any

		$TBS->Plugin(-4); // deactivate other plugins

		$Debug = (($Render & OPENTBS_DEBUG_XML)==OPENTBS_DEBUG_XML);
		if ($Debug) $this->DebugLst = array();

		$TbsShow = (($Render & OPENTBS_DEBUG_AVOIDAUTOFIELDS)!=OPENTBS_DEBUG_AVOIDAUTOFIELDS);

		switch ($this->ExtEquiv) {
			case 'ods':  $this->OpenDoc_SheetSlides_DeleteAndDisplay(true); break;
			case 'odp':  $this->OpenDoc_SheetSlides_DeleteAndDisplay(false); break;
			case 'xlsx': $this->MsExcel_SheetDeleteAndDisplay(); break;
			case 'pptx': $this->MsPowerpoint_SlideDelete(); break;
		}
		
		$explicitRef = ($TBS->OtbsMsExcelExplicitRef && ($this->ExtEquiv==='xlsx'));
		
		// Merges all modified subfiles
		$idx_lst = array_keys($this->TbsStoreLst);
		foreach ($idx_lst as $idx) {
			$TBS->Source = $this->TbsStoreLst[$idx]['src'];
			$onshow = $this->TbsStoreLst[$idx]['onshow'];
			unset($this->TbsStoreLst[$idx]); // save memory space
			$TBS->OtbsCurrFile = $this->TbsGetFileName($idx); // usefull for TbsPicAdd()
			$this->TbsCurrIdx = $idx; // usefull for debug mode
			if ($TbsShow && $onshow) $TBS->Show(TBS_NOTHING);
			if ($this->ExtEquiv == 'docx') {
				$this->MsWord_RenumDocPr($TBS->Source);
			}
			if ($explicitRef && (!isset($this->MsExcel_KeepRelative[$idx])) ) {
				$this->MsExcel_ConvertToExplicit($TBS->Source);
			}
			if ($Debug) $this->DebugLst[$this->TbsGetFileName($idx)] = $TBS->Source;
			$this->FileReplace($idx, $TBS->Source, TBSZIP_STRING, $TBS->OtbsAutoUncompress);
		}
		$TBS->Plugin(-10); // reactivate other plugins
		$this->TbsCurrIdx = false;

		if ($this->OpenXmlCTypes!==false) $this->OpenXML_CTypesCommit($Debug);    // Commit special OpenXML features if any
		if ($this->OpenDocManif!==false)  $this->OpenDoc_ManifestCommit($Debug);  // Commit special OpenDocument features if any
		if ($this->OpenXmlRid!==false) $this->OpenXML_Rels_CommitNewRids($Debug); // Must be done also after the loop because some Rid can be added with [onshow]
		
		if ($TBS->OtbsGarbageCollector) {
			if ($this->ExtType=='openxml') $this->OpenMXL_GarbageCollector();
		}

		if ( ($TBS->ErrCount>0) && (!$TBS->NoErr) && (!$Debug)) {
			$TBS->meth_Misc_Alert('Show() Method', 'The output is cancelled by the OpenTBS plugin because at least one error has occured.');
			exit;
		}

		if ($Debug) {
			// Do the debug even if other options are used
			$this->TbsDebug_Merge(true, false);
		} elseif (($Render & TBS_OUTPUT)==TBS_OUTPUT) { // notice that TBS_OUTPUT = OPENTBS_DOWNLOAD
			// download
			$ContentType = (isset($this->ExtInfo['ctype'])) ? $this->ExtInfo['ctype'] : '';
			$this->Flush($Render, $File, $ContentType); // $Render is used because it can contain options OPENTBS_DOWNLOAD and OPENTBS_NOHEADER.
			$Render = $Render - TBS_OUTPUT; //prevent TBS from an extra output.
		} elseif(($Render & OPENTBS_FILE)==OPENTBS_FILE) {
			// to file
			$this->Flush(TBSZIP_FILE, $File);
		} elseif(($Render & OPENTBS_STRING)==OPENTBS_STRING) {
			// to string
			$this->Flush(TBSZIP_STRING);
			$TBS->Source = $this->OutputSrc;
			$this->OutputSrc = '';
		}

		if (($Render & TBS_EXIT)==TBS_EXIT) {
			$this->Close();
			exit;
		}

		return false; // cancel the default Show() process

	}

	function OnCacheField($BlockName,&$Loc,&$Txt,$PrmProc) {

		if (isset($Loc->PrmLst['ope'])) {

			$ope_lst = explode(',', $Loc->PrmLst['ope']); // in this event, ope is not exploded

			// Prepare to change picture
			if (in_array('changepic', $ope_lst)) {
				$this->TbsPicPrepare($Txt, $Loc, true); // add parameter "att" which will be processed just after this event, when the field is cached
			} elseif (in_array('mergecell', $ope_lst)) {
				$this->TbsPrepareMergeCell($Txt, $Loc);
			} elseif (in_array('docfield', $ope_lst)) {
				$this->TbsDocFieldPrepare($Txt, $Loc);
			}

			// Change cell type
			foreach($ope_lst as $ope) {
				$x = substr($ope,0,4);
				if( ($x==='tbs:') || ($x==='xlsx') || (substr($ope,0,3)==='ods') ) {
					if ($this->ExtEquiv==='ods') {
						$z = '';
						$this->OpenDoc_ChangeCellType($Txt, $Loc, $ope, false, $z);
					} elseif ($this->ExtEquiv==='xlsx') {
						$this->MsExcel_ChangeCellType($Txt, $Loc, $ope);
					}
					return; // do only one change
				}
			}

		}

	}

	function OnOperation($FieldName,&$Value,&$PrmLst,&$Txt,$PosBeg,$PosEnd,&$Loc) {
	// in this event, ope is exploded, there is one function call for each ope command
		$ope = $PrmLst['ope'];
		if ($ope==='addpic') {
			// for compatibility
			$this->TbsPicAdd($Value, $PrmLst, $Txt, $Loc, 'ope=addpic');
		} elseif ($ope==='changepic') {
			$this->TbsPicPrepare($Txt, $Loc, false);
			$this->TbsPicAdd($Value, $PrmLst, $Txt, $Loc, 'ope=changepic');
		} elseif ($ope==='delcol') {
			// Delete the TBS field otherwise « return false » will produce a TBS error « doesn't have any subname » with [onload] fields.
			$Txt = substr_replace($Txt, '', $PosBeg, $PosEnd - $PosBeg + 1);
			$this->TbsDeleteColumns($Txt, $Value, $PrmLst, $PosBeg);
			return false; // prevent TBS from actually merging the field
		} elseif ($ope==='mergecell') {
			if (isset($this->PrevVals[$Loc->FullName])) {
				if ($Value==$this->PrevVals[$Loc->FullName]) {
					$Value = '<w:vMerge w:val="continue"/>';
				} else {
					$this->PrevVals[$Loc->FullName] = $Value;
					$Value = '<w:vMerge w:val="restart"/>';
				}
			}
		} elseif ($ope==='docfield') {
			$this->TbsDocFieldPrepare($Txt, $Loc);
		} else {
			$x = substr($ope,0,4);
			if( ($x==='tbs:') || ($x==='xlsx') || (substr($ope,0,3)==='ods') ) {
				if ($this->ExtEquiv==='ods') {
					if (!isset($Loc->PrmLst['cellok'])) $this->OpenDoc_ChangeCellType($Txt, $Loc, $ope, true, $Value);
				} elseif ($this->ExtEquiv==='xlsx') {
					if (!isset($Loc->PrmLst['cellok'])) $this->MsExcel_ChangeCellType($Txt, $Loc, $ope);
					$this->MsExcel_ChangeCellValue($Loc, $Value);
				}
			}
		}
	}

	function OnCommand($Cmd, $x1=null, $x2=null, $x3=null, $x4=null, $x5=null) {

		if ($Cmd==OPENTBS_INFO) {
			// Display debug information
			echo "<strong>OpenTBS plugin Information</strong><br>\r\n";
			return $this->Debug();
		} elseif ( ($Cmd==OPENTBS_DEBUG_INFO) || ($Cmd==OPENTBS_DEBUG_CHART_LIST) ) {
			if (is_null($x1)) $x1 = true;
			$this->TbsDebug_Info($x1);
			return true;
		}

		if($Cmd==OPENTBS_MAKE_OPTIMIZED_TEMPLATE) {
				
			// save options
			$s_onload = $this->TBS->GetOption('onload');
			$s_onshow = $this->TBS->GetOption('onshow');
			
			// change options
			$this->TBS->SetOption('onload', false);
			$this->TBS->SetOption('onshow', false);
			
			// load the template
			$this->TBS->LoadTemplate($x1);
			
			if ($this->ExtEquiv == 'xlsx') {
				// load all sheets
				$this->MsExcel_SheetInit();
				foreach($this->MsExcel_Sheets as $o) {
					$this->TbsLoadSubFileAsTemplate('xl/'.$o->file);
				}
			} elseif ($this->ExtEquiv == 'pptx') {
				// load all slides
				$this->MsPowerpoint_InitSlideLst();
				foreach ($this->OpenXmlSlideLst as $s) {
					$this->TbsLoadSubFileAsTemplate($s['file']);
				}
			}

			// save the result
			$this->TBS->Show(OPENTBS_FILE + OPENTBS_DEBUG_AVOIDAUTOFIELDS, $x2);
			
			// restore options
			$this->TBS->SetOption('onload', $s_onload);
			$this->TBS->SetOption('onshow', $s_onshow);
			
			return true;
			
		}
		
		// Check that a template is loaded
		if ($this->ExtInfo===false) {
			$this->RaiseError("Cannot execute the plug-in commande because no template is loaded.");
			return true;
		}
		
		if ($Cmd==OPENTBS_RESET) {

			// Reset all mergings
			$this->ArchCancelModif();
			$this->TbsStoreLst = array();
			$TBS =& $this->TBS;
			$TBS->Source = '';
			$TBS->OtbsCurrFile = false;
			if (is_string($TBS->OtbsSubFileLst)) {
				$f = '#'.$TBS->OtbsSubFileLst;
				$h = '';
				$this->BeforeLoadTemplate($f,$h);
			}
			return true;

		} elseif ($Cmd==OPENTBS_SELECT_FILE) {
		
			// Raise an error is the file is not found
			return $this->TbsLoadSubFileAsTemplate($x1);
		
		} elseif ( ($Cmd==OPENTBS_ADDFILE) || ($Cmd==OPENTBS_REPLACEFILE) ) {

			// Add a new file or cancel a previous add
			$Name = (is_null($x1)) ? false : $x1;
			$Data = (is_null($x2)) ? false : $x2;
			$DataType = (is_null($x3)) ? TBSZIP_STRING : $x3;
			$Compress = (is_null($x4)) ? true : $x4;

			if ($Cmd==OPENTBS_ADDFILE) {
				return $this->FileAdd($Name, $Data, $DataType, $Compress);
			} else {
				return $this->FileReplace($Name, $Data, $DataType, $Compress);
			}

		} elseif ($Cmd==OPENTBS_DELETEFILE) {

			// Delete an existing file in the archive
			$Name = (is_null($x1)) ? false : $x1;
			$this->FileCancelModif($Name, false);    // cancel added files
			return $this->FileReplace($Name, false); // mark the file as to be deleted

		} elseif ($Cmd==OPENTBS_FILEEXISTS) {

			return $this->FileExists($x1);

		} elseif ($Cmd==OPENTBS_CHART) {

			$ChartRef = $x1;
			$SeriesNameOrNum = $x2;
			$NewValues = (is_null($x3)) ? false : $x3;
			$NewLegend = (is_null($x4)) ? false : $x4;

			if ($this->ExtType=='odf') {
				return $this->OpenDoc_ChartChangeSeries($ChartRef, $SeriesNameOrNum, $NewValues, $NewLegend);
			} else {
				return $this->OpenXML_ChartChangeSeries($ChartRef, $SeriesNameOrNum, $NewValues, $NewLegend);
			}
		} elseif ($Cmd==OPENTBS_CHART_INFO) {

			$ChartRef = $x1;
			$Complete = $x2;
			
			if ($this->ExtType=='odf') {
				return $this->OpenDoc_ChartReadSeries($ChartRef, $Complete);
			} else {
				return $this->OpenXML_ChartReadSeries($ChartRef, $Complete);
			}
			
			
		} elseif ($Cmd==OPENTBS_DEBUG_XML_SHOW) {

			$this->TBS->Show(OPENTBS_DEBUG_XML);

		} elseif ($Cmd==OPENTBS_DEBUG_XML_CURRENT) {

			$this->TbsStorePark();
			$this->DebugLst = array();
			foreach ($this->TbsStoreLst as $idx=>$park) $this->DebugLst[$this->TbsGetFileName($idx)] = $park['src'];
			$this->TbsDebug_Merge(true, true);

			if (is_null($x1) || $x1) exit();

		} elseif($Cmd==OPENTBS_FORCE_DOCTYPE) {

			return $this->Ext_PrepareInfo($x1);

		} elseif ($Cmd==OPENTBS_DELETE_ELEMENTS) {

			if (is_string($x1)) $x1 = explode(',', $x1);
			if (is_null($x2)) $x2 = false; // OnlyInner
			return $this->XML_DeleteElements($this->TBS->Source, $x1, $x2);

		} elseif ($Cmd==OPENTBS_SELECT_MAIN) {

			if ( ($this->ExtInfo!==false) && isset($this->ExtInfo['main']) ) {
				$this->TbsLoadSubFileAsTemplate($this->ExtInfo['main']);
				return true;
			} else {
				return false;
			}

		} elseif ($Cmd==OPENTBS_SELECT_SHEET) {

			if ($this->ExtEquiv=='ods') {
				$this->TbsLoadSubFileAsTemplate($this->ExtInfo['main']);
				return true;
			}

			// Only XLSX files have sheets in separated subfiles.
			if ($this->ExtEquiv==='xlsx') {
				$SearchBy = ($x2) ? array('name', 'sheetId') : array('name', 'num');
				$o = $this->MsExcel_SheetGetConf($x1, $SearchBy, true);
				if ($o===false) return;
				if ($o->file===false) return $this->RaiseError("($Cmd) Error with sheet '$x1'. The corresponding XML subfile is not referenced.");
				return $this->TbsLoadSubFileAsTemplate('xl/'.$o->file);
			}
			
			return false;

		} elseif ( ($Cmd==OPENTBS_DELETE_SHEETS) || ($Cmd==OPENTBS_DISPLAY_SHEETS) || ($Cmd==OPENTBS_DELETE_SLIDES) || ($Cmd==OPENTBS_DISPLAY_SLIDES) ) {

			$delete = ( ($Cmd==OPENTBS_DELETE_SHEETS) || ($Cmd==OPENTBS_DELETE_SLIDES) ) ;
			$this->TbsSheetSlide_DeleteDisplay($x1, $x2, $delete);

		} elseif ($Cmd==OPENTBS_MERGE_SPECIAL_ITEMS) {

			if ($this->ExtEquiv!='xlsx') return 0;
			$lst = $this->MsExcel_GetDrawingLst();
			$this->TbsQuickLoad($lst);

		} elseif ($Cmd==OPENTBS_SELECT_SLIDE) {

			if ($this->ExtEquiv=='odp') {
				$this->TbsLoadSubFileAsTemplate($this->ExtInfo['main']);
				return true;
			}
			
			if ($this->ExtEquiv!='pptx') return false;

			$master  = (is_null($x2)) ? false : $x2;
			$slide = ($master) ? 'slide master' : 'slide';
			$RefLst = $this->MsPowerpoint_InitSlideLst($master);

			$s = intval($x1)-1;
			if (isset($RefLst[$s])) {
				$this->TbsLoadSubFileAsTemplate($RefLst[$s]['idx']);
				return true;
			} else {
				return $this->RaiseError("($Cmd) $slide number $x1 is not found inside the Presentation.");
			}

		} elseif ($Cmd==OPENTBS_DELETE_COMMENTS) {

			// Default values
			$MainTags = false;
			$CommFiles = false;
			$CommTags = false;
			$Inner = false;

			if ($this->ExtType=='odf') {
				$MainTags = array('office:annotation', 'officeooo:annotation'); // officeooo:annotation is used in ODP Presentations
			} else {
				switch ($this->ExtEquiv) {
				case 'docx':
					$MainTags = array('w:commentRangeStart', 'w:commentRangeEnd', 'w:commentReference');
					$CommFiles = array('wordprocessingml.comments+xml');
					$CommTags = array('w:comment');
					$Inner = true;
					break;
				case 'xlsx':
					$CommFiles = array('spreadsheetml.comments+xml');
					$CommTags = array('comment', 'author');
					break;
				case 'pptx':
					$CommFiles = array('presentationml.comments+xml');
					$CommTags = array('p:cm');
					break;
				default:
					return 0;
				}
			}

			return $this->TbsDeleteComments($MainTags, $CommFiles, $CommTags, $Inner);

		} elseif ($Cmd==OPENTBS_CHANGE_PICTURE) {

			static $UniqueId = 0;

			$code = $x1;
			$file = $x2;
			$prms = array('default'=>'current', 'adjust' => 'inside');
			if (is_array($x3)) {
				$prms = array_merge($prms, $x3);
			} else {
				// Compatibility v <= 1.9.0
				if (!is_null($x3)) $prms['default'] = $x3;
				if (!is_null($x4)) $prms['adjust'] = $x4;
			}
			$prms_flat = array();
			foreach($prms as $p => $v) $prms_flat[] = $p.'='.$v;
			$prms_flat = implode(';', $prms_flat);
			
			$UniqueId++;
			$name = 'OpenTBS_Change_Picture_'.$UniqueId;
			$tag = "[$name;ope=changepic;tagpos=inside;$prms_flat]";

			$nbr = false;
			$TBS =& $this->TBS; 
			$TBS->Source = str_replace($code, $tag, $TBS->Source, $nbr); // argument $nbr supported buy PHP >= 5
			if ($nbr!==0) $TBS->MergeField($name, $file);

			return $nbr;

		} elseif ($Cmd==OPENTBS_COUNT_SLIDES) {

			$master  = (is_null($x1)) ? false : $x1;
			
			if ($this->ExtEquiv=='pptx') {
				$RefLst = $this->MsPowerpoint_InitSlideLst($master);
				return count($RefLst);
			} elseif ($this->ExtEquiv=='odp') {
				$idx = $this->Ext_GetMainIdx();
				$txt = $this->TbsStoreGet($idx, "Command OPENTBS_COUNT_SLIDES");
				return substr_count($txt, '</draw:page>');
			} else {
				return 0;
			}
			
		} elseif ($Cmd==OPENTBS_COUNT_SHEETS) {

			if ($this->ExtEquiv=='xlsx') {
				$this->MsExcel_SheetInit();
				return count($this->MsExcel_Sheets);
			} elseif ($this->ExtEquiv=='ods') {
				$idx = $this->Ext_GetMainIdx();
				$txt = $this->TbsStoreGet($idx, "Command OPENTBS_COUNT_SHEETS");
				return substr_count($txt, '</table:table>');
			} else {
				return 0;
			}
			
		} elseif ($Cmd==OPENTBS_SEARCH_IN_SLIDES) {

			if ($this->ExtEquiv=='pptx') {
				$option = (is_null($x2)) ? OPENTBS_FIRST : $x2;
				$returnFirstFound = (($option & OPENTBS_ALL)!=OPENTBS_ALL);
				$find = $this->MsPowerpoint_SearchInSlides($x1, $returnFirstFound);
				if ($returnFirstFound) {
					$slide = $find['key'];
					if ( ($slide!==false) && (($option & OPENTBS_GO)==OPENTBS_GO) ) $this->OnCommand(OPENTBS_SELECT_SLIDE, $slide);
					return ($slide);
				} else {
					$res = array();
					foreach($find as $f) $res[] = $f['key'];
					return $res;
				}
			} elseif ($this->ExtEquiv=='odp') {
				// Only for compatibility
				$p = instr($TBS->Source, $str);
				return ($p===false) ? false : 1;
			} else {
				return false;
			}
			
		} elseif ( ($Cmd==OPENTBS_SELECT_HEADER) || ($Cmd==OPENTBS_SELECT_FOOTER) ) {
		
			$file = false;

			switch ($this->ExtEquiv) {
			case 'docx':
				$x2 = intval($x2); // 0 by default
				$file = $this->MsWord_GetHeaderFooterFile($Cmd, $x1, $x2);
				break;
			case 'odt': case 'ods': case 'odp':
				$file = $this->ExtInfo['main'];
			case 'xlsx': case 'pptx': 
				return false;
				break;
			}
			
			return $this->TbsLoadSubFileAsTemplate($file);
		
		} elseif ($Cmd==OPENTBS_GET_HEADERS_FOOTERS) {

			$res = array();
		
			switch ($this->ExtEquiv) {
			case 'docx':
				$this->MsWord_InitHeaderFooter();
				foreach ($this->MsWord_HeaderFooter as $info) {
					$res[] = $info['file'];
				}				
				break;
			case 'odt': case 'ods': case 'odp':
				// Headers and footers are in the main file.
				// Handout headers and footers for presentations (PPTX & ODP) are not supported for now.
				if (isset($this->ExtInfo['main'])) $res[] = $this->ExtInfo['main'];
			case 'xlsx':
				$FileName = $this->CdFileLst[$this->TbsCurrIdx];
				if ($this->MsExcel_SheetIsIt($FileName) ) $res[] = $FileName;
				break;
			case 'pptx': 
				// Headers and footers are in the selected sheet or slide.
				$FileName = $this->CdFileLst[$this->TbsCurrIdx];
				if ($this->MsPowerpoint_SlideIsIt($FileName) ) $res[] = $FileName;
				break;
			}
			
			return $res;
		
		} elseif ($Cmd==OPENTBS_SYSTEM_CREDIT) {

			$x1 = (boolean) $x1;
			$this->TbsSystemCredits = $x1;
			return $x1;

		} elseif ($Cmd==OPENTBS_ADD_CREDIT) {

			return $this->Misc_EditCredits($x1, true, false, $x2);
			
		} elseif ($Cmd==OPENTBS_RELATIVE_CELLS) {

			$KeepRelative = (boolean) $x1;
			if ($x2 == OPENTBS_ALL) {
				// Al$ sheets
				$this->TBS->OtbsMsExcelExplicitRef = (!$KeepRelative);
			} else {
				// Current sheet
				if ($KeepRelative) {
					$this->MsExcel_KeepRelative[$this->TbsCurrIdx] = true;
				} else {
					unset($this->MsExcel_KeepRelative[$this->TbsCurrIdx]);
				}
			}
			return $KeepRelative;
			
		} elseif ($Cmd==OPENTBS_EDIT_ENTITY) {
			
			$AddElIfMissing = (boolean) $x5;
			return $this->XML_ForceAtt($x1, $x2, $x3, $x4, $AddElIfMissing);
			
		} elseif ($Cmd==OPENTBS_GET_FILES) {
	
			$files = array();
			// All files in the archive
			foreach ($this->CdFileLst as $f) {
				$files[] = $f['v_name'];
			}
			return $files;
			
		} elseif ($Cmd==OPENTBS_CHART_DELETE_CATEGORY) {
			
			if (is_null($x3)) {
				$x3 = false;
			}
			
			if ($this->ExtType=='odf') {
				return $this->OpenDoc_ChartDelCategories($x1, $x2, $x3);
			} elseif ($this->ExtType=='openxml') {
				return $this->OpenXML_ChartDelCategories($x1, $x2, $x3);
			} else {
				return false;
			}
			
		} elseif ($Cmd==OPENTBS_GET_OPENED_FILES) {
			
			$files = array();
			foreach ($this->TbsStoreLst as $idx => $info) {
				// Files loaded manually, that are not the current selected file
				if ($info['onshow'] && ($idx !== $this->TbsCurrIdx)) {
					$name = $this->CdFileLst[$idx]['v_name'];
					$files[] = $name;
				}
			}
			// the current selected file
			if ($this->TbsCurrIdx !== false) {
				$name = $this->CdFileLst[$this->TbsCurrIdx]['v_name'];
				$files[] = $name;
			}
			return $files;
			
		} elseif ($Cmd==OPENTBS_WALK_OPENED_FILES) {
			
			foreach ($this->TbsStoreLst as $idx => $info) {
				// Files loaded manually, that are not the current selected file
				if ($info['onshow'] && ($idx !== $this->TbsCurrIdx)) {
					$name = $this->CdFileLst[$idx]['v_name'];
					call_user_func_array($x1, array(&$this->TbsStoreLst[$idx]['src'], $name));
				}
			}
			// the current selected file
			if ($this->TbsCurrIdx !== false) {
				$name = $this->CdFileLst[$this->TbsCurrIdx]['v_name'];
				call_user_func_array($x1, array(&$this->TBS->Source, $name));
			}
			
		} elseif ($Cmd == OPENTBS_GET_CELLS) {
			
			return $this->Sheet_VisitCells($x1, $x2, false);
			
		} elseif ($Cmd == OPENTBS_SET_CELLS) {
			
			return $this->RaiseError("Command OPENTBS_SET_CELLS will be available in a next version.");
		}

	}

	// Initialize template information
	function TbsInitArchive() {

		$TBS =& $this->TBS;

		$TBS->OtbsCurrFile = false;

		$this->TbsStoreLst = array();
		$this->TbsCurrIdx = false;
		$this->TbsSystemCredits = true;
		$this->TbsNoField = array(); // idx of sub-file having no TBS fields
		$this->IdxToCheck = array(); // index of files to check
		$this->PrevVals = array(); // Previous values for 'mergecell' operator

		$this->ImageIndex = 1;          // Serial for inserted images
		$this->ImageInternal = array(); // Internal names of inserted image

		$this->ExtEquiv = false;
		$this->ExtType = false;
		
		// Common
		$this->OtbsSheetSlidesDelete = array();
		$this->OtbsSheetSlidesVisible = array();
		$this->OtbsSheetRangeNames = false;

		// LibreOffice
		$this->OpenDocCharts = false;
		$this->OpenDocManif = false;
		$this->OpenDoc_SheetSlides = false;
		$this->OpenDoc_Styles = false;

		// MsOffice
		$this->OpenXmlRid = false;
		$this->OpenXmlCTypes = false;
		$this->OpenXmlCharts = false;
		$this->OpenXmlSharedStr = false;
		$this->OpenXmlSlideLst = false;
		$this->OpenXmlSlideMasterLst = false;
		$this->MsExcel_Sheets = false;
		$this->MsExcel_NoTBS = array(); // shared string containing no TBS field
		$this->MsExcel_KeepRelative = array();
		$this->MsExcel_Formulas = array();
		$this->MsWord_HeaderFooter = false;
		$this->MsWord_DocPrId = 0;

		$this->Ext_PrepareInfo(); // Set extension information

	}

	/**
	 * Load one or several sub-files of the archive as the current template.
	 * If a sub-template is loaded for the first time, then automatic merges and clean-up are performed.
	 * Return true if the file is correctly loaded.
	 * @param $SubFileLst Can be an index or a name or a file, or an array of such values.
	 */
	function TbsLoadSubFileAsTemplate($SubFileLst) {

		if (!is_array($SubFileLst)) $SubFileLst = array($SubFileLst);

		$ok = true;
		$TBS = false;

		foreach ($SubFileLst as $SubFile) {

			$idx = $this->FileGetIdx($SubFile);
			if ($idx===false) {
				$ok = $this->RaiseError('Cannot load "'.$SubFile.'". The file is not found in the archive "'.$this->ArchFile.'".');
			} elseif ($idx!==$this->TbsCurrIdx) {
				// Save the current loaded subfile if any
				$this->TbsStorePark();
				// Load the subfile
				if (!is_string($SubFile)) $SubFile = $this->TbsGetFileName($idx);
				$this->TbsStoreLoad($idx, $SubFile);
				if ($this->LastReadNotStored) {
					// Loaded for the first time
					if ($TBS===false) {
						$this->TbsSwitchMode(true); // Configuration which prevents from other plug-ins when calling LoadTemplate()
						$MergeAutoFields = $this->TbsMergeAutoFields();
						$TBS =& $this->TBS;
					}
					if ($this->LastReadComp<=0) { // the contents is not compressed
						if ($this->ExtInfo!==false) {
							$i = $this->ExtInfo;
							$e = $this->ExtEquiv;
							if ($this->TbsApplyOptim($TBS->Source, true)) {
								if (isset($i['rpl_what'])) {
									// auto replace strings in the loaded file
									$TBS->Source = str_replace($i['rpl_what'], $i['rpl_with'], $TBS->Source);
								}
								if (($e==='odt') && $TBS->OtbsClearWriter) {
									$this->OpenDoc_CleanRsID($TBS->Source);
								}
								if (($e==='ods') && $TBS->OtbsMsExcelCompatibility) {
									$this->OpenDoc_DeleteUselessRepeatedElements($TBS->Source);
								}
								if ($e==='docx') {
									if ($TBS->OtbsSpacePreserve) $this->MsWord_CleanSpacePreserve($TBS->Source);
									if ($TBS->OtbsClearMsWord) $this->MsWord_Clean($TBS->Source);
								}
								if (($e==='pptx') && $TBS->OtbsClearMsPowerpoint) {
									$this->MsPowerpoint_Clean($TBS->Source);
								}
								if (($e==='xlsx') && $TBS->OtbsMsExcelConsistent) {
									$this->MsExcel_DeleteFormulaResults($idx, $TBS->Source);
									$this->MsExcel_ConvertToRelative($TBS->Source);
								}
							}
						}
						// apply default TBS behaviors on the uncompressed content: other plug-ins + [onload] fields
						if ($MergeAutoFields) $TBS->LoadTemplate(null,'+');
					}
				}
			}

		}

		if ($TBS!==false) $this->TbsSwitchMode(false); // Reactivate default configuration
		
		return $ok;
		
	}

	// Return true if automatic fields must be merged
	function TbsMergeAutoFields() {
		return (($this->TBS->Render & OPENTBS_DEBUG_AVOIDAUTOFIELDS)!=OPENTBS_DEBUG_AVOIDAUTOFIELDS);
	}

	function TbsSwitchMode($PluginMode) {
		$TBS = &$this->TBS;
		if ($PluginMode) {
			$this->_ModeSave = $TBS->_Mode;
			$TBS->_Mode++;    // deactivate TplVars[] reset and Charset reset.
			$TBS->Plugin(-4); // deactivate other plugins
		} else {
			// Reactivate default configuration
			$TBS->_Mode = $this->_ModeSave;
			$TBS->Plugin(-10); // reactivate other plugins
		}
	}

	// Save the last opened subfile into the store, and close the subfile
	function TbsStorePark() {
		if ($this->TbsCurrIdx!==false) {
			$this->TbsStoreLst[$this->TbsCurrIdx] = array('src'=>$this->TBS->Source, 'onshow'=>true);
			$this->TBS->Source = '';
			$this->TbsCurrIdx = false;
		}
	}

	// Load a subfile from the store to be the current subfile
	function TbsStoreLoad($idx, $file=false) {
		$this->TBS->Source = $this->TbsStoreGet($idx, false);
		$this->TbsCurrIdx = $idx;
		if ($file===false) $file = $this->TbsGetFileName($idx);
		$this->TBS->OtbsCurrFile = $file;
	}

	/**
	 * Save a given source in the store.
	 * $onshow=true means [onshow] are merged before the output. 
	 * If $onshow is null, then the 'onshow' option stays unchanged.
	 */
	function TbsStorePut($idx, $src, $onshow = null) {
		if ($idx===$this->TbsCurrIdx) {
			$this->TBS->Source = $src;
		} else {
			if (is_null($onshow)) {
				if (isset($this->TbsStoreLst[$idx])) {
					$onshow = $this->TbsStoreLst[$idx]['onshow'];
				} else {
					$onshow = false;
				}
			}
			$this->TbsStoreLst[$idx] = array('src'=>$src, 'onshow'=>$onshow);
		}
	}

	/**
	 * Return a source from the current merging, the store, or the archive.
	 * Take care that if the source it taken from the archive, then it is not saved in the store.
	 * @param {integer} $idx The index of the file to read.
	 * @param {string|false} $caller A text describing the calling function, for error reporting purpose. If caller=false it means TbsStoreLoad().
	 */
	function TbsStoreGet($idx, $caller) {
		$this->LastReadNotStored = false;
		if ($idx===$this->TbsCurrIdx) {
			return $this->TBS->Source;
		} elseif (isset($this->TbsStoreLst[$idx])) {
			$txt = $this->TbsStoreLst[$idx]['src'];
			if ($caller===false) $this->TbsStoreLst[$idx]['src'] = ''; // save memory space
			return $txt;
		} else {
			$this->LastReadNotStored = true;
			$txt = $this->FileRead($idx, true);
			if ($this->LastReadComp>0) {
				if ($caller===false) {
					return $txt; // return the uncompressed contents
				} else {
					return $this->RaiseError("(".$caller.") unable to uncompress '".$this->TbsGetFileName($idx)."'.");
				}
			} else {
				return $txt;
			}
		}
	}

	// Load a list of sub-files, but only if they have TBS fields.
	// This is in order to merge automatic fields in special XML sub-files that are not usually loaded manually.
	function TbsQuickLoad($NameLst) {

		if (!is_array($NameLst)) $NameLst = array($NameLst);
		$nbr = 0;
		$TBS = &$this->TBS;

		foreach ($NameLst as $FileName) {
			$idx = $this->FileGetIdx($FileName);
			if ( (!isset($this->TbsStoreLst[$idx])) && (!isset($this->TbsNoField[$idx])) ) {
				$txt = $this->FileRead($idx, true);
				if (strpos($txt, $TBS->_ChrOpen)!==false) {
					// merge
					$nbr++;
					if ($nbr==1) {
						$MergeAutoFields = $this->TbsMergeAutoFields();
						$SaveIdx = $this->TbsCurrIdx; // save the index of sub-file before the QuickLoad
						$SaveName = $TBS->OtbsCurrFile;
						$this->TbsSwitchMode(true);
					}
					$this->TbsStorePark(); // save the current file in the store
					$TBS->Source = $txt;
					unset($txt);
					$TBS->OtbsCurrFile = $FileName; // may be needed for [onload] parameters
					$this->TbsCurrIdx = $idx;
					if ($MergeAutoFields) $TBS->LoadTemplate(null,'+');
				} else {
					$this->TbsNoField[$idx] = true;
				}
			}
		}

		if ($nbr>0) {
			$this->TbsSwitchMode(false);
			$this->TbsStorePark(); // save the current file in the store
			$this->TbsStoreLoad($SaveIdx, $SaveName); // restore the sub-file as before the QuickLoad
		}

		return $nbr;

	}

	function TbsGetFileName($idx) {
		if (isset($this->CdFileLst[$idx])) {
			return $this->CdFileLst[$idx]['v_name'];
		} else {
			return '(id='.$idx.')';
		}
	}

	/**
	 * Tells if optimisation marker is prensent in the current source, eventually add it if it is not.
	 * The optimization marker is a simple space (' ') before the closing chars of the "<? ?>" element.
	 * @param  string  $Txt  The text source to check
	 * @param  boolean $mark Set to true to mark the source as done if it is not the case.
	 * @return boolean True if the current source has just been marked done. Null if it is not possible to telle if it is done or note. Fasle if is is done before.
	 */
	function TbsApplyOptim(&$Txt, $mark) {
		if (substr($Txt, 0, 2) === '<?') {
			$p = strpos($Txt, '?>');
			if (substr($Txt, $p-1, 1) === ' ') {
				return false;
			} else {
				if ($mark) {
					$Txt = substr_replace($Txt, ' ', $p, 0);
				}
				return true;
			}
		} else {
			return null;
		}
	}
	
	/**
	 * Display the header of the debug mode (only once)
	 */
	function TbsDebug_Init(&$nl, &$sep, &$bull, $type) {

		static $DebugInit = false;

		if ($DebugInit) return;
		$DebugInit = true;

		$nl = "\n";
		$sep = str_repeat('-',30);
		$bull = $nl.'  - ';


		if (!headers_sent()) header('Content-Type: text/plain; charset="UTF-8"');

		echo "* OPENTBS DEBUG MODE: if the star, (*) on the left before the word OPENTBS, is not the very first character of this page, then your
merged Document will be corrupted when you use the OPENTBS_DOWNLOAD option. If there is a PHP error message, then you have to fix it.
If they are blank spaces, line beaks, or other unexpected characters, then you have to check your code in order to avoid them.";
		echo $nl;
		echo $nl.$sep.$nl.'INFORMATION'.$nl.$sep;
		echo $nl.'* Debug command: '.$type;
		echo $nl.'* OpenTBS version: '.$this->Version;
		echo $nl.'* TinyButStrong version: '.$this->TBS->Version;
		echo $nl.'* PHP version: '.PHP_VERSION;
		echo $nl.'* Zlib enabled: '.($this->Meth8Ok) ? 'YES' : 'NO (it should be enabled)';
		echo $nl.'* Opened document: '.(($this->ArchFile==='') ? '(none)' : $this->ArchFile);
		echo $nl.'* Activated features for document type: '.(($this->ExtInfo===false) ? '(none)' : $this->ExtType.'/'.$this->ExtEquiv);

	}

	function TbsDebug_Info($Exit) {

		$this->TbsDebug_Init($nl, $sep, $bull, 'OPENTBS_DEBUG_INFO');

		if ($this->ExtInfo !== false) {
		
			switch ($this->ExtEquiv) {
			case 'docx': $this->MsWord_DocDebug($nl, $sep, $bull); break;
			case 'xlsx': $this->MsExcel_SheetDebug($nl, $sep, $bull); break;
			case 'pptx': $this->MsPowerpoint_SlideDebug($nl, $sep, $bull); break;
			case 'ods' : $this->OpenDoc_SheetSlides_Debug(true, $nl, $sep, $bull); break;
			case 'odp' : $this->OpenDoc_SheetSlides_Debug(false, $nl, $sep, $bull); break;
			}

			switch ($this->ExtType) {
			case 'openxml': $this->OpenXML_ChartDebug($nl, $sep, $bull); break;
			case 'odf':     $this->OpenDoc_ChartDebug($nl, $sep, $bull); break;
			}
			
		}

		if ($Exit) exit;

	}

	function TbsDebug_Merge($XmlFormat = true, $Current) {
	// display modified and added files

		$this->TbsDebug_Init($nl, $sep, $bull, ($Current ? 'OPENTBS_DEBUG_XML_CURRENT' :'OPENTBS_DEBUG_XML_SHOW'));

		// scann files for collecting information
		$mod_lst = ''; // id of modified files
		$del_lst = ''; // id of deleted  files
		$add_lst = ''; // id of added    files

		// files marked as replaced in TbsZip
		$idx_lst = array_keys($this->ReplInfo);
		foreach ($idx_lst as $idx) {
			$name = $this->TbsGetFileName($idx);
			if ($this->ReplInfo[$idx]===false) {
				$del_lst .= $bull.$name;
			} else {
				$mod_lst .= $bull.$name;
			}
		}

		// files marked as modified in the Park
		$idx_lst = array_keys($this->TbsStoreLst);
		foreach ($idx_lst as $idx) {
			if (!isset($this->ReplInfo[$idx])) {
				$mod_lst .= $bull.$this->TbsGetFileName($idx);
			}
		}

		// files marked as added in TbsZip
		$idx_lst = array_keys($this->AddInfo);
		foreach ($idx_lst as $idx) {
			$name = $this->AddInfo[$idx]['name'];
			$add_lst .= $bull.$name;
		}

		if ($mod_lst==='')  $mod_lst = ' none';
		if ($del_lst==='')  $del_lst = ' none';
		if ($add_lst==='')  $add_lst = ' none';

		echo $nl.'* Deleted files in the archive:'.$del_lst;
		echo $nl.'* Added files in the archive:'.$add_lst;
		echo $nl.'* Modified files in the archive:'.$mod_lst;
		echo $nl;

		// display contents merged with OpenTBS
		foreach ($this->DebugLst as $name=>$src) {
			$x = trim($src);
			$info = '';
			$xml = ((strlen($x)>0) && $x[0]==='<');
			if ($XmlFormat && $xml) {
				$info = ' (XML reformated for debuging only)';
				$src = $this->XmlFormat($src);
			}
			echo $nl.$sep;
			echo $nl.'File merged with OpenTBS'.$info.': '.$name;
			echo $nl.$sep;
			echo $nl.$src;
		}

	}

	function ConvXmlOnly($Txt, $ConvBr) {
	// Used by TBS to convert special chars and new lines.
		$x = htmlspecialchars($Txt);
		if ($ConvBr) $this->ConvBr($x);
		return $x;
	}

	function ConvXmlUtf8($Txt, $ConvBr) {
	// Used by TBS to convert special chars and new lines.
		$x = htmlspecialchars(utf8_encode($Txt));
		if ($ConvBr) $this->ConvBr($x);
		return $x;
	}

	function ConvBr(&$x) {
		$z = $this->TBS->OtbsConvBr;
		if ($z===false) return;
		$x = nl2br($x); // Convert any type of line break
		$x = str_replace("\r", '' ,$x);
		$x = str_replace("\n", '' ,$x);
		$x = str_replace('<br />',$z ,$x);

	}

	function XmlFormat($Txt) {
	// format an XML source the be nicely aligned

		// delete line breaks
		$Txt = str_replace("\r",'',$Txt);
		$Txt = str_replace("\n",'',$Txt);

		// init values
		$p = 0;
		$lev = 0;
		$Res = '';

		$to = true;
		while ($to!==false) {
			$to = strpos($Txt,'<',$p);
			if ($to!==false) {
				$tc = strpos($Txt,'>',$to);
				if ($to===false) {
					$to = false; // anomaly
				} else {
					// get text between the tags
					$x = trim(substr($Txt, $p, $to-$p),' ');
					if ($x!=='') $Res .= "\n".str_repeat(' ',max($lev,0)).$x;
					// get the tag
					$x = substr($Txt, $to, $tc-$to+1);
					if ($Txt[$to+1]==='/') $lev--;
					$Res .= "\n".str_repeat(' ',max($lev,0)).$x;
					// change the level
					if (($Txt[$to+1]!=='?') && ($Txt[$to+1]!=='/') && ($Txt[$tc-1]!=='/')) $lev++;
					// next position
					$p = $tc + 1;
				}
			}
		}

		$Res = substr($Res, 1); // delete the first line break
		if ($p<strlen($Txt)) $Res .= trim(substr($Txt, $p), ' '); // complete the end

		return $Res;

	}

	/**
	 * Raise an error message and return false.
	 * @param {string}  $Msg      
	 * @param {boolean} $NoErrMsg Add the TBS message about noerr option.
	 *
	 * @return {boolean} Always return false.
	 */
	function RaiseError($Msg, $NoErrMsg=false) {
		// Overwrite the parent RaiseError() method.
		$exit = (!$this->TBS->NoErr);
		if ($exit) $Msg .= ' The process is ending, unless you set NoErr property to true.';
		$this->TBS->meth_Misc_Alert('OpenTBS Plugin', $Msg, $NoErrMsg);
		if ($exit) {
			if ($this->DebugLst!==false) {
				if ($this->TbsCurrIdx!==false) $this->DebugLst[$this->TbsGetFileName($this->TbsCurrIdx)] = $this->TBS->Source;
				$this->TbsDebug_Merge(true, false);
			}
			exit;
		}
		return false;
	}

	/**
	 * Return the item of an array if exits, or the default value.
	 */
	function getItem($array, $item, $default) {
		if (isset($array[$item])) {
			return $array[$item];
		} else {
			return $default;
		}
	}
	
	// Found the relevant attribute for the image source, and then add parameter 'att' to the TBS locator.
	function TbsPicPrepare(&$Txt, &$Loc, $IsCaching) {

		if (isset($Loc->PrmLst['pic_prepared'])) {
			return true;
		}
	
		if (isset($Loc->PrmLst['att'])) {
			return $this->RaiseError('Parameter att is used with parameter ope=changepic in the field ['.$Loc->FullName.']. changepic will be ignored');
		}
		
		$backward = true;

		if (isset($Loc->PrmLst['tagpos'])) {
			$s = $Loc->PrmLst['tagpos'];
			if ($s=='before') {
				$backward = false;
			} elseif ($s=='inside') {
				if ($this->ExtType=='openxml') $backward = false;
			}
		}
		
		// Find the target attribute
		$att = false;
		if ($this->ExtType==='odf') {
			$att = 'draw:image#xlink:href';
		} elseif ($this->ExtType==='openxml') {
			$att = $this->OpenXML_FirstPicAtt($Txt, $Loc->PosBeg, $backward);
			if ($att===false) return $this->RaiseError('Parameter ope=changepic used in the field ['.$Loc->FullName.'] has failed to found the picture.');
		} else {
			return $this->RaiseError('Parameter ope=changepic used in the field ['.$Loc->FullName.'] is not supported with the current document type.');
		}
				
		// Move the field to the attribute
		// This technical works with cached fields because already cached fields are placed before the picture.
		$prefix = ($backward) ? '' : '+';
		$Loc->PrmLst['att'] = $prefix.$att;
		clsTinyButStrong::f_Xml_AttFind($Txt,$Loc,true);

		// Delete parameter att to prevent TBS from another processing
		unset($Loc->PrmLst['att']);
	   
		// Get picture dimension information
		if (isset($Loc->PrmLst['adjust'])) {
			$FieldLen = 0;
			if ($this->ExtType==='odf') {
				$Loc->otbsDim = $this->TbsPicGetDim_ODF($Txt, $Loc->PosBeg, false, $Loc->PosBeg, $FieldLen);
			} else {
				if (strpos($att,'v:imagedata')!==false) { 
					$Loc->otbsDim = $this->TbsPicGetDim_OpenXML_vml($Txt, $Loc->PosBeg, false, $Loc->PosBeg, $FieldLen);
				} else {
					$Loc->otbsDim = $this->TbsPicGetDim_OpenXML_dml($Txt, $Loc->PosBeg, false, $Loc->PosBeg, $FieldLen);
				}
			}
		}
		
		// Set the original picture to empty
		if ( isset($Loc->PrmLst['unique']) && $Loc->PrmLst['unique'] ) {

			// Get the value in the template
			$Value = substr($Txt, $Loc->PosBeg, $Loc->PosEnd -  $Loc->PosBeg +1);

			if ($this->ExtType==='odf') {
				$InternalPicPath = $Value;
			} elseif ($this->ExtType==='openxml') {
				$InternalPicPath = $this->OpenXML_GetInternalPicPath($Value);
				if ($InternalPicPath === false) {
					$this->RaiseError('The picture to merge with field ['.$Loc->FullName.'] cannot be found. Value=' . $Value);
				}
			}

			// Set the picture file to empty
			$this->FileReplace($InternalPicPath, '', TBSZIP_STRING, false);		

		}
		
		$Loc->PrmLst['pic_prepared'] = true;
		return true;

	}

	function TbsPicGetDim_ODF($Txt, $Pos, $Forward, $FieldPos, $FieldLen) {
	// Found the attributes for the image dimensions, in an ODF file
		// unit (can be: mm, cm, in, pi, pt)
		$Offset = 0;
		$dim = $this->TbsPicGetDim_Any($Txt, $Pos, $Forward, $FieldPos, $FieldLen, $Offset, 'draw:frame', 'svg:width="', 'svg:height="', 3, false, false);
		return array($dim);
	}

	function TbsPicGetDim_OpenXML_vml($Txt, $Pos, $Forward, $FieldPos, $FieldLen) {
		$Offset = 0;
		$dim = $this->TbsPicGetDim_Any($Txt, $Pos, $Forward, $FieldPos, $FieldLen, $Offset, 'v:shape', 'width:', 'height:', 2, false, false);
		return array($dim);
	}

	function TbsPicGetDim_OpenXML_dml($Txt, $Pos, $Forward, $FieldPos, $FieldLen) {

		$Offset = 0;

		// Try to find the drawing element
		if (isset($this->ExtInfo['pic_entity'])) {
			$tag = $this->ExtInfo['pic_entity'];
			$Loc = clsTbsXmlLoc::FindElement($Txt, $this->ExtInfo['pic_entity'], $Pos, false);
			if ($Loc) {
				$Txt = $Loc->GetSrc();
				$Pos = 0;
				$Forward = true;
				$Offset = $Loc->PosBeg;
			}
		}

		$dim_shape = $this->TbsPicGetDim_Any($Txt, $Pos, $Forward, $FieldPos, $FieldLen, $Offset, 'wp:extent', 'cx="', 'cy="', 0, 12700, false);
		$dim_inner = $this->TbsPicGetDim_Any($Txt, $Pos, $Forward, $FieldPos, $FieldLen, $Offset, 'a:ext'    , 'cx="', 'cy="', 0, 12700, 'uri="');
		$dim_drawing = $this->TbsPicGetDim_Drawings($Txt, $Pos, $FieldPos, $FieldLen, $Offset, $dim_inner); // check for XLSX

		// dims must be sorted in reverse order of location
		$result = array();
		if ($dim_shape!==false)   $result[$dim_shape['wb']] = $dim_shape;
		if ($dim_inner!==false)   $result[$dim_inner['wb']] = $dim_inner;
		if ($dim_drawing!==false) $result[$dim_drawing['wb']] = $dim_drawing;
		krsort($result);
		
		return $result;
		
	}

	// Found the attributes for the image dimensions, in an ODF file
	function TbsPicGetDim_Any($Txt, $Pos, $Forward, $FieldPos, $FieldLen, $Offset, $Element, $AttW, $AttH, $AllowedDec, $CoefToPt, $IgnoreIfAtt) {

		while (true) {

			$p = clsTinyButStrong::f_Xml_FindTagStart($Txt, $Element, true, $Pos, $Forward, true);
			if ($p===false) return false;

			$pe = strpos($Txt, '>', $p);
			if ($pe===false) return false;

			$x = substr($Txt, $p, $pe -$p);

			if ( ($IgnoreIfAtt===false) || (strpos($x, $IgnoreIfAtt)===false) ) {

				$att_lst = array('w'=>$AttW, 'h'=>$AttH);
				$res_lst = array();

				foreach ($att_lst as $i=>$att) {
						$l = strlen($att);
						$b = strpos($x, $att);
						if ($b===false) return false;
						$b = $b + $l;
						$e = strpos($x, '"', $b);
						$e2 = strpos($x, ';', $b); // in case of VML format, width and height are styles separted by ;
						if ($e2!==false) $e = min($e, $e2);
						if ($e===false) return false;
						$lt = $e - $b;
						$t = substr($x, $b, $lt);
						$pu = $lt; // unit first char
						while ( ($pu>1) && (!is_numeric($t[$pu-1])) ) $pu--;
						$u = ($pu>=$lt) ? '' : substr($t, $pu);
						$v = floatval(substr($t, 0, $pu));
						$beg = $Offset+$p+$b;
						if ($beg>$FieldPos) $beg = $beg - $FieldLen;
						$res_lst[$i.'b'] = $beg; // start position in the main string
						$res_lst[$i.'l'] = $lt; // length of the text
						$res_lst[$i.'u'] = $u; // unit
						$res_lst[$i.'v'] = $v; // value
						$res_lst[$i.'t'] = $t; // text
						$res_lst[$i.'o'] = 0; // offset
				}

				$res_lst['r'] = ($res_lst['hv']==0) ? 0.0 : $res_lst['wv']/$res_lst['hv']; // ratio W/H
				$res_lst['dec'] = $AllowedDec; // save the allowed decimal for this attribute
				$res_lst['cpt'] = $CoefToPt;
				return $res_lst;

			} else {

				// Next try
				$Pos = $p + (($Forward) ? +1 : -1);

			}

		}

	}

	// Get Dim in an OpenXML Drawing (pictures in an XLSX)
	function TbsPicGetDim_Drawings($Txt, $Pos, $FieldPos, $FieldLen, $Offset, $dim_inner) {

		// The <a:ext> coordinates must have been found previously.
		if ($dim_inner===false) return false;
		// The current file must be an XLSX drawing sub-file.
		if (strpos($this->TBS->OtbsCurrFile, 'xl/drawings/')!==0) return false;
		
		if ($Pos==0) {
			// The parent element has already been found
			$PosEl = 0;
		} else {
			// Found  parent element
			$loc = clsTbsXmlLoc::FindStartTag($Txt, 'xdr:twoCellAnchor', $Pos, false);
			if ($loc===false) return false;
			$PosEl = $loc->PosBeg;
		}
		
		$loc = clsTbsXmlLoc::FindStartTag($Txt, 'xdr:to', $PosEl, true);
		if ($loc===false) return false;
		$p = $loc->PosBeg;

		$res = array();

		$el_lst = array('w'=>'xdr:colOff', 'h'=>'xdr:rowOff');
		foreach ($el_lst as $i=>$el) {
			$loc = clsTbsXmlLoc::FindElement($Txt, $el, $p, true);
			if ($loc===false) return false;
			$beg =  $Offset + $loc->GetInnerStart();
			if ($beg>$FieldPos) $beg = $beg - $FieldLen;
			$val = $dim_inner[$i.'v'];
			$tval = $loc->GetInnerSrc();
			$res[$i.'b'] = $beg;
			$res[$i.'l'] = $loc->GetInnerLen();
			$res[$i.'u'] = '';
			$res[$i.'v'] = $val;
			$res[$i.'t'] = $tval;
			$res[$i.'o'] = intval($tval) - $val;
		}

		$res['r'] = ($res['hv']==0) ? 0.0 : $res['wv']/$res['hv']; // ratio W/H;
		$res['dec'] = 0;
		$res['cpt'] = 12700;

		return $res;

	}

	/**
	 * Return the path of the image on the server corresponding the current field being merged.
	 */
	function TbsPicExternalPath(&$Value, &$PrmLst) {
	
		$TBS = &$this->TBS;
	
		// set the path where files should be taken
		if (isset($PrmLst['from'])) {
			if (!isset($PrmLst['pic_prepared'])) $TBS->meth_Merge_AutoVar($PrmLst['from'],true); // merge automatic TBS fields in the path
			$FullPath = str_replace($TBS->_ChrVal,$Value,$PrmLst['from']); // merge [val] fields in the path
		} else {
			$FullPath = $Value;
		}
		if ( (!isset($PrmLst['pic_prepared'])) && isset($PrmLst['default']) ) $TBS->meth_Merge_AutoVar($PrmLst['default'],true); // merge automatic TBS fields in the path

		// check if the picture exists, and eventually use the default picture
		if (!file_exists($FullPath)) {
			if (isset($PrmLst['default'])) {
				$x = $PrmLst['default'];
				if ($x==='current') {
					return false;
				} elseif (file_exists($x)) {
					$FullPath = $x;
				} else {
					return $this->RaiseError('The default picture "'.$x.'" defined by parameter "default" of the field ['.$Loc->FullName.'] is not found.');
				}
			} else {
				return false;
			}
		}

		return $FullPath;
		
	}
	
	/**
	 * Add a picture inside the archive, use parameters 'from' and 'as'.
	 * Argument $Prm is only used for error messages.
	 */
	function TbsPicAdd(&$Value, &$PrmLst, &$Txt, &$Loc, $Prm) {
		
		$TBS = &$this->TBS;

		$PrmLst['pic_prepared'] = true; // mark the locator as Picture prepared
		
		$ExternalPath = $this->TbsPicExternalPath($Value, $PrmLst);
		
		if ($ExternalPath === false) {
			if (isset($PrmLst['att'])) {
				// can happen when using MergeField()
				unset($PrmLst['att']);
				$Value = '';
			} else {
				// parameter att already applied during Field caching
				$Value = substr($Txt, $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1);
			}
			return false;
		}

		// set the name of the internal file
		if (isset($PrmLst['as'])) {
			if (!isset($PrmLst['pic_prepared'])) $TBS->meth_Merge_AutoVar($PrmLst['as'],true); // merge automatic TBS fields in the path
			$InternalPath = str_replace($TBS->_ChrVal,$Value,$PrmLst['as']); // merge [val] fields in the path
		} else {
			// uniqueness by the name of the file, not its full path, this is a weakness
			// OpenXML does not support spaces and accents in internal file names.
			$x = basename($ExternalPath);
			if (!isset($this->ImageInternal[$x])) {
				$ext = $this->Misc_FileExt(basename($ExternalPath));
				$this->ImageInternal[$x] = 'opentbs_added_' . $this->ImageIndex . '.' . $ext;
				$this->ImageIndex++;
			}
			$InternalPath = $this->ImageInternal[$x];
		}

		// the value of the current TBS field becomes the full internal path
		if (isset($this->ExtInfo['pic_path'])) $InternalPath = $this->ExtInfo['pic_path'].$InternalPath;

		// actually add the picture inside the archive
		if ($this->FileGetIdxAdd($InternalPath)===false) $this->FileAdd($InternalPath, $ExternalPath, TBSZIP_FILE, true);

		// preparation for others file in the archive
		$Rid = false;
		if ($this->ExtType==='odf') {
			// OpenOffice document
			$this->OpenDoc_ManifestChange($InternalPath,'');
		} elseif ($this->ExtType==='openxml') {
			// Microsoft Office document
			$this->OpenXML_CTypesPrepareExt($InternalPath, '');
			$BackNbr = max(substr_count($TBS->OtbsCurrFile, '/') - 1, 0); // docx=>"media/img.png", xlsx & pptx=>"../media/img.png"
			$TargetDir = str_repeat('../', $BackNbr).'media/';
			$FileName = basename($InternalPath);
			$Rid = $this->OpenXML_Rels_AddNewRid($TBS->OtbsCurrFile, $TargetDir, $FileName);
		}

		// change the value of the field for the merging process
		if ($Rid===false) {
			$Value = $InternalPath;
		} else {
			$Value = $Rid; // the Rid is used instead of the file name for the merging
		}

		// Change the dimensions of the picture
		if (isset($Loc->otbsDim)) {
			$this->TbsPicAdjust($Txt, $Loc, $ExternalPath);
		}

		return true;

	}

	function TbsDocFieldPrepare(&$Txt, &$Loc) {
		
		if ($this->ExtEquiv === 'docx') {
			
			// Find the first <w:r> element of the Complexe Field
			$loc_beg = clsTbsXmlLoc::FindStartTagHavingAtt($Txt, 'w:fldCharType="begin"', $Loc->PosBeg, false); // find a <w:fldChar>
			if ($loc_beg === false) return;
			$loc_beg = clsTbsXmlLoc::FindStartTag($Txt, 'w:r', $loc_beg->PosBeg, false); // the <w:r> that contains the <w:fldChar>
			
			// Find the last <w:r> element of the Complexe Field
			$loc_end = clsTbsXmlLoc::FindStartTagHavingAtt($Txt, 'w:fldCharType="end"', $Loc->PosEnd, true); // find a <w:fldChar>
			if ($loc_end === false) return;
			$loc_end = clsTbsXmlLoc::FindElement($Txt, 'w:r', $loc_end->PosBeg, false); // the <w:r> that contains the <w:fldChar>

			// The penultimate <w:r> element is the latest calculated field. We use it for getting the formating text.
			$loc_wr = clsTbsXmlLoc::FindElement($Txt, 'w:r', $loc_end->PosBeg - 1, false);
			if ($loc_wr === false) {
				$x = '<w:r><w:t>DOCFIELD</w:t></w:r>';
			} else {
				// Delete the Complete Field element if any (should not)
				$x = $loc_wr->GetSrc();
				$this->XML_DeleteElements($x, array('w:instrText', 'w:fldChar'));
				// Replace text
				$lz = clsTbsXmlLoc::FindElement($x, 'w:t', 0);
				if ($lz === false) {
					// Not found => we create a new text before the closing tag
					$x = substr_replace($x, '<w:t>DOCFIELD</w:t>', -6, 0);
				} else {
					$lz->ReplaceInnerSrc('DOCFIELD');
				}
			}
			
			// Replace template
			$len = $loc_end->PosEnd - $loc_beg->PosBeg + 1;
			$Txt = substr_replace($Txt, $x, $loc_beg->PosBeg, $len);
			
			// Move the locator
			$p = strpos($x, '>DOCFIELD<') + 1;
			$Loc->PosBeg = $loc_beg->PosBeg + $p;
			$Loc->PosEnd = $Loc->PosBeg + strlen('DOCFIELD') - 1;
			
		} elseif ($this->ExtEquiv === 'odt') {
			
			$loc_el = clsTbsXmlLoc::FindStartTagByPrefix($Txt, '', $Loc->PosBeg, false);
			if ( ($loc_el !== false) && ($loc_el->Name === 'text:conditional-text') ) {
				$loc_el->FindEndTag();
				$Loc->PosBeg = $loc_el->PosBeg;
				$Loc->PosEnd = $loc_el->PosEnd; 
			}
			
		}
		
	}

	// Adjust the dimensions if the picture
	function TbsPicAdjust(&$Txt, &$Loc, &$File) {

		$fDim = @getimagesize($File); // file dimensions
		if (!is_array($fDim)) return;
		$w = (float) $fDim[0];
		$h = (float) $fDim[1];
		$r = ($w/$h);
		$delta = 0;
		$adjust = $Loc->PrmLst['adjust'];
		if ( (!is_string($adjust)) || ($adjust=='') ) $adjust = 'inside';
		if (strpos($adjust, '%')!==false) {
			$adjust_coef = floatval(str_replace('%','',$adjust))/100.0;
			$adjust = '%';
		}

		// Save position of the locator before dims are modified
		if (!isset($Loc->svPosBeg)) {
			$Loc->svPosBeg = $Loc->PosBeg;
			$Loc->svPosEnd = $Loc->PosEnd;
		}

		foreach ($Loc->otbsDim as $tDim) { // template dimensions. They must be sorted in reverse order of location
			if ($tDim!==false) {
				// find what dimensions should be edited
				if ($adjust=='%') {
					if ($tDim['wb']>$tDim['hb']) { // the last attribute must be processed first
						$edit_lst = array('w' =>  $adjust_coef * $w, 'h' =>  $adjust_coef * $h );
					} else {
						$edit_lst = array('h' =>  $adjust_coef * $h, 'w' =>  $adjust_coef * $w );
					}
				} elseif ($adjust=='samewidth') {
					$edit_lst = array('h' => $tDim['wv'] * $h / $w );
				} elseif ($adjust=='sameheight') {
					$edit_lst = array('w' =>  $r * $tDim['hv'] );
				} else { // default value
					if ($tDim['r']>=$r) {
						$edit_lst = array('w' =>  $r * $tDim['hv'] ); // adjust width
					} else {
						$edit_lst = array('h' => $tDim['wv'] * $h / $w ); // adjust height
					}
				}
				// edit dimensions
				foreach ($edit_lst as $what=>$new) {
					$beg  = $tDim[$what.'b'];
					$len  = $tDim[$what.'l'];
					$unit = $tDim[$what.'u'];
					if ($adjust=='%') {
						if ($tDim['cpt']!==false) $new = $new * $tDim['cpt']; // apply the coef to Point conversion if any
						if ($unit!=='') { // force unit to pt, if units are allowed
							$unit = 'pt';
						}
					}
					$new = $new + $tDim[$what.'o']; // add the offset (xlsx only)
					$new = number_format($new, $tDim['dec'], '.', '').$unit;
					$Txt = substr_replace($Txt, $new, $beg, $len);
					if ($Loc->PosBeg>$beg) $delta = $delta + strlen($new) - $len;
				}
			}
		}

		// Update the position
		$Loc->PosBeg = $Loc->svPosBeg + $delta;
		$Loc->PosEnd = $Loc->svPosEnd + $delta;

	}
	
	/**
	 * Search 1 or 2 strings in a list if several sub-file in the archive.
	 *
	 * @param string|array $files An associated array of sub-files to scann or a pattern using 1 wildcard '*'.
	 * @param string|array $str   The strings that all be prensents in the content of the file.
	 *                            It can be a array of strings, or a single string.
	 * @param boolean             $returnFirstFind  true to return only the first record fund.
	 *
	 * @return array Return a single record or a recordset structured like: array('key'=>, 'idx'=>, 'src'=>, 'pos'=>, 'curr'=>)
	 */
	function TbsSearchInFiles($files, $str, $returnFirstFound = true) {

		// Prepare variables
	
		if (is_string($str)) {
			$str = array($str);
		}
	
		$keys_todo = array(); // list of keys that remains to be done
		$idx_keys = array();  // trancoding idx to key
		if (is_array($files)) {
			// A list of files given => transform the list of files into a list of available idx
			foreach($files as $k => $f) {
				$idx = $this->FileGetIdx($f);
				if ($idx!==false) {
					$keys_todo[$k] = $idx;
					$idx_keys[$idx] = $k;
				}
			}
		} else {
			// A string is given => 
			$parts = explode('*', $files);
			$last = count($parts) - 1;
			if ($last == 0) {
				$parts[1] = '';
				$last = 1;
			}
			$len_0 = strlen($parts[0]);
			$len_1 = strlen($parts[1]);
			foreach ($this->CdFileByName as $f => $idx) {
				if ( ($len_0 == 0) || (substr($f, 0, $len_0) == $parts[0]) ) {
					if ( ($len_1 == 0) || (substr($f, -$len_1) == $parts[1]) ) {
						$keys_todo[$f] = $idx;
						$idx_keys[$idx] = $f;
					}
				}
			}
		}

		// Start search
		
		$result = array();
		
		// Search in the current sub-file
		if ( ($this->TbsCurrIdx!==false) && isset($idx_keys[$this->TbsCurrIdx]) ) {
			$key = $idx_keys[$this->TbsCurrIdx];
			$p = true;
			foreach ($str as $s) {
				if ($p !== false) {
					$p = strpos($this->TBS->Source, $s);
				}
			}
			if ($p !== false) {
				$result[] = array('key' => $key, 'idx' => $this->TbsCurrIdx, 'src' => &$this->TBS->Source, 'pos' => $p, 'curr'=>true);
				if ($returnFirstFound) return $result[0];
			}
			unset($keys_todo[$key]);
		}

		// Search in the store
		foreach($this->TbsStoreLst as $idx => $info) {
			if ( ($idx!==$this->TbsCurrIdx) && isset($idx_keys[$idx]) ) {
				$key = $idx_keys[$idx];
				$p = true;
				foreach ($str as $s) {
					if ($p !== false) {
						$p = strpos($info['src'], $s);
					}
				}
				if ($p !== false) {
					$result[] = array('key' => $key, 'idx' => $idx, 'src' => &$info['src'], 'pos' => $p, 'curr'=>false);
					if ($returnFirstFound) return $result[0];
				}
				unset($keys_todo[$key]);
			}
		}

		// Search in other sub-files (never opened)
		foreach ($keys_todo as $key => $idx) {
			$txt = $this->FileRead($idx);
			$p = true;
			foreach ($str as $s) {
				if ($p !== false) {
					$p = strpos($txt, $s);
				}
			}
			if ($p !== false) {
				$result[] = array('key' => $key, 'idx' => $idx, 'src' => $txt, 'pos' => $p, 'curr'=>false);
				if ($returnFirstFound) return $result[0];
			}
		}

		if ($returnFirstFound) {
			return  array('key'=>false, 'idx'=>false, 'src'=>false, 'pos'=>false, 'curr'=>false);
		} else {
			return $result;
		}

	}

	// Check after the sheet process
	function TbsSheetCheck() {
		if (count($this->OtbsSheetSlidesDelete)>0) $this->RaiseError("Unable to delete the following sheets because they are not found in the workbook: ".(str_replace(array('i:','n:'),'',implode(', ',$this->OtbsSheetSlidesDelete))).'.');
		if (count($this->OtbsSheetSlidesVisible)>0) $this->RaiseError("Unable to change visibility of the following sheets because they are not found in the workbook: ".(str_replace(array('i:','n:'),'',implode(', ',array_keys($this->OtbsSheetSlidesVisible)))).'.');
	}

	function TbsDeleteComments($MainTags, $CommFiles, $CommTags, $Inner) {

		$nbr = 0;

		// Retrieve the Comment sub-file (OpenXML only)
		if ($CommFiles!==false) {
			$Files = $this->OpenXML_MapGetFiles($CommFiles);
			foreach ($Files as $file) {
				$idx = $this->FileGetIdx($file);
				if ($idx!==false) {
					// Delete inner text of the comments to be sure that contents is deleted
					// we only empty the comment elements in case some comments are referenced in other special part of the document
					$Txt = $this->TbsStoreGet($idx, "Delete Comments");
					$nbr = $nbr + $this->XML_DeleteElements($Txt, $CommTags, $Inner);
					$this->TbsStorePut($idx, $Txt);
				}
			}
		}

		// Retrieve the Main sub-file
		if ($MainTags!==false) {
			$idx = $this->Ext_GetMainIdx();
			if ($idx===false) return false;
			// Delete Comment locators
			$Txt = $this->TbsStoreGet($idx, "Delete Comments");
			$nbr2 = $this->XML_DeleteElements($Txt, $MainTags);
			$this->TbsStorePut($idx, $Txt);
			if ($CommFiles===false) $nbr = $nbr2;
		}

		return $nbr;

	}

	/**
	 * Replace var fields in a Parameter of a block.
	 * @param  string $PrmVal The parameter value.
	 * @param  string $FldVal The value of the field that holds the value.
	 * @return string The merged value of the parameter.
	 */
	function TbsMergeVarFields($PrmVal, $FldVal) {
		if ($PrmVal === true) $PrmVal = ''; // TBS set the value to true if no value set, but it is converted into '1'.
		$this->TBS->meth_Merge_AutoVar($PrmVal, true);
		$PrmVal = str_replace($this->TBS->_ChrVal, $FldVal, $PrmVal);
		return $PrmVal;
	}

	function TbsDeleteColumns(&$Txt, $Value, $PrmLst, $PosBeg) {

		$ext = $this->ExtEquiv;
		if ($ext==='docx') {
			$el_table = 'w:tbl';
			$el_delete = array(
				array('p'=>'w:tblGrid', 'c'=>'w:gridCol', 's'=>false),
				array('p'=>'w:tr', 'c'=>'w:tc', 's'=>false),
			);
		} elseif ($ext==='odt') {
			$el_table = 'table:table';
			$el_delete = array(
				array('p'=>false, 'c'=>'table:table-column', 's'=>'table:number-columns-repeated'),
				array('p'=>'table:table-row', 'c'=>'table:table-cell', 's'=>false),
			);
		} else {
			return false;
		}
		
		if (is_array($Value)) $Value = implode(',', $Value);

		// Column set
		$shift_ok = false;
		if (isset($PrmLst['colset'])) {
			$col_set = str_replace(' ', '', $PrmLst['colset']);
			$col_set = explode('|', $col_set);
			$col_lst = array();
			$val_lst = explode(',', $Value);
			foreach ($val_lst as $s) {
				$idx = intval($s) - 1;
				if (isset($col_set[$idx])) {
					$col_lst[] = $col_set[$idx];
				}
			}
			$col_lst = implode(',', $col_lst);
		} elseif (isset($PrmLst['colnum'])) {
			// Retreive the list of columns id to delete
			$col_lst = $this->TbsMergeVarFields($PrmLst['colnum'], $Value); // prm equal to true if value is not given
			$shift_ok = true;
		} else {
			$col_lst = $Value;
			$shift_ok = true;
		}
		
		// Convert the colmun list into an array
		$col_lst = str_replace(' ', '', $col_lst);
		if ( ($col_lst=='') || ($col_lst=='0') ) return false; // there is nothing to do
		$col_lst = explode(',', $col_lst);
		$col_nbr = count($col_lst);
		for ($c = 0; $c < $col_nbr; $c++) {
			// In cas of range separator
			$x = explode('-', $col_lst[$c]);
			$x0 = intval($x[0]);
			$col_lst[$c] = $x0;
			// Range separator
			if (isset($x[1])) {
				$x1 = intval($x[1]);
				for ($i = $x0 + 1 ; $i <= $x1 ; $i++) {
					$col_lst[] = $i; // added after the existing values
				}
			}
		}
		
		// Add columns by shifting
		if ($shift_ok && isset($PrmLst['colshift'])) {
			$col_shift = intval($this->TbsMergeVarFields($PrmLst['colshift'], $Value));
			if ($col_shift<>0) {
				$step = ($col_shift>0) ? -1 : +1;
				for ($s = $col_shift; $s<>0; $s = $s + $step) {
					for ($c=0; $c<$col_nbr; $c++) $col_lst[] = $col_lst[$c] + $s;
				}
			}
		}

		// prepare column info
		$col_lst = array_unique($col_lst, SORT_NUMERIC); // Delete duplicated columns
		sort($col_lst, SORT_NUMERIC); // Sort colmun id in order
		$col_max = $col_lst[(count($col_lst)-1)]; // Last column to delete
		
		// Delete impossible col num (like zero)
		while ( (count($col_lst) > 0) && ($col_lst[0] <= 0) ) {
			array_shift($col_lst);
		}
		if (count($col_lst) == 0) return false;
		
		// Look for the source of the table
		$Loc = clsTbsXmlLoc::FindElement($Txt, $el_table, $PosBeg, false);
		if ($Loc===false) return false;

		$Src = $Loc->GetSrc();

		foreach ($el_delete as $info) {
			if ($info['p']===false) {
				$this->XML_DeleteColumnElements($Src, $info['c'], $info['s'], $col_lst, $col_max);
			} else {
				$ParentPos = 0;
				while ($ParentLoc = clsTbsXmlLoc::FindElement($Src, $info['p'], $ParentPos, true)) {
					$ParentSrc = $ParentLoc->GetSrc();
					$ModifNbr = $this->XML_DeleteColumnElements($ParentSrc, $info['c'], $info['s'], $col_lst, $col_max);
					if ($ModifNbr>0) $ParentLoc->ReplaceSrc($ParentSrc);
					$ParentPos = $ParentLoc->PosEnd + 1;
				}
			}
		}

		$Loc->ReplaceSrc($Src);

	}

	/**
	 * Delete or Display a Sheet or a Slide according to its numbre or its name
	 * @param $id_or_name Id or Name of the Sheet/Slide
	 * @param $ok         true to Keep or Display, false to Delete or Hide
	 * @param $delete     true to Delete/Keep, false to Display/Hide
	 */
	function TbsSheetSlide_DeleteDisplay($id_or_name, $ok, $delete) {

		if (is_null($ok)) $ok = true; // default value


		$ext = $this->ExtEquiv;

		$ok = (boolean) $ok;
		if (!is_array($id_or_name)) $id_or_name = array($id_or_name);

		foreach ($id_or_name as $item=>$action) {
			if (!is_bool($action)) {
				$item = $action;
				$action = $ok;
			}
			$item_ref = (is_string($item)) ? 'n:'.htmlspecialchars($item) : 'i:'.$item; // help to make the difference beetween id and name
			if ($delete) {
				if ($ok) {
					$this->OtbsSheetSlidesDelete[$item_ref] = $item;
				} else {
					unset($this->OtbsSheetSlidesVisible[$item_ref]);
				}
			} else {
				$this->OtbsSheetSlidesVisible[$item_ref] = $ok;
			}
		}

	}

	/**
	 * Prepare the locator for merging cells.
	 */
	function TbsPrepareMergeCell(&$Txt, &$Loc) {
		if ($this->ExtEquiv=='docx') {
			// Move the locator just inside the <w:tcPr> element.
			// See OnOperation() for other process
			$xml = clsTbsXmlLoc::FindStartTag($Txt, 'w:tcPr', $Loc->PosBeg, false);
			if ($xml) {
				$Txt = substr_replace($Txt, '', $Loc->PosBeg, $Loc->PosEnd - $Loc->PosBeg + 1);
				$Loc->PosBeg = $xml->PosEnd+1;
				$Loc->PosEnd = $xml->PosEnd;
				$this->PrevVals[$Loc->FullName] = ''; // the previous value is saved in property because they can be several sections, and thus several Loc for the same column.
				//$Loc->Prms['strconv']='no'; // should work
				$Loc->ConvStr=false;
			}
		}
	}


	
	/**
	 * Actualize property ExtInfo (Extension Info).
	 * ExtInfo will be an array with keys 'load', 'br', 'ctype' and 'pic_path'. Keys 'rpl_what' and 'rpl_with' are optional.
	 *  load:     files in the archive to be automatically loaded by OpenTBS when the archive is loaded. Separate files with comma ';'.
	 *  br:       string that replace break-lines in the values merged by TBS, set to false if no conversion.
	 *  frm:      format of the file ('odf' or 'openxml'), for now it is used only to activate a special feature for openxml files
	 *  ctype:    (optional) the Content-Type header name that should be use for HTTP download. Omit or set to '' if not specified.
	 *  pic_path: (optional) the folder nale in the archive where to place pictures
	 *  rpl_what: (optional) string to replace automatically in the files when they are loaded. Can be a string or an array.
	 *  rpl_with: (optional) to be used with 'rpl_what',  Can be a string or an array.
	 * User can define his own Extension Information, they are taken in acount if saved int the global variable $_OPENTBS_AutoExt.
	 */
	function Ext_PrepareInfo($Ext=false) {

		$this->ExtEquiv = false;
		$this->ExtType = false;
	
		if ($Ext===false) {
			// Get the extension of the current archive
			if ($this->ArchIsStream) {
				$Ext = '';
			} else {
				$Ext = basename($this->ArchFile);
				$p = strrpos($Ext, '.');
				$Ext = ($p===false) ? '' : strtolower(substr($Ext, $p + 1));
			}
			$Frm = $this->Ext_DeductFormat($Ext, true); // may change $Ext
			// Rename the name of the phantom file if it is a stream
			if ( $this->ArchIsStream && (strlen($Ext)>2) ) $this->ArchFile = str_replace('.zip', '.'.$Ext, $this->ArchFile);
		} else {
			// The extension is forced
			$Frm = $this->Ext_DeductFormat($Ext, false); // may change $Ext
		}

		$TBS = &$this->TBS;
		$set_option = method_exists($TBS, 'SetOption');
		
		$i = false;
		$block_alias = false;
		
		if (isset($GLOBAL['_OPENTBS_AutoExt'][$Ext])) {
			// User defined information
			$i = $GLOBAL['_OPENTBS_AutoExt'][$Ext];
			if (isset($i['equiv'])) $this->ExtEquiv = $i['equiv'];
			if (isset($i['frm'])) $this->ExtType = $i['frm'];
		} elseif ($Frm==='odf') {
			// OpenOffice & LibreOffice documents
			$i = array('main' => 'content.xml', 'br' => '<text:line-break/>', 'ctype' => 'application/vnd.oasis.opendocument.', 'pic_path' => 'Pictures/', 'rpl_what' => '&apos;', 'rpl_with' => '\'');
			if ($this->FileExists('styles.xml')) $i['load'] = array('styles.xml'); // styles.xml may contain header/footer contents
			if ($Ext==='odf') $i['br'] = false;
			if ($Ext==='odm') $this->ExtEquiv = 'odt';
			if ($Ext==='ots') $this->ExtEquiv = 'ods';
			$this->ExtType = 'odf';
			$ctype = array('t' => 'text', 's' => 'spreadsheet', 'g' => 'graphics', 'f' => 'formula', 'p' => 'presentation', 'm' => 'text-master');
			$i['ctype'] .= $ctype[($Ext[2])];
			$i['pic_ext'] = array('png' => 'png', 'bmp' => 'bmp', 'gif' => 'gif', 'jpg' => 'jpeg', 'jpeg' => 'jpeg', 'jpe' => 'jpeg', 'jfif' => 'jpeg', 'tif' => 'tiff', 'tiff' => 'tiff');
			$block_alias = array(
				'tbs:p' => 'text:p',              // ODT+ODP
				'tbs:title' => 'text:h',          // ODT+ODP
				'tbs:section' => 'text:section',  // ODT
				'tbs:table' => 'table:table',     // ODT (sheet for ODS)
				'tbs:row' => 'table:table-row',   // ODT+ODS
				'tbs:cell' => 'table:table-cell', // ODT+ODS
				'tbs:comment' => 'office:annotation',
				'tbs:page' => array(&$this, 'OpenDoc_GetPage'), // ODT
				'tbs:slide' => 'draw:page',       // ODP
				'tbs:sheet' => 'table:table',     // ODS (table for ODT)
				'tbs:draw' => array(&$this, 'OpenDoc_GetDraw'),
				'tbs:drawgroup' => 'draw:g',
				'tbs:drawitem' => array(&$this, 'OpenDoc_GetDraw'),
				'tbs:listitem' => 'text:list-item', // ODT+ODP
			);
			if ($set_option) {
				$TBS->SetOption('parallel_conf', 'tbs:table', 
					array(
						'parent' => 'table:table',
						'ignore' => array('table:covered-table-cell', 'table:table-header-rows'),
						'cols' => array('table:table-column' => 'table:number-columns-repeated'),
						'rows' => array('table:table-row'),
						'cells' => array('table:table-cell' => 'table:number-columns-spanned'),
					)
				);
			}
		} elseif ($Frm==='openxml') {
			// Microsoft Office documents
			$this->OpenXML_MapInit();
			if ($TBS->OtbsConvertApostrophes) {
				$x = array(chr(226) . chr(128) . chr(152), chr(226) . chr(128) . chr(153));
			} else {
				$x = null;
			}
			$ctype = 'application/vnd.openxmlformats-officedocument.';
			if ( ($Ext==='docx') || ($Ext==='docm') ) {
				// Notes: (1) '<w:br/>' works but '</w:t><w:br/><w:t>' enforce compatibility with Libre Office. (2) Line-breaks merged in attributes will corrupt the DOCX anyway.
				$i = array('br' => '</w:t><w:br/><w:t>', 'ctype' => $ctype . 'wordprocessingml.document', 'pic_path' => 'word/media/', 'rpl_what' => $x, 'rpl_with' => '\'', 'pic_entity'=>'w:drawing');
				if ($Ext==='docm') $i['ctype'] = 'application/vnd.ms-word.document.macroEnabled.12';
				$i['main'] = $this->OpenXML_MapGetMain('wordprocessingml.document.main+xml', 'word/document.xml');
				$i['load'] = $this->OpenXML_MapGetFiles(array('wordprocessingml.header+xml', 'wordprocessingml.footer+xml'));
				$this->ExtEquiv = 'docx';
				$block_alias = array(
					'tbs:p' => 'w:p',
					'tbs:title' => 'w:p',
					'tbs:section' => array(&$this, 'MsWord_GetSection'),
					'tbs:table' => 'w:tbl',
					'tbs:row' => 'w:tr',
					'tbs:cell' => 'w:tc',
					'tbs:page' => array(&$this, 'MsWord_GetPage'),
					'tbs:draw' => array(&$this, 'MsWord_GetDraw'),
					'tbs:drawgroup' => array(&$this, 'MsWord_GetDraw'),
					'tbs:drawitem' => 'wps:wsp',
					'tbs:listitem' => 'w:p',
				);  
				if ($set_option) {
					$TBS->SetOption('parallel_conf', 'tbs:table', 
						array(
							'parent' => 'w:tbl',
							'ignore' => array('w:tblPr', 'w:tblGrid'),
							'cols' => array('w:gridCol' => ''),
							'rows' => array('w:tr'),
							'cells' => array('w:tc' => ''), // <w:gridSpan w:val="2"/>
						)
					);
				}
			} elseif ( ($Ext==='xlsx') || ($Ext==='xlsm')) {
				$this->MsExcel_DeleteCalcChain();
				$i = array('br' => false, 'ctype' => $ctype . 'spreadsheetml.sheet', 'pic_path' => 'xl/media/', 'pic_entity'=>'xdr:twoCellAnchor');
				if ($Ext==='xlsm') $i['ctype'] = 'application/vnd.ms-excel.sheet.macroEnabled.12';
				$i['main'] = $this->OpenXML_MapGetMain('spreadsheetml.worksheet+xml', 'xl/worksheets/sheet1.xml');
				$this->ExtEquiv = 'xlsx';
				$block_alias = array(
					'tbs:row' => 'row',
					'tbs:cell' => 'c',
					'tbs:draw' => 'xdr:twoCellAnchor',
					'tbs:drawgroup' => 'xdr:twoCellAnchor',
					'tbs:drawitem' => 'xdr:sp',
				);
			} elseif ( ($Ext==='pptx') || ($Ext==='pptm') ){
				$i = array('br' => false, 'ctype' => $ctype . 'presentationml.presentation', 'pic_path' => 'ppt/media/', 'rpl_what' => $x, 'rpl_with' => '\'', 'pic_entity'=>'p:pic');
				if ($Ext==='pptm') $i['ctype'] = 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
				$this->MsPowerpoint_InitSlideLst();
				$i['main'] = (isset($this->OpenXmlSlideLst[0])) ? $this->OpenXmlSlideLst[0]['file'] : 'ppt/slides/slide1.xml';
				$i['load'] = $this->OpenXML_MapGetFiles(array('presentationml.notesSlide+xml')); // auto-load comments
				$this->ExtEquiv = 'pptx';
				$block_alias = array(
					'tbs:p' => 'a:p',
					'tbs:title' => 'a:p',
					'tbs:table' => 'a:tbl',
					'tbs:row' => 'a:tr',
					'tbs:cell' => 'a:tc',
					'tbs:draw' => 'p:sp',
					'tbs:drawgroup' => 'p:grpSp',
					'tbs:drawitem' => 'p:sp',
					'tbs:listitem' => 'a:p',
				);
			}
			$i['pic_ext'] = array('png' => 'png', 'bmp' => 'bmp', 'gif' => 'gif', 'jpg' => 'jpeg', 'jpeg' => 'jpeg', 'jpe' => 'jpeg', 'tif' => 'tiff', 'tiff' => 'tiff', 'ico' => 'x-icon', 'svg' => 'svg+xml');
		}

		if ($i!==false) {
			$i['ext'] = $Ext;
			if (!isset($i['load'])) $i['load'] = array();
			$i['load'][] = $i['main']; // add to main file at the end of the files to load
		}
		  
		if ($set_option && ($block_alias!==false)) $TBS->SetOption('block_alias', $block_alias);

		$this->ExtInfo = $i;
		if ($this->ExtEquiv===false) $this->ExtEquiv = $Ext;
		if ($this->ExtType===false)  $this->ExtType = $Frm;
		return (is_array($i)); // return true if the extension is supported
	}

	// Return the type of document corresponding to the given extension.
	function Ext_DeductFormat(&$Ext, $Search) {
		if (strpos(',odt,ods,odg,odf,odp,odm,ott,ots,otg,otp,', ',' . $Ext . ',') !== false) return 'odf';
		if (strpos(',docx,docm,xlsx,xlsm,pptx,pptm,', ',' . $Ext . ',') !== false) return 'openxml';
		if (!$Search) return false;
		if ($this->FileExists('content.xml')) {
			// OpenOffice documents
			if ($this->FileExists('META-INF/manifest.xml')) {
				$Ext = '?'; // not needed for processing OpenOffice documents
				return 'odf';
			}
		} elseif ($this->FileExists('[Content_Types].xml')) {
			// Ms Office documents
			if ($this->FileExists('word/document.xml')) {
				$Ext = 'docx';
				return 'openxml';
			} elseif ($this->FileExists('xl/workbook.xml')) {
				$Ext = 'xlsx';
				return 'openxml';
			} elseif ($this->FileExists('ppt/presentation.xml')) {
				$Ext = 'pptx';
				return 'openxml';
			}
		}
		return false;
	}

	// Return the idx of the main document, if any.
	function Ext_GetMainIdx() {
		if ( ($this->ExtInfo!==false) && isset($this->ExtInfo['main']) ) {
			return $this->FileGetIdx($this->ExtInfo['main']);
		} else {
			return false;
		}
	}

	/**
     * Search the next tag of the asked type searching forward. (Not specific to MsWord, works for any XML)
	 * @param string  $Txt
	 * @param string  $Tag     must be prefixed with '<' or '</'.
	 * @param integer $PosBeg 
	 * @return integer|false
	 */
	function XML_SearchTagForward($Txt, $Tag, $PosBeg) {
		$len = strlen($Tag);
		$p = $PosBeg;
		while ($p!==false) {
			$p = strpos($Txt, $Tag, $p);
			if ($p===false) return false;
			$x = substr($Txt, $p+$len, 1);
			if (($x===' ') || ($x==='/') || ($x==='>') ) {
				return $p;
			} else {
				$p = $p+$len;
			}
		}
		return false;
	}
	
	/**
	 * Delete all tags of the types given in the list.
	 * @param {string} $Txt The text content to search into.
	 * @param {array} $TagLst List of tag names to delete.
	 * @param {boolean} $OnlyInner Set to true to keep the content inside the element. Set to false to delete the entire element. Default is false.
	 */
	function XML_DeleteElements(&$Txt, $TagLst, $OnlyInner=false) {
		$nb = 0;
		$Content = !$OnlyInner;
		foreach ($TagLst as $tag) {
			$p = 0;
			while ($x = clsTbsXmlLoc::FindElement($Txt, $tag, $p)) {
				$x->Delete($Content);
				$p = $x->PosBeg;
				$nb++;
			}
		}
		return $nb;
	}

	/**
	 * Delete all column elements  according to their position.
	 * Return the number of deleted elements.
	 */
	function XML_DeleteColumnElements(&$Txt, $Tag, $SpanAtt, $ColLst, $ColMax) {

		$ColNum = 0;
		$ColPos = 0;
		$ColQty = 1;
		$Continue = true;
		$ModifNbr = 0;

		while ($Continue && ($Loc = clsTbsXmlLoc::FindElement($Txt, $Tag, $ColPos, true)) ) {

			// get colmun quantity covered by the element (1 by default)
			if ($SpanAtt!==false) {
				$ColQty = $Loc->GetAttLazy($SpanAtt);
				$ColQty = ($ColQty===false) ? 1 : intval($ColQty);
			}
			// count column to keep
			$KeepQty = 0;
			for ($i=1; $i<=$ColQty ;$i++) {
				if (array_search($ColNum+$i, $ColLst)===false) $KeepQty++;
			}
			if ($KeepQty==0) {
				// delete the tag
				$Loc->ReplaceSrc('');
				$ModifNbr++;
			} else {
				if ($KeepQty!=$ColQty) {
					// edit the attribute
					$Loc->ReplaceAtt($SpanAtt, $KeepQty);
					$ModifNbr++;
				}
				$ColPos = $Loc->PosEnd + 1;
			}

			$ColNum += $ColQty;
			if ($ColNum>$ColMax) $Continue = false;
		}

		return $ModifNbr;

	}

	/**
	 * Change an attribute's value or an entity's value in the first element in a given sub-file.
	 * @param {mixed}  $SubFile : the name or the index of the sub-file. Use value false to get the current sub-file.
	 * @param {string} $ElPath  : path of the element. For example : 'w:document/w:body/w:p'.
	 * @param {string|boolean} $Att    : the attribute, or false to replace the entity's value.
	 * @param {string|boolean} $NewVal : the new value, or false to delete the attribute.
	 * @return {boolean} True if the attribute is found and processed. False otherwise.
	 */
	function XML_ForceAtt($SubFile, $ElPath, $Att, $NewVal, $AddElIfMissing = false) {
	
		// Find the file
		$idx = $this->FileGetIdx($SubFile);
		if ($idx === false) return false;
		$Txt = $this->TbsStoreGet($idx, 'XML_ForceAtt');
	
		// Find the element
		$el_lst = explode('/', $ElPath);
		$p = 0;
		$el_idx = 0;
		$el_nb = count($el_lst);
		$end = $el_nb;
		$loc = false;
		$loc_prev = false;
		while ($el_idx < $end) {
			$loc_prev = $loc;
			$loc = clsTbsXmlLoc::FindStartTag($Txt, $el_lst[$el_idx], $p);
			if ($loc === false) {
				if ($AddElIfMissing) {
					// stop the loop
					$end = $el_idx;
				} else {
					return false;
				}
			} else {
				$p = $loc->PosEnd;
				$el_idx++;
			}
		}

		if (($loc === false) && ($loc_prev === false)) return false;

		$save = true;
		if ($el_idx < $el_nb) {
			// One of the entities is not found => create entities
			if ($NewVal === false) {
				// Nothing to do
				$save = false;
			} else {
				$before = '';
				$after = '';
				$i_end = ($end - 1);
				for ($i = $el_idx ; $i < $i_end ; $i++) {
					$before .= '<' .  $el_lst[$i] . '>';
					$after = '</' .  $el_lst[$i] . '>' . $after;
				}
				if ($Att === false) {
					$x = $before . '<' . $el_lst[$i] . '>' . $NewVal . '</' . $el_lst[$i] . '>' . $after;
				} else {
					$x = $before . '<' . $el_lst[$i] . ' ' . $Att . '="' . $NewVal . '" />' . $after;
				}
				$loc_prev->FindEndTag();
				if ($loc_prev->pET_PosBeg === false) {
					return $this->RaiseError("Cannot apply attribute because entity '" . $loc_prev->FindName() . "' has no ending tag in file [$SubFile].");
				}
				$Txt = substr_replace($Txt, $x, $loc_prev->pET_PosBeg, 0);
			}
		} else {
			// The last entity is found => force the attribute
			if ($NewVal === false) {
				if ($Att === false) {
					// delete the entity
					$loc->Delete();
				} else {
					// delete the attribute
					$loc->DeleteAtt($Att);
				}
			} else {
				if ($Att === false) {
					// change the entity's value
					$loc->FindEndTag();
					$loc->ReplaceInnerSrc($NewVal);
				} else {
					// change the attribute's value
					$loc->ReplaceAtt($Att, $NewVal, true);
				}
			}
		}

		// Save the file
		if ($save) {
			$this->TbsStorePut($idx, $Txt);
		}

		return true;
		
	}
	
	/**
	 * Function used by Block Alias
	 * The first start tag on the left is supposed to be the good one.
	 * Note: encapsulation is not yet supported in this version.
	 */
	function XML_BlockAlias_Prefix($TagPrefix, $Txt, $PosBeg, $Forward, $LevelStop) {

		$loc = clsTbsXmlLoc::FindStartTagByPrefix($Txt, $TagPrefix, $PosBeg, false);

		if ($Forward) {
			$loc->FindEndTag();
			return $loc->PosEnd;
		} else {
			return $loc->PosBeg;
		}

	}

	/**
	 * Return the next cell of the range (actual or virtual). Return false if there is no more cells for this range in the sheet.
	 * The process assumes that :
	 * - trailing rows of the range can be misssing in the sheet but there is no missing row  between rows.
	 * - trailing cols of the range can be misssing in a row     but there is no missing cell between cells.
	 * If « $AddMissing = false » :
	 *   Cells of missing columns are return by the function as a valid object with the property « Exists = false ».
	 *   Cells of missing rows    are return by the function as false.
	 *   But missing cells are skiped in case of a range with with full columns.
	 *
     * @param string|object  $SheetLoc The locator of the sheet entity that directly contains row.
	 * @param array          $Range    A range info formated as array('cs'=>...,'rs'=>...,'ce'=>...,'re'=>...)
	 * @param object         $PrevLoc  The previous locator returned by the function.
	 * @param string         $RowEl    Name of the XML entity for rows.
	 * @param string         $CellEl   Name of the XML entity for cells.
	 * @param boolean        $AddMissRow True means that an empty row in inserted in order to finish the range visit.
	 *
	 * @return object The clsTbsXmlLoc object of the cell element, with extra properties info : cellCol, cellRow
	 *                Note that is can be a not existing item if the asked range goes out of the sheet.
	 */
	function XML_GetNextCellLoc(&$SheetLoc, $Range, $PrevLoc, $RowEl, $CellEl, $AttRowR, $AttCellR, $AddMissing) {
		
		$debug = false;
		// Retreive previous and current cell coordinates
		if ( $PrevLoc === false ) {
			$rowLoc = false;
			$currRow = 0;
			$currCol = 0;
			$targetRow = $Range['rs'];
			$targetCol = $Range['cs'];
			$currRowOk = true;
			$currColOk = true;
			$r_pos = 0;
			$c_pos = 0;
		} else {
			$repeat = false;
			$rowLoc = $PrevLoc->Parent;
			$currRow = $PrevLoc->cellRow + ($rowLoc->RepeatMax - $rowLoc->RepeatIdx);
			$currCol = $PrevLoc->cellCol;
			$targetCol = $currCol + 1;
			$same_row = ($targetCol <=  $Range['ce']);
			if ($Range['cfull'] && (!$PrevLoc->Exists)) {
				$same_row = false;
			}
			if ($same_row) {
				// we will search next cell in the same row
				$targetRow = $currRow;
				if ($PrevLoc->RepeatIdx < $PrevLoc->RepeatMax) {
					// the cell is repeated
					$PrevLoc->RepeatIdx++;
					$repeat = $PrevLoc;
				} elseif (isset($rowLoc->CellLst[$targetCol])) {
					// the row is repeated
					$repeat = $rowLoc->CellLst[$targetCol];
				} else {
					// no repeated
					$c_pos = $PrevLoc->PosEnd + 1;
					$currColOk = $PrevLoc->Exists;
				}
			} else {
				// we will search the first cell of the range in the next row
				$currCol = 0;
				$targetCol = $Range['cs'];
				$targetRow = $currRow + 1;
				if ($targetRow > $Range['re']) {
					return false;
				}
				// we look if the row is repeated
				if ($rowLoc->RepeatIdx < $rowLoc->RepeatMax) {
					$rowLoc->RepeatIdx++;
					$repeat = $rowLoc->CellLst[$targetCol];
				} else {
					$c_pos = 0;
					$currColOk = true;
				}
			}
			// Return the repeated cell if any
			if ($repeat !== false) {
				if (!isset($targetRow)) exit("\n oups");
				if ($debug) echo "\n* XML_GetNextCellLoc CELLULE REPETEE : targetRow = $targetRow, targetCol = $targetCol";
				$repeat->cellCol = $targetCol;
				$repeat->cellRow = $targetRow;
				return $repeat;
			}
			$currRowOk = $PrevLoc->RowOk;
			$r_pos = $PrevLoc->Parent->PosEnd + 1;
		}


		// Moves to the next cell

		if ($debug) echo "\n* XML_GetNextCellLoc : currRow = $currRow, currCol = $currCol | targetRow = $targetRow, targetCol = $targetCol | currRowOk = ".var_export($currRowOk,true).", currColOk = ".var_export($currColOk,true);
		
		// Reach the asked row 
		if ($debug) echo "\n  * Search Row : loop";
		while ( $currRowOk && ($currRow < $targetRow) ) {
			if ($debug) echo "\n  * Search Row #{$currRow}, r_pos=$r_pos : ";
			if ( ($rowLoc !== false) && ($rowLoc->RepeatIdx < $rowLoc->RepeatMax) ) {
				if ($debug) echo "ok REPEATED";
				// It is a repated row
				$rowLoc->RepeatIdx++;
				$currRow++;
			} else {
				$rowLoc = clsTbsXmlLoc::FindElement($SheetLoc, $RowEl, $r_pos, true);
				if ($rowLoc === false) {
					if ($debug) echo "FAIL row not found";
					$currRowOk = false;
				} else {
					if ($debug) echo "ok found";
					$r_pos = $rowLoc->PosEnd + 1;
					$currRow++;
					// Repeat info
					$rowLoc->RepeatIdx = 1;
					$rowLoc->RepeatMax = 1;
					$rowLoc->CellLst = array();
					if ($AttRowR) {
						$max = $rowLoc->GetAttLazy($AttRowR);
						if ($max !== false) {
							$rowLoc->RepeatMax = intval($max);
						}
					}
				}
			}
		}
		
		// Insert missing rows
		if ($debug) echo "\n  * Insert Row : check";
		if (!$currRowOk) {
			$currColOk = false;
			if ($AddMissing) {
				// Insert the empty rows
				$x = '<' . $RowEl . '></' . $RowEl . '>';
				$x_len = strlen($x);
				$nb = ($targetRow - $currRow);
				$SheetLoc->Txt = substr_replace($SheetLoc->Txt, str_repeat($x, $nb), $r_pos, 0);
				// The row locator must be targeted on the last inserted row
				$r_pos = $r_pos + ($nb - 1) * $x_len;
				$rowLoc = new clsTbsXmlLoc($SheetLoc->Txt, $RowEl, $r_pos, null, $SheetLoc, false);
				$rowLoc->FindEndTag();
			} else {
				// No more data
				return false;
			}
		}
		
		// Reach the asked cell 
		if ($debug) echo "\n  * Search Col : loop : currColOk=" . var_export($currColOk, true) . ", currCol=$currCol, targetCol=$targetCol";
		$cellLoc = false;
		while ($currColOk && ($currCol < $targetCol)) {
			if ($rowLoc === false) return $this->RaiseError("No parent row.");
			if (isset($rowLoc->CellLst[$currCol])) return $this->RaiseError("This repeated row should have been previsouly catched.");
			if ($debug) echo "\n  * Search Col (c_pos=$c_pos) : ";
			if ( ($cellLoc !== false) && ($cellLoc->RepeatIdx < $cellLoc->RepeatMax) ) {
				if ($debug) echo "ok REPEATED";
				// It is a repated row
				$cellLoc->RepeatIdx++;
				$currCol++;
			} else {
				$cellLoc = clsTbsXmlLoc::FindElement($rowLoc, $CellEl, $c_pos, true);
				if ($cellLoc === false) {
					if ($debug) echo "FAIL, rowLoc = " . $rowLoc->GetSrc();
					$currColOk = false;
				} else {
					if ($debug) echo "ok found";
					$c_pos = $cellLoc->PosEnd + 1;
					$currCol++;
					// Repeat info
					$cellLoc->RepeatIdx = 1;
					$cellLoc->RepeatMax = 1;
					if ($AttCellR) {
						$max = $cellLoc->GetAttLazy($AttCellR);
						if ($max !== false) {
							$cellLoc->RepeatMax = intval($max);
						}
					}
				}
			}
		}

		// Insert missing cells
		if ($debug) echo "\n  * Insert Cell : check";
		if (!$currColOk) {
			if ($AddMissing) {
				// Insert the empty cells
				$x = '<' . $CellEl . '></' . $CellEl . '>';
				$x_len = strlen($x);
				$nb = ($targetCol - $currCol);
				$rowLoc->AppendInnerSrc(str_repeat($x, $nb));
				// The cell locator must be targeted on the last inserted cell
				$cellLoc = new clsTbsXmlLoc($rowLoc->Txt, $CellEl, ($rowLoc->GetInnerAppendPos() - $x_len), null, $rowLoc, false);
				$cellLoc->FindEndTag();
			} else {
				// No more data => locator on a non-existing entity ($cellLoc->Exists = false)
				if ($debug) echo "\n* Insert Cell : create phantom cell";
				$cellLoc = clsTbsXmlLoc::CreatePhantomElement($rowLoc, $rowLoc->GetInnerAppendPos());
			}
			$cellLoc->RepeatIdx = 1;
			$cellLoc->RepeatMax = 1;
		}
			
		$cellLoc->RowOk   = $currRowOk; // true if the row did exists, false if the row has been added by the function 
		$cellLoc->cellRow = $targetRow; // row num in the sheet
		$cellLoc->cellCol = $targetCol; // col num in the sheet

		// If the row is repeated, then we save its childs
		if ( ($rowLoc->RepeatMax > 1) && ($rowLoc->RepeatIdx === 1) ) {
			$rowLoc->CellLst[$targetCol] = $cellLoc;
		}
		
		//echo "\n   rowLoc->GetSrc   = " . $rowLoc->GetSrc();
		//echo "\n   cellLoc = " . var_export($cellLoc, true);

		if ($debug) echo "\n  * Result cellLoc = ($targetRow, $targetCol)  RowOk=".var_export($currRowOk,true).", Exists=".var_export($cellLoc->Exists,true);
		if ($debug) echo "\n  * cellLoc->GetSrc  = " . $cellLoc->GetSrc();

		// In case of a full colmun range, we don't return the extra missing columns.
		// This means a valid row can have no cells.
		if ( $Range['cfull'] && (!$cellLoc->Exists) ) {
			if ($debug) echo " ... swicht to next cell";
			return $this->XML_GetNextCellLoc($SheetLoc, $Range, $cellLoc, $RowEl, $CellEl, $AttRowR, $AttCellR, $AddMissing);
		}
		
		return $cellLoc;
		
	}
	
	/**
	 * Return the column number from a cell reference. First colmun is number 1.
	 * Can also return the row number if asked.
	 * Return 0 is the column is not specified.
	 * Return '' for the row num if it is not specified.
	 *
	 * @param string  $CellRef  The reference of a cell. Like "B3" or "AZ48".
	 * @param boolean $WithRow  (optional) Use true in order to return both col and row numbers.
	 *
	 * @return integer|array|false  The column number, or an array with both the colum number and the row number.
	 */
	function Sheet_ColNum($CellRef, $WithRow = false) {

		$col = 0;
		$row = '';
		$rank = 0;
		$Prefix = '$'; // character prefix allowed before col and row values.
		
		// We read the string backward because that the only way to know the rank.
		for ($i = strlen($CellRef) -1 ; $i >= 0 ; $i--) {
			$l = $CellRef[$i];
			if ($l === $Prefix) {
			} elseif (is_numeric($l)) {
				$row = $l . $row; // backwards
			} else {
				$l = ord(strtoupper($l)) -64;
				if ($l>0 && $l<27) {
					$col = $col + $l*pow(26,$rank);
				} else {
					return false;
				}
				$rank++;
			} 
		}

		if ($WithRow) {
			return array($col, intval($row));
		} else {
			return $col;
		}

	}
	
	/**
	 * Return the reference of the cell, such as 'A10'.
	 * @param integer $Col  The column number (first is 1)
	 * @param integer $Row  The row    number (first is 1)
	 * @param string  $Char (optional) The prefix for col and row num.
	 * @return string
	 */
	function Sheet_CellRef($Col, $Row, $Char = '') {
		$r = '';
		$x = $Col;
		do {
			$x = $x - 1;
			$c = ($x % 26);
			$x = ($x - $c)/26;
			$r = chr(65 + $c) . $r; // chr(65)='A'
		} while ($x>0);
		return ($Char . $r . $Char . $Row);
	}

	/**
	 * Return the info of a range definition.
	 * Support both XLSX and ODS, range with or without sheet name, single cell range, full column range, full row range, absolute or relative cells, and multi-range.
	 *
	 * @param string $ref  The range reference.
	 * 
	 * XLSX :
	 * 'the_sheet_name'!$A1
	 * 'the_sheet_name'!$A$1:$B$2
	 * 'the_sheet_name'!$A$1:$B$2,'the_sheet_name2'!$A$1:$B$2
	 * ODS :
	 * Sheet5.C9
	 * $'the_sheet_name'.$A$1
	 * $'the_sheet_name'.$A$1:.$B$2
	 * $'the_sheet_name'.$A$1:'the_sheet_name'.$B$2
	 *
	 * forbidden chars in sheet name : XLSX = ": ' [  ]"  ; ODS = "[ ] * ? : / \ " or "'" as first char
	 * simple quotes are doubled
	 * the name can be delimited with "'" if there is any special char
	 * cell separator : XLSX = '!' ; ODS = "." (can be the first char if sheet name is ommited)
	 * range separator : XLSX = "," ; ODS = impossible
	 *
	 * @return array A recordset of info.
	 */
	function Sheet_GetRangeInfo($ref) {

		$delim = "'"; // Sheet name delimitor
		$rsep = ",";  // Range separator
		
		$result = array();

		$i_end =  strlen($ref) -1;
		$new = true;
		// I's easier to read backward since the range definition always ends with a cell reference
		for ($i = $i_end ; $i >= 0 ; $i--) {

			// Initialize a new range info
			if ($new) {
				$sheet = '';
				$cells = '';
				$in_delim = false; // true if we are inside the delimited string
				$is_cell = true;   // true if wee reading the cell part
				$is_xslx = false;  // true if is seems to be an XLSX syntax 
				$new = false;      // true if it is a new range defintion (XLSX ranges can be multi-range)
			}
			
			// Read and interpret the char
			$x = $ref[$i];
			
			if ($is_cell) {
				// We are reading the cell part
				if ( ($x === '!') || ($x === '.') ) {
					// We meet a cell separator
					$is_cell = false;
					$is_xslx = ($x === '!');
				} elseif ($x === $rsep) {
					// We meet a range separator
					$new = true;
				} elseif ($x === $delim) {
					// should never happen, but we can workaround this
					$is_cell = false;
					$sheet = $x;
				} else {
					$cells = $x . $cells;
				}
			} else {
				// We are reading the sheet part
				if ($in_delim) {
					if ($x === $delim) {
						if ( ($i > 0) && ($ref[$i -1] === $delim) ) {
							// It's a double delim
							$sheet = $x . $sheet;
							$i--;
						} else {
							// It's a single delim. Note that the bound delim are not kept.
							$in_delim = false;
						}	
					} else {
						$sheet = $x . $sheet;
					}
				} else {
					if ($x === $delim) {
						$in_delim = true;
					} elseif ($x === ':') {
						// case of ODS with two cells
						$cells = $x . $cells;
						$is_cell = true;
						$sheet = ''; // the sheet name can be repeated in the second cell with Database Range
					} elseif ($x === $rsep) {
						$new = true;
					} else {
						$sheet = $x . $sheet;
					}
				}
			}

			// Check for a new range info
			if ( $new || ($i == 0) ) {
				
				// Clean up the sheet name
				$sheet = trim($sheet, '$'); // ODS can ref can start with $ before the sheet name.
				if (!$is_xslx) {
					$sheet = htmlspecialchars_decode($sheet); // ODS only
				}
				
				// pattern with all props
				$info = array(
					'sheet' => $sheet,
					'cells' => $cells,
					'err'   => false,
					'cs'   => false,
					'rs'   => false,
					'ce'   => false,
					're'   => false,
					'single' => false,
					'cfull' => false,
					'rfull' => false,
				);
				
				// Analyze the cells ref
				$parts = explode(':', $cells);
				foreach ($parts as $idx => $cell) {
					$w = $this->Sheet_ColNum($cell, true);
					if ($w === false) {
						$info['err'] = "The range reference '{$cell}' is not recognized.";
						$info['_ref'] = $ref; // for debuging
					} else {
						$ok = true;
						if ($is_xslx) {
							// we have to check that both col and row values have a $, otherwise the Excel syntaxe is not the same
							// it is very curious : B8 => XFD1, C8 => A1, C$8 => A$8, $C8 => $C1 !!??
							if ($cell[0] !== '$') {
								$ok = false;
							} elseif (($w[0] !== 0) && ($w[1] !== 0) && (substr_count($cell, '$') != 2)) {
								$ok = false;
							}
						}
						if ($ok) {
							$z = ($idx === 0) ? 's' : 'e';
							$info['c'.$z] = $w[0];
							$info['r'.$z] = $w[1];
						} else {
							$info['err'] = "OpenTBS supports only absolute references in XLSX ranges.";
							$info['_ref'] = $ref; // for debuging
						}
					}
				}

				// For facilities, endings should be available
				if ( ($info['ce'] === false) && ($info['re'] === false) ) {
					$info['ce'] = $info['cs'];
					$info['re'] = $info['rs'];
					$info['single'] = true;
				}
				
				// Set the full row or column info
				if ($info['cs'] === 0) {
					$info['cs'] = 1;
					$info['ce'] = PHP_INT_MAX;
					$info['cfull'] = true;
				}
				if ($info['re'] === 0) {
					$info['rs'] = 1;
					$info['re'] = PHP_INT_MAX;
					$info['rfull'] = true;
				}
				
				$result[] = $info;
				
			}
			
		}
		
		return $result;
		
	}

	/**
	 * Visit all the cells of a range for get or set.
	 *
	 */
	function Sheet_VisitCells($RangeRef, $Options, $Set) {
		
		// Retrieve options
		if (!is_array($Options)) {
			$Options = array();
		}
		$opt_header    = $this->getItem($Options, 'header', false);
		$opt_noerr     = $this->getItem($Options, 'noerr', false);
		$opt_rangeinfo = $this->getItem($Options, 'rangeinfo', false);
		$opt_columns   = $this->getItem($Options, 'columns', false);
		$opt_dbr       = $this->getItem($Options, 'del_blank_rows', false);
		$opt_row_max   = $this->getItem($Options, 'row_max', false);
		
		// Get the type of contents
		if ($this->ExtEquiv == 'ods') {
			$isXlsx = false;
		} elseif ($this->ExtEquiv == 'xlsx') {
			$isXlsx = true;
		} else {
			// Not supported
			return false;
		}

		if ($isXlsx) {
			$this->MsExcel_RangeNamesInit();
		} else {
			$this->OpenDoc_RangeNamesInit();
		}
		
		// Get range information
		if (isset($this->OtbsSheetRangeNames[$RangeRef])) {
			// Named range
			$x = $this->OtbsSheetRangeNames[$RangeRef];
		} else {
			// Custom range definition
			$x = $this->Sheet_GetRangeInfo($RangeRef);
		}

		if (isset($x[0])) {
			$Range = $x[0];
			if ($Range['err'] !== false) {
				if ($opt_noerr) return false;
				return $this->RaiseError("(VisitCells) The range reference '{$RangeRef}' cannot be found.");
			}
		} else {
			if ($opt_noerr) return false;
			return $this->RaiseError("(VisitCells) Unable to read the definition for the range named '{$RangeRef}'.");
		}
		if ($Range['err']) {
			if ($opt_noerr) return false;
			return $this->RaiseError("(VisitCells) Error for the range '{$RangeRef}' : " . $Range['err']);
		}
		
		if ($opt_rangeinfo) {
			return $Range;
		}
		
		// Get sheet and range information
		if ($isXlsx) {
			$SheetLoc = $this->MsExcel_GetSheetLoc($Range);
		} else {
			$SheetLoc = $this->OpenDoc_GetSheetLoc($Range);
			$this->OpenDoc_CoveredCells_Replace($SheetLoc, true);
		}
		if (!$SheetLoc) {
			return false;
		}

		//var_export($SheetLoc->GetSrc()); exit;

		$RowEl    = ($isXlsx) ? 'row' : 'table:table-row';
		$CellEl   = ($isXlsx) ? 'c'   : 'table:table-cell';
		$AttRowR  = ($isXlsx) ? false : 'table:number-rows-repeated';
		$AttCellR = ($isXlsx) ? false : 'table:number-columns-repeated';
		
		// Visit all cells of the range
		$ok = true;
		$cell = false;
		$result = array();
		$row_idx = -1;
		$col_idx = -1;
		// Manage header
		$hdr_ok = false; // true if $hdr_lst is set
		$hdr_lst = array();
		// Manage blank rows
		$del_row = false; // Number of not empty values in the row
		while ($ok && ($cell = $this->XML_GetNextCellLoc($SheetLoc, $Range, $cell, $RowEl, $CellEl, $AttRowR, $AttCellR, false)) ) {
			if ($cell) {
				
				// Retrieve row and col indexes
				$NewRow = ($cell->cellCol == $Range['cs']);
				if ($NewRow) {
					
					// Manage header and colmun renaming
					if ($row_idx === 0) {
						if ($opt_header) {
							$hdr_lst = $row;
							$hdr_ok = true;
							// Delete the row from the result
							unset($result[0]);
							$row_idx = -1;
							$opt_header = false; // avoid a second pass
						}
						if (is_array($opt_columns)) {
							if ($hdr_ok) {
								$hdr_lst2 = array();
								foreach ($hdr_lst as $i => $n) {
									if (isset($opt_columns[$i])) {
										$hdr_lst2[$i] = $opt_columns[$i];
									} elseif (isset($opt_columns[$n])) {
										$hdr_lst2[$i] = $opt_columns[$n];
									}
								}
								$hdr_lst = $hdr_lst2;
								unset($hdr_lst2);
							} else {
								$hdr_ok = true;
								$hdr_lst = $opt_columns;
							}
							$opt_columns = false; // avoid a second pass
						}
					}
					
					// The cell is the first of a row
					$col_idx = 0;
					unset($row);
					if (!$del_row) $row_idx++;
					$del_row = $opt_dbr;
					
					if ($opt_row_max !== false) {
						if ($row_idx >= $opt_row_max) {
							$ok = false;
						}
					}

					// Add a new empty row
					if ($ok) {
						$result[$row_idx] = array();
						$row =& $result[$row_idx];
					}

				} else {
					$col_idx++;
				}
				
				// Get column key and check if we read the value
				if ($hdr_ok) {
					if (isset($hdr_lst[$col_idx])) {
						$col_key = $hdr_lst[$col_idx];
					} else {
						$col_key = false;
					}
				} else {
					$col_key = $col_idx;
				}
				
				// Read the value
				if ($ok && ($col_key !== false)) {
					if ($isXlsx) {
						$val = $this->MsExcel_GetCellValue($cell, $SheetLoc->xlsxFileIdx);
					} else {
						$val = $this->OpenDoc_GetCellValue($cell);
					}
					// Save the value
					$row[$col_key] = $val;
					// Manage blank rows
					if ($del_row && (!is_null($val)) ) $del_row = false;
					
				}
			
			} else {
				$ok = false;
			}
		}
		
		unset($row);
		
		// Delete the last inserted if needed
		if ($del_row) {
			unset($result[$row_idx]);
		}
		
		return $result;
		
	}
	
	/**
	 * Return the extension of the file, lower case and without the dot. Example: 'png'.
	 */
	function Misc_FileExt($FileOrExt) {
		$p = strrpos($FileOrExt, '.');
		$ext = ($p===false) ? $FileOrExt : substr($FileOrExt, $p+1);
		$ext = strtolower($ext);
		return $ext;
	}

	/**
	 * Add or replace a credit information in the appropriate property of the document.
	 * Return the new credit text if succeed.
	 * Return false if the expected file is not found.
	 * @param string  $NewCredit  The text to set.
	 * @param boolean $Add        Add the item.
	 * @param boolean $System     Automatic system information.
	 * @param string  $Type       (optional) type of the item to add.
	 */
	function Misc_EditCredits($NewCredit, $Add, $System, $Type = null) {
	
		if ($this->ExtType=='odf') {
			$File = 'meta.xml';
			$Tag = 'meta:user-defined';
			if (is_string($Type)) {
				$n = $Type;
			} else {
				$n = ($System) ? 'Producer' : 'Creator';
			}
			$Att = 'meta:name="' . $n .'"';
			$Parent = 'office:meta';
		} elseif ($this->ExtType=='openxml') {
			$File = 'docProps/core.xml';
			$Tag = (is_string($Type)) ? $Type : 'dc:creator';
			$Att = false;
			$Parent = 'cp:coreProperties';
		} else {
			return false;
		}
	
		$idx = $this->FileGetIdx($File);
		if ($idx===false) return false;
		
		// prevent from XML injection
		$NewCredit = htmlspecialchars($NewCredit, ENT_NOQUOTES); // ENT_NOQUOTES because target is an element's content
		
		$Txt = $this->TbsStoreGet($idx, "EditCredits");
		
		if ($Att) {
			$loc = clsTbsXmlLoc::FindElementHavingAtt($Txt, $Att, 0);
			$TagOpen = $Tag.' '.$Att;
		} else {
			$loc = clsTbsXmlLoc::FindElement($Txt, $Tag, 0);
			$TagOpen = $Tag;
		}
		
		// On both OpenXML and ODF, the item must be unique.
		if ($loc===false) {
			$p = strpos($Txt, '</'.$Parent.'>');
			if ($p===false) return $p;
			$Txt = substr_replace($Txt, '<'.$TagOpen.'>'.$NewCredit.'</'.$Tag.'>', $p, 0);
		} else {
			if ($Add) {
				$NewCredit = $loc->GetInnerSrc().';'.$NewCredit;
			}
			$loc->ReplaceInnerSrc($NewCredit);
		}
		
		$this->TbsStorePut($idx, $Txt);
		
		return $NewCredit;
		
	}
	
	/**
	 * Return the path of file $FullPath relatively to the path of file $RelativeTo.
	 * For example:
	 * 'dir1/dir2/file_a.xml' relatively to 'dir1/dir2/file_b.xml' is 'file_a.xml'
	 * 'dir1/file_a.xml' relatively to 'dir1/dir2/file_b.xml' is '../file_a.xml'
	 */
	function OpenXML_GetRelativePath($FullPath, $RelativeTo) {
		
		$fp = explode('/', $FullPath);
		$fp_file = array_pop($fp);
		$fp_max = count($fp)-1;
		
		$rt = explode('/', $RelativeTo);
		$rt_file = array_pop($rt);
		$rt_max = count($rt)-1;
		
		// First different item
		$min = min($fp_max, $rt_max);
		while( ($min>=0) && ($fp[0]==$rt[0])  ) {
			$min--;
			array_shift($fp);
			array_shift($rt);
		}

		$path  = str_repeat('../', count($rt));
		$path .= implode('/', $fp);
		if (count($fp)>0) $path .= '/';
		$path .= $fp_file;
		
		return $path;
		
	}

	/**
	 * Return the absolute path of file $RelativePath which is relative to the full path $RelativeTo.
	 * For example:
	 * '../file_a.xml' relatively to 'dir1/dir2/file_b.xml' is 'dir1/file_a.xml'
	 */    
	function OpenXML_GetAbsolutePath($RelativePath, $RelativeTo) {
		
		// May be reltaive to the root
		if (substr($RelativePath, 0, 1) == '/') {
			return substr($RelativePath, 1);
		}

		$rp = explode('/', $RelativePath);
		$rt = explode('/', $RelativeTo);
		
		// Get off the file name;
		array_pop($rt);
		
		while ($rp[0] == '..') {
			array_pop($rt);
			array_shift($rp);
		}
		
		while ($rp[0] == '.') {
			array_shift($rp);
		}
		
		$path = array_merge($rt, $rp);
		$path = implode('/', $path);
		
		return $path;
		
	}
	
	function OpenXML_GetMediaRelativeToCurrent() {
		$file = $this->TBS->OtbsCurrFile;
		$x = explode('/', $file);
		$dir = $x[0] . '/media';
		return $this->OpenXML_GetRelativePath($dir, $file);
	}

	/**
	 * Return the absolute internal path of a target for a given Rid used in the current file.
	 */
	function OpenXML_GetInternalPicPath($Rid) {
		// $this->OpenXML_CTypesPrepareExt($InternalPicPath, '');
		$TargetDir = $this->OpenXML_GetMediaRelativeToCurrent();
		$o = $this->OpenXML_Rels_GetObj($this->TBS->OtbsCurrFile, $TargetDir);
		if (isset($o->TargetLst[$Rid])) {
			$x = $o->TargetLst[$Rid]; // relative path
			return $this->OpenXML_GetAbsolutePath($x, $this->TBS->OtbsCurrFile);
		} else {
			return false;
		}
	}
	
	/**
	 * Delete an XML file in the OpenXML archive.
	 * The file is delete from the declaration file [Content_Types].xml and from the relationships of the specified files.
	 * @param {string} $FullPath The full path of the file to delete.
	 * @param {array}  $RelatedTo List of the the full paths of the files than may have relationship with the file to delete.
	 * @return {mixed} False if it is not possible to delete the file, or the number of modifier relations ship in case of success (may be 0). 
	 */
	function OpenXML_DeleteFile($FullPath, $RelatedTo) {

		// Delete the file in the archive
		$idx = $this->FileGetIdx($FullPath);
		if ($idx==false) return false;
		$this->FileReplace($idx, false);

		// Delete the declaration of the file
		$this->OpenXML_CTypesDeletePart('/' . $FullPath);
		 
		// Delete the relationships
		$nb = 0;
		foreach ($RelatedTo as $file) {
			$target = $this->OpenXML_GetRelativePath($FullPath, $file);
			$att = 'Target="' . $target . '"';
			if ($this->OpenXML_Rels_DeleteRel($file, $att)) {
				$nb++;
			}
		}
		
		return $nb;
		
	}

	/**
	 * Return the path of the Rel file in the archive for a given XML document.
	 * @param $DocPath      Full path of the sub-file in the archive
	 */
	function OpenXML_Rels_GetPath($DocPath) {
		$DocName = basename($DocPath);
		return str_replace($DocName,'_rels/'.$DocName.'.rels',$DocPath);
	}

	/**
	 * Delete an element in a Rels file.
	 * Take car that there is another technic for listing and adding targets wish is working with a persistent object which is commit at the end of the merge..
	 * @param string $DocPath   The fullpath of the document file.
	 * @param string $AttExpr   The target att expression to find.
	 * @param string|boolean $ReturnAttLst The list of att values to return.
	 * @return mixed $ReturnAttVal (or True) if the change is applied.
	 */
	function OpenXML_Rels_DeleteRel($DocPath, $AttExpr, $ReturnAttLst = false) {
	
		$RelsPath = $this->OpenXML_Rels_GetPath($DocPath);
		$idx = $this->FileGetIdx($RelsPath);
		if ($idx===false) $this->RaiseError("Cannot edit target in '$RelsPath' because the file is not found.");
		$txt = $this->TbsStoreGet($idx, 'Replace target in rels file');
		
		$loc = clsTbsXmlLoc::FindElementHavingAtt($txt, $AttExpr, 0);
		if ($loc) {
			$ret = true;
			if (is_array($ReturnAttLst)) {
				$ret = array();
				foreach ($ReturnAttLst as $att) {
					$ret[$att] = $loc->GetAttLazy($att);
				}
			}
			$loc->Delete();
			$this->TbsStorePut($idx, $txt);
			return $ret;
		} else {
			return false;
		}
	
	}

	/**
	 * Return an object that represents the informations of an .rels file, but for optimization, targets are scanned only for asked directories.
	 * The result is stored in a cache so that a second call will not compute again.
	 * The function stores Rids of files existing in a the $TargetPrefix directory of the archive (image, ...).
	 * @param $DocPath      Full path of the sub-file in the archive
	 * @param $TargetPrefix Prefix of the 'Target' attribute. For example $TargetPrefix='../drawings/'
	 */
	function OpenXML_Rels_GetObj($DocPath, $TargetPrefix) {

		if ($this->OpenXmlRid===false) $this->OpenXmlRid = array();

		// Create the object if it does not exist yet
		if (!isset($this->OpenXmlRid[$DocPath])) {

			$o = (object) null;
			$o->RidLst = array();    // Current Rids in the template ($Target=>$Rid)
			$o->TargetLst = array(); // Current Targets in the template ($Rid=>$Target)
			$o->RidNew = array();    // New Rids to add at the end of the merge
			$o->DirLst = array();    // Processed target dir
			$o->ChartLst = false;    // Chart list, computed in another method

			$o->FicPath = $this->OpenXML_Rels_GetPath($DocPath);

			$FicIdx = $this->FileGetIdx($o->FicPath);
			if ($FicIdx===false) {
				$o->FicType = 1;
				$Txt = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>';
			} else {
				$o->FicIdx = $FicIdx;
				$o->FicType = 0;
				$Txt = $this->FileRead($FicIdx, true);
			}
			$o->FicTxt = $Txt;
			$o->ParentIdx = $this->FileGetIdx($DocPath);

			$this->OpenXmlRid[$DocPath] = &$o;

		} else {

			$o = &$this->OpenXmlRid[$DocPath];
			$Txt = &$o->FicTxt;

		}

		// Feed the Rid and Target lists for the asked directory
		if (!isset($o->DirLst[$TargetPrefix])) {

			$o->DirLst[$TargetPrefix] = true;

			// read existing Rid in the file
			$zTarget = ' Target="'.$TargetPrefix;
			$zId  = ' Id="';
			$p = -1;
			while (($p = strpos($Txt, $zTarget, $p+1))!==false) {
				// Get the target name
				$p1 = $p + strlen($zTarget);
				$p2 = strpos($Txt, '"', $p1);
				if ($p2===false) return $this->RaiseError("(OpenXML) end of attribute Target not found in position ".$p1." of sub-file ".$o->FicPath);
				$TargetEnd = substr($Txt, $p1, $p2 -$p1);
				$Target = $TargetPrefix.$TargetEnd;
				// Get the Id
				$p1 = strrpos(substr($Txt,0,$p), '<');
				if ($p1===false) return $this->RaiseError("(OpenXML) beginning of tag not found in position ".$p." of sub-file ".$o->FicPath);
				$p1 = strpos($Txt, $zId, $p1);
				if ($p1!==false) {
					$p1 = $p1 + strlen($zId);
					$p2 = strpos($Txt, '"', $p1);
					if ($p2===false) return $this->RaiseError("(OpenXML) end of attribute Id not found in position ".$p1." of sub-file ".$o->FicPath);
					$Rid = substr($Txt, $p1, $p2 - $p1);
					$o->RidLst[$Target] = $Rid;
					$o->TargetLst[$Rid] = $Target;
				}
			}

		}

		return $o;

	}

	/* 
	* Add a new Rid in the file in the Rels file. Return the Rid.
	* Rels files are attached to XML files and are listing, and gives all rids and their corresponding targets used in the XML file.
	*/
	function OpenXML_Rels_AddNewRid($DocPath, $TargetDir, $FileName) {

		$o = $this->OpenXML_Rels_GetObj($DocPath, $TargetDir);

		$Target = $TargetDir.$FileName;

		if (isset($o->RidLst[$Target])) return $o->RidLst[$Target];

		// Add the Rid in the information
		$NewRid = 'opentbs'.(1+count($o->RidNew));
		$o->RidLst[$Target] = $NewRid;
		$o->RidNew[$Target] = $NewRid;

		$this->IdxToCheck[$o->ParentIdx] = $o->FicIdx;

		return $NewRid;

	}

	// Save the changes in the rels files (works only for images for now)
	function OpenXML_Rels_CommitNewRids ($Debug) {

		foreach ($this->OpenXmlRid as $doc => $o) {

			if (count($o->RidNew)>0) {

				// search position for insertion
				$p = strpos($o->FicTxt, '</Relationships>');
				if ($p===false) return $this->RaiseError("(OpenXML) closing tag </Relationships> not found in subfile ".$o->FicPath);

				// build the string to insert
				$x = '';
				foreach ($o->RidNew as $target=>$rid) {
					$x .= '<Relationship Id="'.$rid.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="'.$target.'"/>';
				}

				// insert
				$o->FicTxt = substr_replace($o->FicTxt, $x, $p, 0);

				// save
				if ($o->FicType==1) {
					$this->FileAdd($o->FicPath, $o->FicTxt);
				} else {
					$this->FileReplace($o->FicIdx, $o->FicTxt);
				}

				// debug mode
				if ($Debug) $this->DebugLst[$o->FicPath] = $o->FicTxt;
				
				$this->OpenXmlRid[$doc]->RidNew = array(); // Erase the Rid done because there can be another commit

			}
		}

	}

	/**
	 * Initialize modifications in '[Content_Types].xml'.
	 */
	function OpenXML_CTypesInit() {
		if ($this->OpenXmlCTypes===false){
			$this->OpenXmlCTypes = array(
				'Extension'=>array(),
				'PartName'=>array()
			);
		}
	}

	/**
	 * Prepare information for adding a content type for an extension.
	 * It needs to be completed when a new picture file extension is added in the document.
	 */
	function OpenXML_CTypesPrepareExt($FileOrExt, $ct='') {

		$ext = $this->Misc_FileExt($FileOrExt);

		$this->OpenXML_CTypesInit();
		
		$lst =& $this->OpenXmlCTypes['Extension'];
		if (isset($lst[$ext]) && ($lst[$ext]!=='') ) return;

		if (($ct==='') && isset($this->ExtInfo['pic_ext'][$ext])) $ct = 'image/'.$this->ExtInfo['pic_ext'][$ext];

		$lst[$ext] = $ct;

	}

	/**
	 * Delete a file in the declaration file.
	 * @param $PartName : path of the file to delete
	 */
	function OpenXML_CTypesDeletePart($PartName) {
		$this->OpenXML_CTypesInit();
		$this->OpenXmlCTypes['PartName'][$PartName] = false;
	}
	
	function OpenXML_CTypesCommit($Debug) {

		$file = '[Content_Types].xml';
		$idx = $this->FileGetIdx($file);
		if ($idx===false) {
			$Txt = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"></Types>';
		} else {
			$Txt = $this->FileRead($idx, true);
		}

		$ok = false;
		
		// Delete PartNames
		foreach ($this->OpenXmlCTypes['PartName'] as $part=>$val) {
			if ($val===false) {
				$loc = clsTbsXmlLoc::FindElementHavingAtt($Txt, 'PartName="'.$part.'"', 0);
				if ($loc!==false) {
					$loc->ReplaceSrc('');
					$ok = true;
				}
			}
		}		

		// Add missing extensions
		$x = '';
		foreach ($this->OpenXmlCTypes['Extension'] as $ext=>$ct) {
			$p = strpos($Txt, ' Extension="'.$ext.'"');
			if ($p===false) {
				if ($ct==='') {
					$this->RaiseError("(OpenXML) '".$ext."' is not an picture's extension recognize by OpenTBS.");
				} else {
					$x .= '<Default Extension="'.$ext.'" ContentType="'.$ct.'"/>';
				}
			}
		}
		if ($x!=='') {
			$p = strpos($Txt, '</Types>'); // search position for insertion
			if ($p===false) return $this->RaiseError("(OpenXML) closing tag </Types> not found in subfile ".$file);
			$Txt = substr_replace($Txt, $x, $p ,0);
			$ok = true;
		}


		if ($ok) {
			// debug mode
			if ($Debug) $this->DebugLst[$file] = $Txt;

			if ($idx===false) {
				$this->FileAdd($file, $Txt);
			} else {
				$this->FileReplace($idx, $Txt);
			}

		}

	}

	function OpenXML_FirstPicAtt($Txt, $Pos, $Backward) {
	// search the first image element in the given direction. Two types of image can be found. Return the value required for "att" parameter.
		$TypeVml = '<v:imagedata ';
		$TypeDml = '<a:blip ';

		if ($Backward) {
			// search the last image position this code is compatible with PHP 4
			$p = -1;
			$pMax = -1;
			$t_curr = $TypeVml;
			$t = '';
			do {
				$p = strpos($Txt, $t_curr, $p+1);
				if ( ($p===false) || ($p>=$Pos) ) {
					if ($t_curr===$TypeVml) {
						// we take a new search for the next type of image
						$t_curr = $TypeDml;
						$p = -1;
					} else {
						$p = false;
					}
				} elseif ($p>$pMax) {
					$pMax = $p;
					$t = $t_curr;
				}
			} while ($p!==false);
		} else {
			$p1 = strpos($Txt, $TypeVml, $Pos);
			$p2 = strpos($Txt, $TypeDml, $Pos);
			if (($p1===false) && ($p2===false)) {
				$t = '';
			} elseif ($p1===false) {
				$t = $TypeDml;
			} elseif ($p2===false) {
				$t = $TypeVml;
			} else {
				$t = ($p1<$p2) ? $TypeVml : $TypeDml;
			}
		}

		if ($t===$TypeVml) {
			return 'v:imagedata#r:id';
		} elseif ($t===$TypeDml) {
			return 'a:blip#r:embed';
		} else {
			return false;
		}

	}

	function OpenXML_MapInit() {
	// read the Content_Type XML file and save a sumup in the OpenXmlMap property.

		$this->OpenXmlMap = array();
		$Map =& $this->OpenXmlMap;

		$file = '[Content_Types].xml';
		$idx = $this->FileGetIdx($file);
		if ($idx===false) return;

		$Txt = $this->FileRead($idx, true);

		$type = ' ContentType="application/vnd.openxmlformats-officedocument.';
		$type_l = strlen($type);
		$name = ' PartName="';
		$name_l = strlen($name);

		$p = -1;
		while ( ($p=strpos($Txt, '<', $p+1))!==false) {
			$pe = strpos($Txt, '>', $p);
			if ($pe===false) return; // syntax error in the XML
			$x = substr($Txt, $p+1, $pe-$p-1);
			$pi = strpos($x, $type);
			if ($pi!==false) {
				$pi = $pi + $type_l;
				$pc = strpos($x, '"', $pi);
				if ($pc===false) return; // syntax error in the XML
				$ShortType = substr($x, $pi, $pc-$pi); // content type's short value
				$pi = strpos($x, $name);
				if ($pi!==false) {
					$pi = $pi + $name_l;
					$pc = strpos($x, '"', $pi);
					if ($pc===false) return; // syntax error in the XML
					$Name = substr($x, $pi, $pc-$pi); // name
					if ($Name[0]=='/') $Name = substr($Name,1); // fix the file path
					if (!isset($Map[$ShortType])) $Map[$ShortType] = array();
					$Map[$ShortType][] = $Name;
				}
			}
			$p = $pe;
		}

	}

	function OpenXML_MapGetFiles($ShortTypes) {
	// Return all values for a given type (or array of types) in the map.
		if (is_string($ShortTypes)) $ShortTypes = array($ShortTypes);
		$res = array();
		foreach ($ShortTypes as $type) {
			if (isset($this->OpenXmlMap[$type])) {
				$val = $this->OpenXmlMap[$type];
				foreach ($val as $file) $res[] = $file;
			}
		}
		return $res;
	}

	function OpenXML_MapGetMain($ShortType, $Default) {
	// Return all values for a given type (or array of types) in the map.
		if (isset($this->OpenXmlMap[$ShortType])) {
			return $this->OpenXmlMap[$ShortType][0];
		} else {
			return $Default;
		}
	}

	/**
	 * Build the list of chart files.
	 */
	function OpenXML_ChartInit() {

		$this->OpenXmlCharts = array();

		foreach ($this->CdFileByName as $f => $i) {
			// Note : some of liste files are style or color files, not chart.
			if (strpos($f, '/charts/')!==false) {
				$x = explode('/',$f);
				$n = count($x) -1;
				if ( ($n>=2) && ($x[$n-1]==='charts') ) {
					$x = $x[$n]; // name of the xml file
					if (substr($x,-4)==='.xml') {
						$x = substr($x,0,strlen($x)-4);
						$this->OpenXmlCharts[$x] = array('idx'=>$i, 'parent_idx'=>false, 'series'=>false);
					}
				}
			}
		}

	}

	function OpenXML_ChartDebug($nl, $sep, $bull) {

		if ($this->OpenXmlCharts===false) $this->OpenXML_ChartInit();

		echo $nl;
		echo $nl."Charts technically stored in the document: (use command OPENTBS_CHART_INFO to get series's names and data)";
		echo $nl."------------------------------------------";

		// list of supported charts
		$nbr = 0;
		foreach ($this->OpenXmlCharts as $key => $info) {
			$ok = true;
			if (!isset($info['series_nbr'])) {
				$txt = $this->FileRead($info['idx'], true);
				$info['series_nbr'] = substr_count($txt, '<c:ser>');
				$ok = (strpos($txt, '<c:chart>')!==false);
			}
			if ($ok) {
				$nbr++;
				echo $bull."name: '".$key."' , number of series: ".$info['series_nbr'];
			}
		}

		if ($this->TbsCurrIdx===false) {
			echo $bull."(unable to scann more because no subfile is loaded)";
		} else {
			$x = ' ProgID="MSGraph.Chart.';
			$x_len = strlen($x);
			$p = 0;
			$txt = $this->TBS->Source;
			while (($p=strpos($txt, $x, $p))!==false) {
				// check that the text is inside an xml tag
				$p = $p + $x_len;
				$p1 = strpos($txt, '>', $p);
				$p2 = strpos($txt, '<', $p);
				if ( ($p1!==false) && ($p2!==false) && ($p1<$p2) ) {
					$nbr++;
					$p1 = strpos($txt, '"', $p);
					$z = substr($txt, $p, $p1-$p);
					echo $bull."1 chart created using MsChart version ".$z." (series can't be merged with OpenTBS)";
				}
			}
		}

		if ($nbr==0) echo $bull."(none)";

	}

	/**
	 * Search for the series in the chart definition
	 * @return mixed An Array if success, or a string if error.
	 */
	function OpenXML_ChartSeriesFound(&$Txt, $SeriesNameOrNum, $OnlyBounds) {

		if (is_numeric($SeriesNameOrNum)) {
			$p = strpos($Txt, '<c:order val="'.($SeriesNameOrNum-1).'"/>'); // position of the series
			if ($p===false) return "Number of the series not found.";
		} else {
			$SeriesNameOrNum = htmlspecialchars($SeriesNameOrNum, ENT_NOQUOTES); // ENT_NOQUOTES because target is an element's content
			$p = strpos($Txt, '>'.$SeriesNameOrNum.'<');
			if ($p===false) return "Name of the series not found.";
			$p++;
		}

		$res = array('p'=>$p);

		$loc = clsTbsXmlLoc::FindElement($Txt, 'c:ser', $p, false);
		if ($loc === false) {
			return "XML entity not found.";
		}
		
		$res['p'] = $loc->PosBeg;
		$res['l'] = $loc->PosEnd - $loc->PosBeg + 1;

		if ($OnlyBounds) {
			return $res;
		}

		$src = substr($Txt, $res['p'], $res['l']);
		
		$this->OpenXML_ChartConvCacheToLiteral($src);

		// Legend, may be absent
		$p = 0;
		$p1 = strpos($src, '<c:tx>');
		if ($p1 > 0) {
			$p2 = strpos($src, '</c:tx>', $p1);
			$tag = '<c:v>';
			$p1 = strpos($src, $tag, $p1);
			if ( ($p1!==false) && ($p1<$p2) ) {
				$p1 = $p1 + strlen($tag);
				$p2 = strpos($src, '<', $p1);
				$res['leg_p'] = $p1;
				$res['leg_l'] = $p2 - $p1;
				$p = $p2;
			} 
		}

		// Data X & Y, we assume that (X or Category) are always first and (Y or Value) are always second
		// Correspond elements are <c:cat> and <c:val> or <c:xVal> and <c:yVal>
		// Some charts may not have categories, they cannot be merged :-(
		for ($i = 1 ; $i <= 2 ; $i++) {
			$p1 = strpos($src, '<c:ptCount ', $p);
			if ($p1===false) return ($i==1) ? "categories or values not found." : "categories not found, check the chart to add categories.";
			// Points elements can be childs of <c:numCache> or <c:strCache> (the most common, means the source is a reference to a XLSX range),
			// but also <c:numLit> or <c:strLit> if the source is literal (rare, means the source is given has is in the chart, I've seen it possible only in XSLX)
			$p2 = strpos($src, 'Lit>', $p1);
			/* no need if cache values have bee previously converted into literal
			if ($p2 === false) {
				$p2 = strpos($src, 'Cache>', $p1);
			}
			*/
			if ($p2===false) return "Neither Literal nor Cached data is found for categories or values.";
			$p2 = $p2 - 7;
			$res['point'.$i.'_p'] = $p1;
			$res['point'.$i.'_l'] = $p2 - $p1;
			$p = $p2;
		}
		
		$res['src'] = $src;
		
		return $res;

	}

	/**
	 * Find a chart in the template by its reference.
	 * Returns the OpenTBS's internal chart ref if found.
	 */
	function OpenXML_ChartFind($ChartRef, $ErrTitle) {
		
		if ($this->OpenXmlCharts===false) $this->OpenXML_ChartInit();
		
		$ref = ''.$ChartRef;
		// try with $ChartRef as number
		if (!isset($this->OpenXmlCharts[$ref])) {
			$ref = 'chart'.$ref;
		}
		// try with $ChartRef as name of the file
		if (!isset($this->OpenXmlCharts[$ref])) {
			$charts = array();
			$idx = false;
			if ($this->ExtEquiv=='pptx') {
				// search in slides
				$find = $this->MsPowerpoint_SearchInSlides(' title="'.$ChartRef.'"');
				$idx = $find['idx'];
			} elseif ($this->ExtEquiv=='xlsx') {
				// search in drawings
				$find = $this->TbsSearchInFiles('xl/drawings/*.xml', ' title="'.$ChartRef.'"', true);
				$idx = $find['idx'];
			} else {
				$idx =$this->Ext_GetMainIdx();
			}
			if ($idx !== false) {
				$charts = $this->OpenXML_ChartGetInfoFromFile($idx);
			}
			// Search the chart having the title
			foreach($charts as $c) {
				if ($c['title']===$ChartRef) $ref = $c['name'];
			}
			if (isset($this->OpenXmlCharts[$ref])) {
				$chart = &$this->OpenXmlCharts[$ref];
				$this->OpenXmlCharts[$ChartRef] = &$chart; 
				// For debug
				$chart['parent_idx'] = $idx;
			} else {
				return $this->RaiseError("($ErrTitle) : unable to found the chart corresponding to '".$ChartRef."'.");
			}
		}
		
		return $ref;
		
	}
	
	function OpenXML_ChartChangeSeries($ChartRef, $SeriesNameOrNum, $NewValues, $NewLegend=false) {
		
		// Search the chart
		$ref = $this->OpenXML_ChartFind($ChartRef, 'ChartChangeSeries');
		if ($ref===false) return false;

		// Open the chart doc
		$chart =& $this->OpenXmlCharts[$ref];
		$Txt = $this->TbsStoreGet($chart['idx'], 'ChartChangeSeries');
		if ($Txt===false) return false;

		$Delete = ($NewValues===false);
		if (is_array($SeriesNameOrNum)) return $this->RaiseError("(ChartChangeSeries) '$ChartRef' : The series reference is an array, a string or a number is expected. ".$ChartRef."'."); // usual mistake in arguments
		$ser = $this->OpenXML_ChartSeriesFound($Txt, $SeriesNameOrNum, $Delete);
		if (!is_array($ser)) return $this->RaiseError("(ChartChangeSeries) '$ChartRef' : unable change series '".$SeriesNameOrNum."' in the chart '".$ref."' : ".$ser);

		if ($Delete) {

			$Txt = substr_replace($Txt, '', $ser['p'], $ser['l']);

		} else {

			$point1 = ''; // category
			$point2 = ''; // value
			$i = 0;
			$v = reset($NewValues);
			if (is_array($v)) {
				// syntax 2: $NewValues = array( array('cat1','cat2',...), array(val1,val2,...) );
				$k = key($NewValues);
				$key_lst = &$NewValues[$k];
				$val_lst = &$NewValues[1];
				$simple = false;
			} else {
				// syntax 1: $NewValues = array('cat1'=>val1, 'cat2'=>val2, ...);
				$key_lst = &$NewValues;
				$val_lst = &$NewValues;
				$simple = true;
			}
			foreach ($key_lst as $k=>$v) {
				if ($simple) {
					$x = $k;
					$y = $v;
				} else {
					$x = $v;
					$y = isset($val_lst[$k]) ? $val_lst[$k] : null;
				}
				// a category should not be missing otherwise its caption may not be display if the series is the first one
				$point1 .= '<c:pt idx="'.$i.'"><c:v>'.$x.'</c:v></c:pt>';
				// a missing value is possible
				if ( (!is_null($y)) && ($y!==false) && ($y!=='') && ($y!=='NULL') ) {
					$point2 .= '<c:pt idx="'.$i.'"><c:v>'.$y.'</c:v></c:pt>';
				}
				$i++;
			} 
			$point1 = '<c:ptCount val="'.$i.'"/>'.$point1;
			$point2 = '<c:ptCount val="'.$i.'"/>'.$point2; // yes, the count is the same as point1 whenever missing values

			// change info in reverse order of placement in order to avoid extention problems
			$src = $ser['src'];
			unset($ser['src']);
			$src = substr_replace($src, $point2, $ser['point2_p'], $ser['point2_l']);
			$src = substr_replace($src, $point1, $ser['point1_p'], $ser['point1_l']);
			if ( is_string($NewLegend) && isset($ser['leg_p']) && ($ser['leg_p'] < $ser['point1_p']) ) {
				$NewLegend = htmlspecialchars($NewLegend, ENT_NOQUOTES); // ENT_NOQUOTES because target is an element's content
				$src = substr_replace($src, $NewLegend, $ser['leg_p'], $ser['leg_l']);
			}

			$Txt = substr_replace($Txt, $src, $ser['p'], $ser['l']);
			
		}

		$this->TbsStorePut($chart['idx'], $Txt, true);

		return true;

	}

	/**
	 * Return the list of all charts in the current sub-file, with title and description if any.
	 */
	function OpenXML_ChartGetInfoFromFile($idx, $Txt=false) {

		if ($idx===false) return false;

		$file = $this->CdFileLst[$idx]['v_name'];
		$relative = (substr_count($file, '/')==1) ? '' : '../';
		$o = $this->OpenXML_Rels_GetObj($file, $relative.'charts/');

		if ($o->ChartLst===false) {

			if ($Txt===false) $Txt = $this->TbsStoreGet($idx, 'OpenXML_ChartGetInfoFromFile');

			$o->ChartLst = array();

			$p = 0;
			while ($t = clsTbsXmlLoc::FindStartTag($Txt, 'c:chart', $p)) {
				$rid = $t->GetAttLazy('r:id');
				$name = false;
				$title = false;
				$descr = false;
				// DOCX <w:drawing> can embeds <wp:inline> if inline with text, or <wp:anchor> otherwise                
				$parent = clsTbsXmlLoc::FindStartTag($Txt, 'w:drawing', $t->PosBeg, false);
				if ($parent===false) {
					// PPTX
					$parent = clsTbsXmlLoc::FindStartTag($Txt, 'p:nvGraphicFramePr', $t->PosBeg, false);
				}
				if ($parent===false) {
					// XLSX
					$parent = clsTbsXmlLoc::FindStartTag($Txt, 'xdr:nvGraphicFramePr', $t->PosBeg, false);
				}
				if ($parent!==false) {
					$parent->FindEndTag();
					$src = $parent->GetInnerSrc();
					$el = clsTbsXmlLoc::FindStartTagHavingAtt($src, 'title', 0);
					if ($el!==false) $title = $el->GetAttLazy('title');
					$el = clsTbsXmlLoc::FindStartTagHavingAtt($src, 'descr', 0);
					if ($el!==false) $descr = $el->GetAttLazy('descr');
				}

				if (isset($o->TargetLst[$rid])) {
					$name = basename($o->TargetLst[$rid]);
					if (substr($name,-4)==='.xml') $name = substr($name,0,strlen($name)-4);
				}
				$o->ChartLst[] = array('rid'=>$rid, 'title'=>$title, 'descr'=>$descr, 'name'=>$name);
				$p = $t->PosEnd;
			}

		}

		return $o->ChartLst;

	}

	/**
	 * Convert all cache values into literal values.
	 */
	function OpenXML_ChartConvCacheToLiteral(&$Txt) {

		// Unlink the data sheet by deleting references
		$this->XML_DeleteElements($Txt, array('c:f'));
		
		$p = 0;
		$ok = true;
		while ( $ok  && (($loc = clsTbsXmlLoc::FindElement($Txt, 'c:tx', $p)) !== false) ) {
			// The title of the series can be <c:strRef>, <c:v> or <c:rich>
			// We try to replace <c:strRef> with <c:v>, without touching to <c:rich>.
			if ($loc_ref = clsTbsXmlLoc::FindElement($loc, 'c:strRef', 0)) {
				if ($loc_v = clsTbsXmlLoc::FindElement($loc_ref, 'c:v', 0)) {
					$src = $loc_v->getSrc();
					$loc->ReplaceInnerSrc($src);
				} else {
					$ok = false; // error met
				}
			}
			$p = $loc->PosEnd;
		}
		
		// Replace Reference values with Literal values
		if ($ok) {
			$this->XML_DeleteElements($Txt, array('c:numCache', 'c:strCache'), true);
			$Txt = str_replace('c:strRef>', 'c:strLit>', $Txt); 
			$Txt = str_replace('c:numRef>', 'c:numLit>', $Txt); 
		}
	
	}

	/**
	 * Delete one, sveral or all categories in the chart.
	 * @param string       $ChartRef       The chart reference.
	 * @param string|array $del_categories An array of categories to delete, on the name of a category, all the keywork '*' that means all categories.
	 * @param boolean      $no_err         Indicate if an error is return when a searched category is not found.
	 * @return boolean Return true if all the searched categories are deleted.
	 */
	function OpenXML_ChartDelCategories($ChartRef, $del_categories, $no_err) {

		$nb_series = 0;
	
		// Search the chart
		$ref = $this->OpenXML_ChartFind($ChartRef, 'ChartChangeSeries');
		if ($ref===false) return false;

		// Open the chart doc
		$chart =& $this->OpenXmlCharts[$ref];
		$Txt = $this->TbsStoreGet($chart['idx'], 'ChartChangeSeries');
		if ($Txt===false) return false;

		// Prepare info for the search
		$del_all = false;
		if (is_string($del_categories)) {
			if ($del_categories == '*') {
				$del_all = true;
				$del_categories = array();
			} else {
				$del_categories = array($del_categories);
			}
		}
		$del_categories = array_flip($del_categories);
		
		// global info
		$glob_remain_cat = $del_categories;
		$glob_nb_del = 0;
		
		$ps = 0;
		while ($ser = clsTbsXmlLoc::FindElement($Txt, 'c:ser', $ps, true)) {
			
			$cat = clsTbsXmlLoc::FindElement($ser, 'c:cat', 0, true);
			
			// Scan all categories
			$pc = 0;
			$new_idx = array(); // associative list of array(old_idx => new_idx)
			$del_nb = 0;
			while ($cpt = clsTbsXmlLoc::FindElement($cat, 'c:pt', $pc, true)) {
				// Note: a <c:pt> element can be missing if the is no data. So the true position is given by attribute idx.
				$idx = intval($cpt->GetAttLazy('idx'));
				$del = false;
				$cv = clsTbsXmlLoc::FindElement($cpt, 'c:v', 0, true);
				$category = $cv->GetInnerSrc();
				if ( $del_all || isset($del_categories[$category]) ) {
					$cpt->Delete();
					$cpt->UpdateParent(true);
					$del = true;
					unset($glob_remain_cat[$category]);
				}
				if ($del) {
					$del_nb++;
					$glob_nb_del++;
					$new_idx[$idx] = false;
					// do not change $pc
				} else {
					$new_idx[$idx] = $idx - $del_nb;
					if ($del_nb > 0) {
						// The category to delete is found. Next categories must have they idx updated.
						$idx = intval($cpt->GetAttLazy('idx'));
						$cpt->ReplaceAtt('idx', $new_idx[$idx]);
						$cpt->UpdateParent(true);
					}
					$pc = $cpt->PosEnd;
				}
			}

			if ($del_nb > 0) {
				
				// Update the count of categories. If not done then the chart displays an extra blank category.
				if ($cnt = clsTbsXmlLoc::FindStartTag($cat, 'c:ptCount', 0, true)) {
					$nb = intval($cnt->GetAttLazy('val'));
					$cnt->ReplaceAtt('val', $nb - $del_nb);
					$cnt->UpdateParent(true);
				}
				
				// Delete the points that corresponf with the deleted categories
				$del_val_nb = false;
				$val = clsTbsXmlLoc::FindElement($ser, 'c:val', 0, true); // usually after <c:cat>
				$pc = 0;
				while ($cpt = clsTbsXmlLoc::FindElement($val, 'c:pt', $pc, true)) {
					$idx = intval($cpt->GetAttLazy('idx'));
					if ($new_idx[$idx] === false) {
						$del_val_nb = true;
						$cpt->Delete();
						$cpt->UpdateParent(true);
						// do not change $pc
					} elseif ($new_idx[$idx] === $idx) {
						// no change
						$pc = $cpt->PosEnd;
					} else {
						// change the index
						$cpt->ReplaceAtt('idx',$new_idx[$idx]);
						$cpt->UpdateParent(true);
						$pc = $cpt->PosEnd;
					}
				}
				
				if ($del_val_nb > 0) {
					// Update the count of values. If not done then the chart displays an extra blank category.
					if ($cnt = clsTbsXmlLoc::FindStartTag($val, 'c:ptCount', 0, true)) {
						$nb = intval($cnt->GetAttLazy('val'));
						$cnt->ReplaceAtt('val', $nb - $del_val_nb);
						$cnt->UpdateParent(true);
					}
				}
				
			}
			
			$ps = $ser->PosEnd;
			
		}
		
		// Save the file if modified
		if ($glob_nb_del > 0) {
			$this->TbsStorePut($chart['idx'], $Txt, true);
		}
		
		// Result of the function
		if ( $del_all || (count($glob_remain_cat) == 0) ) {
			// All searched categories are deleted
			return true;
		} else {
			if ($no_err) {
				return false;
			} else {	
				return $this->RaiseError("(ChartDelCategory) '$ChartRef' : unable to find categories '" . implode(', ', array_keys($glob_remain_cat)) . "' in the chart ".$ref.".");
			}
		}
		
	}

	/**
	 * Return information and adata about all series in the chart.
	 */
	function OpenXML_ChartReadSeries($ChartRef, $Complete) {
		
		// Search the chart
		$ref = $this->OpenXML_ChartFind($ChartRef, 'ChartReadSerials');
		if ($ref===false) return false;

		// Open the chart doc
		$chart =& $this->OpenXmlCharts[$ref];

		$Txt = $this->TbsStoreGet($chart['idx'], 'ChartReadSerials');
		if ($Txt===false) return false;

		// Prepare loops
		$serials = array();
		
		$loop_conf = array(
			'names' => array('parent' => 'c:tx',  'format' => false), // name of the series
			'cat'   => array('parent' => 'c:cat', 'format' => 'c:formatCode'), // categories of the series
			'val'   => array('parent' => 'c:val', 'format' => 'c:formatCode'), // values of the series
		);

		// Loop
		$loop_res = array();
		$ser_p = 0;
		while ($ser_loc = clsTbsXmlLoc::FindElement($Txt, 'c:ser', $ser_p)) {
			$res = array();
			foreach ($loop_conf as $key => $conf) {
				if ($loc_parent = clsTbsXmlLoc::FindElement($ser_loc, $conf['parent'], 0)) {
					// Search format
					$format = false;
					if ($conf['format']) {
						if ($loc = clsTbsXmlLoc::FindElement($loc_parent, $conf['format'], 0)) {
							$format = $loc->GetInnerSrc();
							$res[$key . '_format'] = $format;
						}
					}
					// Search items. Works for both References or Literals values
					// It is possible that a val item is missing for a cat idx
					$items = array();
					$loc_p = 0;
					while ($loc_pt = clsTbsXmlLoc::FindElement($loc_parent, 'c:pt', $loc_p)) {
						$idx = $loc_pt->GetAttLazy('idx');
						$loc = clsTbsXmlLoc::FindElement($loc_pt, 'c:v', 0);
						$items[$idx] = $loc->GetInnerSrc();
						$loc_p = $loc_pt->PosEnd;
					}
					$res[$key] = $items;
				} else {
					$res[$key] = false;
				}
			}
			
			// simplify name info
			$names = $res['names'];
			if (is_array($names) && isset($res['names'][0])) {
				$res['name'] = $res['names'][0];
			} else {
				$res['name'] = false;
			}
			if (is_array($names)) {
				if (count($names) > 0) {
					unset($res['names']);
				}
			} else {
				unset($res['names']);
			}

			$loop_res[] = $res;
			$ser_p = $ser_loc->PosEnd;
		}
		
		if ($Complete) {
			return array(
				'file_idx' => $chart['idx'],
				'file_name' => $this->TbsGetFileName($chart['idx']),
				'parent_idx' => $chart['parent_idx'],
				'parent_name' => $this->TbsGetFileName($chart['parent_idx']),
				'series' => $loop_res,
			);
		} else {
			$series = array();
			foreach ($loop_res as $res) {
				$series[$res['name']] = array($res['cat'], $res['val']);
			}
			return $series;
		}
		
		return $loop_res;

	}
	
	function OpenXML_SharedStrings_Prepare() {

		$file = 'xl/sharedStrings.xml';
		$idx = $this->FileGetIdx($file);
		if ($idx===false) return;

		$Txt = $this->TbsStoreGet($idx, 'Excel SharedStrings');
		if ($Txt===false) return false;
		$this->TbsStorePut($idx, $Txt); // save for any further usage (is this usefull ??)

		$this->OpenXmlSharedStr = array();
		$this->OpenXmlSharedSrc =& $this->TbsStoreLst[$idx]['src'];

	}

	function OpenXML_SharedStrings_GetVal($id) {
	// this function return the XML content of the string and put previous met values in cache
		if ($this->OpenXmlSharedStr===false) $this->OpenXML_SharedStrings_Prepare();

		$Txt =& $this->OpenXmlSharedSrc;

		if (!isset($this->OpenXmlSharedStr[$id])) {
			$last_id = count($this->OpenXmlSharedStr) - 1; // last id in the cache
			if ($last_id<0) {
				$p2 = 0; // no items found yet
			} else {
				$p2 = $this->OpenXmlSharedStr[$last_id]['end'];
			}
			$x1 = '<si'; // SharedString Item
			$x1_len = strlen($x1);
			$x2 = '</si>';
			while ($last_id<$id) {
				$last_id++;
				$p1 = strpos($Txt, $x1, $p2+1);
				if ($p1===false) return $this->RaiseError("(Excel SharedStrings) id $id is searched but id $last_id is not found.");
				$p1 = strpos($Txt, '>', $p1+$x1_len)+1;
				$p2 = strpos($Txt, $x2, $p1);
				if ($p2===false) return $this->RaiseError("(Excel SharedStrings) id $id is searched but no closing tag found for id $last_id.");
				$this->OpenXmlSharedStr[$last_id] = array('beg'=>$p1, 'end'=>$p2, 'len'=>($p2-$p1));
			}
		}

		$str =& $this->OpenXmlSharedStr[$id];

		return substr($Txt, $str['beg'], $str['len']);

	}

	// Delete unreferenced images
	function OpenMXL_GarbageCollector() {

		if ( (count($this->IdxToCheck)==0) && (count($this->OtbsSheetSlidesDelete)==0) ) return;

		// Key for Pictures
		$pic_path = $this->ExtInfo['pic_path'];
		$pic_path_len = strlen($pic_path);

		// Key for Rels
		$rels_ext = '.rels';
		$rels_ext_len = strlen($rels_ext);

		// List all Pictures and Rels files
		$pictures = array();
		$rels = array();
		foreach ($this->CdFileLst as $idx=>$f) {
			$n = $f['v_name'];
			if (substr($n, 0, $pic_path_len)==$pic_path) {
				$short = basename($pic_path).'/'.basename($n);
				$pictures[] = array('name'=>$n, 'idx'=>$idx, 'nbr'=>0, 'short'=>$short);
			} elseif (substr($n, -$rels_ext_len)==$rels_ext) {
				if ($this->FileGetState($idx)!='d') $rels[$n] = $idx;
			}
		}

		// Read contents or Rels files
		foreach ($rels as $n=>$idx) {
			$txt = $this->TbsStoreGet($idx, 'GarbageCollector');
			foreach ($pictures as $i=>$info) {
				if (strpos($txt, $info['short'].'"')!==false) $pictures[$i]['nbr']++;
			}
		}

		// Delete unused Picture files
		foreach ($pictures as $info) {
			if ($info['nbr']==0) $this->FileReplace($info['idx'], false);
		}

		
	}

	function MsExcel_ConvertToRelative(&$Txt) {
		// <row r="10" ...> attribute "r" is optional since missing row are added using <row />
		// <c r="D10" ...> attribute "r" is optional since missing cells are added using <c />
		$Loc = new clsTbsLocator;
		$this->MsExcel_ConvertToRelative_Item($Txt, $Loc, 'row', 'r', true);
	}

	function MsExcel_ConvertToRelative_Item(&$Txt, &$Loc, $Tag, $Att, $IsRow) {
	// convert tags $Tag which have a position (defined with attribute $Att) into relatives tags without attribute $Att. Missing tags are added as empty tags.
		$item_num = 0;
		$tag_len = strlen($Tag);
		$missing = '<'.$Tag.'/>';
		$closing = '</'.$Tag.'>';
		$p = 0;
		$compat_limit_miss = 1000;
		$compat_limit_num = 1048576 - 10000;
		while (($p=clsTinyButStrong::f_Xml_FindTagStart($Txt, $Tag, true, $p, true, true))!==false) {

			$Loc->PrmPos = array();
			$Loc->PrmLst = array();
			$p2 = $p + $tag_len + 2; // count the char '<' before and the char ' ' after
			$PosEnd = strpos($Txt, '>', $p2);
			clsTinyButStrong::f_Loc_PrmRead($Txt,$p2,true,'\'"','<','>',$Loc, $PosEnd, true); // read parameters
			$Delete = false;
			if (isset($Loc->PrmPos[$Att])) {
				// attribute found
				$a = $Loc->PrmLst[$Att];
				if ($IsRow) {
					$r = intval($a);
				} else {
					$r = $this->Sheet_ColNum($a);
					if ($r === false) {
						return $this->RaiseError('(ConvertToRelative) Reference of cell \'' . $a . '\' cannot be recognized.');
					}
				}
				$missing_nbr = $r - $item_num -1;
				if ($missing_nbr<0) {
					return $this->RaiseError('(Excel Consistency) error in counting items <'.$Tag.'>, found number '.$r.', previous was '.$item_num);
				} elseif($IsRow && ($missing_nbr > $compat_limit_miss) && ($r >= $compat_limit_num)) { // Excel limit is 1048576
					// Useless final rows: LibreOffice add several final useless rows in the sheet when saving as XLSX.
					$Delete = true;
					$item_num++;
				} else {
					// delete the $Att attribute
					$pp = $Loc->PrmPos[$Att];
					$pp[3]--; //while ($Txt[$pp[3]]===' ') $pp[3]--; // external end of the attribute, may has an extra spaces
					$x_p = $pp[0]-1; // we take out the space
					$x_len = $pp[3] - $x_p +1;
					$Txt = substr_replace($Txt, '', $x_p, $x_len);
					$PosEnd = $PosEnd - $x_len;
					// If it's a cell, we look if it's a good idea to replace the shared string
					if ( (!$IsRow) && isset($Loc->PrmPos['t']) && ($Loc->PrmLst['t']==='s') ) $this->MsExcel_ReplaceString($Txt, $p, $PosEnd);
					// add missing items before the current item
					if ($missing_nbr>0) {
						$x = str_repeat($missing, $missing_nbr);
						$x_len = strlen($x);
						$Txt = substr_replace($Txt, $x, $p, 0);
						$PosEnd = $PosEnd + $x_len;
						$x = ''; // empty the memory
					}
					$item_num = $r;
				}
			} else {
				// nothing to change the item is already relative
				$item_num++;
			}
			if ($Delete) {
				if (($Txt[$PosEnd-1]!=='/')) {
					$x_p = strpos($Txt, $closing, $PosEnd);
					if ($x_p===false) return $this->RaiseError('(Excel Consistency) closing row tag is not found.');
					$PosEnd = $x_p + strlen($closing) - 1;
				}
				$Txt = substr_replace($Txt, '', $p, $PosEnd - $p + 1);
			} elseif ($IsRow && ($Txt[$PosEnd-1]!=='/')) {
				// It's a row item that may contain columns
				$x_p = strpos($Txt, $closing, $PosEnd);
				if ($x_p===false) return $this->RaiseError('(Excel Consistency) closing row tag is not found.');
				$x_len0 = $x_p - $PosEnd -1;
				$x = substr($Txt, $PosEnd+1, $x_len0);
				$this->MsExcel_ConvertToRelative_Item($x, $Loc, 'c', 'r', false);
				$Txt = substr_replace($Txt, $x, $PosEnd+1, $x_len0);
				$x_len = strlen($x);
				$p = $x_p + $x_len - $x_len0;
			} else {
				$p = $PosEnd;
			}
		}
	
	}

	/**
	 * Add the attribute in all <row> and <c> items, and delete empty items.
	 */
	function MsExcel_ConvertToExplicit(&$Txt) {
		if (strpos($Txt, '<sheetData>')===false) return;
		$this->MsExcel_ConvertToExplicit_Item($Txt, 'row', 'r', false);
	}

	/**
	 * Add the attribute that gives the reference of the item.
	 * Return the number of inserted attributes.
	 * Note: substr() and strpos() function's execution time are geometrically increasing with then string length.
	 *       So it is for this function. converting a sheet with 5.000 rows may have a duration of 15 sec.
	 */
	function MsExcel_ConvertToExplicit_Item(&$Txt, $Tag, $Att, $ParentRowNum) {

		$tag_pc = strlen($Tag) + 1;
		$rpl = '<'.$Tag.' '.$Att.'="';
		$rpl_len = strlen($rpl);
		$rpl_nbr = 0;
		$item_num = 0;

		$p = clsTinyButStrong::f_Xml_FindTagStart($Txt, $Tag, true, 0, true, true);
		if ($p === false) return;

		if ($p === 0) {
			$Txt_Done = '';
		} else {
			$Txt_Done = substr($Txt, 0, $p);
			$Txt = substr($Txt, $p);
		}
		
		do {

			// Next item
			$p_next = clsTinyButStrong::f_Xml_FindTagStart($Txt, $Tag, true, 0 + $tag_pc, true, true);
			
			// Small text containing the current item
			if ($p_next === false) {
				$Txt_Curr = $Txt;
				$Txt = '';
			} else {
				$Txt_Curr = substr($Txt, 0, $p_next);
				$Txt = substr($Txt, $p_next);
			}

			$item_num++;
			
			if (substr($Txt_Curr, 0 + $tag_pc, 1) == '/') {

				// It's an empty item => Delete the item
				$Txt_Done .= substr($Txt_Curr, 0 + $tag_pc + 2); // +2 is for the tail '/>'

			} else {

				// The item is not empty => replace attribute and delete the previous empty item in the same time
				$ref = ($ParentRowNum===false) ? $item_num : $this->Sheet_CellRef($item_num, $ParentRowNum);
				$Txt_Curr = $rpl . $ref . '"' . substr($Txt_Curr, 0 + $tag_pc);
				$rpl_nbr++;

				// If it's a row => search for cells
				if ($ParentRowNum===false) {
					$nbr = $this->MsExcel_ConvertToExplicit_Item($Txt_Curr, 'c', 'r', $item_num);
				}
				
				$Txt_Done .= $Txt_Curr;
			
			}

		} while ($p_next !== false);
		
		$Txt = $Txt_Done . $Txt;
		
		return $rpl_nbr;

	}
	
	/**
	 * Cells with formulas also have a cached result (stored in the <v> element).
	 * When Excel open a sheet, the cached result is displayed, not the actualized result.
	 * We can add attribute ca="true" to the <f> element in order to have them actualized.
	 * But this des not work well when the XLSX is opened with LibreOffice.
	 * So the solid solution is to simply delete the cached result.
	 *
	 * Cached values are stored in an array so it can be retrived for Sheet_VisitCells()
	 */
	function MsExcel_DeleteFormulaResults($idx, &$Txt) {
	
		if (!isset($this->MsExcel_Formulas[$idx])) {
			$this->MsExcel_Formulas[$idx] = array();
		}
		$formulas =& $this->MsExcel_Formulas[$idx];
	
		$p = 0;
		while ( ($locF = clsTbsXmlLoc::FindElement($Txt, 'f', $p, true)) !== false ) {
			$f = $locF->GetInnerSrc();
			$p = $locF->PosEnd;
			$v = null;
			if ($locC = clsTbsXmlLoc::FindElement($Txt, 'c', $locF->PosBeg, false)) {
				if ($locV = clsTbsXmlLoc::FindElement($locC, 'v', 0, true)) {
					$v = $locV->GetInnerSrc();
					$locV->Delete();
					$locV->UpdateParent(true);
				}
				$p = $locC->PosEnd;
			}
			$formulas[$f] = $v;
		}

	}

	/**
	 * XLSX has a file that refers to formulas in the entire workbook in order to schedule the calculations. 
	 * The cells references in this file mey become erroneous since cell has been deleted or added in some sheets.
	 * Hopefully this file is optional. We have to deleted it.
	 */
	function MsExcel_DeleteCalcChain() {
		return $this->OpenXML_DeleteFile('xl/calcChain.xml', array('xl/workbook.xml'));
	}
	
	function MsExcel_ReplaceString(&$Txt, $p, &$PosEnd) {
	// replace a SharedString into an InlineStr only if the string contains a TBS field
		static $c = '</c>';
		static $v1 = '<v>';
		static $v1_len = 3;
		static $v2 = '</v>';
		static $v2_len = 4;

		// found position of the <c> element, and extract its contents
		$p_close = strpos($Txt, $c, $PosEnd);
		if ($p_close===false) return;
		$x_len = $p_close - $p;
		$x = substr($Txt, $p, $x_len); // [<c ...> ... ]</c>

		// found position of the <v> element, and extract its contents
		$v1_p = strpos($x, $v1);
		if ($v1_p==false) return false;
		$v2_p = strpos($x, $v2, $v1_p);
		if ($v2_p==false) return false;
		$vt = substr($x, $v1_p+$v1_len, $v2_p - $v1_p - $v1_len);

		// extract the SharedString id, and retrieve the corresponding text
		$v = intval($vt);
		if (($v==0) && ($vt!='0')) return false;
		if (isset($this->MsExcel_NoTBS[$v])) return true;
		$s = $this->OpenXML_SharedStrings_GetVal($v);

		// if the SharedSring has no TBS field, then we save the id in a list of known id, and we leave the function
		if (strpos($s, $this->TBS->_ChrOpen)===false) {
			$this->MsExcel_NoTBS[$v] = true;
			return true;
		}

		// prepare the replace
		$x1 = substr($x, 0, $v1_p);
		$x3 = substr($x, $v2_p + $v2_len);
		$x2 = '<is>'.$s.'</is>';
		$x = str_replace(' t="s"', ' t="inlineStr"', $x1).$x2.$x3;

		$Txt = substr_replace($Txt, $x, $p, $x_len);

		$PosEnd = $p + strlen($x); // $PosEnd is used to search the next item, so we update it

	}

	function MsExcel_ChangeCellType(&$Txt, &$Loc, $Ope) {
	// change the type of a cell in an XLSX file

		$Loc->PrmLst['cellok'] = $Ope; // avoid the field to be processed twice

		if ( ($Ope==='xlsxString') || ($Ope==='tbs:string')) return true;

		static $OpeLst = array(
			'tbs:bool'=>' t="b"',
			'xlsxBool'=>' t="b"',
			'xlsxDate'=>'',
			'xlsxNum'=>'',
			'tbs:date'=>'',
			'tbs:num'=>'',
			// compatibility with ODF format
			'tbs:time'=>'',
			'tbs:percent'=>'',
			'tbs:curr'=>'',
		);

		if (!isset($OpeLst[$Ope])) return false;

		$t0 = clsTinyButStrong::f_Xml_FindTagStart($Txt, 'c', true, $Loc->PosBeg, false, true);
		if ($t0===false) return false; // error in the XML structure

		$te = strpos($Txt, '>', $t0);
		if ( ($te===false) || ($te>$Loc->PosBeg) ) return false; // error in the XML structure

		$len = $te - $t0 + 1;
		$c_open = substr($Txt, $t0, $len); // '<c ...>'
		$c_open = str_replace(' t="inlineStr"', $OpeLst[$Ope], $c_open);

		$t1 = strpos($Txt, '</c>', $te);
		if ($t1===false) return false; // error in the XML structure

		$p_is1 = strpos($Txt, '<is>', $te);
		if (($p_is1===false) || ($p_is1>$t1) ) return false; // error in the XML structure

		$is2 = '</is>';
		$p_is2 = strpos($Txt, $is2, $p_is1);
		if (($p_is2===false) || ($p_is2>$t1) ) return false; // error in the XML structure
		$p_is2 = $p_is2 + strlen($is2); // move to end the of the tag

		$middle_len = $p_is1 - $te - 1;
		$middle = substr($Txt, $te + 1, $middle_len); // text bewteen <c...> and <is>

		// new tag to replace <is>...</is>
		static $v = '<v>[]</v>';
		$v_len = strlen($v);
		$v_pos = strpos($v, '[]');

		$x = $c_open.$middle.$v;

		$Txt = substr_replace($Txt, $x, $t0, $p_is2 - $t0);

		// move the TBS field
		$p_fld = $t0 + strlen($c_open) + $middle_len + $v_pos;
		$Loc->PosBeg = $p_fld;
		$Loc->PosEnd = $p_fld +1;

	}
	
	function MsExcel_ChangeCellValue(&$Loc, &$Value) {
	
		switch ($Loc->PrmLst['cellok']) {
		case 'tbs:num': 
		case 'tbs:curr': 
		case 'tbs:percent': 
		case 'xlsxNum':
			if (is_numeric($Value)) {
				// we have to check contents in order to avoid Excel errors. Note that value '0.00000000000000' makes an Excel error.
				if (strpos($Value,'e')!==false) { // exponential representation
					$Value = var_export((float) $Value, true); // this string conversion is not affected by the decimal separator given by the locale setting
				} elseif (strpos($Value,'x')!==false) { // hexa representation
					$Value = '' . hexdec($Value);
				} elseif (strpos($Value,'.')===false) {
					// it is better to not convert because of big numbers
					// intval(7580563123) returns -1009371469 in 32bits
				} else {
					$Value = var_export((float) $Value, true);
				}
			} else {
				$Value = '';
			}
			break;
		case 'tbs:bool':
		case 'xlsxBool':
			$Value = ($Value) ? 1 : 0;
			break;
		case 'tbs:date':
		case 'tbs:time':
		case 'xlsxDate':
			if (is_string($Value)) {
				$t = strtotime($Value); // We look if it's a date
			} else {
				$t = $Value;
			}
			if (($t===-1) || ($t===false)|| ($t===null)) { // Date not recognized
				$Value = '';
			} elseif ($t===943916400) { // Date to zero
				$Value = '';
			} else { // It's a date
				$Value = ($t/86400.00)+25569; // unix: 1 means 01/01/1970, xlsx: 1 means 01/01/1900
			}
			break;
		default:
			// do nothing
		}
	}

	function MsExcel_SheetInit() {

		if ($this->MsExcel_Sheets!==false) return;

		$this->MsExcel_Sheets = array();   // sheet info sorted by location

		$idx = $this->FileGetIdx('xl/workbook.xml');
		$this->MsExcel_Sheets_WkbIdx = $idx;
		if ($idx===false) return;

		$Txt = $this->TbsStoreGet($idx, 'SheetInfo'); // use the store, so the file will be available for editing if needed
		if ($Txt===false) return false;
		$this->TbsStorePut($idx, $Txt);

		// scann sheet list
		$p = 0;
		$i = 0;
		$rels = array();
		while ($loc=clsTbsXmlLoc::FindStartTag($Txt, 'sheet', $p, true) ) {
			$o = (object) null;
			$o->num = $i + 1;
			// SheetId is not the numbered sheet in the workbook. It may have a missing sheet id.
			$o->sheetId   = $loc->GetAttLazy('sheetId');
			$o->rid   = $loc->GetAttLazy('r:id');
			$o->name  = $loc->GetAttLazy('name');
			$o->state = $loc->GetAttLazy('state');
			$o->stateR = ($o->state===false) ? 'visible' : $o->state;
			$o->file  = false;
			$this->MsExcel_Sheets[$i] = $o;
			$rels[$o->rid] =& $this->MsExcel_Sheets[$i]; 
			$i++;
			$p = $loc->PosEnd;
		}

		// Retrieve Sheet files
		$idx = $this->FileGetIdx('xl/_rels/workbook.xml.rels');
		$Txt = $this->FileRead($idx);
		if ($Txt===false) return false;

		$p = 0;
		while ($loc=clsTbsXmlLoc::FindStartTag($Txt, 'Relationship', $p, true) ) {
			$rid = $loc->GetAttLazy('Id');
			if (isset($rels[$rid])) $rels[$rid]->file =  $loc->GetAttLazy('Target');
			$p = $loc->PosEnd;
		}

	}

	/**
	 * Return the sheet info corresponding to the name, number or internal id.
	 * @param string|integer $IdOrName
	 * @param array          $SearchBy    A list of search condition, in order. Supported items : 'name' , 'sheetId', 'num' 
	 * @param boolean        $RaiseError  Set true if an error is raised if the sheet is not found.
	 * @param object A special object. See MsExcel_SheetInit(). 
	 */
	function MsExcel_SheetGetConf($IdOrName, $SearchBy, $RaiseError) {
		
		$this->MsExcel_SheetInit();
		
		$by_name    = in_array('name',    $SearchBy, true);
		$by_sheetId = in_array('sheetId', $SearchBy, true);
		$by_num     = in_array('num',     $SearchBy, true);
		
		foreach($this->MsExcel_Sheets as $o) {
			// Check by order of search.
			foreach ($SearchBy as $s) {
				$ok = false;
				$ok = $ok || ( ($s === 'name') && ($o->name == $IdOrName) );
				$ok = $ok || ( ($s === 'sheetId') && ($o->sheetId == $IdOrName) );
				$ok = $ok || ( ($s === 'num') && ($o->num == $IdOrName) );
				if ($ok) {
					return $o;
				}
			}
		}
		
		if ($RaiseError) {
			return $this->RaiseError("(MsExcel_SheetInit) The sheet '$IdOrName' is not found inside the Workbook. Try command OPENTBS_DEBUG_INFO to check all sheets inside the current Workbook.");
		} else {
			return false;
		}
		
	}

	/**
	 * Check if the file name is a subfile corresponding to a sheet.
	 */
	function MsExcel_SheetIsIt($FileName) {
		$this->MsExcel_SheetInit();
		foreach($this->MsExcel_Sheets as $o) {
			if ($FileName=='xl/'.$o->file) return true;
		}
		return false;
	}
	
	function MsExcel_SheetDebug($nl, $sep, $bull) {

		$this->MsExcel_SheetInit();

		echo $nl;
		echo $nl."Sheets in the Workbook:";
		echo $nl."-----------------------";
		foreach ($this->MsExcel_Sheets as $o) {
			$name = str_replace(array('&amp;','&quot;','&lt;','&gt;'), array('&','"','<','>'), $o->name);
			echo $bull."num: ".$o->num.", id: ".$o->sheetId.", name: [".$name."], state: ".$o->stateR.", file: xl/".$o->file;
		}

	}

	// Actually delete, display of hide sheet marked for this operations.
	function MsExcel_SheetDeleteAndDisplay() {

		if ( (count($this->OtbsSheetSlidesDelete)==0) && (count($this->OtbsSheetSlidesVisible)==0) ) return;

		$this->MsExcel_SheetInit();
		
		$WkbTxt = $this->TbsStoreGet($this->MsExcel_Sheets_WkbIdx, 'Sheet Delete and Display');
		$nothing = false;
		
		$change = false;
		$refToDel = array();

		// process sheet in reverse order of their positions
		foreach ($this->MsExcel_Sheets as $o) {
			$zid = 'i:'.$o->num;
			$zname = 'n:'.$o->name; // the value in the name attribute is XML protected
			if ( isset($this->OtbsSheetSlidesDelete[$zname]) || isset($this->OtbsSheetSlidesDelete[$zid]) ) {
				// Delete the sheet
				$this->MsExcel_DeleteSheetFile($o->file, $o->rid, $WkbTxt);
				$change = true;
				$ref1 = str_replace(array('&quot;','\''), array('"','\'\''), $o->name);
				$ref2 = "'".$ref1."'";
				$refToDel[] = $ref1;
				$refToDel[] = $ref2;
				unset($this->OtbsSheetSlidesDelete[$zname]);
				unset($this->OtbsSheetSlidesDelete[$zid]);
				unset($this->OtbsSheetSlidesVisible[$zname]);
				unset($this->OtbsSheetSlidesVisible[$zid]);
			} elseif ( isset($this->OtbsSheetSlidesVisible[$zname]) || isset($this->OtbsSheetSlidesVisible[$zid]) ) {
				// Hide or display the sheet
				$visible = (isset($this->OtbsSheetSlidesVisible[$zname])) ? $this->OtbsSheetSlidesVisible[$zname] : $this->OtbsSheetSlidesVisible[$zid];
				$state = ($visible) ? 'visible' : 'hidden';
				if ($o->stateR!=$state) {
					if (!$visible) $change = true;
					$loc = clsTbsXmlLoc::FindStartTagHavingAtt($WkbTxt, 'r:id="'.$o->rid.'"', 0);
					if ($loc!==false) $loc->ReplaceAtt('state', $state, true);
				}
				unset($this->OtbsSheetSlidesVisible[$zname]);
				unset($this->OtbsSheetSlidesVisible[$zid]);
			}
		}

		// If they are deleted or hidden sheet, then it could be the active sheet, so we delete the active tab information
		// Note: activeTab attribute seems to not be a sheet id, but rather a tab id.
		if ($change) {
			$loc = clsTbsXmlLoc::FindStartTag($WkbTxt, 'workbookView', 0);
			if ($loc!==false) $loc->DeleteAtt('activeTab');
		}

		// Delete name of cells (<definedName>) that refer to a deleted sheet
		foreach ($refToDel as $ref) {
			// The name of the sheets is used in the reference, but with small changes
			$p = 0;
			while ( ($p = strpos($WkbTxt, '>'.$ref.'!', $p)) !==false ) {
				$p2 = strpos($WkbTxt, '>', $p+1);
				$p1 = strrpos(substr($WkbTxt, 0, $p), '<');
				if ( ($p1!==false) && ($p2!==false) ) {
					$WkbTxt = substr_replace($WkbTxt, '', $p1, $p2 - $p1 +1);
				} else {
					$p++;
				}
			}
			//<pivotCaches><pivotCache cacheId="1" r:id="rId5"/></pivotCaches>
		}
		
		// can make Excel error, no problem with <definedNames>
		$WkbTxt = str_replace('<pivotCaches></pivotCaches>', '', $WkbTxt);
	
		// store the result
		$this->TbsStorePut($this->MsExcel_Sheets_WkbIdx, $WkbTxt);

		$this->TbsSheetCheck();

	}

	function MsExcel_DeleteSheetFile($file, $rid, &$WkbTxt) {

		$this->OpenXML_DeleteFile('xl/' . $file, array('xl/workbook.xml'));

		// Delete in workbook.xml
		if ($rid!=false) {
			$loc = clsTbsXmlLoc::FindElementHavingAtt($WkbTxt, 'r:id="'.$rid.'"', 0);
			if ($loc!==false) $loc->ReplaceSrc('');
		}
		
	}
	
	// Return the list of images in the current sheet
	function MsExcel_GetDrawingLst() {

		$lst = array();

		$dir = '../drawings/';
		$dir_len = strlen($dir);
		$o = $this->OpenXML_Rels_GetObj($this->TBS->OtbsCurrFile, $dir);
		foreach($o->TargetLst as $t) {
			if ( (substr($t, 0, $dir_len)===$dir) && (substr($t, -4)==='.xml') ) $lst[] = 'xl/drawings/'.substr($t, $dir_len);
		}

		return $lst;
	}

	function MsExcel_RangeNamesInit() {
		
		if ($this->OtbsSheetRangeNames !== false) return;
		
		$this->OtbsSheetRangeNames = array();

		// Get the workbook.xml contents
		$idx = $this->FileGetIdx('xl/workbook.xml');
		if ($idx===false) return;
		$Txt = $this->TbsStoreGet($idx, 'RangeNamesInit'); // use the store, so the file will be available for editing if needed
		if ($Txt===false) return false;
		//$this->TbsStorePut($idx, $Txt);

		$p = 0;
		while ( $el = clsTbsXmlLoc::FindElement($Txt, 'definedName', $p, true) ) {
			$name = $el->GetAttLazy('name'); // forbidden in range name : ", ', ' ', 
			$ref = $el->GetInnerSrc();
			$this->OtbsSheetRangeNames[$name] = $this->Sheet_GetRangeInfo($ref);
			$p = $el->PosEnd;
		}
		
	}

	/**
	 * Get the locator of the sheet element.
	 * @param  array  $Range  The range information.
	 * @return object A clsTbsXmlLoc locator of false if error.
	 */
	function MsExcel_GetSheetLoc($Range) {
		
		// Get the sheet content
		if ($Range['sheet']) {
			$o = $this->MsExcel_SheetGetConf($Range['sheet'], array('name'), true);
			if ($o === false) return false;
			$idx = $this->FileGetIdx('xl/'.$o->file);
			$Txt = $this->TbsStoreGet($idx, 'MsExcel_GetSheetLoc');
			if ($this->LastReadNotStored) {
				$this->MsExcel_ConvertToRelative($Txt);
			}
		} else {
			$Txt = $this->TBS->Source;
			$idx = $this->TbsCurrIdx;
		}
		
		$SheetLoc = clsTbsXmlLoc::FindElement($Txt, 'sheetData', 0, true);
		$SheetLoc->xlsxFileIdx = $idx;
		return $SheetLoc;
		
	}
	
	function MsExcel_GetCellValue($Loc, $fileIdx = -1) {
		
		$x = null;
		
		if ( $Loc->Exists && ($Loc->GetInnerStart() !== false) ) {
			$type = $Loc->GetAttLazy('t');
			$vtag = ($type === 'inlineStr') ? 't' : 'v';
			if ($locV = clsTbsXmlLoc::FindElement($Loc, $vtag, 0, true)) {
				$v = $locV->GetInnerSrc();
			} else {
				$v = false;
				if ($locF = clsTbsXmlLoc::FindElement($Loc, 'f', 0, true)) {
					if (isset($this->MsExcel_Formulas[$fileIdx])) {
						$f = $locF->GetInnerSrc();
						if (isset($this->MsExcel_Formulas[$fileIdx][$f])) {
							$v = $this->MsExcel_Formulas[$fileIdx][$f];
						}
					}
					if ($v === false) {
						$x = "#OpenTBS: formula without cached result";
					}
				} else {
					// it's valid to have no value
				}
			}
			if ($v !== false) {
				switch ($type) {
				case 'b': // boolean: 0=false
					$x = (boolean) $v; break;
				case 's': // shared string
					$x = $this->OpenXML_SharedStrings_GetVal($v);
					$this->XML_DeleteElements($x, array('t'), true);
					break;
				case 'inlineStr': // inline string
					$x = $v; break;
				case 'str': // formula returning a string				
					$x = $v; break;
				case 'd': // date
					$t = ($v-25569.0) * 86400.0; // unix: 1 means 01/01/1970, xlsx: 1 means 01/01/1900
					$x = date('Y-m-d h:i:s', $t);
					break;
				case 'e': // error, example of value: #DIV/0!
					$x = $v; break;
				default: // false or 'n' => it's a number
					if (strpos($v, '.') === false) {
						$x = intval($v);
					} else {
						$x = floatval($v);
					}
				}
			}
		}
		
		return $x;
		
	}
	
	/**
	 * Return the list of slides in the Ms Powerpoint presentation.
	 * @param {boolean} $Master Trye to operate on master slides.
	 * @return {array} The list of the slides, of false if an error occurs.
	 */
	function MsPowerpoint_InitSlideLst($Master = false) {

		if ($Master) {
			$RefLst = &$this->OpenXmlSlideMasterLst;
		} else {
			$RefLst = &$this->OpenXmlSlideLst;
		}
		
		if ($RefLst!==false) return $RefLst;

		$PresFile = 'ppt/presentation.xml';

		$prefix = ($Master) ? 'slideMasters/' : 'slides/';
		$o = $this->OpenXML_Rels_GetObj('ppt/presentation.xml', $prefix);

		$Txt = $this->FileRead($PresFile);
		if ($Txt===false) return false;

		$p = 0;
		$i = 0;
		$lst = array();
		$tag = ($Master) ? 'p:sldMasterId' : 'p:sldId';
		while ($loc = clsTbsXmlLoc::FindStartTag($Txt, $tag, $p)) {
			$i++;
			$rid = $loc->GetAttLazy('r:id');
			if ($rid===false) {
				$this->RaiseError("(Init Slide List) attribute 'r:id' is missing for slide #$i in '$PresFile'.");
			} elseif (isset($o->TargetLst[$rid])) {
				$f = 'ppt/'.$o->TargetLst[$rid];
				$lst[] = array('file' => $f, 'idx' => $this->FileGetIdx($f), 'rid' => $rid);
			} else {
				$this->RaiseError("(Init Slide List) Slide corresponding to rid=$rid is not found in the Rels file of '$PresFile'.");
			}
			$p = $loc->PosEnd;
		}

		$RefLst = $lst;
		return $RefLst;

	}

	// Clean tags in an Ms Powerpoint slide
	function MsPowerpoint_Clean(&$Txt) {

		$this->MsPowerpoint_CleanRpr($Txt, 'a:rPr');
		$Txt = str_replace('<a:rPr/>', '', $Txt);

		$this->MsPowerpoint_CleanRpr($Txt, 'a:endParaRPr');
		$Txt = str_replace('<a:endParaRPr/>', '', $Txt); // do not delete, can change layout

		// Join split elements
		$Txt = str_replace('</a:t><a:t>', '', $Txt);
		$Txt = str_replace('</a:t></a:r><a:r><a:t>', '', $Txt); // this join TBS split tags

		// Delete empty elements
		// An <a:r> must contain at least one <a:t>. An empty <a:t> may exist after several merges or an OpenTBS cleans.
		$Txt = str_replace('<a:r><a:t></a:t></a:r>', '', $Txt);

	}

	function MsPowerpoint_CleanRpr(&$Txt, $elem) {
		$p = 0;
		while ($x = clsTbsXmlLoc::FindStartTag($Txt, $elem, $p)) {
			$x->DeleteAtt('noProof');
			$x->DeleteAtt('lang');
			$x->DeleteAtt('err');
			$x->DeleteAtt('smtClean');
			$x->DeleteAtt('dirty');
			$p = $x->PosEnd;
		}
	}

	/**
	 * Search a string in all slides of the Presentation.
	 */
	function MsPowerpoint_SearchInSlides($str, $returnFirstFound = true) {

		// init the list of slides
		$this->MsPowerpoint_InitSlideLst(); // List of slides

		// build the list of files in the expected structure
		$files = array();
		foreach($this->OpenXmlSlideLst as $i=>$s) $files[$i+1] = $s['idx'];

		// search
		$find = $this->TbsSearchInFiles($files, $str, $returnFirstFound);

		return $find;

	}

	function MsPowerpoint_SlideDebug($nl, $sep, $bull) {

		$this->MsPowerpoint_InitSlideLst(); // List of slides

		echo $nl;
		echo $nl.count($this->OpenXmlSlideLst)." slide(s) in the Presentation:";
		echo $nl."-------------------------------";
		foreach ($this->OpenXmlSlideLst as $i => $s) {
			echo $bull."#".($i+1).": ".basename($s['file']).", file: " . $s['file'];
		}
		if (count($this->OpenXmlSlideLst)==0) echo $bull."(none)";

		// List of charts
		echo $nl;
		echo $nl."Charts found in slides:";
		echo $nl."-------------------------";

		$nbr = 0;
		for ($s=1; $s <= count($this->OpenXmlSlideLst); $s++) {
			$this->OnCommand(OPENTBS_SELECT_SLIDE, $s);
			$ChartLst = $this->OpenXML_ChartGetInfoFromFile($this->TbsCurrIdx);
			foreach ($ChartLst as $i=>$c) {
				$name = ($c['name']===false) ? '(not found)' : $c['name'];
				$title = ($c['title']===false) ? '(not found)' : var_export($c['title'], true);
				echo $bull."slide: $s, chart name: '$name', title: $title";
				if ($c['descr']!==false) echo ", description: ".$c['descr'];
				$nbr++;
			}
		}
		if ($nbr==0) echo $bull."(none)";

	}

	// Actually delete slides in the Presentation
	function MsPowerpoint_SlideDelete() {

		if ( (count($this->OtbsSheetSlidesDelete)==0) && (count($this->OtbsSheetSlidesVisible)==0) ) return;

		$this->MsPowerpoint_InitSlideLst();

		// Edit both XML and REL of file 'presentation.xml'

		$xml_file = 'ppt/presentation.xml';
		$xml_idx = $this->FileGetIdx($xml_file);
		$rel_idx = $this->FileGetIdx($this->OpenXML_Rels_GetPath($xml_file));

		$xml_txt = $this->TbsStoreGet($xml_idx, 'Slide Delete and Display / XML');
		$rel_txt = $this->TbsStoreGet($rel_idx, 'Slide Delete and Display / REL');

		$del_lst = array();
		$del_lst2 = array();
		$first_kept = false; // Name of the first slide, to be kept 
		foreach ($this->OpenXmlSlideLst as $i=>$s) {
			$ref = 'i:'.($i+1);
			if (isset($this->OtbsSheetSlidesDelete[$ref]) && $this->OtbsSheetSlidesDelete[$ref] ) {

				// the rid may be used several time in the fiel. i.e.: in <p:sldIdLst><p:sldIdLst>, but also in <p:custShow><p:sldLst>
				while ( ($x = clsTbsXmlLoc::FindElementHavingAtt($xml_txt, 'r:id="'.$s['rid'].'"', 0))!==false ) {
					$x->ReplaceSrc(''); // delete the element
				}

				$x = clsTbsXmlLoc::FindElementHavingAtt($rel_txt, 'Id="'.$s['rid'].'"', 0);
				if ($x!==false) $x->ReplaceSrc(''); // delete the element

				$del_lst[] = $s['file'];
				$del_lst[] = $this->OpenXML_Rels_GetPath($s['file']);
				$del_lst2[] = basename($s['file']);

			} else {
				$first_kept = basename($s['file']);
			}
		}

		$this->TbsStorePut($xml_idx, $xml_txt);
		$this->TbsStorePut($rel_idx, $rel_txt);
		unset($xml_txt, $rel_txt);

		// Delete references in '[Content_Types].xml'
		foreach ($del_lst as $f) {
			$this->OpenXML_CTypesDeletePart('/'.$f);
		}

		// Change references in 'viewProps.xml.rels'
		$idx = $this->FileGetIdx('ppt/_rels/viewProps.xml.rels');
		$txt = $this->TbsStoreGet($idx, 'Slide Delete and Display / viewProps');
		$ok = false;
		foreach ($del_lst2 as $f) {
			$z = 'Target="slides/'.$f.'"';
			if (strpos($txt, $z)) {
				if ($first_kept===false) return $this->RaiseError("(Slide Delete and Display) : no slide left to replace the default slide in 'viewProps.xml.rels'.");
				$ok = true;
				$txt = str_replace($z, 'Target="slides/'.$first_kept.'"' , $txt);
			}
		}
		if ($ok) $this->TbsStorePut($idx, $txt);

		// Actually delete the slide files
		foreach ($del_lst as $f) {
			$idx = $this->FileGetIdx($f);
			unset($this->TbsStoreLst[$idx]); // delete the slide from the merging if any
			$this->FileReplace($idx, false);
		}

	}

	/**
	 * Return true if the file name is a slide.
	 */
	function MsPowerpoint_SlideIsIt($FileName) {
		$this->MsPowerpoint_InitSlideLst();
		foreach ($this->OpenXmlSlideLst as $i => $s) {
			if ($FileName==$s['file']) return true;
		}
		return false;
	}
	
	// Cleaning tags in MsWord
	function MsWord_Clean(&$Txt) {
		$Txt = str_replace('<w:lastRenderedPageBreak/>', '', $Txt); // faster
		//$this->MsWord_CleanFallbacks($Txt);
		$this->XML_DeleteElements($Txt, array('w:proofErr', 'w:noProof', 'w:lang', 'w:lastRenderedPageBreak'));
		$this->MsWord_CleanSystemBookmarks($Txt);
		$this->MsWord_CleanRsID($Txt);
		$this->MsWord_CleanDuplicatedLayout($Txt);
	}
	
	/**
	 * <mc:Fallback> entities may contains duplicated TBS fields and this may corrupt the merging.
	 * This function delete such entities if they seems to contain TBS fields. This make the DOCX content less compatible with previous Word versions.
	 * https://wiki.openoffice.org/wiki/OOXML/Markup_Compatibility_and_Extensibility
	 */ 
	function MsWord_CleanFallbacks(&$Txt) {
		
		$p = 0;
		$nb = 0;
		while ( ($loc = clsTbsXmlLoc::FindElement($Txt,'mc:Fallback',$p))!==false ) {
			if (strpos($loc->GetSrc(), $this->TBS->_ChrOpen) !== false ) {
				$loc->Delete();
				$nb++;
			}
			$p = $loc->PosEnd;
		}

	}
	
	function MsWord_CleanSystemBookmarks(&$Txt) {
	// Delete GoBack hidden bookmarks that appear since Office 2010. Example: <w:bookmarkStart w:id="0" w:name="_GoBack"/><w:bookmarkEnd w:id="0"/>

		$x = ' w:name="_GoBack"/><w:bookmarkEnd ';
		$x_len = strlen($x);

		$b = '<w:bookmarkStart ';
		$b_len = strlen($b);

		$nbr_del = 0;

		$p = 0;
		while ( ($p=strpos($Txt, $x, $p))!==false ) {
			$pe = strpos($Txt, '>', $p + $x_len);
			if ($pe===false) return false;
			$pb = strrpos(substr($Txt,0,$p) , '<');
			if ($pb===false) return false;
			if (substr($Txt, $pb, $b_len)===$b) {
				$Txt = substr_replace($Txt, '', $pb, $pe - $pb + 1); 
				$p = $pb;
				$nbr_del++;
			} else {
				$p = $pe +1;
			}
		}

		return $nbr_del;

	}

	/**
	 * Delete XML attributes relative to log of user modifications. Returns the number of deleted attributes.
	 * In order to insert such information, MsWord does split TBS tags with XML elements.
	 * After such attributes are deleted, we can concatenate duplicated XML elements.
	 */
	function MsWord_CleanRsID(&$Txt) {

		$rs_lst = array('w:rsidR', 'w:rsidRPr');

		$nbr_del = 0;
		foreach ($rs_lst as $rs) {

			$rs_att = ' '.$rs.'="';
			$rs_len = strlen($rs_att);

			$p = 0;
			while ($p!==false) {
				// search the attribute
				$ok = false;
				$p = strpos($Txt, $rs_att, $p);
				if ($p!==false) {
					// attribute found, now seach tag bounds
					$po = strpos($Txt, '<', $p);
					$pc = strpos($Txt, '>', $p);
					if ( ($pc!==false) && ($po!==false) && ($pc<$po) ) { // means that the attribute is actually inside a tag
						$p2 = strpos($Txt, '"', $p+$rs_len); // position of the delimiter that closes the attribute's value
						if ( ($p2!==false) && ($p2<$pc) ) {
							// delete the attribute
							$Txt = substr_replace($Txt, '', $p, $p2 -$p +1);
							$ok = true;
							$nbr_del++;
						}
					}
					if (!$ok) $p = $p + $rs_len;
				}
			}

		}

		// delete empty tags
		$Txt = str_replace('<w:rPr></w:rPr>', '', $Txt);
		$Txt = str_replace('<w:pPr></w:pPr>', '', $Txt);

		return $nbr_del;

	}

	/**
	 * MsWord cut the source of the text when a modification is done. This is splitting TBS tags.
	 * This function repare the split text by searching and delete duplicated layout.
	 * Return the number of deleted dublicates.
	 */
	function MsWord_CleanDuplicatedLayout(&$Txt) {

		$wro = '<w:r';
		$wro_len = strlen($wro);

		$wrc = '</w:r';
		$wrc_len = strlen($wrc);

		$wto = '<w:t';
		$wto_len = strlen($wto);

		$wtc = '</w:t';
		$wtc_len = strlen($wtc);

		$preserve = 'xml:space="preserve"';
		$preserve_len = strlen($preserve);

		$nb_tot = 0;
		$wro_p = 0;
		while ( ($wro_p = $this->XML_SearchTagForward($Txt, $wro, $wro_p)) !== false ) { // next <w:r> tag
			$wto_p = $this->XML_SearchTagForward($Txt, $wto, $wro_p); // next <w:t> tag
			if ($wto_p === false) return false; // error in the structure of the <w:r> element
			$first = true;
			$nb = 0; // number of duplicated layouts for the current text snippet
			do {
				$ok = false;
				$wtc_p = $this->XML_SearchTagForward($Txt, $wtc, $wto_p); // next </w:t> tag
				if ($wtc_p === false) return false;
				$wrc_p = $this->XML_SearchTagForward($Txt, $wrc, $wro_p); // next </w:r> tag (only to check inclusion)
				if ($wrc_p === false) return false;
				if ( ($wto_p < $wrc_p) && ($wtc_p < $wrc_p) ) { // if the <w:t> is actually included in the <w:r> element
					if ($first) {
						// we build the xml that would be the duplicated layout if any
						$p = strpos($Txt, '<', $wrc_p + $wrc_len);
						$x = substr($Txt, $wtc_p, $p - $wtc_p); // '</w:t></w:r>   ' may include some linebreaks or spaces after the closing tags
						$src_to_del = $x . substr($Txt, $wro_p, ($wto_p + $wto_len) - $wro_p); // without the last symbol, like: '</w:t></w:r><w:r>....<w:t'
						$src_to_del = str_replace('<w:tab/>', '', $src_to_del); // tabs must not be deleted between parts => they nt be in the superfluous string
						$src_to_del_len = strlen($src_to_del);
						$first = false;
					}
					// if the following source is a duplicated layout then we delete it by joining the <w:r> elements.
					$p_att = $wtc_p + $src_to_del_len;
					$x = substr($Txt, $p_att, 1); // help to optimize the check because if it's a duplicated layout, the char after is the end of the '<w:t' element.
					if ( (($x === ' ') || ($x === '>')) && (substr($Txt, $wtc_p, $src_to_del_len)===$src_to_del) ) {
						$p_end = strpos($Txt, '>', $p_att); //
						if ($p_end === false) return false; // error in the structure of the <w:t> tag
						$Txt = substr_replace($Txt, '', $wtc_p, $p_end - $wtc_p + 1); // delete superfluous part + <w:t> attributes
						$nb_tot++;
						$nb++;
						$ok = true;
					}
				}
			} while ($ok);

			// Add or delete the attribute « xml:space="preserve" » that must be set if there is a space before of after the text
			if ($nb > 0) {
				$with_space = false;
				if ( substr($Txt, $wtc_p - 1, 1) === ' ') $with_space = true;
				$p_end = strpos($Txt, '>', $wto_p); // first char of the text
				if ( substr($Txt, $p_end + 1, 1) === ' ') $with_space = true;
				$src = substr($Txt, $wto_p, $p_end - $wto_p + 1);
				$p = strpos($src, $preserve);
				if ( $with_space && ($p === false) ) {
					// add the attribute
					$Txt = substr_replace($Txt, ' ' . $preserve, $p_end, 0);
				} elseif ( (!$with_space) && ($p !== false) ) {
					// delete the attribute
					$Txt = substr_replace($Txt, '', $wto_p + $p -1, $preserve_len + 1); // delete the attribut with the space before it
				}
			}

			$wro_p = $wro_p + $wro_len;

		}

		return $nb_tot; // number of total replacements

	}

	/**
	 * Prevent from the problem of missing spaces when calling ->MsWord_CleanRsID() or under certain merging circumstances.
	 * Replace attribute xml:space="preserve" used in <w:t>, with the same attribute in <w:document>.
	 * This trick use an attribute of <w:document> element that works for all MsWord versions (last tested is 2019) but is undocumented.
	 * So it may may be disabled by default in a next version.
	 * LibreOffice does ignore this attribute in <w:document>.
	 */
	function MsWord_CleanSpacePreserve(&$Txt) {
		
		$XmlLoc = clsTbsXmlLoc::FindStartTag($Txt, 'w:document', 0);

		// Checks
		if ($XmlLoc===false) return;
		if ($XmlLoc->GetAttLazy('xml:space') === 'preserve') return;
		
		// We delete the attribute on <w:t> elements. This is not not mendatory but cleanner and save space.
		// Canceled because needed for LibreOffice
		// $Txt = str_replace(' xml:space="preserve"', '', $Txt);
		
		$XmlLoc->ReplaceAtt('xml:space', 'preserve', true);

	}

	/**
	 * Renumber attribute "id " of elements <wp:docPr> in order to ensure unicity.
	 * Such elements are used in objects.
	 */
	function MsWord_RenumDocPr(&$Txt) {

		$this->MsWord_DocPrId;
	
		$el = '<wp:docPr ';
		$el_len = strlen($el);

		$id = ' id="';
		$id_len = strlen($id);

		$nbr = 0;

		$p = 0;
		while ($p!==false) {
			// search the element
			$p = strpos($Txt, $el, $p);
			if ($p!==false) {
				// attribute found, now seach tag bounds
				$p = $p + $el_len -1; // don't take the space, it is used for the next search
				$pc = strpos($Txt, '>', $p);
				if ($pc!==false) {
					$x = substr($Txt, $p, $pc - $p);
					$pi = strpos($x, $id);
					if ($pi!==false) {
						$pi = $pi + $id_len;
						$pq = strpos($x, '"', $pi);
						if ($pq!==false) {
							$i_len = $pq - $pi;
							$i = intval(substr($x, $pi, $i_len));
							if ($i>0) { // id="0" is erroneous
								if ($i > $this->MsWord_DocPrId) {
									$this->MsWord_DocPrId = $i; // nothing else to do
								} else {
									$this->MsWord_DocPrId++;
									$id_txt = '' . $this->MsWord_DocPrId;
									$Txt = substr_replace($Txt, $id_txt, $p + $pi, $i_len);
									$nbr++;
								}
							}
						}
					}
				}
			}
		}

		return $nbr;

	}

	// Alias of block: 'tbs:page'
	function MsWord_GetPage($Tag, $Txt, $Pos, $Forward, $LevelStop) {

		// Search the two possible tags for having a page-break
		$loc1 = clsTbsXmlLoc::FindStartTagHavingAtt($Txt, 'w:type="page"', $Pos, $Forward);
		$loc2 = clsTbsXmlLoc::FindStartTag($Txt, 'w:pageBreakBefore', $Pos, $Forward);

		// Define the position of start for the corresponding paragraph 
		if ( ($loc1===false) && ($loc2===false) ) {
			if ($Forward) {
				// End of the last paragraph of the document.
				// The <w:p> elements can be embeded, and it can be a single tag if it cnotains no text.
				$loc = clsTbsXmlLoc::FindElement($Txt, 'w:p', strlen($Txt), false);
				if ($loc===false) return false;
				return $loc->PosEnd;
			} else {
				// start of the first paragraph of the document
				$loc = clsTbsXmlLoc::FindStartTag($Txt, 'w:p', 0, true);
				if ($loc===false) return false;
				return $loc->PosBeg;
			}
		}

		// Take care that <w:p> elements can be sef-embeded.
		// 	That's why we assume that there is no page-break in an embeded paragraph while it is useless but possible.
		if ($loc1===false) {
			$s = $loc2->PosBeg;
		} elseif($loc2===false) {
			$s = $loc1->PosBeg;
		} else {
			if ($Forward) {
				$s = ($loc1->PosBeg < $loc2->PosBeg) ? $loc1->PosBeg : $loc2->PosBeg;
			} else {
				$s = ($loc1->PosBeg > $loc2->PosBeg) ? $loc1->PosBeg : $loc2->PosBeg;
			}
		}
		$loc = clsTbsXmlLoc::FindStartTag($Txt, 'w:p', $s, false);

		$p = $loc->PosBeg;
		if ($Forward) $p--; // if it's forward, we stop the block before the paragraph with page-break
		return $p;
		
	}

	// Alias of block: 'tbs:draw'
	function MsWord_GetDraw($Tag, $Txt, $Pos, $Forward, $LevelStop) {
		
		$tags = array('mc:AlternateContent', 'w:r');
		
		$p = $Pos;
		
		if ($Forward) {
			foreach ($tags as $tag) {
				$p = strpos($Txt, '</' . $tag . '>', $p);
				if ($p === false) return false;
			}
		} else {
			foreach ($tags as $tag) {
				$loc = clsTbsXmlLoc::FindStartTag($Txt, $tag, $p, false);
				if ($loc === false) return false;
				$p = $loc->PosBeg;
			}
		}
		
		return $p;
		
	}

	/**
	 * Alias of block: 'tbs:section'
	 * In Docx, section-breaks <w:sectPr> can be saved in the last <w:p> of the section, or just after the last <w:p> of the section.
	 * In practice, there is always at least one sectin-break and only the last section-break is saved outside the <w:p>.
	 */ 
	function MsWord_GetSection($Tag, $Txt, $Pos, $Forward, $LevelStop) {

		// First we check if the TBS tag is inside a <w:p> and if this <w:p> has a <w:sectPr>
		$case = false;
		$locP = clsTbsXmlLoc::FindStartTag($Txt, 'w:p', $Pos, false);
		if ($locP!==false) {
			$locP->FindEndTag(true);
			if ($locP->PosEnd>$Pos) {
				$src = $locP->GetSrc();
				$loc = clsTbsXmlLoc::FindStartTag($src, 'w:sectPr', 0, true);
				if ($loc!==false) $case = true;
			}
		}

		if ($case && $Forward) return $locP->PosEnd;

		// Look for the next section-break
		$p = ($Forward) ? $locP->PosEnd : $locP->PosBeg;
		$locS = clsTbsXmlLoc::FindStartTag($Txt, 'w:sectPr', $p, $Forward);

		if ($locS===false) {
			if ($Forward) {
				// end of the body
				$p = strpos($Txt, '</w:body>', $Pos);
				return ($p===false) ? false : $p - 1;
			} else {
				// start of the body
				$loc2 = clsTbsXmlLoc::FindStartTag($Txt, 'w:body', 0, true);
				return ($loc2===false) ? false : $loc2->PosEnd + 1;
			}
		}

		// is <w:sectPr> inside a <w:p> ?
		$ewp = '</w:p>';
		$inside = false;
		$p = strpos($Txt, $ewp, $locS->PosBeg);
		if ($p!==false) {
			$loc2 = clsTbsXmlLoc::FindStartTag($Txt, 'w:p', $locS->PosBeg, true);
			if ( ($loc2===false) || ($loc2->PosBeg>$p) ) $inside = true;
		}

		$offset = ($Forward) ? 0 : 1;
		if ($inside) {
			return $p + strlen($ewp) - 1 + $offset;
		} else {
			// not inside
			$locS->FindEndTag();
			return $locS->PosEnd + $offset;
		}

	}

	/**
	 * Initialize information about header and footer files
	 */
	function MsWord_InitHeaderFooter() {
	
		if ($this->MsWord_HeaderFooter!==false) return;

		$types_ok = array('default' => true, 'first' => false, 'even' => false);
		
		// Is there a different header/footer for odd an even pages ?
		$idx = $this->FileGetIdx('word/settings.xml');
		if ($idx!==false) {		
			$Txt = $this->TbsStoreGet($idx, 'GetHeaderFooterFile');
			$types_ok['even'] = (strpos($Txt, '<w:evenAndOddHeaders/>')!==false);
			unset($Txt);
		}

		// Is there a different header/footer for the first page ?
		$idx = $this->FileGetIdx('word/document.xml');
		if ($idx===false) return false;
		$Txt = $this->TbsStoreGet($idx, 'GetHeaderFooterFile');
		$types_ok['first'] = (strpos($Txt, '<w:titlePg/>')!==false);

		$places = array('header', 'footer');
		$files = array();
		$rels = $this->OpenXML_Rels_GetObj('word/document.xml', '');
		
		foreach ($places as $place) {
			$p = 0;
			$entity = 'w:' . $place . 'Reference';
			while ($loc = clsTbsXmlLoc::FindStartTag($Txt, $entity, $p)) {
				$p = $loc->PosEnd;
				$type = $loc->GetAttLazy('w:type');
				if (isset($types_ok[$type]) && $types_ok[$type]) {
					$rid = $loc->GetAttLazy('r:id');
					if (isset($rels->TargetLst[$rid])) {
						$target = $rels->TargetLst[$rid];
						$files[] = array('file' => ('word/'.$target), 'type' => $type, 'place' => $place);
					}
				}
			}
		}

		$this->MsWord_HeaderFooter = $files;
	
	}
	
	/**
	 * Retrieve the header/footer sub-file.
	 * @param mixed $TbsCmd  OPENTBS_SELECT_HEADER or OPENTBS_SELECT_FOOTER.
	 * @param mixed $TbsType OPENTBS_DEFAULT, OPENTBS_FIRST or OPENTBS_EVEN. 
	 * @param int [$Offset] Since a DCX can have several sections, and each section can have its own header/footer, this options 
	 * @return mixed The name of the file of false if no file is found. 
	 */
	function MsWord_GetHeaderFooterFile($TbsCmd, $TbsType, $Offset = 0) {

		$this->MsWord_InitHeaderFooter();

		$Place = 'header';
		if ($TbsCmd==OPENTBS_SELECT_FOOTER) {
			$Place = 'footer';
		}

		$Type = 'default';
		if ($TbsType==OPENTBS_FIRST) {
			$Type = 'first';
		} elseif ($TbsType==OPENTBS_EVEN) {
			$Type = 'even';
		}

		$nb = 0;
		foreach($this->MsWord_HeaderFooter as $info) {
			if ( ($info['type']==$Type) && ($info['place']==$Place) ) {
				if ($nb==$Offset) {
					return $info['file'];
				} else {
					$nb++;
				}
			}
		}
		
		return false;
		
	}
	
	function MsWord_DocDebug($nl, $sep, $bull) {

		$ChartLst = $this->OpenXML_ChartGetInfoFromFile($this->Ext_GetMainIdx());

		echo $nl;
		echo $nl."Charts found in the body:";
		echo $nl."-------------------------";
		foreach ($ChartLst as $i=>$c) {
			$name = ($c['name']===false) ? '(not found)' : $c['name'];
			$title = ($c['title']===false) ? '(not found)' : var_export($c['title'], true);
			echo $bull."name: '$name', title: $title";
			if ($c['descr']!==false) echo ", description: ".$c['descr'];
		}

	}

	// OpenOffice documents

	function OpenDoc_CleanRsID(&$Txt) {
	
		// Get all style names about RSID for <span> elements
		$styles = array();
		$p = 0;
		while ( ($el = clsTbsXmlLoc::FindStartTagHavingAtt($Txt, 'officeooo:rsid', $p)) !== false) {
			// If the <style:text-properties> element has only this attribute then its length is 50.
			if ($el->GetLen() < 60) {
				if ($par = clsTbsXmlLoc::FindStartTag($Txt, 'style:style', $el->PosBeg, false)) {
					if ($name = $par->GetAttLazy('style:name')) {
						$styles[] = $name;
					}
				}
			}
			$p = $el->PosEnd;
		}
		
		// Delete <text:span> elements
		$xe = '</text:span>';
		$xe_len = strlen($xe);
		foreach ($styles as $name) {
			$p = 0;
			$x = '<text:span text:style-name="' . $name . '">';
			$x_len = strlen($x);
			while ( ($p = strpos($Txt, $x, $p)) !== false) {
				$pe = strpos($Txt, $xe, $p);
				$src = substr($Txt, $p + $x_len, $pe - $p - $x_len);
				$Txt = substr_replace($Txt, $src, $p, $pe + $xe_len - $p);
				$p = $p + strlen($src);
			}
		}
	
	}
	
	function OpenDoc_ManifestChange($Path, $Type) {
	// Set $Type=false in order to mark the the manifest entry to be deleted.
	// Video and sound files are not to be registered in the manifest since the contents is not saved in the document.

		// Initialization
		if ($this->OpenDocManif===false) $this->OpenDocManif = array();

		// We try to found the type of image
		if (($Type==='') && (substr($Path,0,9)==='Pictures/')) {
			$ext = basename($Path);
			$p = strrpos($ext, '.');
			if ($p!==false) {
				$ext = strtolower(substr($ext,$p+1));
				if (isset($this->ExtInfo['pic_ext'][$ext])) $Type = 'image/'.$this->ExtInfo['pic_ext'][$ext];
			}
		}

		$this->OpenDocManif[$Path] = $Type;

	}

	function OpenDoc_ManifestCommit($Debug) {

		// Retrieve the content of the manifest
		$name = 'META-INF/manifest.xml';
		$idx = $this->FileGetIdx($name);
		if ($idx===false) return;

		$Txt = $this->TbsStoreGet($idx, 'OpenDocumentFormat');
		if ($Txt===false) return false;

		// Perform all changes
		foreach ($this->OpenDocManif as $Path => $Type) {
			$x = 'manifest:full-path="'.$Path.'"';
			$p = strpos($Txt,$x);
			if ($Type===false) {
				// the entry should be deleted
				if ($p!==false) {
					$p1 = strrpos(substr($Txt,0,$p), '<');
					$p2 = strpos($Txt,'>',$p);
					if (($p1!==false) && ($p2!==false)) $Txt = substr($Txt,0,$p1).substr($Txt,$p2+1);
				}
			} else {
				// the entry should be added
				if ($p===false) {
					$p = strpos($Txt,'</manifest:manifest>');
					if ($p!==false) {
						$x = ' <manifest:file-entry manifest:media-type="'.$Type.'" '.$x.'/>'."\n";
						$Txt = substr_replace($Txt, $x, $p, 0);
					}
				}
			}
		}

		// Save changes (no need to save it in the park because this fct is called after merging)
		$this->FileReplace($idx, $Txt);

		if ($Debug) $this->DebugLst[$name] = $Txt;

	}

	function OpenDoc_ChangeCellType(&$Txt, &$Loc, $Ope, $IsMerging, &$Value) {
	// change the type of a cell in an ODS file

		$Loc->PrmLst['cellok'] = true; // avoid the field to be processed twice

		if ($Ope==='odsStr') return true;

		static $OpeLst = array(
			'tbs:num'=>'float',
			'tbs:percent'=>'percentage',
			'tbs:curr'=>'currency',
			'tbs:bool'=>'boolean',
			'tbs:date'=>'date',
			'tbs:time'=>'time',
			// for compatibility
			'odsNum'=>'float',
			'odsPercent'=>'percentage',
			'odsCurr'=>'currency',
			'odsBool'=>'boolean',
			'odsDate'=>'date',
			'odsTime'=>'time',
		);
		
		static $TypeLst = array(
			'float' => array('attval' => 'office:value'),
			'percentage' => array('attval' => 'office:value'),
			'currency' => array('attval' => 'office:value', 'attcurr' => 'office:currency'),
			'boolean' => array('attval' => 'office:boolean-value'),
			'date' => array('attval' => 'office:date-value', 'frm' => 'yyyy-mm-ddThh:nn:ss'),
			'time' => array('attval' => 'office:time-value', 'frm' => '"PT"hh"H"nn"M"ss"S"'),
		);

		if (!isset($OpeLst[$Ope])) return false;

		$new_type = $OpeLst[$Ope];
		$new_atts = $TypeLst[$new_type];
		
		$xLoc = clsTbsXmlLoc::FindStartTag($Txt, 'table:table-cell', $Loc->PosBeg, false);
		if ($xLoc===false) return false; // error in the XML structure
		
		// Replace the current TBS field with blank chars
		// This prevent from cases when the TBS field is not inside the cell (is this even possible ?)
		$len = $Loc->PosEnd - $Loc->PosBeg + 1;
		$Txt = substr_replace($Txt, str_repeat(' ',$len), $Loc->PosBeg, $len);
		
		$xLoc->switchToRelative();
		
		// Update attributes
		$xLoc->DeleteAtt('calcext:value-type'); // new attribute in LibreOffice 4
		$xLoc->ReplaceAtt('office:value-type', $new_type, true);
		$xLoc->ReplaceAtt($new_atts['attval'], '[]', true); // [] are the new bounds of the TBS field
		if (isset($new_atts['attcurr']) && isset($Loc->PrmLst['currency'])) $xLoc->ReplaceAtt('office:currency', $Loc->PrmLst['currency'], true);

		// Delete contents
		$xLocP = clsTbsXmlLoc::FindElement($xLoc, 'text:p', 0);
		if ($xLocP!==false) {
			$xLocP->Delete();
			$xLocP->UpdateParent();
		}
		
		// move the TBS field
		$p_fld = strpos($xLoc->Txt, '[', 0); // new position of the fields in $Txt

		$xLoc->switchToNormal();
		
		$Loc->PosBeg = $xLoc->PosBeg + $p_fld;
		$Loc->PosEnd = $xLoc->PosBeg + $p_fld +1;
		
		if ($IsMerging) {
			// the field is currently being merged
			if ($new_type==='boolean') {
				if ($Value) {
					$Value = 'true';
				} else {
					$Value = 'false';
				}
			} elseif (isset($new_atts['frm'])) {
				$prm = array('frm'=>$new_atts['frm']);
				$Value = $this->TBS->meth_Misc_Format($Value,$prm);
			}
			$Loc->ConvStr = false;
			$Loc->ConvProtect = false;
		} else {
			if (isset($new_atts['frm'])) $Loc->PrmLst['frm'] = $new_atts['frm'];
		}

	}

	function OpenDoc_SheetSlides_Init($sheet, $force = false) {

		if (($this->OpenDoc_SheetSlides!==false) && (!$force) ) return;

		$this->OpenDoc_SheetSlides = array();     // sheet/slide info sorted by location

		$idx = $this->Ext_GetMainIdx();
		if ($idx===false) return;
		$Txt = $this->TbsStoreGet($idx, 'Sheet/Slide Info');
		if ($Txt===false) return false;
		if ($this->LastReadNotStored) $this->TbsStorePut($idx, $Txt);
		$this->OpenDoc_SheetSlides_FileId = $idx;

		$tag = ($sheet) ? 'table:table' : 'draw:page';

		// scann sheet/slide list
		$p = 0;
		$idx = 0;
		while ($loc=clsTinyButStrong::f_Xml_FindTag($Txt, $tag, true, $p, true, false, true, true) ) {
			$this->OpenDoc_SheetSlides[$idx] = $loc;
			$idx++;
			$p = $loc->PosEnd;
		}

	}

	// Actally delete hide or display Sheets and Slides in a ODS or ODP
	function OpenDoc_SheetSlides_DeleteAndDisplay($sheet) {

		if ( (count($this->OtbsSheetSlidesDelete)==0) && (count($this->OtbsSheetSlidesVisible)==0) ) return;

		$this->OpenDoc_SheetSlides_Init($sheet, true);
		$Txt = $this->TbsStoreGet($this->OpenDoc_SheetSlides_FileId, 'Sheet Delete and Display');

		if ($sheet) {
			// Sheet
			$tag_close = '</table:table>';
			$att_name = 'table:name';
			$att_style = 'table:style-name';
			$att_display = 'table:display';
			$yes_display = 'true';
			$not_display = 'false';
			$tag_property = 'style:table-properties';
		} else {
			// Slide
			$tag_close = '</draw:page>';
			$att_name = 'draw:name';
			$att_style = 'draw:style-name';
			$att_display = 'presentation:visibility';
			$yes_display = 'visible';
			$not_display = 'hidden';
			$tag_property = 'style:drawing-page-properties';
		}
		$tag_close_len = strlen($tag_close);

		$styles_to_edit = array();
		// process sheet in rever order of their positions
		for ($idx = count($this->OpenDoc_SheetSlides) - 1; $idx>=0; $idx--) {
			$loc = $this->OpenDoc_SheetSlides[$idx];
			$id = 'i:'.($idx + 1);
			$name = 'n:'.$loc->PrmLst[$att_name];
			if ( isset($this->OtbsSheetSlidesDelete[$name]) || isset($this->OtbsSheetSlidesDelete[$id]) ) {
				// Delete the sheet
				$p = strpos($Txt, $tag_close, $loc->PosEnd);
				if ($p===false) return; // XML error
				$Txt = substr_replace($Txt, '', $loc->PosBeg, $p + $tag_close_len - $loc->PosBeg);
				unset($this->OtbsSheetSlidesDelete[$name]);
				unset($this->OtbsSheetSlidesDelete[$id]);
				unset($this->OtbsSheetSlidesVisible[$name]);
				unset($this->OtbsSheetSlidesVisible[$id]);
			} elseif ( isset($this->OtbsSheetSlidesVisible[$name]) || isset($this->OtbsSheetSlidesVisible[$id]) ) {
				// Hide or dispay the sheet
				$visible = (isset($this->OtbsSheetSlidesVisible[$name])) ? $this->OtbsSheetSlidesVisible[$name] : $this->OtbsSheetSlidesVisible[$id];
				$visible = ($visible) ? $yes_display : $not_display;
				if (isset($loc->PrmLst[$att_style])) {
					$style = $loc->PrmLst[$att_style];
					$new = $style.'_tbs_'.$visible;
					if (!isset($styles_to_edit[$style])) $styles_to_edit[$style] = array();
					$styles_to_edit[$style][$visible] = $new; // mark the style to be edited
					$pi = $loc->PrmPos[$att_style];
					$Txt = substr_replace($Txt, $pi[4].$new.$pi[4], $pi[2], $pi[3]-$pi[2]);
				}
				unset($this->OtbsSheetSlidesVisible[$name]);
				unset($this->OtbsSheetSlidesVisible[$id]);
			}
		}

		// process styles to edit
		if (count($styles_to_edit)>0) {
			$tag_close = '</style:style>';
			$tag_close_len = strlen($tag_close);
			$p = 0;
			while ($loc=clsTinyButStrong::f_Xml_FindTag($Txt, 'style:style', true, $p, true, false, true, false) ) {
				$p = $loc->PosEnd;
				if (isset($loc->PrmLst['style:name'])) {
					$name = $loc->PrmLst['style:name'];
					if (isset($styles_to_edit[$name])) {
						// retrieve the full source of the <style:style> element
						$p = strpos($Txt, $tag_close, $p);
						if ($p===false) return; // bug in the XML contents
						$p = $p + $tag_close_len;
						$src = substr($Txt, $loc->PosBeg, $p - $loc->PosBeg);
						// add the attribute, if missing
						if (strpos($src, ' '.$att_display.'="')===false)  $src = str_replace('<'.$tag_property.' ', '<'.$tag_property.' '.$att_display.'="'.$yes_display.'" ', $src);
						// add new styles
						foreach ($styles_to_edit[$name] as $visible => $newName) {
							$not = ($visible===$not_display) ? $yes_display : $not_display;
							$src2 = str_replace(' style:name="'.$name.'"', ' style:name="'.$newName.'"', $src);
							$src2 = str_replace(' '.$att_display.'="'.$not.'"', ' '.$att_display.'="'.$visible.'"', $src2);
							$Txt = substr_replace($Txt, $src2, $loc->PosBeg, 0);
							$p = $p + strlen($src2);
						}
					}
				}
			}

		}

		// store the result
		$this->TbsStorePut($this->OpenDoc_SheetSlides_FileId, $Txt);

		$this->TbsSheetCheck();

	}

	function OpenDoc_SheetSlides_Debug($sheet, $nl, $sep, $bull) {

		$this->OpenDoc_SheetSlides_Init($sheet);

		$text = ($sheet) ? "Sheets in the Workbook" : "Slides in the Presentation";
		$att = ($sheet) ? 'table:name' : 'draw:name';

		echo $nl;
		echo $nl.$text.":";
		echo $nl."-----------------------";
		foreach ($this->OpenDoc_SheetSlides as $idx => $loc) {
			$name = str_replace(array('&amp;','&quot;','&lt;','&gt;'), array('&','"','<','>'), $loc->PrmLst[$att]);
			echo $bull."id: ".($idx+1).", name: [".$name."]";
		}

	}

	function OpenDoc_StylesInit() {

		if ($this->OpenDoc_Styles!==false) return;

		$this->OpenDoc_Styles = array();     // sheet info sorted by location

		$Styles = array();

		// Read styles in 'styles.xml'
		$idx = $this->FileGetIdx('styles.xml');
		if ($idx!==false) {
			$Txt = $this->TbsStoreGet($idx, 'Style Init styles.xml');
			if ($Txt==!false) $this->OpenDoc_StylesFeed($Styles, $Txt);
		}

		// Read styles in 'content.xml'
		$idx = $this->FileGetIdx('content.xml');
		if ($idx!==false){
			$Txt = $this->TbsStoreGet($idx, 'Style Init content.xml');
			if ($Txt!==false) $this->OpenDoc_StylesFeed($Styles, $Txt);
		}

		// define childs
		foreach($Styles as $n => $s) {
			if ( ($s->parentName!==false) && isset($Styles[$s->parentName]) ) $Styles[$s->parentName]->childs[$s->name] = &$s;
		}

		// propagate page-break property to alla childs
		$this->OpenDoc_StylesPropagate($Styles);

		$this->OpenDoc_Styles = $Styles;

	}

	function OpenDoc_RangeNamesInit() {
		
		if ($this->OtbsSheetRangeNames !== false) return;
		
		$this->OtbsSheetRangeNames = array();

		// Get the content.xml contents
		$idx = $this->FileGetIdx('content.xml');
		if ($idx===false) return;
		$Txt = $this->TbsStoreGet($idx, 'RangeNamesInit'); // use the store, so the file will be available for editing if needed
		if ($Txt===false) return false;
		//$this->TbsStorePut($idx, $Txt);

		// Sheet ranges
		$p = 0;
		while ( $el = clsTbsXmlLoc::FindElement($Txt, 'table:named-range', $p, true) ) {
			$name = $el->GetAttLazy('table:name');
			$ref  = $el->GetAttLazy('table:cell-range-address');
			if ($ref === false) {
				$ref  = $el->GetAttLazy('table:base-cell-address'); // can be a single cell reference
			}
			$this->OtbsSheetRangeNames[$name] = $this->Sheet_GetRangeInfo($ref);
			$p = $el->PosEnd;
		}

		// Database ranges
		$p = 0;
		while ( $el = clsTbsXmlLoc::FindElement($Txt, 'table:database-range', $p, true) ) {
			$name = $el->GetAttLazy('table:name');
			// A Database Range can have the same name as a Sheet Range. Priority to the Sheet Range.
			if (!isset($this->OtbsSheetRangeNames[$name])) {
				$ref  = $el->GetAttLazy('table:target-range-address');
				$this->OtbsSheetRangeNames[$name] = $this->Sheet_GetRangeInfo($ref);
			}
			$p = $el->PosEnd;
		}
		
	}

	/**
	 * Get the locator of the sheet element.
	 * @param  array  $Range  The range information.
	 * @return object A clsTbsXmlLoc locator of false if error.
	 */
	function OpenDoc_GetSheetLoc($Range) {
		
		$idx = $this->FileGetIdx('content.xml');
		$Txt = $this->TbsStoreGet($idx, 'OpenDoc_GetSheetLoc');

		// Find the sheet
		if ($Range['sheet']) {
			// Find the named sheet
			$pos = 0;
			$cont = true;
			while ($cont) {
				if ($SheetLoc = clsTbsXmlLoc::FindStartTag($Txt, 'table:table', $pos, true)) {
					if ($SheetLoc->GetAttLazy('table:name') === $Range['sheet']) {
						$SheetLoc->FindEndTag();
						
						$cont = false;
					} else {
						$pos = $SheetLoc->PosEnd;
					}
				} else {
					$cont = false;
				}
			}
		} else {
			// Get the first sheet
			$SheetLoc = clsTbsXmlLoc::FindElement($Txt, 'table:table', 0, true);
		}


		return $SheetLoc;
		
	}

	function OpenDoc_GetCellValue($Loc) {
		
		$x = null;
		
		if ( $Loc->Exists && ($Loc->GetInnerStart() !== false) ) {
			$type = $Loc->GetAttLazy('office:value-type');
			if ($type === 'string') {
				// errors are in this case, but with attribute « calcext:value-type="error" »
				if ($z = clsTbsXmlLoc::FindElement($Loc, 'text:p', 0, true)) {
					$x = $z->GetInnerSrc();
				}
			} elseif ($type === 'time') {
				$z = $Loc->GetAttLazy('office:time-value');
				if ($z !== false) {
					// 'PT12H23M00S', 'P' means period
					$x = $z;
				}
			} elseif ($type === 'date') {
				$z = $Loc->GetAttLazy('office:date-value');
				if ($z !== false) {
					// Date or DateTime quite ISO 8601
					// '2019-04-06', '2018-12-11T11:45:00'
					$x = $z;
				}
			} elseif ($type === 'boolean') {
				$z = $Loc->GetAttLazy('office:boolean-value');
				if ($z === 'true') {
					$x = true;
				} elseif ($x === 'false') {
					$z = false;
				}
			} else {
				// float, percentage, currency
				$z = $Loc->GetAttLazy('office:value');
				if ($z !== false) {
					$x = floatval($z);
				}
			}
		}
		
		return $x;
		
	}
	
	// Feed $Styles with styles found in $Txt
	function OpenDoc_StylesFeed(&$Styles, $Txt) {
		$p = 0;
		while ($loc = clsTbsXmlLoc::FindElement($Txt, 'style:style', $p)) {
			unset($o);
			$o = (object) null;
			$o->name = $loc->GetAttLazy('style:name');
			$o->parentName = $loc->GetAttLazy('style:parent-style-name');
			$o->childs = array();
			$o->pbreak = false;
			$o->ctrl = false;
			$src = $loc->GetSrc();
			if (strpos($src, ' fo:break-before="page"')!==false) $o->pbreak = 'before';
			if (strpos($src, ' fo:break-after="page"')!==false) $o->pbreak = 'after';
			if ($o->name!==false) $Styles[$o->name] = $o;
			$p = $loc->PosEnd;
		}
	}

	function OpenDoc_StylesPropagate(&$Styles) {
		foreach ($Styles as $i => $s) {
			if (!$s->ctrl) {
				$s->ctrl = true; // avoid circular reference
				if ($s->pbreak!==false) {
					foreach ($s->childs as $j => $c) {
						if ($c->pbreak!==false) $c->pbreak = $s->pbreak;
						$this->OpenDoc_StylesPropagate($c);
					}
				}
				$s->childs = false;
			}
		}
	}

	// TBS Block Alias for pages
	function OpenDoc_GetPage($Tag, $Txt, $Pos, $Forward, $LevelStop) {

		$this->OpenDoc_StylesInit();

		$p = $Pos;

		while (	($loc = clsTbsXmlLoc::FindStartTagHavingAtt($Txt, 'text:style-name', $p, $Forward))!==false) {

			$style = $loc->GetAttLazy('text:style-name');

			if ( ($style!==false) && isset($this->OpenDoc_Styles[$style]) ) {
				$pbreak = $this->OpenDoc_Styles[$style]->pbreak;
				if ($pbreak!==false) {
					if ($Forward) {
						// Forward
						if ($pbreak==='before') {
							return $loc->PosBeg -1; // note that the page-break is not in the block
						} else {
							$loc->FindEndTag();
							return $loc->PosEnd;
						}
					} else {
						// Backward
						if ($pbreak==='before') {
							return $loc->PosBeg;
						} else {
							$loc->FindEndTag();
							return $loc->PosEnd+1; // note that the page-break is not in the block
						}
					}
				}
			}

			$p = ($Forward) ? $loc->PosEnd : $loc->PosBeg; 

		}

		// If we are here, then no tag is found, we return the boud of the main element
		if ($Forward) {
			$p = strpos($Txt, '</office:text');
			if ($p===false) return false;
			return $p-1;
		} else {
			$loc = clsTbsXmlLoc::FindStartTag($Txt, 'office:text', $Pos, false);
			if ($loc===false) return false;
			return $loc->PosEnd + 1;
		}

	}

	// TBS Block Alias for draws
	function OpenDoc_GetDraw($Tag, $Txt, $Pos, $Forward, $LevelStop) {
		return $this->XML_BlockAlias_Prefix('draw:', $Txt, $Pos, $Forward, $LevelStop);
	}
	
	/**
	 * Find a chart in the template by its reference.
	 * Return an array of technical information about the sub-file.
	 */
	function OpenDoc_ChartFind($ChartRef, &$Txt, $ErrTitle) {
		
		if ($this->OpenDocCharts===false) $this->OpenDoc_ChartInit();
		
		// Find the chart
		if (is_numeric($ChartRef)) {
			$ChartCaption = 'number '.$ChartRef;
			$idx = intval($ChartRef) -1;
			if (!isset($this->OpenDocCharts[$idx])) return $this->RaiseError("($ErrTitle) : unable to found the chart $ChartCaption.");
		} else {
			$ChartCaption = 'with title "'.$ChartRef.'"';
			$idx = false;
			$x = htmlspecialchars($ChartRef, ENT_NOQUOTES); // ENT_NOQUOTES because target is an element's content
			foreach($this->OpenDocCharts as $i=>$c) {
				if ($c['title']==$x) $idx = $i;
			}
			if ($idx===false) return $this->RaiseError("($ErrTitle) : unable to found the chart $ChartCaption.");
		}
		$this->_ChartCaption = $ChartCaption; // for error messages

		// Retrieve chart information
		$chart = &$this->OpenDocCharts[$idx];
		if ($chart['to_clear']) $this->OpenDoc_ChartClear($chart);

		// Retrieve the XML of the data
		$file_name = $chart['href'] . '/content.xml';
		$file_idx = $this->FileGetIdx($file_name);
		if ($file_idx===false) return $this->RaiseError("($ErrTitle) : unable to found the data in the chart $ChartCaption.");
		$chart['file_name'] = $file_name;
		$chart['file_idx'] = $file_idx;

		$Txt = $this->TbsStoreGet($file_idx, 'OpenDoc_ChartChangeSeries');

		// Found all chart series
		if (!isset($chart['series'])) {
			$ok = $this->OpenDoc_ChartFindSeries($chart, $Txt);
			if (!$ok) return false;
		}
		
		return $chart;
		
	}
	
	function OpenDoc_ChartChangeSeries($ChartRef, $SeriesNameOrNum, $NewValues, $NewLegend=false) {

		$Txt = false;
		$chart = $this->OpenDoc_ChartFind($ChartRef, $Txt, 'ChartChangeSeries');
		if ($chart === false) return;
		
		$series = &$chart['series'];

		// Found the asked series
		$s_info = false;
		if (is_numeric($SeriesNameOrNum)) {
			$s_caption = 'number '.$SeriesNameOrNum;
			$idx = $SeriesNameOrNum -1;
			if (isset($series[$idx])) $s_info = &$series[$idx];
		} else {
			$s_caption = '"'.$SeriesNameOrNum.'"';
			foreach($series as $idx => $s) {
				if ( ($s_info===false) && ($s['name']==$SeriesNameOrNum) ) $s_info = &$series[$idx];
			}
		}
		if ($s_info===false) return $this->RaiseError("(ChartChangeSeries) : unable to found the series $s_caption in the chart ".$this->_ChartCaption.".");

		if ($NewLegend!==false) $this->OpenDoc_ChartRenameSeries($Txt, $s_info, $NewLegend);

		if ($s_info['local']) {
			$this->OpenDoc_ChartUnlinkSeries($Txt, $chart, $s_info);
		}
		
		// simplified variables
		$col_cat   = $chart['series_cat_col']; // column Category (always 0)
		$col_nb    = $chart['tbl_col_nb']; // number of columns
		$s_col_deb = min($s_info['used_cols']);  // first data column of the series
		$s_col_nb  = count($s_info['used_cols']);
		$s_col_end = $s_col_deb + $s_col_nb - 1;  // last column of the series
		$s_use_cat = (count($s_info['used_cols'])==1); // true if the series uses the column Category

		// Force syntax of data
		if (!is_array($NewValues)) {
			$data = array();
			if ($NewValues===false) $this->OpenDoc_ChartDelSeries($Txt, $s_info);
		} elseif ( $s_use_cat && isset($NewValues[0]) && isset($NewValues[1]) && is_array($NewValues[0]) && is_array($NewValues[1]) ) {
			// syntax 2: $NewValues = array( array('cat1','cat2',...), array(val1,val2,...) );		
			$k = $NewValues[0];
			$v = $NewValues[1];
			$data = array();
			foreach($k as $i=>$x) $data[$x] = isset($v[$i]) ? $v[$i] : false;
			unset($k, $v);
		} else {
			// syntax 1: $NewValues = array( 'cat1'=>val1, 'cat2'=>val2, ... );		
			$data = $NewValues;
		}
		unset($NewValues);

		// Scann all rows (=categories) for changing cells
		$elData = clsTbsXmlLoc::FindElement($Txt, 'table:table-rows', 0);
		$p_row = 0;
		while (($elRow=clsTbsXmlLoc::FindElement($elData, 'table:table-row', $p_row))!==false) {
			$p_cell = 0;
			$category = false;
			$data_r = false;
			for ($i = 0 ; $i <= $s_col_end ; $i++) {
				if ($elCell = clsTbsXmlLoc::FindElement($elRow, 'table:table-cell', $p_cell)) {
					if ($i == $col_cat) {
						// Category
						if ($el = clsTbsXmlLoc::FindElement($elCell, 'text:p', 0)) {
							$category = $el->GetInnerSrc();
						}
					} elseif ($i >= $s_col_deb) {
						// Change the value
						$x = 'NaN'; // default value
						if ($s_use_cat) {
							if ( ($category!==false) && isset($data[$category]) ) {
								$x = $data[$category];
								unset($data[$category]); // delete the category in order to keep only unused
							}
						} else {
							$val_idx = $i - $s_col_deb;
							if ($data_r===false) $data_r = array_shift($data); // (may return null) delete the row in order to keep only unused
							if ( (!is_null($data_r)) && isset($data_r[$val_idx])) $x = $data_r[$val_idx];
						}
						if ( ($x===false) || is_null($x) ) $x = 'NaN';
						$elCell->ReplaceAtt('office:value', $x);
						// Delete the cached legend
						if ($el = clsTbsXmlLoc::FindElement($elCell, 'text:p', 0)) {
							$el->ReplaceSrc('');
							$el->UpdateParent(); // update $elCell source
						}
						// Delete reference to worksheet
						if ($el = clsTbsXmlLoc::FindElement($elCell, 'draw:g', 0)) {
							$el->ReplaceSrc('');
							$el->UpdateParent(); // update $elCell source
						}
						$elCell->UpdateParent(); // update $elRow source
					}
					$p_cell = $elCell->PosEnd;
				} else {
					$i = $s_col_end + 1; // ends the loops
				}
			}
			$elRow->UpdateParent(); // update $elData source
			$p_row = $elRow->PosEnd;
		}

		// Add unused data
		// TODO : we should update the category range in <chart:categories> but LibreOffice seems to not care about it.
		$x = '';
		$x_nan = '<table:table-cell office:value-type="float" office:value="NaN"></table:table-cell>';
		foreach ($data as $cat=>$val) {
			$x .= '<table:table-row>';
			if ($s_use_cat) $val = array($val);
			for ($i = 0 ; $i <= $col_nb -1 ; $i++) {
				if ( ($s_col_deb <= $i) && ($i <= $s_col_end) ) {
					$val_idx = $i - $s_col_deb;
					if (isset($val[$val_idx])) {
						$x .= '<table:table-cell office:value-type="float" office:value="'.$val[$val_idx].'"></table:table-cell>';
					} else {
						$x .= $x_nan;
					}
				} else {
					if ($s_use_cat && ($i == $col_cat) ) {
						// ENT_NOQUOTES because target is an element's content
						$x .= '<table:table-cell office:value-type="string"><text:p>'.htmlspecialchars($cat, ENT_NOQUOTES).'</text:p></table:table-cell>';
					} else {
						$x .= $x_nan;
					}
				}
			}
			$x .= '</table:table-row>';
		}
		$p = strpos($Txt, '</table:table-rows>', $elData->PosBeg);
		if ($x!=='') $Txt = substr_replace($Txt, $x, $p, 0);

		// Save the result
		$this->TbsStorePut($chart['file_idx'], $Txt);

	}
	
	/**
	 * Look for all chart in the document, and store information.
	 */
	function OpenDoc_ChartInit() {

		$this->OpenDocCharts = array();

		$idx = $this->Ext_GetMainIdx();
		$Txt = $this->TbsStoreGet($idx, 'OpenDoc_ChartInit');

		$p = 0;
		while($drEl = clsTbsXmlLoc::FindElement($Txt, 'draw:frame', $p)) {

			$src = $drEl->GetInnerSrc();
			$objEl = clsTbsXmlLoc::FindStartTag($src, 'draw:object', 0);

			if ($objEl) { // Picture have <draw:frame> without <draw:object>
				$href = $objEl->GetAttLazy('xlink:href'); // example "./Object 1"
				if ($href) {

					$imgEl = clsTbsXmlLoc::FindElement($src, 'draw:image', 0);
					$img_href = ($imgEl) ? $imgEl->GetAttLazy('xlink:href') : false; // "./ObjectReplacements/Object 1"
					$img_src = ($imgEl) ? $imgEl->GetSrc('xlink:href') : false;

					$titEl = clsTbsXmlLoc::FindElement($src, 'svg:title', 0);
					$title = ($titEl) ? $titEl->GetInnerSrc() : '';

					if (substr($href,0,2)=='./') $href = substr($href, 2);
					if ( is_string($img_href) && (substr($img_href,0,2)=='./') ) $img_href = substr($img_href, 2);
					$this->OpenDocCharts[] = array('href'=>$href, 'title'=>$title, 'img_href'=>$img_href, 'img_src'=>$img_src, 'to_clear'=> ($img_href!==false) );
				}
			}
			$p = $drEl->PosEnd;
		}

		
	}

	/**
	 * Clear some of the chart informations :
	 * - Delete the picture of the chart which is used as a snapshot.
	 */
	function OpenDoc_ChartClear(&$chart) {

		$chart['to_clear'] = false;
		
		// Delete the file in the archive
		$idx = $this->FileGetIdx($chart['img_href']);
		// One test with ODS had a referenced picture that did not exist in the archive.
		if ($idx !== false) {
			$this->FileReplace($idx, false);
		}

		// Delete the element in the main file
		$main = $this->Ext_GetMainIdx();
		$Txt = $this->TbsStoreGet($main, 'OpenDoc_ChartClear');
		$Txt = str_replace($chart['img_src'], '', $Txt);
		$this->TbsStorePut($main, $Txt);

		// Delete the element in the Manifest file
		$manifest = $this->FileGetIdx('META-INF/manifest.xml');
		if ($manifest!==false) {
			$Txt = $this->TbsStoreGet($manifest, 'OpenDoc_ChartClear');
			$el = clsTbsXmlLoc::FindStartTagHavingAtt($Txt, 'manifest:full-path="'.$chart['img_href']."'", 0);
			if ($el) {
				$el->ReplaceSrc('');
				$this->TbsStorePut($manifest, $Txt);
			}
		}

	}

	/**
	 * Find and save informations abouts all series in the chart.
	 */
	function OpenDoc_ChartFindSeries(&$chart, $Txt) {

		// Find series declarations
		$p = 0;
		$s_idx = 0;
		$series = array();	
		while ($elSeries = clsTbsXmlLoc::FindElement($Txt, 'chart:series', $p)) {
			
			// List of column's nums for other values
			$used_refs   = array();
			$used_refs[] = $elSeries->GetAttLazy('chart:values-cell-range-address');

			$src = $elSeries->GetInnerSrc();
			$p2 = 0;
			// <chart:domain> is used by type of charts: scatter, bubble, surface inorder to save X and Y values.
			// It may have 0, 1 or 2 <chart:domain> entity in each series. 1 entity means X is the num order of the row, and Y is the column given by the domain.
			while ($elDom = clsTbsXmlLoc::FindStartTag($src, 'chart:domain', $p2)) {
				$att = $elDom->GetAttLazy('table:cell-range-address');
				if ($att) {
					$used_refs[] = $att;
				}
				$p2 = $elDom->PosEnd;
			}
			
			// Attribute to re-find the series
			$label_ref = $elSeries->GetAttLazy('chart:label-cell-address');
			
			// Add the series
			$series[$s_idx] = array(
				'name'      => false, // name of the series
				'name_col'  => false, // colmun index for the name of the series (always on first row)
				'val_col'   => false, // column index for the values
				'used_cols' => false, // list of column indexes in the table that are used by the series
				'used_refs' => $used_refs, // list of ref usde by the series, (0] is always for values
				'label_ref' => $label_ref,
				'local'     => true, // indicate if data is stored in the local table (flase means linked to cells)
			);
			$p = $elSeries->PosEnd;
			$s_idx++;
		}

		// Column of categories
		$series_cat_ref = false;
		$elCat = clsTbsXmlLoc::FindStartTag($Txt, 'chart:categories', 0);
		if ($elCat!==false) {
			$series_cat_ref = $elCat->GetAttLazy('table:cell-range-address');
		}

		$elTbl = clsTbsXmlLoc::FindStartTag($Txt, 'table:table', 0);
		if ($elTbl===false) return $this->RaiseError("(ChartFindSeries) : unable to found the local table in the chart ".$this->_ChartCaption.".");
		$tbl_name = $elTbl->GetAttLazy('table:name');

		// Info in the table
		$tbl_columns = array();
		$tbl_cat_ref = '';
		$p = 0;

		// Browse headers columns
		$elRow = clsTbsXmlLoc::FindElement($Txt, 'table:table-header-rows', $elTbl->PosBeg);
		if ($elRow === false) return $this->RaiseError("(ChartFindSeries) : unable to found the header row in the chart ".$this->_ChartCaption.".");

		$col_idx = -1;
		while (($elCell = clsTbsXmlLoc::FindElement($elRow, 'table:table-cell', $p))!==false) {
			$col_idx++;
			// Text in the cell
			$el = clsTbsXmlLoc::FindElement($elCell, 'text:p', 0);
			$name = ($el===false) ? '' : $el->GetInnerSrc();
			// Ref in the cell
			$el = clsTbsXmlLoc::FindElement($elCell, 'svg:desc', 0);
			$label_ref  = ($el===false) ? false : $el->GetInnerSrc();
			// Series info
			$tbl_columns[$col_idx] = array('name' => $name, 'label_ref' => $label_ref, 'val_ref' => false);
			$p = $elCell->PosEnd;
		}
		
		// If the chart is link to a worksheet then the first row contains refrences to the cells
		// Browse first row
		$elRow = clsTbsXmlLoc::FindElement($Txt, 'table:table-row', $elRow->PosEnd);
		if ($elRow === false) return $this->RaiseError("(ChartFindSeries) : unable to found the first data row in the chart ".$this->_ChartCaption.".");

		$tbl_cat_ref = false;
		$col_idx = -1;
		$p = 0;
		while (($elCell = clsTbsXmlLoc::FindElement($elRow, 'table:table-cell', $p))!==false) {
			$col_idx++;
			// Ref in the cell.
			$el = clsTbsXmlLoc::FindElement($elCell, 'svg:desc', 0);
			if ($el !== false) {
				if ($col_idx == 0) {
					$tbl_cat_ref = $el->GetInnerSrc();
				} else {
					//
					$tbl_columns[$col_idx]['val_ref'] = $el->GetInnerSrc();
				}
			}
			$p = $elCell->PosEnd;
		}
		
		// Experimentally first colmun is for categories, and then for each series it is somain cols first and then val col.
		$def_col_idx = 0;

		// Match info between series info and the table
		foreach ($series as $ser_idx => $info) {
			
			$def_col_idx = $def_col_idx + count($info['used_refs']);

			// Name of the series
			$col_idx = $this->OpenDoc_FirstColIdx($info['label_ref'], $tbl_name, $def_col_idx);
			$info['name_col'] = $col_idx;
			if (isset($tbl_columns[$col_idx])) {
				$info['name'] = $tbl_columns[$col_idx]['name'];
			}
			
			// The colmun for values is supposed to be the first column used for values in the series
			$info['used_cols'] = array();
			foreach ($info['used_refs'] as $idx => $ref) {
				$col_idx = $this->OpenDoc_FirstColIdx($ref, $tbl_name, ($def_col_idx - $idx));
				if ($col_idx === false) {
					$info['local'] = false;
					$col_idx = ($def_col_idx - $idx);
				}
				if ($idx === 0) {
					$info['val_col'] = $col_idx;
				}
				$info['used_cols'][] = $col_idx;
			}
			
			$series[$ser_idx] = $info;
		}
		
		// Save info
		$chart['series']         = $series;
		$chart['series_cat_ref'] = $series_cat_ref;
		$chart['series_cat_col'] = $this->OpenDoc_FirstColIdx($series_cat_ref, $tbl_name, 0);
		$chart['tbl_name']    = $tbl_name;
		$chart['tbl_col_nb']  = count($tbl_columns);
		$chart['tbl_cat_ref'] = $tbl_cat_ref;
		$chart['tbl_columns'] = $tbl_columns;
		
		return true;

	}

	/**
	 * Return the column number of the first cell in a range.
	 *
	 * @param string  $RangeRef    The reference of a range. Like "local-table.$B$2:.$B$5"
	 * @param string  $LocTblName  The local table name. The function will return false if the range is prefixed with the wrong table name.
	 * @param integer $def_col_idx The index return if the range is not referenced to the local table name.
	 *
	 * @return integer
	 */
	function OpenDoc_FirstColIdx($RangeRef, $LocTblName, $def_col_idx) {

		$p = strpos($RangeRef, '.');
		if ($p !== false) {
			if (substr($RangeRef, 0 , $p) !== $LocTblName) {
				// It is not the expected table
				return $def_col_idx;
			}
			$RangeRef = substr($RangeRef, $p); // delete the table name wich is in prefix
		}
		$RangeRef = str_replace( array('.','$'), '', $RangeRef);
		$RangeRef = explode(':', $RangeRef);

		$col_num = $this->Sheet_ColNum($RangeRef[0]);
		if ($col_num === false) return $this->RaiseError('(FirstColIdx) Reference of cell \'' . $RangeRef[0] . '\' cannot be recognized.');
		
		return $col_num - 1;

	}
	
	function OpenDoc_ChartDelSeries(&$Txt, &$series) {

		// TODO : we should update the category range in <chart:categories> but LibreOffice seems to not care about it.
		// Note: only the declaration of the series is deleted, not the data.
		$att = 'chart:label-cell-address="'.$series['label_ref'].'"';
		$elSeries = clsTbsXmlLoc::FindElementHavingAtt($Txt, $att, 0);

		if ($elSeries!==false) $elSeries->ReplaceSrc('');

	}

	/**
	 * Delete one, sveral or all categories in the chart.
	 * @param string       $ChartRef       The chart reference.
	 * @param string|array $del_categories An array of categories to delete, on the name of a category, all the keywork '*' that means all categories.
	 * @param boolean      $no_err         Indicate if an error is return when a searched category is not found.
	 * @return boolean Return true if all the searched categories are deleted.
	 */
	function OpenDoc_ChartDelCategories($ChartRef, $del_categories, $no_err) {

		$Txt = false;
		$chart = $this->OpenDoc_ChartFind($ChartRef, $Txt, 'ChartChangeSeries');
		if ($chart === false) return;

		// Colmun that hold the category name
		$col_idx = $chart['series_cat_col'];
		
		// Prepare info for the search
		$del_all = false;
		if (is_string($del_categories)) {
			if ($del_categories == '*') {
				$del_all = true;
				$del_categories = array();
			} else {
				$del_categories = array($del_categories);
			}
		}
		$remain_cat = array_flip($del_categories);
		
		// Scann all rows for changing cells
		$elData = clsTbsXmlLoc::FindElement($Txt, 'table:table-rows', 0);
		$p_row = 0;
		$del_nb = 0;
		while ( ($elRow=clsTbsXmlLoc::FindElement($elData, 'table:table-row', $p_row)) !== false ) {
			$p_cell = 0;
			for ($i = 0; $i <= $col_idx; $i++) {
				if ($elCell = clsTbsXmlLoc::FindElement($elRow, 'table:table-cell', $p_cell)) {
					if ($i == $col_idx) {
						// Category
						if ($elP = clsTbsXmlLoc::FindElement($elCell, 'text:p', 0)) {
							$category = $elP->GetInnerSrc();
							if ($del_all || isset($remain_cat[$category])) {
								$elRow->Delete();
								$elRow->UpdateParent(true);
								$del_nb++;
								unset($remain_cat[$category]);
								// optimisation
								if ( (!$del_all) && (count($remain_cat) == 0) ) {
									return true;
								}
							}
						}
					}
					$p_cell = $elCell->PosEnd;
				} else {
					$i = $col_idx + 1; // ends the loops
				}
			}
			$p_row = $elRow->PosEnd;
		}		

		// Save the file if modified
		if ($del_nb > 0) {
			$this->TbsStorePut($chart['file_idx'], $Txt);
		}
		
		// Result of the function
		if ( $del_all || (count($remain_cat) == 0) ) {
			// All searched categories are deleted
			return true;
		} else {
			if ($no_err) {
				return false;
			} else {	
				return $this->RaiseError("(ChartDelCategory) : unable to find categories '" . implode(', ', array_keys($remain_cat)) . "' in the chart ".$this->_ChartCaption.".");
			}
		}
		
	}
	
	function OpenDoc_ChartRenameSeries(&$Txt, &$series, $NewName) {

		$NewName = htmlspecialchars($NewName, ENT_NOQUOTES); // ENT_NOQUOTES because target is an element's content
		$col_idx = $series['name_col'];

		$el = clsTbsXmlLoc::FindStartTag($Txt, 'table:table-header-rows', 0);
		$el = clsTbsXmlLoc::FindStartTag($Txt, 'table:table-row', $el->PosEnd);
		for ($i = 0; $i < $col_idx; $i++) {
			$el = clsTbsXmlLoc::FindStartTag($Txt, 'table:table-cell', $el->PosEnd);
		}
		$elCell = clsTbsXmlLoc::FindElement($Txt, 'table:table-cell', $el->PosEnd);

		$elP = clsTbsXmlLoc::FindElement($elCell, 'text:p', 0);
		if ($elP===false) {
			$elCell->ReplaceInnerSrc($elCell->GetInnerSrc().'<text:p>'.$NewName.'</text:p>');
		} else {
			if($elP->SelfClosing) {
				$elP->ReplaceSrc('<text:p>'.$NewName.'</text:p>');
			} else {
				$elP->ReplaceInnerSrc($NewName);
			}
			$elP->UpdateParent();
		}

	}

	/**
	 * Returne the local reference for a colmun.
	 */
	function OpenDoc_ChartLocalColRef($chart, $col, $row1, $row2 = false) {
		$ref = $chart['tbl_name'] . '.' . $this->Sheet_CellRef($col, $row1, '$');
		if ($row2 !== false) {
			$ref .= ':' . $this->Sheet_CellRef($col, $row2, '$');
		}
		return $ref;
	}
	
	/**
	 * Unlink a series, so that is becomes attached to the local table.
	 * Example : "local-table.$B$2:.$B$5"
	 */
	function OpenDoc_ChartUnlinkSeries(&$Txt, &$chart, &$series) {

		$att = 'chart:label-cell-address="' . $series['label_ref'] .'"';
		$elSeries = clsTbsXmlLoc::FindElementHavingAtt($Txt, $att, 0);

		if ($elSeries) {
			
			// Replace the label adress
			$col_num = $series['name_col'] + 1;
			$ref = $this->OpenDoc_ChartLocalColRef($chart, $col_num, 1);
			$elSeries->ReplaceAtt('chart:label-cell-address', $ref);
			
			// Replace the value adress
			$col_num = $series['val_col'] + 1;
			$ref = $this->OpenDoc_ChartLocalColRef($chart, $col_num, 2, 2);
			$elSeries->ReplaceAtt('chart:values-cell-range-address', $ref);
			
			// Replace other adresses
			$p = 0;
			foreach ($series['used_cols'] as $idx => $col_idx) {
				if ($col_idx != $series['val_col']) {
					$el = clsTbsXmlLoc::FindStartTag($elSeries, 'chart:domain', $p);
					if ($el) {
						$col_num = $col_idx + 1;
						$ref = $this->OpenDoc_ChartLocalColRef($chart, $col_num, 2, 2);
						$el->ReplaceAtt('table:cell-range-address', $ref);
						$el->UpdateParent();
						$p = $el->PosEnd;
					}
				}
			}
			
		}
		
		$series['local'] = false;
	
	}
	
	/**
	 * Return information and data about all series in the chart.
	 */
	function OpenDoc_ChartReadSeries($ChartRef, $Complete) {
		
		$Txt = false;
		$chart = $this->OpenDoc_ChartFind($ChartRef, $Txt, 'ChartReadSeries');
		if ($chart === false) return;

		// Read the data table
		$table = array();
		$rows = clsTbsXmlLoc::FindElement($Txt, 'table:table-rows', 0);
		$pr = 0;
		while ($r = clsTbsXmlLoc::FindElement($rows, 'table:table-row', $pr)) {
			$pr = $r->PosEnd;
			$pc = 0;
			$row = array();
			while ($c = clsTbsXmlLoc::FindElement($r, 'table:table-cell', $pc)) {
				$pc = $c->PosEnd;
				$val = $c->getAttLazy('office:value');
				if ($val == 'NaN') { // Not a Number, happens when the cell is empty
					$val = false;
					$txt = '';
				} else {
					if ($x = clsTbsXmlLoc::FindElement($c, 'text:p', 0)) {
						$txt = $x->GetInnerSrc();
					} else {
						$txt = false;
					};
				}
				$row[] = array('val' => $val, 'txt' => $txt);
			}
			$table[] = $row;
		}
		
		// Format series information
		$series = array();
		$cat_idx = $chart['series_cat_col'];
		foreach ($chart['series'] as $idx => $info) {
			$cat = array();
			$val = array();
			$col_idx = $info['val_col'];
			foreach ($table as $row) {
				$val[] = $row[$col_idx]['val'];
				$cat[] = $row[$cat_idx]['txt'];
			}
			$series[] = array(
				'name' => $info['name'],
				'cat' => $cat,
				'val' => $val,
			);
		}
		
		if ($Complete) {
			// Complete information about the chart
			$main_idx = $this->Ext_GetMainIdx();
			return array(
				'file_idx' => $chart['file_idx'],
				'file_name' => $chart['file_name'],
				'parent_idx' => $main_idx,
				'parent_name' => $this->TbsGetFileName($main_idx),
				'series' => $series,
			);
		} else {
			// Simple information about data
			$simple = array();
			foreach ($series as $s) {
				$name = $s['name'];
				$simple[$name] = array($s['cat'], $s['val']);
			}
			return $simple;
		}
		
	}
	
	function OpenDoc_ChartDebug($nl, $sep, $bull) {

		if ($this->OpenDocCharts===false) $this->OpenDoc_ChartInit();

		$ChartLst = $this->OpenDocCharts;

		echo $nl;
		echo $nl."Charts found in the contents: (use command OPENTBS_CHART_INFO to get series's names and data)";
		echo $nl."-----------------------------";
		foreach ($ChartLst as $i=>$c) {
			$title = ($c['title']===false) ? '(not found)' : var_export($c['title'], true);
			echo $bull."title: $title";
		}
		if (count($ChartLst)==0) echo $bull."(none)";

	}

	/**
	 * Delete useless reapeated rows, cell and columns.
	 * This operation fixes the problem of ODS files built with LibreOffice >= 4 and merged with OpenTBS and opened with Ms Excel.
	 * The virtual number of row can exceed the maximum supported, then Excel raises an error when opening the file.
	 * LibreOffice does not.
	 */
	function OpenDoc_DeleteUselessRepeatedElements(&$Txt) {
		
		$el_tbl  = 'table:table';
		$el_col  = 'table:table-column'; // Column definition
		$el_row  = 'table:table-row';
		$el_cell = 'table:table-cell';
		$att_rep_col = 'table:number-columns-repeated';
		$att_rep_row = 'table:number-rows-repeated';
		
		$loop = array($att_rep_col, $att_rep_row);
		
		// Loop for deleting useless repeated columns
		foreach ($loop as $att_rep) {
		
			$p = 0;
			while ( $xml = clsTbsXmlLoc::FindElementHavingAtt($Txt, $att_rep, $p) ) {
			
				$xml->FindName();
				$p = $xml->PosEnd;
				
				// Next tag (opening or closing)
				$next = clsTbsXmlLoc::FindStartTagByPrefix($Txt, '', $p);
				$next_name = $next->Name;
				if ($next_name == '') {
					$next_name = $next->GetSrc();
					$next_name = substr($next_name, 1, strlen($next_name) -2);
				};

				$z_src = $next->GetSrc();
				
				//echo " * name=" . $xml->Name . ", suiv_name=$next_name, suiv_src=$z_src\n";

				$delete = false;
				
				if ( ($xml->Name == $el_col) && ($xml->SelfClosing) ) {
					if ( ($next_name == $el_row) || ($next_name == '/' . $el_tbl) ) {
						// It's the last column definition of the sheet, and it is self-closing and repeated
						$delete = true;
					}
				} elseif ( ($xml->Name == $el_cell) && ($xml->SelfClosing) ) {
					if ( $next_name == '/' . $el_row ) {
						// It's the last cell of a row, and it is self-closing and repeated
						$delete = true;
					}
				} elseif ($xml->Name == $el_row) {
					if ( $next_name == '/' . $el_tbl ) {
						$inner_src = '' . $xml->GetInnerSrc();
						if (strpos($inner_src, '<') === false) {
							// It's the last row of a sheet, and it is empty and repeated
							$delete = true;
						}
					}
				}
				
				if ($delete) {
					//echo " * DELETE " . $xml->Name . " : " . $xml->GetSrc() . "\n";
					$p = $xml->PosBeg;
					$xml->Delete();
				}
				
			}

		}
		
	}
	
	/**
	 * Apply a, OpenTBS trick in order to manage covered cells as if there are normal cells.
	 *
	 * @param string|object $src A string or an clsTbsXmlLoc object
	 * @param boolean       $do  True to apply, false to unapply.
	 */
	function OpenDoc_CoveredCells_Replace(&$src, $do) {
	
		$is_loc = is_object($src);
		if ($is_loc) {
			$txt = $src->GetSrc();
		} else {
			$txt =& $src;
		}
		
		$opendoc   = '<table:covered-table-cell';
		$tbs_trick = '<table:table-cell covered="1"';
		if ($do) {
			$txt = str_replace($opendoc, $tbs_trick, $txt);
		} else {
			$txt = str_replace($tbs_trick, $opendoc, $txt);
		}
		
		if ($is_loc) {
			$src->ReplaceSrc($txt);
		}
	
	}
	
	
}

/**
 * clsTbsXmlLoc
 * Wrapper to search and replace in XML entities.
 * The object represents only the opening tag until method FindEndTag() is called.
 * Then is represents the complete entity.
 */
class clsTbsXmlLoc {

	public $PosBeg;
	public $PosEnd; // can the end of the open tag, or end of the close tag.
	public $SelfClosing; // null|false|true, null means unknown.
	public $Txt;
	public $Name = ''; 
	public $Exists; 

	public $pST_PosEnd = false; // position of the end of the start tag
	public $pST_Src = false;    // cached source of the start tag, false if not cached
	public $pET_PosBeg = false; // position of the begining of the end tag

	public $Parent = false; // parent object

	// For relative mode
	public $rel_Txt = false;
	public $rel_PosBeg = false;
	public $rel_Len = false;

	/**
	 * Search a start tag of an element in the TXT contents, and return an object if it is found.
	 * Instead of a TXT content, it can be an object of the class. Thus, the object is linked to a copy
	 *  of the source of the parent element. The parent element can receive the changes of the object using method UpdateParent().
	 */
	static function FindStartTag(&$TxtOrObj, $Tag, $PosBeg, $Forward=true) {

		if (is_object($TxtOrObj)) {
			$TxtOrObj->FindEndTag();
			$Txt = $TxtOrObj->GetSrc();
			if ($Txt===false) return false;
			$Parent = &$TxtOrObj;
		} else {
			$Txt = &$TxtOrObj;
			$Parent = false;
		}

		$PosBeg = clsTinyButStrong::f_Xml_FindTagStart($Txt, $Tag, true , $PosBeg, $Forward, true);
		if ($PosBeg===false) return false;

		return new clsTbsXmlLoc($Txt, $Tag, $PosBeg, null, $Parent);

	}

	/**
	 * Search a start tag by the prefix of the element.
	 * @param  string       $TagPrefix The prefix of the tag. Empty string accepeted.
	 * @return false|object The found object will have its real tag name.
	 */
	static function FindStartTagByPrefix(&$Txt, $TagPrefix, $PosBeg, $Forward=true) {

		$x = '<'.$TagPrefix;
		$xl = strlen($x);

		if ($Forward) {
			$PosBeg = strpos($Txt, $x, $PosBeg);
		} else {
			$PosBeg = strrpos(substr($Txt, 0, $PosBeg+2), $x);
		}
		if ($PosBeg===false) return false;

		// Read the actual tag name
		$Tag = $TagPrefix;
		$p = $PosBeg + $xl;
		do {
			$z = substr($Txt,$p,1);
			if ( ($z!==' ') && ($z!=="\r") && ($z!=="\n") && ($z!=='>') && ($z!=='/') ) {
				$Tag .= $z;
				$p++;
			} else {
				$p = false;
			}
		} while ($p!==false);

		return new clsTbsXmlLoc($Txt, $Tag, $PosBeg);

	}

	// Search an element in the TXT contents, and return an object if it's found.
	static function FindElement(&$TxtOrObj, $Tag, $PosBeg, $Forward=true) {

		$XmlLoc = clsTbsXmlLoc::FindStartTag($TxtOrObj, $Tag, $PosBeg, $Forward);
		if ($XmlLoc===false) return false;

		$XmlLoc->FindEndTag();
		return $XmlLoc;

	}

	/**
	 * Search a start tag in the TXT contents which has the asked attribute.
	 * Note that the element found has an unknown name until FindEndTag() is called.
	 * The function does check if the attribute is inside an XML element.
	 * @param  string  &$Txt    The source to search into.
	 * @param  string  $Att     The attribute name of full definition to search. Example: 'visible' or 'visible="1"'
	 * @param  integer $PosBeg  The offset position of the search.
	 * @param  boolean $Forward (optional) Indicate the direction of the search.
	 * @return false|object
	 */
	static function FindStartTagHavingAtt(&$Txt, $Att, $PosBeg, $Forward=true) {

		$p = $PosBeg - (($Forward) ? 1 : -1);
		$x = (strpos($Att, '=')===false) ? (' '.$Att.'="') : (' '.$Att); // get the item more precise if not yet done
		$search = true;

		do {
			if ($Forward) {
				$p = strpos($Txt, $x, $p+1);
			} else {
				$p = strrpos(substr($Txt, 0, $p+1), $x);
			}
			if ($p===false) return false;
			// Seearch for the bound of an element.
			do {
			  $p = $p - 1;
			  if ($p<0) return false;
			  $z = $Txt[$p];
			} while ( ($z!=='<') && ($z!=='>') );
			// If the bound is an opening tag, then the attribute is inside, otherwise we search the next item.
			if ($z==='<') $search = false;
		} while ($search);

		return new clsTbsXmlLoc($Txt, '', $p);

	}

	/**
	 * Search an element in the TXT contents which has the asked attribute, and return an object if it is found.
	 * @param  string  &$Txt    The source to search into.
	 * @param  string  $Att     The attribute name of full definition to search. Example: 'visible' or 'visible="1"'
	 * @param  integer $PosBeg  The offset position of the search.
	 * @param  boolean $Forward (optional) Indicate the direction of the search.
	 * @return false|object
	 */
	static function FindElementHavingAtt(&$Txt, $Att, $PosBeg, $Forward=true) {

		$XmlLoc = clsTbsXmlLoc::FindStartTagHavingAtt($Txt, $Att, $PosBeg, $Forward);
		if ($XmlLoc===false) return false;

		$XmlLoc->FindEndTag();

		return $XmlLoc;

	}
	
	static function CreatePhantomElement(&$TxtOrObj, $PosBeg) {
		
		if (is_object($TxtOrObj)) {
			$TxtOrObj->FindEndTag();
			$Txt = $TxtOrObj->GetSrc();
			if ($Txt===false) return false;
			$Parent = &$TxtOrObj;
		} else {
			$Txt = &$TxtOrObj;
			$Parent = false;
		}
		
		$Name = '';
		$SelfClosing = null;
		$Exists = false;

		$XmlLoc = new clsTbsXmlLoc($Txt, $Name, $PosBeg, $SelfClosing, $Parent, $Exists);
			
		return $XmlLoc;
		
	}
	
	// Create an instance with the given parameters
	function __construct(&$Txt, $Name, $PosBeg, $SelfClosing = null, $Parent = false, $Exists = true) {
	
		$this->Txt = &$Txt;
		$this->Name = $Name;
		$this->PosBeg = $PosBeg;
		$this->Exists = $Exists;
		if ($Exists) {
			$this->PosEnd = strpos($Txt, '>', $PosBeg);
			if ($this->PosEnd===false) $this->PosEnd = strlen($Txt)-1; // should no happen but avoid errors
		} else {
			$this->PosEnd = $PosBeg - 1;
		}
		$this->pST_PosEnd = $this->PosEnd;
		$this->SelfClosing = $SelfClosing;
		$this->Parent = $Parent;
	}

	// Return an array of (val_pos, val_len, very_sart, very_len) of the attribute. Return false if the attribute is not found.
	// Positions are relative to $this->PosBeg.
	// This method is lazy because it assumes the attribute is separated by a space and its value is delimited by double-quote.
	function _GetAttValPos($Att) {
		if ($this->pST_Src===false) $this->pST_Src = substr($this->Txt, $this->PosBeg, $this->pST_PosEnd - $this->PosBeg + 1 );
		$a = ' '.$Att.'="';
		$p0 = strpos($this->pST_Src, $a);
		if ($p0!==false) {
			$p1 = $p0 + strlen($a);
			$p2 = strpos($this->pST_Src, '"', $p1);
			if ($p2!==false) return array($p1, $p2-$p1, $p0, $p2-$p0+1);
		}
		return false;
	}
	
	// Update positions when attributes of the start tag has been upated.
	function _ApplyDiffFromStart($Diff) {
		$this->pST_PosEnd += $Diff;
		$this->pST_Src = false;
		if ($this->pET_PosBeg!==false) $this->pET_PosBeg += $Diff;
		$this->PosEnd += $Diff;
	}
	
	// Update all positions.
	function _ApplyDiffToAll($Diff) {
		$this->PosBeg += $Diff;
		$this->PosEnd += $Diff;
		$this->pST_PosEnd += $Diff;
		if ($this->pET_PosBeg!==false) $this->pET_PosBeg += $Diff;
	}
	
	/**
	 * Convert a self-closing entity to a start+end entity if needed.
	 * @param string $inner The inner content to insert. 
	 */
	function _ConvertToCouple($inner) {
		$end = '>' . $inner . '</' . $this->FindName() . '>';
		$this->Txt = substr_replace($this->Txt, $end, $this->PosEnd - 1, 2);
		$this->SelfClosing = false;
		$this->pST_PosEnd = $this->PosEnd - 1;
		$this->pET_PosBeg = $this->pST_PosEnd + strlen($inner) + 1;
		$this->PosEnd = $this->pST_PosEnd + strlen($end) - 1;		
	}
	
	// Return true is the ending position is a self-closing.
	function _SelfClosing($PosEnd) {
		return (substr($this->Txt, $PosEnd-1, 1)=='/');
	}
	
	// Return the outer len of the locator.
	function GetLen() {
		return $this->PosEnd - $this->PosBeg + 1;
	}

	// Return the outer source of the locator.
	function GetSrc() {
		return substr($this->Txt, $this->PosBeg, $this->GetLen() );
	}

	// Replace the source of the locator in the TXT contents.
	// Update the locator's ending position.
	// Too complicated to update other information, given that it can be deleted.
	function ReplaceSrc($new) {
		$len = $this->GetLen(); // avoid PHP error : Strict Standards: Only variables should be passed by reference
		$this->Txt = substr_replace($this->Txt, $new, $this->PosBeg, $len);
		$diff = strlen($new) - $len;
		$this->PosEnd += $diff;
		$this->pST_Src = false;
		if ($new==='') {
			$this->pST_PosBeg = false;
			$this->pST_PosEnd = false;
			$this->pET_PosBeg = false;
			$this->Exists = false;
		} else {
			$this->pST_PosEnd += $diff; // CAUTION: may be wrong if attributes has changed
			if ($this->pET_PosBeg!==false) $this->pET_PosBeg += $diff; // CAUTION: right only if the tag name is the same
			$this->Exists = true;
		}
	}

	/**
	 * Return the position for appending at the end of the inner contents.
	 * Return false if SelfClosing.
	 */
	function GetInnerAppendPos() {
		return $this->pET_PosBeg;
	}

	// Return the start of the inner content.
	// Return false if SelfClosing.
	function GetInnerStart() {
		return ($this->pST_PosEnd===false) ? false : $this->pST_PosEnd + 1;
	}

	// Return the length of the inner content, or false if it's a self-closing tag
	// Assume FindEndTag() is previously called.
	// Return false if SelfClosing.
	function GetInnerLen() {
		return ($this->pET_PosBeg===false) ? false : $this->pET_PosBeg - $this->pST_PosEnd - 1;
	}

	// Return the length of the inner content, or false if it's a self-closing tag 
	// Assume FindEndTag() is previously called.
	// Return false if SelfClosing.
	function GetInnerSrc() {
		return ($this->pET_PosBeg===false) ? false : substr($this->Txt, $this->pST_PosEnd + 1, $this->pET_PosBeg - $this->pST_PosEnd - 1 );
	}

	// Replace the inner source of the locator in the TXT contents. Update the locator's positions.
	// Assume FindEndTag() is previously called.
	// Convert a self-closing entity to a start+end entity if needed.
	function ReplaceInnerSrc($new) {
		if ($this->SelfClosing) {
			$this->_ConvertToCouple($new);
		} else {
			$len = $this->GetInnerLen();
			if ($len===false) return false;
			$this->Txt = substr_replace($this->Txt, $new, $this->pST_PosEnd + 1, $len);
			$this->PosEnd += strlen($new) - $len;
			$this->pET_PosBeg += strlen($new) - $len;
		}
	}

	/**
	 * Append a contents at the end of the inner source.
	 */
	function AppendInnerSrc($add) {
		if ($this->SelfClosing) {
			$this->_ConvertToCouple($add);
		} else {
			$this->Txt = substr_replace($this->Txt, $add, $this->pET_PosBeg, 0);
			$this->PosEnd += strlen($add);
			$this->pET_PosBeg += strlen($add);
		}
	}
	
	// Update the parent object, if any.
	function UpdateParent($Cascading=false) {
		if ($this->Parent) {
			$this->Parent->ReplaceSrc($this->Txt);
			if ($Cascading) $this->Parent->UpdateParent($Cascading);
		}
	}
	
	// Get an attribute's value. Or false if the attribute is not found.
	// It's a lazy way because the attribute is searched with the patern {attribute="value" }
	function GetAttLazy($Att) {
		$z = $this->_GetAttValPos($Att);
		if ($z===false) return false;
		return substr($this->pST_Src, $z[0], $z[1]);
	}

	function ReplaceAtt($Att, $Value, $AddIfMissing = false) {

		$Value = ''.$Value;

		$z = $this->_GetAttValPos($Att);
		if ($z===false) {
			if ($AddIfMissing) {
				// Add the attribute
				$Value = ' '.$Att.'="'.$Value.'"';
				$pi = $this->pST_PosEnd;
				if ($this->_SelfClosing($pi)) $pi--;
				$z = array($pi - $this->PosBeg, 0);
			} else {
				return false;
			}
		}

		$this->Txt = substr_replace($this->Txt, $Value, $this->PosBeg + $z[0], $z[1]);

		// update info
		$this->_ApplyDiffFromStart(strlen($Value) - $z[1]);

		return true;

	}
	
	// Delete the element with or without the content.
	function Delete($Contents=true) {
		$this->FindEndTag();
		if ($Contents || $this->SelfClosing) {
			$this->ReplaceSrc('');
		} else {
			$inner = $this->GetInnerSrc();
			$this->ReplaceSrc($inner);
		}
	}
	
	/**
	 * Return true if the attribute existed and is deleted, otherwise return false.
	 */
	function DeleteAtt($Att) {
		$z = $this->_GetAttValPos($Att);
		if ($z===false) return false;
		$this->Txt = substr_replace($this->Txt, '', $this->PosBeg + $z[2], $z[3]);
		$this->_ApplyDiffFromStart( - $z[3]);
		return true;
	}

	// Find the name of the element
	function FindName() {
		if ( ($this->Name==='') && $this->Exists ) {
			$p = $this->PosBeg;
			do {
				$p++;
				$z = $this->Txt[$p];
			} while ( ($z!==' ') && ($z!=="\r") && ($z!=="\n") && ($z!=='>') && ($z!=='/') );
			$this->Name = substr($this->Txt, $this->PosBeg + 1, $p - $this->PosBeg - 1);
		}
		return $this->Name;
	}

	// Find the ending tag of the object
	// Use $Encaps=true if the element can be self encapsulated (like <div>).
	// Return true if the end is funf
	function FindEndTag($Encaps=false) {
		if (is_null($this->SelfClosing)) {
			$pe = $this->PosEnd;
			$SelfClosing = $this->_SelfClosing($pe);
			if (!$SelfClosing) {
				if ($Encaps) {
					$loc = clsTinyButStrong::f_Xml_FindTag($this->Txt , $this->FindName(), null, $pe, true, -1, false, false);
					if ($loc===false) return false;
					$this->pET_PosBeg = $loc->PosBeg;
					$this->PosEnd = $loc->PosEnd;
				} else {
					$pe = clsTinyButStrong::f_Xml_FindTagStart($this->Txt, $this->FindName(), false, $pe, true , true);
					if ($pe===false) return false;
					$this->pET_PosBeg = $pe;
					$pe = strpos($this->Txt, '>', $pe);
					if ($pe===false) return false;
					$this->PosEnd = $pe;
				}
			}
			$this->SelfClosing = $SelfClosing;
		}
		return true;
	}

	// Swith the locator to a relative one that has no XML contents before and no XML contents after.
	// Useful to save time in search and replace.
	function switchToRelative() {
		$this->FindEndTag();
		// Save info
		$this->rel_Txt = &$this->Txt;
		$this->rel_PosBeg = $this->PosBeg;
		$this->rel_Len = $this->GetLen();
		// Change the univers
		$src = $this->GetSrc();
		$this->Txt = &$src;
		// Change positions
		$this->_ApplyDiffToAll(-$this->PosBeg);
	}

	// To use after switchToRelative(): save modification to the normal contents and update positions.
	function switchToNormal() {
		// Save info
		$src = $this->GetSrc();
		$this->Txt = &$this->rel_Txt;
		$x = false;
		$this->rel_Txt = &$x;
		$this->Txt = substr_replace($this->Txt, $src, $this->rel_PosBeg, $this->rel_Len);
		$this->_ApplyDiffToAll(+$this->rel_PosBeg);
		$this->rel_PosBeg = false;
		$this->rel_Len = false;
	}

}

/*
TbsZip version 2.16
Date    : 2014-04-08
Author  : Skrol29 (email: http://www.tinybutstrong.com/onlyyou.html)
Licence : LGPL
This class is independent from any other classes and has been originally created for the OpenTbs plug-in
for TinyButStrong Template Engine (TBS). OpenTbs makes TBS able to merge OpenOffice and Ms Office documents.
Visit http://www.tinybutstrong.com
*/

define('TBSZIP_DOWNLOAD',1);   // download (default)
define('TBSZIP_NOHEADER',4);   // option to use with DOWNLOAD: no header is sent
define('TBSZIP_FILE',8);       // output to file  , or add from file
define('TBSZIP_STRING',32);    // output to string, or add from string

class clsTbsZip {

	function __construct() {
		$this->Meth8Ok = extension_loaded('zlib'); // check if Zlib extension is available. This is need for compress and uncompress with method 8.
		$this->DisplayError = true;
		$this->ArchFile = '';
		$this->Error = false;
	}

	function CreateNew($ArchName='new.zip') {
	// Create a new virtual empty archive, the name will be the default name when the archive is flushed.
		if (!isset($this->Meth8Ok)) $this->__construct();  // for PHP 4 compatibility
		$this->Close(); // note that $this->ArchHnd is set to false here
		$this->Error = false;
		$this->ArchFile = $ArchName;
		$this->ArchIsNew = true;
		$bin = 'PK'.chr(05).chr(06).str_repeat(chr(0), 18);
		$this->CdEndPos = strlen($bin) - 4;
		$this->CdInfo = array('disk_num_curr'=>0, 'disk_num_cd'=>0, 'file_nbr_curr'=>0, 'file_nbr_tot'=>0, 'l_cd'=>0, 'p_cd'=>0, 'l_comm'=>0, 'v_comm'=>'', 'bin'=>$bin);
		$this->CdPos = $this->CdInfo['p_cd'];
	}

	function Open($ArchFile, $UseIncludePath=false) {
	// Open the zip archive
		if (!isset($this->Meth8Ok)) $this->__construct();  // for PHP 4 compatibility
		$this->Close(); // close handle and init info
		$this->Error = false;
		$this->ArchIsNew = false;
		$this->ArchIsStream = (is_resource($ArchFile) && (get_resource_type($ArchFile)=='stream'));
		if ($this->ArchIsStream) {
			$this->ArchFile = 'from_stream.zip';
			$this->ArchHnd = $ArchFile;
		} else {
			// open the file
			$this->ArchFile = $ArchFile;
			$this->ArchHnd = fopen($ArchFile, 'rb', $UseIncludePath);
		}
		$ok = !($this->ArchHnd===false);
		if ($ok) $ok = $this->CentralDirRead();
		return $ok;
	}

	function Close() {
		if (isset($this->ArchHnd) and ($this->ArchHnd!==false)) fclose($this->ArchHnd);
		$this->ArchFile = '';
		$this->ArchHnd = false;
		$this->CdInfo = array();
		$this->CdFileLst = array();
		$this->CdFileNbr = 0;
		$this->CdFileByName = array();
		$this->VisFileLst = array();
		$this->ArchCancelModif();
	}

	function ArchCancelModif() {
		$this->LastReadComp = false; // compression of the last read file (1=compressed, 0=stored not compressed, -1= stored compressed but read uncompressed)
		$this->LastReadIdx = false;  // index of the last file read
		$this->ReplInfo = array();
		$this->ReplByPos = array();
		$this->AddInfo = array();
	}

	function FileAdd($Name, $Data, $DataType=TBSZIP_STRING, $Compress=true) {

		if ($Data===false) return $this->FileCancelModif($Name, false); // Cancel a previously added file

		// Save information for adding a new file into the archive
		$Diff = 30 + 46 + 2*strlen($Name); // size of the header + cd info
		$Ref = $this->_DataCreateNewRef($Data, $DataType, $Compress, $Diff, $Name);
		if ($Ref===false) return false;
		$Ref['name'] = $Name;
		$this->AddInfo[] = $Ref;
		return $Ref['res'];

	}

	function CentralDirRead() {
		$cd_info = 'PK'.chr(05).chr(06); // signature of the Central Directory
		$cd_pos = -22;
		$this->_MoveTo($cd_pos, SEEK_END);
		$b = $this->_ReadData(4);
		if ($b===$cd_info) {
			$this->CdEndPos = ftell($this->ArchHnd) - 4;
		} else {
			$p = $this->_FindCDEnd($cd_info);
			//echo 'p='.var_export($p,true); exit;
			if ($p===false) {
				return $this->RaiseError('The End of Central Directory Record is not found.');
			} else {
				$this->CdEndPos = $p;
				$this->_MoveTo($p+4);
			}
		}
		$this->CdInfo = $this->CentralDirRead_End($cd_info);
		$this->CdFileLst = array();
		$this->CdFileNbr = $this->CdInfo['file_nbr_curr'];
		$this->CdPos = $this->CdInfo['p_cd'];

		if ($this->CdFileNbr<=0) return $this->RaiseError('No header found in the Central Directory.');
		if ($this->CdPos<=0) return $this->RaiseError('No position found for the Central Directory.');

		$this->_MoveTo($this->CdPos);
		for ($i=0;$i<$this->CdFileNbr;$i++) {
			$x = $this->CentralDirRead_File($i);
			if ($x!==false) {
				$this->CdFileLst[$i] = $x;
				$this->CdFileByName[$x['v_name']] = $i;
			}
		}
		return true;
	}

	function CentralDirRead_End($cd_info) {
		$b = $cd_info.$this->_ReadData(18);
		$x = array();
		$x['disk_num_curr'] = $this->_GetDec($b,4,2);  // number of this disk
		$x['disk_num_cd'] = $this->_GetDec($b,6,2);    // number of the disk with the start of the central directory
		$x['file_nbr_curr'] = $this->_GetDec($b,8,2);  // total number of entries in the central directory on this disk
		$x['file_nbr_tot'] = $this->_GetDec($b,10,2);  // total number of entries in the central directory
		$x['l_cd'] = $this->_GetDec($b,12,4);          // size of the central directory
		$x['p_cd'] = $this->_GetDec($b,16,4);          // position of start of central directory with respect to the starting disk number
		$x['l_comm'] = $this->_GetDec($b,20,2);        // .ZIP file comment length
		$x['v_comm'] = $this->_ReadData($x['l_comm']); // .ZIP file comment
		$x['bin'] = $b.$x['v_comm'];
		return $x;
	}

	function CentralDirRead_File($idx) {

		$b = $this->_ReadData(46);

		$x = $this->_GetHex($b,0,4);
		if ($x!=='h:02014b50') return $this->RaiseError("Signature of Central Directory Header #".$idx." (file information) expected but not found at position ".$this->_TxtPos(ftell($this->ArchHnd) - 46).".");

		$x = array();
		$x['vers_used'] = $this->_GetDec($b,4,2);
		$x['vers_necess'] = $this->_GetDec($b,6,2);
		$x['purp'] = $this->_GetBin($b,8,2);
		$x['meth'] = $this->_GetDec($b,10,2);
		$x['time'] = $this->_GetDec($b,12,2);
		$x['date'] = $this->_GetDec($b,14,2);
		$x['crc32'] = $this->_GetDec($b,16,4);
		$x['l_data_c'] = $this->_GetDec($b,20,4);
		$x['l_data_u'] = $this->_GetDec($b,24,4);
		$x['l_name'] = $this->_GetDec($b,28,2);
		$x['l_fields'] = $this->_GetDec($b,30,2);
		$x['l_comm'] = $this->_GetDec($b,32,2);
		$x['disk_num'] = $this->_GetDec($b,34,2);
		$x['int_file_att'] = $this->_GetDec($b,36,2);
		$x['ext_file_att'] = $this->_GetDec($b,38,4);
		$x['p_loc'] = $this->_GetDec($b,42,4);
		$x['v_name'] = $this->_ReadData($x['l_name']);
		$x['v_fields'] = $this->_ReadData($x['l_fields']);
		$x['v_comm'] = $this->_ReadData($x['l_comm']);

		$x['bin'] = $b.$x['v_name'].$x['v_fields'].$x['v_comm'];

		return $x;
	}

	function RaiseError($Msg) {
		if ($this->DisplayError) {
			if (PHP_SAPI==='cli') {
				echo get_class($this).' ERROR with the zip archive: '.$Msg."\r\n";
			} else {
				echo '<strong>'.get_class($this).' ERROR with the zip archive:</strong> '.$Msg.'<br>'."\r\n";
			}
		}
		$this->Error = $Msg;
		return false;
	}

	function Debug($FileHeaders=false) {

		$this->DisplayError = true;

		if ($FileHeaders) {
			// Calculations first in order to have error messages before other information
			$idx = 0;
			$pos = 0;
			$pos_stop = $this->CdInfo['p_cd'];
			$this->_MoveTo($pos);
			while ( ($pos<$pos_stop) && ($ok = $this->_ReadFile($idx,false)) ) {
				$this->VisFileLst[$idx]['p_this_header (debug_mode only)'] = $pos;
				$pos = ftell($this->ArchHnd);
				$idx++;
			}
		}

		$nl = "\r\n";
		echo "<pre>";

		echo "-------------------------------".$nl;
		echo "End of Central Directory record".$nl;
		echo "-------------------------------".$nl;
		print_r($this->DebugArray($this->CdInfo));

		echo $nl;
		echo "-------------------------".$nl;
		echo "Central Directory headers".$nl;
		echo "-------------------------".$nl;
		print_r($this->DebugArray($this->CdFileLst));

		if ($FileHeaders) {
			echo $nl;
			echo "------------------".$nl;
			echo "Local File headers".$nl;
			echo "------------------".$nl;
			print_r($this->DebugArray($this->VisFileLst));
		}

		echo "</pre>";

	}

	function DebugArray($arr) {
		foreach ($arr as $k=>$v) {
			if (is_array($v)) {
				$arr[$k] = $this->DebugArray($v);
			} elseif (substr($k,0,2)=='p_') {
				$arr[$k] = $this->_TxtPos($v);
			}
		}
		return $arr;
	}

	function FileExists($NameOrIdx) {
		return ($this->FileGetIdx($NameOrIdx)!==false);
	}

	function FileGetIdx($NameOrIdx) {
	// Check if a file name, or a file index exists in the Central Directory, and return its index
		if (is_string($NameOrIdx)) {
			if (isset($this->CdFileByName[$NameOrIdx])) {
				return $this->CdFileByName[$NameOrIdx];
			} else {
				return false;
			}
		} else {
			if (isset($this->CdFileLst[$NameOrIdx])) {
				return $NameOrIdx;
			} else {
				return false;
			}
		}
	}

	function FileGetIdxAdd($Name) {
	// Check if a file name exists in the list of file to add, and return its index
		if (!is_string($Name)) return false;
		$idx_lst = array_keys($this->AddInfo);
		foreach ($idx_lst as $idx) {
			if ($this->AddInfo[$idx]['name']===$Name) return $idx;
		}
		return false;
	}

	function FileRead($NameOrIdx, $Uncompress=true) {

		$this->LastReadComp = false; // means the file is not found
		$this->LastReadIdx = false;

		$idx = $this->FileGetIdx($NameOrIdx);
		if ($idx===false) return $this->RaiseError('File "'.$NameOrIdx.'" is not found in the Central Directory.');

		$pos = $this->CdFileLst[$idx]['p_loc'];
		$this->_MoveTo($pos);

		$this->LastReadIdx = $idx; // Can be usefull to get the idx

		$Data = $this->_ReadFile($idx, true);

		// Manage uncompression
		$Comp = 1; // means the contents stays compressed
		$meth = $this->CdFileLst[$idx]['meth'];
		if ($meth==8) {
			if ($Uncompress) {
				if ($this->Meth8Ok) {
					$Data = gzinflate($Data);
					$Comp = -1; // means uncompressed
				} else {
					$this->RaiseError('Unable to uncompress file "'.$NameOrIdx.'" because extension Zlib is not installed.');
				}
			}
		} elseif($meth==0) {
			$Comp = 0; // means stored without compression
		} else {
			if ($Uncompress) $this->RaiseError('Unable to uncompress file "'.$NameOrIdx.'" because it is compressed with method '.$meth.'.');
		}
		$this->LastReadComp = $Comp;

		return $Data;

	}

	function _ReadFile($idx, $ReadData) {
	// read the file header (and maybe the data ) in the archive, assuming the cursor in at a new file position

		$b = $this->_ReadData(30);

		$x = $this->_GetHex($b,0,4);
		if ($x!=='h:04034b50') return $this->RaiseError("Signature of Local File Header #".$idx." (data section) expected but not found at position ".$this->_TxtPos(ftell($this->ArchHnd)-30).".");

		$x = array();
		$x['vers'] = $this->_GetDec($b,4,2);
		$x['purp'] = $this->_GetBin($b,6,2);
		$x['meth'] = $this->_GetDec($b,8,2);
		$x['time'] = $this->_GetDec($b,10,2);
		$x['date'] = $this->_GetDec($b,12,2);
		$x['crc32'] = $this->_GetDec($b,14,4);
		$x['l_data_c'] = $this->_GetDec($b,18,4);
		$x['l_data_u'] = $this->_GetDec($b,22,4);
		$x['l_name'] = $this->_GetDec($b,26,2);
		$x['l_fields'] = $this->_GetDec($b,28,2);
		$x['v_name'] = $this->_ReadData($x['l_name']);
		$x['v_fields'] = $this->_ReadData($x['l_fields']);

		$x['bin'] = $b.$x['v_name'].$x['v_fields'];

		// Read Data
		if (isset($this->CdFileLst[$idx])) {
			$len_cd = $this->CdFileLst[$idx]['l_data_c'];
			if ($x['l_data_c']==0) {
				// Sometimes, the size is not specified in the local information.
				$len = $len_cd;
			} else {
				$len = $x['l_data_c'];
				if ($len!=$len_cd) {
					//echo "TbsZip Warning: Local information for file #".$idx." says len=".$len.", while Central Directory says len=".$len_cd.".";
				}
			}
		} else {
			$len = $x['l_data_c'];
			if ($len==0) $this->RaiseError("File Data #".$idx." cannt be read because no length is specified in the Local File Header and its Central Directory information has not been found.");
		}

		if ($ReadData) {
			$Data = $this->_ReadData($len);
		} else {
			$this->_MoveTo($len, SEEK_CUR);
		}

		// Description information
		$desc_ok = ($x['purp'][2+3]=='1');
		if ($desc_ok) {
			$b = $this->_ReadData(12);
			$s = $this->_GetHex($b,0,4);
			$d = 0;
			// the specification says the signature may or may not be present
			if ($s=='h:08074b50') {
				$b .= $this->_ReadData(4); 
				$d = 4;
				$x['desc_bin'] = $b;
				$x['desc_sign'] = $s;
			} else {
				$x['desc_bin'] = $b;
			}
			$x['desc_crc32']    = $this->_GetDec($b,0+$d,4);
			$x['desc_l_data_c'] = $this->_GetDec($b,4+$d,4);
			$x['desc_l_data_u'] = $this->_GetDec($b,8+$d,4);
		}

		// Save file info without the data
		$this->VisFileLst[$idx] = $x;

		// Return the info
		if ($ReadData) {
			return $Data;
		} else {
			return true;
		}

	}

	function FileReplace($NameOrIdx, $Data, $DataType=TBSZIP_STRING, $Compress=true) {
	// Store replacement information.

		$idx = $this->FileGetIdx($NameOrIdx);
		if ($idx===false) return $this->RaiseError('File "'.$NameOrIdx.'" is not found in the Central Directory.');

		$pos = $this->CdFileLst[$idx]['p_loc'];

		if ($Data===false) {
			// file to delete
			$this->ReplInfo[$idx] = false;
			$Result = true;
		} else {
			// file to replace
			$Diff = - $this->CdFileLst[$idx]['l_data_c'];
			$Ref = $this->_DataCreateNewRef($Data, $DataType, $Compress, $Diff, $NameOrIdx);
			if ($Ref===false) return false;
			$this->ReplInfo[$idx] = $Ref;
			$Result = $Ref['res'];
		}

		$this->ReplByPos[$pos] = $idx;

		return $Result;

	}

	/**
	 * Return the state of the file.
	 * @return {string} 'u'=unchanged, 'm'=modified, 'd'=deleted, 'a'=added, false=unknown
	 */
	function FileGetState($NameOrIdx) {

		$idx = $this->FileGetIdx($NameOrIdx);
		if ($idx===false) {
			$idx = $this->FileGetIdxAdd($NameOrIdx);
			if ($idx===false) {
				return false;
			} else {
				return 'a';
			}
		} elseif (isset($this->ReplInfo[$idx])) {
			if ($this->ReplInfo[$idx]===false) {
				return 'd';
			} else {
				return 'm';
			}
		} else {
			return 'u';
		}

	}

	function FileCancelModif($NameOrIdx, $ReplacedAndDeleted=true) {
	// cancel added, modified or deleted modifications on a file in the archive
	// return the number of cancels

		$nbr = 0;

		if ($ReplacedAndDeleted) {
			// replaced or deleted files
			$idx = $this->FileGetIdx($NameOrIdx);
			if ($idx!==false) {
				if (isset($this->ReplInfo[$idx])) {
					$pos = $this->CdFileLst[$idx]['p_loc'];
					unset($this->ReplByPos[$pos]);
					unset($this->ReplInfo[$idx]);
					$nbr++;
				}
			}
		}

		// added files
		$idx = $this->FileGetIdxAdd($NameOrIdx);
		if ($idx!==false) {
			unset($this->AddInfo[$idx]);
			$nbr++;
		}

		return $nbr;

	}

	function Flush($Render=TBSZIP_DOWNLOAD, $File='', $ContentType='') {

		if ( ($File!=='') && ($this->ArchFile===$File) && ($Render==TBSZIP_FILE) ) {
			$this->RaiseError('Method Flush() cannot overwrite the current opened archive: \''.$File.'\''); // this makes corrupted zip archives without PHP error.
			return false;
		}

		$ArchPos = 0;
		$Delta = 0;
		$FicNewPos = array();
		$DelLst = array(); // idx of deleted files
		$DeltaCdLen = 0; // delta of the CD's size

		$now = time();
		$date  = $this->_MsDos_Date($now);
		$time  = $this->_MsDos_Time($now);

		if (!$this->OutputOpen($Render, $File, $ContentType)) return false;

		// output modified zipped files and unmodified zipped files that are beetween them
		ksort($this->ReplByPos);
		foreach ($this->ReplByPos as $ReplPos => $ReplIdx) {
			// output data from the zip archive which is before the data to replace
			$this->OutputFromArch($ArchPos, $ReplPos);
			// get current file information
			if (!isset($this->VisFileLst[$ReplIdx])) $this->_ReadFile($ReplIdx, false);
			$FileInfo =& $this->VisFileLst[$ReplIdx];
			$b1 = $FileInfo['bin'];
			if (isset($FileInfo['desc_bin'])) {
				$b2 = $FileInfo['desc_bin'];
			} else {
				$b2 = '';
			}
			$info_old_len = strlen($b1) + $this->CdFileLst[$ReplIdx]['l_data_c'] + strlen($b2); // $FileInfo['l_data_c'] may have a 0 value in some archives
			// get replacement information
			$ReplInfo =& $this->ReplInfo[$ReplIdx];
			if ($ReplInfo===false) {
				// The file is to be deleted
				$Delta = $Delta - $info_old_len; // headers and footers are also deleted
				$DelLst[$ReplIdx] = true;
			} else {
				// prepare the header of the current file
				$this->_DataPrepare($ReplInfo); // get data from external file if necessary
				$this->_PutDec($b1, $time, 10, 2); // time
				$this->_PutDec($b1, $date, 12, 2); // date
				$this->_PutDec($b1, $ReplInfo['crc32'], 14, 4); // crc32
				$this->_PutDec($b1, $ReplInfo['len_c'], 18, 4); // l_data_c
				$this->_PutDec($b1, $ReplInfo['len_u'], 22, 4); // l_data_u
				if ($ReplInfo['meth']!==false) $this->_PutDec($b1, $ReplInfo['meth'], 8, 2); // meth
				// prepare the bottom description if the zipped file, if any
				if ($b2!=='') {
					$d = (strlen($b2)==16) ? 4 : 0; // offset because of the signature if any
					$this->_PutDec($b2, $ReplInfo['crc32'], $d+0, 4); // crc32
					$this->_PutDec($b2, $ReplInfo['len_c'], $d+4, 4); // l_data_c
					$this->_PutDec($b2, $ReplInfo['len_u'], $d+8, 4); // l_data_u
				}
				// output data
				$this->OutputFromString($b1.$ReplInfo['data'].$b2);
				unset($ReplInfo['data']); // save PHP memory
				$Delta = $Delta + $ReplInfo['diff'] + $ReplInfo['len_c'];
			}
			// Update the delta of positions for zipped files which are physically after the currently replaced one
			for ($i=0;$i<$this->CdFileNbr;$i++) {
				if ($this->CdFileLst[$i]['p_loc']>$ReplPos) {
					$FicNewPos[$i] = $this->CdFileLst[$i]['p_loc'] + $Delta;
				}
			}
			// Update the current pos in the archive
			$ArchPos = $ReplPos + $info_old_len;
		}

		// Ouput all the zipped files that remain before the Central Directory listing
		if ($this->ArchHnd!==false) $this->OutputFromArch($ArchPos, $this->CdPos); // ArchHnd is false if CreateNew() has been called
		$ArchPos = $this->CdPos;

		// Output file to add
		$AddNbr = count($this->AddInfo);
		$AddDataLen = 0; // total len of added data (inlcuding file headers)
		if ($AddNbr>0) {
			$AddPos = $ArchPos + $Delta; // position of the start
			$AddLst = array_keys($this->AddInfo);
			foreach ($AddLst as $idx) {
				$n = $this->_DataOuputAddedFile($idx, $AddPos);
				$AddPos += $n;
				$AddDataLen += $n;
			}
		}

		// Modifiy file information in the Central Directory for replaced files
		$b2 = '';
		$old_cd_len = 0;
		for ($i=0;$i<$this->CdFileNbr;$i++) {
			$b1 = $this->CdFileLst[$i]['bin'];
			$old_cd_len += strlen($b1);
			if (!isset($DelLst[$i])) {
				if (isset($FicNewPos[$i])) $this->_PutDec($b1, $FicNewPos[$i], 42, 4);   // p_loc
				if (isset($this->ReplInfo[$i])) {
					$ReplInfo =& $this->ReplInfo[$i];
					$this->_PutDec($b1, $time, 12, 2); // time
					$this->_PutDec($b1, $date, 14, 2); // date
					$this->_PutDec($b1, $ReplInfo['crc32'], 16, 4); // crc32
					$this->_PutDec($b1, $ReplInfo['len_c'], 20, 4); // l_data_c
					$this->_PutDec($b1, $ReplInfo['len_u'], 24, 4); // l_data_u
					if ($ReplInfo['meth']!==false) $this->_PutDec($b1, $ReplInfo['meth'], 10, 2); // meth
				}
				$b2 .= $b1;
			}
		}
		$this->OutputFromString($b2);
		$ArchPos += $old_cd_len;
		$DeltaCdLen =  $DeltaCdLen + strlen($b2) - $old_cd_len;

		// Output until "end of central directory record"
		if ($this->ArchHnd!==false) $this->OutputFromArch($ArchPos, $this->CdEndPos); // ArchHnd is false if CreateNew() has been called

		// Output file information of the Central Directory for added files
		if ($AddNbr>0) {
			$b2 = '';
			foreach ($AddLst as $idx) {
				$b2 .= $this->AddInfo[$idx]['bin'];
			}
			$this->OutputFromString($b2);
			$DeltaCdLen += strlen($b2);
		}

		// Output "end of central directory record"
		$b2 = $this->CdInfo['bin'];
		$DelNbr = count($DelLst);
		if ( ($AddNbr>0) || ($DelNbr>0) ) {
			// total number of entries in the central directory on this disk
			$n = $this->_GetDec($b2, 8, 2);
			$this->_PutDec($b2, $n + $AddNbr - $DelNbr,  8, 2);
			// total number of entries in the central directory
			$n = $this->_GetDec($b2, 10, 2);
			$this->_PutDec($b2, $n + $AddNbr - $DelNbr, 10, 2);
			// size of the central directory
			$n = $this->_GetDec($b2, 12, 4);
			$this->_PutDec($b2, $n + $DeltaCdLen, 12, 4);
			$Delta = $Delta + $AddDataLen;
		}
		$this->_PutDec($b2, $this->CdPos+$Delta , 16, 4); // p_cd  (offset of start of central directory with respect to the starting disk number)
		$this->OutputFromString($b2);

		$this->OutputClose();

		return true;

	}

	// ----------------
	// output functions
	// ----------------

	function OutputOpen($Render, $File, $ContentType) {

		if (($Render & TBSZIP_FILE)==TBSZIP_FILE) {
			$this->OutputMode = TBSZIP_FILE;
			if (''.$File=='') $File = basename($this->ArchFile).'.zip';
			$this->OutputHandle = @fopen($File, 'w');
			if ($this->OutputHandle===false) {
				return $this->RaiseError('Method Flush() cannot overwrite the target file \''.$File.'\'. This may not be a valid file path or the file may be locked by another process or because of a denied permission.');
			}
		} elseif (($Render & TBSZIP_STRING)==TBSZIP_STRING) {
			$this->OutputMode = TBSZIP_STRING;
			$this->OutputSrc = '';
		} elseif (($Render & TBSZIP_DOWNLOAD)==TBSZIP_DOWNLOAD) {
			$this->OutputMode = TBSZIP_DOWNLOAD;
			// Output the file
			if (''.$File=='') $File = basename($this->ArchFile);
			if (($Render & TBSZIP_NOHEADER)==TBSZIP_NOHEADER) {
			} else {
				header ('Pragma: no-cache');
				if ($ContentType!='') header ('Content-Type: '.$ContentType);
				header('Content-Disposition: attachment; filename="'.$File.'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: public');
				header('Content-Description: File Transfer'); 
				header('Content-Transfer-Encoding: binary');
				$Len = $this->_EstimateNewArchSize();
				if ($Len!==false) header('Content-Length: '.$Len); 
			}
		} else {
			return $this->RaiseError('Method Flush is called with a unsupported render option.');
		}

		return true;

	}

	function OutputFromArch($pos, $pos_stop) {
		$len = $pos_stop - $pos;
		if ($len<0) return;
		$this->_MoveTo($pos);
		$block = 1024;
		while ($len>0) {
			$l = min($len, $block);
			$x = $this->_ReadData($l);
			$this->OutputFromString($x);
			$len = $len - $l;
		}
		unset($x);
	}

	function OutputFromString($data) {
		if ($this->OutputMode===TBSZIP_DOWNLOAD) {
			echo $data; // donwload
		} elseif ($this->OutputMode===TBSZIP_STRING) {
			$this->OutputSrc .= $data; // to string
		} elseif (TBSZIP_FILE) {
			fwrite($this->OutputHandle, $data); // to file
		}
	}

	function OutputClose() {
		if ( ($this->OutputMode===TBSZIP_FILE) && ($this->OutputHandle!==false) ) {
			fclose($this->OutputHandle);
			$this->OutputHandle = false;
		}
	}

	// ----------------
	// Reading functions
	// ----------------

	function _MoveTo($pos, $relative = SEEK_SET) {
		fseek($this->ArchHnd, $pos, $relative);
	}

	function _ReadData($len) {
		if ($len>0) {
			$x = fread($this->ArchHnd, $len);
			return $x;
		} else {
			return '';
		}
	}

	// ----------------
	// Take info from binary data
	// ----------------

	function _GetDec($txt, $pos, $len) {
		$x = substr($txt, $pos, $len);
		$z = 0;
		for ($i=0;$i<$len;$i++) {
			$asc = ord($x[$i]);
			if ($asc>0) $z = $z + $asc*pow(256,$i);
		}
		return $z;
	}

	function _GetHex($txt, $pos, $len) {
		$x = substr($txt, $pos, $len);
		return 'h:'.bin2hex(strrev($x));
	}

	function _GetBin($txt, $pos, $len) {
		$x = substr($txt, $pos, $len);
		$z = '';
		for ($i=0;$i<$len;$i++) {
			$asc = ord($x[$i]);
			if (isset($x[$i])) {
				for ($j=0;$j<8;$j++) {
					$z .= ($asc & pow(2,$j)) ? '1' : '0';
				}
			} else {
				$z .= '00000000';
			}
		}
		return 'b:'.$z;
	}

	// ----------------
	// Put info into binary data
	// ----------------

	function _PutDec(&$txt, $val, $pos, $len) {
		$x = '';
		for ($i=0;$i<$len;$i++) {
			if ($val==0) {
				$z = 0;
			} else {
				$z = intval($val % 256);
				if (($val<0) && ($z!=0)) { // ($z!=0) is very important, example: val=-420085702
					// special opration for negative value. If the number id too big, PHP stores it into a signed integer. For example: crc32('coucou') => -256185401 instead of  4038781895. NegVal = BigVal - (MaxVal+1) = BigVal - 256^4
					$val = ($val - $z)/256 -1;
					$z = 256 + $z;
				} else {
					$val = ($val - $z)/256;
				}
			}
			$x .= chr($z);
		}
		$txt = substr_replace($txt, $x, $pos, $len);
	}

	function _MsDos_Date($Timestamp = false) {
		// convert a date-time timstamp into the MS-Dos format
		$d = ($Timestamp===false) ? getdate() : getdate($Timestamp);
		return (($d['year']-1980)*512) + ($d['mon']*32) + $d['mday'];
	}
	function _MsDos_Time($Timestamp = false) {
		// convert a date-time timstamp into the MS-Dos format
		$d = ($Timestamp===false) ? getdate() : getdate($Timestamp);
		return ($d['hours']*2048) + ($d['minutes']*32) + intval($d['seconds']/2); // seconds are rounded to an even number in order to save 1 bit
	}

	function _MsDos_Debug($date, $time) {
		// Display the formated date and time. Just for debug purpose.
		// date end time are encoded on 16 bits (2 bytes) : date = yyyyyyymmmmddddd , time = hhhhhnnnnnssssss
		$y = ($date & 65024)/512 + 1980;
		$m = ($date & 480)/32;
		$d = ($date & 31);
		$h = ($time & 63488)/2048;
		$i = ($time & 1984)/32;
		$s = ($time & 31) * 2; // seconds have been rounded to an even number in order to save 1 bit
		return $y.'-'.str_pad($m,2,'0',STR_PAD_LEFT).'-'.str_pad($d,2,'0',STR_PAD_LEFT).' '.str_pad($h,2,'0',STR_PAD_LEFT).':'.str_pad($i,2,'0',STR_PAD_LEFT).':'.str_pad($s,2,'0',STR_PAD_LEFT);
	}

	function _TxtPos($pos) {
		// Return the human readable position in both decimal and hexa
		return $pos." (h:".dechex($pos).")";
	}

	/**
	 * Search the record of end of the Central Directory.
	 * Return the position of the record in the file.
	 * Return false if the record is not found. The comment cannot exceed 65335 bytes (=FFFF).
	 * The method is read backwards a block of 256 bytes and search the key in this block.
	 */
	function _FindCDEnd($cd_info) {
		$nbr = 1;
		$p = false;
		$pos = ftell($this->ArchHnd) - 4 - 256;
		while ( ($p===false) && ($nbr<256) ) {
			if ($pos<=0) {
				$pos = 0;
				$nbr = 256; // in order to make this a last check
			}
			$this->_MoveTo($pos);
			$x = $this->_ReadData(256);
			$p = strpos($x, $cd_info);
			if ($p===false) {
				$nbr++;
				$pos = $pos - 256 - 256;
			} else {
				return $pos + $p;
			}
		}
		return false;
	}
	
	function _DataOuputAddedFile($Idx, $PosLoc) {

		$Ref =& $this->AddInfo[$Idx];
		$this->_DataPrepare($Ref); // get data from external file if necessary

		// Other info
		$now = time();
		$date  = $this->_MsDos_Date($now);
		$time  = $this->_MsDos_Time($now);
		$len_n = strlen($Ref['name']);
		$purp  = 2048 ; // purpose // +8 to indicates that there is an extended local header 

		// Header for file in the data section 
		$b = 'PK'.chr(03).chr(04).str_repeat(' ',26); // signature
		$this->_PutDec($b,20,4,2); //vers = 20
		$this->_PutDec($b,$purp,6,2); // purp
		$this->_PutDec($b,$Ref['meth'],8,2);  // meth
		$this->_PutDec($b,$time,10,2); // time
		$this->_PutDec($b,$date,12,2); // date
		$this->_PutDec($b,$Ref['crc32'],14,4); // crc32
		$this->_PutDec($b,$Ref['len_c'],18,4); // l_data_c
		$this->_PutDec($b,$Ref['len_u'],22,4); // l_data_u
		$this->_PutDec($b,$len_n,26,2); // l_name
		$this->_PutDec($b,0,28,2); // l_fields
		$b .= $Ref['name']; // name
		$b .= ''; // fields

		// Output the data
		$this->OutputFromString($b.$Ref['data']);
		$OutputLen = strlen($b) + $Ref['len_c']; // new position of the cursor
		unset($Ref['data']); // save PHP memory

		// Information for file in the Central Directory
		$b = 'PK'.chr(01).chr(02).str_repeat(' ',42); // signature
		$this->_PutDec($b,20,4,2);  // vers_used = 20
		$this->_PutDec($b,20,6,2);  // vers_necess = 20
		$this->_PutDec($b,$purp,8,2);  // purp
		$this->_PutDec($b,$Ref['meth'],10,2); // meth
		$this->_PutDec($b,$time,12,2); // time
		$this->_PutDec($b,$date,14,2); // date
		$this->_PutDec($b,$Ref['crc32'],16,4); // crc32
		$this->_PutDec($b,$Ref['len_c'],20,4); // l_data_c
		$this->_PutDec($b,$Ref['len_u'],24,4); // l_data_u
		$this->_PutDec($b,$len_n,28,2); // l_name
		$this->_PutDec($b,0,30,2); // l_fields
		$this->_PutDec($b,0,32,2); // l_comm
		$this->_PutDec($b,0,34,2); // disk_num
		$this->_PutDec($b,0,36,2); // int_file_att
		$this->_PutDec($b,0,38,4); // ext_file_att
		$this->_PutDec($b,$PosLoc,42,4); // p_loc
		$b .= $Ref['name']; // v_name
		$b .= ''; // v_fields
		$b .= ''; // v_comm

		$Ref['bin'] = $b;

		return $OutputLen;

	}

	function _DataCreateNewRef($Data, $DataType, $Compress, $Diff, $NameOrIdx) {

		if (is_array($Compress)) {
			$result = 2;
			$meth = $Compress['meth'];
			$len_u = $Compress['len_u'];
			$crc32 = $Compress['crc32'];
			$Compress = false;
		} elseif ($Compress and ($this->Meth8Ok)) {
			$result = 1;
			$meth = 8;
			$len_u = false; // means unknown
			$crc32 = false;
		} else {
			$result = ($Compress) ? -1 : 0;
			$meth = 0;
			$len_u = false;
			$crc32 = false;
			$Compress = false;
		}

		if ($DataType==TBSZIP_STRING) {
			$path = false;
			if ($Compress) {
				// we compress now in order to save PHP memory
				$len_u = strlen($Data);
				$crc32 = crc32($Data);
				$Data = gzdeflate($Data);
				$len_c = strlen($Data);
			} else {
				$len_c = strlen($Data);
				if ($len_u===false) {
					$len_u = $len_c;
					$crc32 = crc32($Data);
				}
			}
		} else {
			$path = $Data;
			$Data = false;
			if (file_exists($path)) {
				$fz = filesize($path);
				if ($len_u===false) $len_u = $fz;
				$len_c = ($Compress) ? false : $fz;
			} else {
				return $this->RaiseError("Cannot add the file '".$path."' because it is not found.");
			}
		}

		// at this step $Data and $crc32 can be false only in case of external file, and $len_c is false only in case of external file to compress
		return array('data'=>$Data, 'path'=>$path, 'meth'=>$meth, 'len_u'=>$len_u, 'len_c'=>$len_c, 'crc32'=>$crc32, 'diff'=>$Diff, 'res'=>$result);

	}

	function _DataPrepare(&$Ref) {
	// returns the real size of data
		if ($Ref['path']!==false) {
			$Ref['data'] = file_get_contents($Ref['path']);
			if ($Ref['crc32']===false) $Ref['crc32'] = crc32($Ref['data']);
			if ($Ref['len_c']===false) {
				// means the data must be compressed
				$Ref['data'] = gzdeflate($Ref['data']);
				$Ref['len_c'] = strlen($Ref['data']);
			}
		}
	}

	function _EstimateNewArchSize($Optim=true) {
	// Return the size of the new archive, or false if it cannot be calculated (because of external file that must be compressed before to be insered)

		if ($this->ArchIsNew) {
			$Len = strlen($this->CdInfo['bin']);
		} elseif ($this->ArchIsStream) {
			$x = fstat($this->ArchHnd);
			$Len = $x['size'];
		} else {
			$Len = filesize($this->ArchFile);
		}

		// files to replace or delete
		foreach ($this->ReplByPos as $i) {
			$Ref =& $this->ReplInfo[$i];
			if ($Ref===false) {
				// file to delete
				$Info =& $this->CdFileLst[$i];
				if (!isset($this->VisFileLst[$i])) {
					if ($Optim) return false; // if $Optimization is set to true, then we d'ont rewind to read information
					$this->_MoveTo($Info['p_loc']);
					$this->_ReadFile($i, false);
				}
				$Vis =& $this->VisFileLst[$i];
				$Len += -strlen($Vis['bin']) -strlen($Info['bin']) - $Info['l_data_c'];
				if (isset($Vis['desc_bin'])) $Len += -strlen($Vis['desc_bin']);
			} elseif ($Ref['len_c']===false) {
				return false; // information not yet known
			} else {
				// file to replace
				$Len += $Ref['len_c'] + $Ref['diff'];
			}
		}

		// files to add
		$i_lst = array_keys($this->AddInfo);
		foreach ($i_lst as $i) {
			$Ref =& $this->AddInfo[$i];
			if ($Ref['len_c']===false) {
				return false; // information not yet known
			} else {
				$Len += $Ref['len_c'] + $Ref['diff'];
			}
		}

		return $Len;

	}

}
