Option Explicit

Public Sub p_TBS_clean()

' This macro for Ms Words help you to clean up TBS tags in the active document.
'
' @author  Skrol29
' @version 0.03
' @date    2016-01-07
'
' Use :
' Put the cursor inside a TBS tag, or select a text, or select one or several cells,
' then click on the button dedicated to this macro, then :
' - the selection is cleared from check language and proffing
' - the selection is uniformly formated (only if its a single TBS tag)
' - document does not save internal RSIDs no more
'
' Installation :
' - Go to the View ribbon, click on « Macros / View macros... ».
' - In the text zone « name of the macro », enter « TBS ».
' - Click on button [Create]
' - Paste the current code in the Visual Basic window that is displayed.
' - ...
'

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
   
End Sub


