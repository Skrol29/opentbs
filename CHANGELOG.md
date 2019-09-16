# Change Log

All notable changes to this project will be documented in this file.

## [1.10.0] - 2019-09-16

### New features

- New command OPENTBS_GET_CELLS for reading range in a workbook.

- Parameter « ope=delcol »
  - a new secondary parameter "colset" enables you to define several set of columns to delete in the template 
  - parameter "colnum" becomes optional
  - support ranges of columns in the list of columns to delete
  
- New parameter « ope=docfield » enables you to replace document calculated fields with TBS fields.

### Bug fixes

- DOCX only: some spaces between words could be not displayed in LibreOffice.

- Alias of block « tbs:draw » did not work correctly with MsWord.

### Enhancements

- Get off PHP 4 compatibilty.

## [1.9.12] - 2019-03-10

### New features

- Can merge charts in XLSX and ODS.
- Command OPENTBS_CHART_DELETE_CATEGORY can now delete several categories or all categories.

### Bug fixes

- Ms Office documents (DOCX, XLSX, PPTX) could be corrupted after merging a chart containing filtered series. That is a series which is included in the chart, but not displayed.
- LibreOffice Document : fix the error message when merging a chart: "ObjectReplacements/Object 1" is not found in the Central Directory.

### Enhancements

- More robust technical when merging chart data is Ms Office documents (DOCX, XLSX, PPTX).

## [1.9.11] - 2017-10-03

### New features

- New command OPENTBS_CHART_DELETE_CATEGORY
- New command OPENTBS_GET_OPENED_FILES
- New command OPENTBS_WALK_OPENED_FILES

## [1.9.10] - 2017-07-05

### Bug fixes

- DOCM, PPTM and XSLM documents (that is documents with macros) are merged correctly but Ms Office display an error message when the file is downloaded using $TBS->Show(OPENTBS_DOWNLOAD,...).

## [1.9.9] - 2017-05-28

### Bug fixes

- XLSX sheet containing an empty and unformatted row may produce in some circumstances a corrupted result when merged.

## [1.9.8] - 2016-12-27

### New features

- New command OPENTBS_MAKE_OPTIMIZED_TEMPLATE

### Bug fixes

- Processed templates are not marked as prepared.

## [1.9.7] - 2016-08-16

### New features

- New command OPENTBS_GET_FILES

### Bug fixes

- Parameter "ope=delcol": if parameter "colnum" is empty then first colmun is deleted while it should be no column.
- Command OPENTBS_CHART: error message « Name of the series not found. » when the series contains special characters, like accents.

## [1.9.6] - 2016-03-24

### Bug fixes

- Some commands used to find a worksheet by its internal id instead of its number in the worksheet list.
  Now they all search by number in the worksheet list.
  Please not that you may have to change your code if you are using those command with the number if the sheet rather of the name of the sheet.
  Concerned commands are:
  - OPENTBS_SELECT_SHEET
  - OPENTBS_DELETE_SHEET
  - OPENTBS_DISPLAY_SHEETS
- Merging text with line-breaks in a DOCX was not displayed correctly in LibreOffice.

### Enhancements

- DOCX: Some special merging of enhanced graphical objects (like merging fill color in a shape) may corrupt the document
  because they are stored in several ways by Ms Word.
- The XML synopsis in now available in HTML.
  
## [1.9.5] - 2016-02-09

### New features

- New command OPENTBS_EDIT_ENTITY
- New command OPENTBS_CHART_INFO
- LoadTemplate(false) does close the current template so the template file is no longer locked.
- New OpenTBS add-in for Microsoft Word: it helps to clean TBS tags.

### Enhancements

- new property $TBS->OtbsDeleteObsoleteChartData ## false; (Ms Office only)
- OpenTBS do not redo optimisation on the loaded template if it has already been done by OpenTBS previously.
- Chart in Ms Office : Ensure the caption of a category is displayed even if is has missing data.
- Some code cleanup.

### Bug fixes

