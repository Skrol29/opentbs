Option Explicit

Public Sub p_TBS_clean()

' OpenTBS tag cleaner.
' This macro for Ms Word free TBS tags of internal splits due to spelling, grammar, style inconsistences, and others...
'
' @author  Skrol29
' @version 0.04
' @date    2016-01-27
'
' --------------
' Installation :
' --------------
' * 1) Save the macro
' - Go to the View ribbon, click on « Macros / View macros... ».
' - Be sure the « Macro in » option is selected to « All active templates and documents ».
' - In the text zone « name of the macro », enter « TBS ».
' - Click on button [Create], then the VBA Editor is opened in an extra window and it displays a new empty Macro.
'   In the Project browser at the right panel, the current macro is placed under Normal / Module.
' - Replace the entire code in the editor window with this current code.
' - Save the macro and close the VBA Editor window.
' * 2) Add a button to start the macro
' - At the very top menu of Ms Word, there is the Quick Access Toolbar with a small drop-down button.
' - Click on the small drop-down button and choose item « More Commands... »
' - A new dialog box is opened, in the « Choose command from » list, select item « Macros ».
' - Then the list-box below displays all available macros. Select item « Normal.NewMacro.p_TBS_clean » (it may have another suffix).
' - Click on button « Add » and then « Ok » in order to save the button.
' - Now the button is available in the Quick Access Toolbar.
'
' --------------
' Use :
' --------------
' Put the cursor inside a TBS tag, or select a text, or select one or several cells,
' then click on the button dedicated to this macro, then :
' - the selection is cleared from check language and proofing
' - the selection is uniformly formatted (only if it's a single TBS tag)
' - document does not save internal RSIDs no more
'
'

    Const c_Title = "OpenTBS cleaner macro"

    Dim DelimLevel As Integer
    Dim DelimMet As Boolean
    Dim TxtCurr As String
    Dim TxtPrev As String
    Dim x  As String
    Dim n  As Long
    Dim Ok As Boolean
    Dim Direction As Integer
    Dim SelectTbsTag As Integer
   
    ' vbLf = chr(10) ; vbCr = chr(13) ; vbFormFeed = chr(12)
    Dim chrCell As String
    chrCell = Chr(7) ' New cell
       
    ' delete special chars that may be selected at the end of before (happens with tables)
    x = Selection.Text
    x = Replace(x, vbCr, "")
    x = Replace(x, vbLf, "")
    x = Replace(x, vbFormFeed, "")
    x = Replace(x, chrCell, "")
    If Len(x) < 2 Then ' Note : An empty selection has .Text equal to the first next char

        Direction = -1
        DelimMet = False
       
        While Direction <> 0

            TxtCurr = ""
            Ok = True
            While Ok
           
                ' Extend the selection to the left
                If (Direction = -1) Then
                    n = Selection.MoveStart(wdCharacter, -1)
                Else
                    n = Selection.MoveRight(wdCharacter, 1, wdExtend)
                End If
               
                ' Check for line breaks or cell separator
                x = Selection.Text
                If (InStr(x, vbCr) > 0) Or (InStr(x, vbLf) > 0) Or (InStr(x, vbFormFeed) > 0) Or (InStr(x, chrCell) > 0) Then
                    Ok = False
                    If (Direction = -1) Then
                        n = Selection.MoveRight(wdCharacter, 1, wdMove)
                    Else
                        n = Selection.MoveRight(wdCharacter, -1, wdExtend)
                    End If
                End If

                ' Check if the content has change. Ovoid infinit loop.
                TxtPrev = TxtCurr
                TxtCurr = Selection.Text
                If TxtPrev = TxtCurr Then
                    Ok = False
                End If
               
                ' Check for a TBS ending char tag
                If (Direction = -1) Then
                    x = Left$(Selection.Text, 1)
                Else
                    x = Right$(Selection.Text, 1)
                End If
                If (x = "[") Then
                    DelimLevel = DelimLevel + 1
                    DelimMet = True
                    If (DelimLevel > 0) And (Direction = -1) Then
                        Ok = False
                    End If
                ElseIf (x = "]") Then
                    DelimLevel = DelimLevel - 1
                    DelimMet = True
                End If
                If DelimMet And (DelimLevel = 0) Then
                    SelectTbsTag = True
                    Ok = False
                End If
               
            Wend

            ' Next direction
            If Direction = -1 Then
                Direction = 1  ' Right
            Else
                Direction = 0 ' Stop
            End If

        Wend
   
    End If
   
    ' Formating
    If SelectTbsTag Then
        Selection.CopyFormat  ' Copy the format of the first character of the selection
        Selection.PasteFormat ' Copy the format to the entire selection
    End If
    ' Selection.ClearCharacterDirectFormatting ' Not good : delete local formating such as color, bold, ...
    
    ' Language
    Selection.LanguageID = wdNoProofing ' Add <w:noProof/> in the XML
    ' Selection.LanguageID = ActiveDocument.Styles(Selection.ParagraphFormat.Style).LanguageID ' Useless: <w:noProof/> is added anyway.
    Selection.NoProofing = True
    Application.CheckLanguage = False
   
    ' Prevent internal SRID that may invisibly split TBS tag.
    Application.Options.StoreRSIDOnSave = False
    
    ' Under certain cicumstances, the NoProofing has no effect, this is because a spelling error remains according to Ms Word.
    ' Most of the time we just have to add a space in order to avoid the Ms Word spelling error.
    If (SelectTbsTag) And ((Selection.Range.SpellingErrors.Count > 0) Or (Selection.Range.GrammaticalErrors.Count > 0)) Then
        Selection.InsertBefore " "
        Selection.LanguageID = wdNoProofing ' Add <w:noProof/> in the XML
        Selection.NoProofing = True
        Application.CheckLanguage = False
        MsgBox "A space as been added before the TBS tag in order to avoid the remaining spelling error.", vbInformation + vbOKOnly, c_Title
    End If
   
End Sub



