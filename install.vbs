' VBScript to install Antlog3 into Xampp
'
' (c) Gary Aylward (Team BeligerAnt) 2015
' Released under BSD licence, see LICENSE.md

const xamppPath = "C:\xampp"
const xamppStart = "C:\xampp\xampp_start.exe"
const xamppStop = "C:\xampp\xampp_stop.exe"
const mysqlCmd = "%comspec% /c C:\xampp\mysql\bin\mysql.exe --user=root < C:\xampp\antlog\antlog.sql"
const xamppConfFile = "C:\xampp\apache\conf\extra\httpd-xampp.conf"
const xamppConfTempFile = "C:\xampp\apache\conf\extra\temp.conf"
const indexFile = "C:\xampp\htdocs\index.php"
const indexTempFile = "C:\xampp\htdocs\temp.php"
const runtimeFolder = "C:\xampp\antlog\runtime"
const yii2Folder = "C:\xampp\antlog\vendor\yiisoft\yii2"
const assetsFolder = "C:\xampp\antlog\antlog\assets"
const antlogSourceFolder = "C:\xampp\antlog\antlog"
const antlogDestFolder = "C:\xampp\htdocs\antlog"
const replaceText =	"Allow from ::1 127.0.0.0/8"

const forReading = 1
const forWriting = 2

dim fso, msg, shell, folder, inFile, outFile, textString, regEx, retVal, replaced

set fso = CreateObject("Scripting.FileSystemObject")
set shell = WScript.CreateObject("WScript.Shell")
set regEx = New RegExp
regEx.Pattern = "xampp"
' check xampp folder exists
if fso.folderExists(xamppPath) then
	' check xampp_start.exe and xampp_stop.exe exist
	if fso.FileExists(xamppStart) and fso.FileExists(xamppStop) then
		' check index.php and httpd-xampp.conf exist
		if fso.FileExists(indexFile) and fso.FileExists(xamppConfFile) then
			' check antlog folder exists
			if fso.FolderExists(antlogSourceFolder) then
				' ensure assets folder is not read-only
				folder = fso.GetFolder(assetsFolder)
				if folder.Attributes <> 0 then
					folder.Attributes = 0
				end if
				' check destination folder does not exist
				if not(fso.FolderExists(antlogDestFolder)) then
					' move antlog sub-folder to htdocs
					fso.MoveFolder antlogSourceFolder, antlogDestFolder
					' ensure runtime folder is not read-only
					folder = fso.GetFolder(runtimeFolder)
					if folder.Attributes <> 0 then
						folder.Attributes = 0
					end if
					' ensure yii2 folder is not read-only
					folder = fso.GetFolder(yii2Folder)
					if folder.Attributes <> 0 then
						folder.Attributes = 0
					end if
					' edit index.php to redirect to antlog instead of xampp
					set inFile = fso.OpenTextFile(indexFile, forReading)
					set outFile = fso.OpenTextFile(indexTempFile, forWriting, true)
					textString = inFile.ReadAll
					inFile.Close
					if regEx.Find(textString) then
						replaced = true
						textString = regEx.Replace(textString, "antlog")
						outFile.Write textString
					else
						replaced = false
						msg = msgBox("index.php not modified" & vbCrLf & vbCrLf & _
						"Possibly not a problem if re-installing Antlog", vbExclamation + vbOKOnly, "Antlog3 Installation")
					end if
					outFile.Close
					if replaced = true then
						' delete original file and rename temporary file
						fso.DeleteFile(inFile)
						fso.MoveFile outFile, inFile
					else
						fso.DeleteFile(outFile)
					end if
					' edit httpd-xampp.conf to restrict access to xampp control panel etc
					set inFile = fso.OpenTextFile(xamppConfFile, forReading)
					set outFile = fso.OpenTextFile(xamppConfTempFile, forWriting, true)
					regEx.Pattern = "^[ \t]*Allow from[\s:\.0-9a-f\\/]*"
					regEx.Multiline = true
					textString = inFile.ReadAll
					if regEx.Find(textString) then
					replaced = true
						textString = regEx.Replace(textString, vbTab & replaceText & vbCrLf & vbCrLf & vbTab)
						outFile.Write textString
					else
						replaced = false
						msg = msgBox("httpd-xampp.conf not modified" & vbCrLf & vbCrLf & _
						"Possibly not a problem if re-installing Antlog", vbExclamation + vbOKOnly, "Antlog3 Installation")
					end if
					inFile.Close
					outFile.Close
					if replaced = true then
						' delete original file and rename temporary file
						fso.DeleteFile(inFile)
						fso.MoveFile outFile, inFile
					else
						fso.DeleteFile(outFile)
					end if
					' start the xampp server
					shell.run xamppStart, 7
					Wscript.Sleep 1000
					' create antlog database in mysql
					set retVal = shell.exec(mysqlCmd)
					do while retVal.Status = 0
						WScript.Sleep 100
					loop
					if retValExitCode = 0 then
						msg = msgBox(retVal.StdOut.ReadAll & vbCrLf & vbCrLf & retVal.StdErr.ReadAll, vbInformation + vbOKOnly, "Antlog3 Installation")
					else
						msg = msgBox("Error running mySQL" & vbCrLf & vbCrLf & _
						"Ensure that the Xampp portable application is correctly installed.", vbExclamation + vbOKOnly, "Antlog3 Installation")
					end if
					shell.run xamppStop, 7
				else
					msg = msgBox(antlogDestFolder & " exists" & vbCrLf & vbCrLf & _
					"Delete the folder if attempting to re-install Antlog", vbExclamation + vbOKOnly, "Antlog3 Installation")
				end if
			else
				msg = msgBox("Could not find Antlog files" & vbCrLf & vbCrLf & _
				"The Antlog files must be placed in C:\xampp\antlog", vbExclamation + vbOKOnly, "Antlog3 Installation")
			end if
		else
			msg = msgBox("Could not find index.php or httpd-xampp.conf" & vbCrLf & vbCrLf & _
			"Ensure that the Xampp portable application is correctly installed.", vbExclamation + vbOKOnly, "Antlog3 Installation")
		end if
	else
		msg = msgBox("Could not find xampp_start.exe or xampp_stop.exe" & vbCrLf & vbCrLf & _
		"Ensure that the Xampp portable application is correctly installed.", vbExclamation + vbOKOnly, "Antlog3 Installation")
	end if
else
	msg = msgBox("Xampp installation not found at C:\xampp" & vbCrLf & vbCrLf & _
	"You have to install Xampp before the installation can continue.", vbExclamation + vbOKOnly, "Antlog3 Installation")
end if