- PHP error with ODT templates when changing the name of a Chart series that hadn't any name before.
- PHP error when using command OPENTBS_SEARCH_IN_SLIDES.
- OpenTBS error « unable to found the chart corresponding to 'xxx' » in DOCX when the chart is not formated as "inline with text".

## [1.9.4] - 2015-02-11

### Bug fixes

- Document corruption with OpenTBS 1.9.3 when merging pictures in a block and using parameter "adjust".

## [1.9.3] - 2015-01-16

### Bug fixes

- XLSX corruption when merging a float value to a cell when the decimal separator is not a dot because of the locale setting.
- Possible DOCX corruption when using text box or tables in header and footer.
- Produce a corrupted Ms Office document when delete the last series of a chart using command OPENTBS_CHART. May happens with other series.

### Enhancements

- OpenTBS clear error message when using Show() without template loaded. Instead of an ugly PHP error.

## [1.9.2] - 2014-09-25

### Enhancements

- 6 times faster when saving XLSX merged sheets with numerous rows.

### New features

- New command OPENTBS_RELATIVE_CELLS  : optimizes XLSX merged sheets with numerous rows.

## [1.9.1] - 2014-09-20

### Bug fixes

- Adjusting size of image in docx.
- When turn a sheet to hidden in an XLSX, then the file may be corrupted.
- Command OPENTBS_COUNT_SLIDES did not work for ODP.
- A PPTX can be corrupted when opening a template which is a previous result of a merge. This is because an <a:r> must contain at least one <a:t>.
- Merging several XLSX with the same OpenTBS instance can produce erroneous merged cells.

### New features

- New parameter "unique" for picture
- ODS files are now recognized. It is equivalent to ODS. 
- New command OPENTBS_COUNT_SHEETS
- New command OPENTBS_ADD_CREDIT
- New command OPENTBS_SYSTEM_CREDIT

### Enhancements

- OPENTBS_CHANGE_PICTURE now use an array of parameters.

## [1.9.0] - 2014-04-10

### Bug fixes

- Corrupted MS Office files when inserting images named with space or accent.
- XLSX warning for corrupted subfile "/xl/calcChain.xml-Part".
- Some ODS templates compatible with Ms Excel can become erroneous for Ms 
  Excel after the merge with OpenTBS. Message "The workbook cannot be opened 
  or repaired by Microsoft Excel because it is corrupt".
- Some XLSX templates built with LibreOffice can be very long to be opened 
  with OpenTBS. That is because LibreOffice add some extra useless rows 
  definition at the bottom limit of the sheets.
- A big number merged in a XLSX with parameter "ope=tbs:num" can display 
  another value. Example : 7580563123 displays -1009371469 in 32bits.
- Inappropriate error message "ExtType is not defined" when execute a 
  command but no template is loaded.
- OPTBS_SELECT_SHEET with an ODS template do nothing. Now it selects the 
   main file (contains all sheets).
- OPTBS_SELECT_SLIDE with an ODP template do nothing. Now it selects the 
  main file (contains all slides).

### New features

- new command OPENTBS_SELECT_FILE
- new command OPENTBS_SELECT_HEADER
- new command OPENTBS_SELECT_FOOTER
- new command OPENTBS_GET_HEADERS_FOOTERS
- new command OPENTBS_SEARCH_IN_SLIDES
- new parameter $Master for command OPENTBS_SELECT_SLIDE
  and OPENTBS_COUNT_SLIDES

### Enhancements

- Debug mode available even if no template is loaded.
- Debug mode display the Zlib availability.
- Based on TbsZip 2.16

## [1.8.3] - 2014-02-02

### Bug fixes

- (since version 1.8.0) the changed picture is another picture in the document or an empty picture.
  This could happen if you used parameter "changepic" with both [onload] and [onshow] or both MergeBlock() and [onshow].

## [1.8.2] - 2014-01-26

### Bug fixes

- some TBS fields seems to be ignored in ODT files edited with LibreOffice 4 or higher. This was due to a new RSID feature in LibreOffice that inserts invisible XML elements.

- no data displayed when merging numerical cells in ODS files built with LibreOffice 4 or higher. This was due to a new attribute in subjacent XML elements.

### New features

- internal option : $TBS->OtbsClearWriter

### Enhancements

- Supports new parameter "parallel" of TinyButStrong 3.9.0.

- based on TbsZip 2.15

## [1.8.1] - 2013-08-30

### New features

- the loaded template can be a PHP file handle.

### Bug fixes

- A DOCX file could be corrupted when using "block=tbs:page" and the last paragraph of the document has no text.

### Enhancements

- Ms Excel Sheets are now saved with explicit references for rows and cells, so merged templates are viewable with Libre Office and other third viewers.

- keywords for changing cell types is the same for LibreOffice and Ms Office.

- based on TbsZip 2.14

## [1.8.0] - 2013-05-04

### New features

- automatically cleans up spelling in PPTX templates (such information may deconstruct the TBS tags). This feature can be disabled.

- Block Alias helps to define TBS blocks easily on pages, sections,....

- merging a chart from its title.

- new parameter "tagpos" to define the position of the TBS tag realtively to the image (when using "ope=changepic").

- new parameter "delcol" to delete columns in tables.

- new parameter "mergecell" to merge cells in tables.

- new command OPENTBS_SELECT_SLIDE

- new command OPENTBS_DELETE_SLIDES

- new command OPENTBS_DISPLAY_SLIDES

- new command OPENTBS_COUNT_SLIDES

- new command OPENTBS_MERGE_SPECIAL_ITEMS

- new command OPENTBS_CHANGE_PICTURE

### Bug fixes

- parameter "ope=changepic" did not work in PPTX documents.

- parameter "ope=changepic" did not work with [onload] fields in Ms Office.

- parameter "default=current" did not work when using MergeField() instead of MergeBlock().

- some tab may be deleted in the template (during the automatic cleanup process).

### Enhancements

- merging charts is also available for LibreOffice documents

- when using "ope=changepic", default value of parameter "default" is now "current".

- delete unused pictures.

- based on TbsZip 2.13

- requires TBS 3.8.0

## [1.7.6] - 2012-06-06

### Bug fixes

- Restore lost spaces around merged TBS fields in Ms Word documents. The patch doesn't work for headers and footers, unfortunately.

## [1.7.5] - 2012-02-14

### Bug fixes

- Avoid erroneous Ms Word merged documents when duplicating objects such as drawings and shapes.

### Enhancements

- Based on TbZip version 2.11

- New coding shorctut $TBS->TbsZip.

- More examples of formulas for Xlsx and Ods speadsheets. 

## [1.7.4] - 2011-10-20

### New features

- parameter "defaut=current" does not work and may build invalid documents when the target image is missing.

- new command OPENTBS_REPLACEFILE

- new command OPENTBS_FILEEXISTS

## [1.7.3] - 2011-10-13

### Bug fixes

- in Ms Word documents, automatic fields (onload, onshow) placed in headers and footers with parameter "ope=changepic" are producing an erroneous merge. In Word 2010 the picture may by missing, in Word 2007 the docx file may be considered as corrupted.

## [1.7.2] - 2011-10-12 

### Bug fixes

- error when using command OPENTBS_SELECT_SHEET with a sheet name: Notice: Undefined index: xxx in xxx on line 1986. 

## [1.7.1] - 2011-10-07

### Bug fixes

- first non-empty cell of an Excel Spreadsheet is never merged if it contains a TBS field.

### Enhancements

- minor internal improvements.

## [1.7.0] - 2011-08-21

### New features

- new parameter 'adjust' for changing picture size

- new command OPENTBS_DEBUG_INFO 

- new command OPENTBS_SELECT_MAIN 

- new command OPENTBS_SELECT_SHEET

- new command OPENTBS_DISPLAY_SHEETS

- new command OPENTBS_DELETE_SHEETS

- new command OPENTBS_DELETE_COMMENTS

- new command OPENTBS_DELETE_ELEMENTS

- parameter 'changepic' is optimized

## [1.6.2] - 2011-07-12

### Bug fixes

- Ms Excel cells could consider as error some formatted values such as '0.00000000000000'.

## [1.6.1] - 2011-06-08

### Bug fixes

- some documents may be corrupted when created using OPENTBS_DOWNLOAD because of a PHP error "supplied argument is not a valid stream resource" or "Undefined property: clsOpenTBS::$OutputHandle".

- using keyword "xlsxNum", "xlsxDate" or "xlsxBool" inside a cell that is not merged can make a corrupted XLSX spreadsheet.

### Enhancements

- updated templates in the demo.

- based on a TbsZip v2.8

## [1.6.0] - 2011-06-07

### New features

- merge charts in Ms Word documents.

- merge rows and columns Ms Excel workbooks.

- new "ope" parameters for forcing cells type in Ms Excel (Numeric, Date and Boolean).

- debug mode enhanced.

- force the type of document using command OPENTBS_FORCE_DOCTYPE.

- deal with apostrophes using property OtbsConvertApostrophes.

### Enhancements

- if the document extension is not recognized, then try to recognize document type by sub-file presence.

- can use the Direct Command feature of TBS 3.7.0.

- based on a TbsZip v2.6

## [1.5.0] - 2011-03-20

### New features

- headers and footers are automatically loaded for OpenOffice & MsOffice.

- automatically cleans up spelling and change trackings information in MsWord templates (such information may deconstruct the TBS tags). This feature can be disabled.

- new constant OPENTBS_DEBUG_AVOIDAUTOFIELDS

### Bug fixes

- in debug mode: "warning function.str-repeat: Second argument has to be greater than or equal to 0"

- when using OPENTBS_RESET: "Warning: Missing argument 2 for clsOpenTBS::OnCommand() in ... on line 225"

- DML images were not found when using parameter "ope=changepic" in a DOCX document

- the script ends and display the XML contents when a when using parameter "ope=changepic" with a new image type in a DOCX document

### Enhancements

- Debug doesn't stopped if an OpenTBS alert occurs.

- OpenTBS alerts say if the process will be stopped.

## [1.4.1] - 2010-10-28

### Bug fixes

- major bug fixed: due to TbsZip, some added or modified files can be saved the document with a wrong CRC control code. This could make softwares to consider the document as corrupted, but were often easily fixed by OpenOffice and Ms Office. Only few CRC codes are wrongly saved, thus the bug is rare and can seem to appear randomly on few documents.

## [1.4.0] - 2010-10-05

### New features

- new parameters "changepic" and "default"

## [1.3.3] - 2010-08-05

### Bug fixes

- property version of OpenTBS version 1.3.2 was saying 1.3.1

## [1.3.2] - 2010-07-23

### New features

- possibility to change de default data conversion using the new constants OPENTBS_DEFAULT, OPENTBS_ALREADY_XML or OPENTBS_ALREADY_UTF8

### Enhancements

- enhanced debug mode: listing of added, deleted and modified files ; and show XML formated contents of files merged with OpenTBS.

## [1.3.1] - 2010-07-01

### Bug fixes

-  based on TbsZip version 2.1: fixes a bug that saved a bad time of modification file was added, and saved time modification when a file content is replaced.

-  the addpic operator now automatically updates the "fanifest.xml" file on OpenOffice document. Without this fix, an ODP merged document could be open with an error message with OpenOffice >= 3.2

## [1.3] - 2010-06-01

### New features

- a new plugin command that add a new file in the archive

- a new plugin command that delete a new file in the archive

- a parameter 'ope=addpic' that add a new picture in the archive directly from the template

### Enhancements

- based on a TbsZip v2 (modify/delete/add files in a zip archive, )

## [1.1] - 2009-11-19

### New features

-  render option : OPENTBS_STRING

-  can reset changes in the current archive using $TBS->Plugin(OPENTBS_PLUGIN, OPENTBS_RESET);

### Enhancements

-  extension of the archive is ignored by LoadTemplate() if the name is ended with '#'

### Bug fixes

-  in case of several files to take from the archive in one shot, then only the last one had [onload] fields merged.