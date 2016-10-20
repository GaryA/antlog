' VBScript to install Antlog3 into Xampp
'
' (c) Gary Aylward (Team BeligerAnt) 2015
' Released under BSD licence, see LICENSE.md

const xamppStart = "xampp_start.exe"
const xamppStop = "xampp_stop.exe"
const cmd = "%comspec% /c "
const mysqlCmd = "mysql\bin\mysql.exe --user=root < "
const mysqlExe = "mysql\bin\mysql.exe"
const demoSqlFile = "antlog\antlog_demo.sql"
const emptySqlFile = "antlog\antlog.sql"
const indexFile = "htdocs\index.php"
const indexTempFile = "htdocs\temp.php"
const configFile = "antlog\config\params.php"
const configTempFile = "antlog\config\params_temp.php"
const runtimeFolder = "antlog\runtime"
const yii2Folder = "antlog\vendor\yiisoft\yii2"
const assetsFolder = "antlog\antlog\assets"
const antlogSourceFolder = "antlog\antlog"
const antlogDestFolder = "htdocs\antlog"
const htdocsFolder = "htdocs"

' No need to edit httpd-xampp.conf if using Xampp 5.6.21 or (presumably) later

const forReading = 1
const forWriting = 2

dim gFso, gMsg, gShell, gWshShell, gFolder, gFile
dim gXamppPath

set gFso = CreateObject("Scripting.FileSystemObject")
set gWshShell = WScript.CreateObject("WScript.Shell")
set gShell = WScript.CreateObject("Shell.Application")
set gFile = gFso.GetFile(Wscript.ScriptFullName)
' xampp installation folder is 2 levels up from the script
gFolder = gFso.GetParentFolderName(gFso.GetParentFolderName(gFile))
' set gFolder = gShell.BrowseForFolder(0, "Select the Xampp installation folder:", 0, 17)
if not (gFolder is Nothing) then
	gXamppPath = gFolder & "\"
	'gXamppPath = gFolder.Self.Path & "\"
	' check xampp_start.exe and xampp_stop.exe exist
	if gFso.FileExists(gXamppPath & xamppStart) and gFso.FileExists(gXamppPath & xamppStop) then
		' check index.php exists
		if gFso.FileExists(gXamppPath & indexFile) then
			' check antlog folder exists
			if gFso.FolderExists(gXamppPath & antlogSourceFolder) then
				' ensure assets folder is not read-only
				makeFolderWritable gXamppPath & assetsFolder
				' check destination folder does not exist
				if not(gFso.FolderExists(gXamppPath & antlogDestFolder)) then
					' move antlog sub-folder to htdocs
					gFso.MoveFolder gXamppPath & antlogSourceFolder, gXamppPath & antlogDestFolder
					' ensure runtime folder is not read-only
					makeFolderWritable gXamppPath & runtimeFolder
					' ensure yii2 folder is not read-only
					makeFolderWritable gXamppPath & yii2Folder
					' ensure htdocs folder is not read-only
					makeFolderWritable gXamppPath & htdocsFolder
					' edit index.php to redirect to antlog instead of xampp
					editIndexFile
					' edit config/params.php to set the path to mysql.exe
					editConfigFile
					' start the xampp server
					gWshShell.run gXamppPath & xamppStart, 7
					gMsg = msgBox("Xampp should start now. Please wait." & vbCrLf & _
						"Your firewall may ask you to allow it to run." & vbCrLf & _
						"It may also be hidden beneath this or other windows, check the taskbar." & _
						vbCrLf & vbCrLf & "Has Xampp started properly?", vbYesNo, "Antlog3 Installation")
					if gMsg = vbYes then
						' choose between empty and demo database
						gMsg = msgBox("You can install a demo database or an empty database. The demo data" & vbCrLf & _
							"will be overwritten when you download real data from the web site." & vbCrLf & _
							"You can use the demo data to practice using Antlog." & vbCrLf & vbCrLf & _
							"Do you want to install the demonstration database?", _
							vbYesNo, "Antlog3 Installation")
						if gMsg = vbYes then
							chosenSqlFile = demoSqlFile
						else
							chosenSqlFile = emptySqlFile
						end if
						' create antlog database in mysql
						createDatabase gXamppPath, chosenSqlFile
						' stop the xampp server
						gWshShell.run gXamppPath & xamppStop, 7
					else
						call msgBox("Since Xampp has not started properly there is" & vbCrLf & _
							"a problem with the installation." & vbCrLf & vbCrLf & _
							"Please download a fresh copy of Xampp.", _
							vbExclamation + vbOKOnly, "Antlog3 Installation")
					end if
				else
					call msgBox(gXamppPath & antlogDestFolder & " exists" & vbCrLf & vbCrLf & _
						"Delete the folder if attempting to re-install Antlog", _
						vbExclamation + vbOKOnly, "Antlog3 Installation")
				end if
			else
				call msgBox("Could not find Antlog files" & vbCrLf & vbCrLf & _
					"The Antlog files must be placed in " & gXamppPath & "antlog", _
					vbExclamation + vbOKOnly, "Antlog3 Installation")
			end if
		else
			call msgBox("Could not find index.php" & vbCrLf & vbCrLf & _
				"Ensure that Xampp is correctly installed.", _
				vbExclamation + vbOKOnly, "Antlog3 Installation")
		end if
	else
		call msgBox("Could not find xampp_start.exe or xampp_stop.exe" & vbCrLf & vbCrLf & _
			"Ensure that Xampp is correctly installed.", _
			vbExclamation + vbOKOnly, "Antlog3 Installation")
	end if
end if

sub makeFolderWritable(folderName)
	' ensure that folder is not read-only
	dim folder
	if gFso.FolderExists(folderName) then
		set folder = gFso.GetFolder(folderName)
		if folder.Attributes <> 0 then
			folder.Attributes = 0
		end if
	end if
end sub

sub editIndexFile
	' edit index.php to change the default location to antlog
	dim regEx, inFile, outFile, textString, replaced
	set regEx = New RegExp
	regEx.Pattern = "/.+/"
	set inFile = gFso.OpenTextFile(gXamppPath & indexFile, forReading)
	set outFile = gFso.OpenTextFile(gXamppPath & indexTempFile, forWriting, true)
	textString = inFile.ReadAll
	inFile.Close
	if regEx.Test(textString) then
		replaced = true
		textString = regEx.Replace(textString, "/antlog/")
		outFile.Write textString
	else
		replaced = false
		call msgBox("index.php not modified" & vbCrLf & vbCrLf & _
			"Possibly not a problem if re-installing Antlog", _
			vbExclamation + vbOKOnly, "Antlog3 Installation")
	end if
	outFile.Close
	if replaced = true then
		' delete original file and rename temporary file
		gFso.DeleteFile(gXamppPath & indexFile)
		gFso.MoveFile gXamppPath & indexTempFile, gXamppPath & indexFile
	else
		gFso.DeleteFile(gXamppPath & indexTempFile)
	end if
end sub

sub editConfigFile
	' edit params.php in the config folder to change the default path to mysql.exe
	dim regEx, inFile, outFile, textString, newPath, newDrive, replaced
	set regEx = new RegExp
	set inFile = gFso.OpenTextFile(gXamppPath & configFile, forReading)
	set outFile = gFso.OpenTextFile(gXamppPath & configTempFile, forWriting, true)
	textString = inFile.ReadAll
	inFile.Close
	newPath = Right(gXamppPath, Len(gXamppPath) - InStr(gXamppPath, ":")) & mysqlExe
	newDrive = gFso.GetDriveName(gXamppPath)
	regEx.Pattern = "\\xampp\\mysql\\bin\\mysql.exe"
	regEx.Global = false
	if regEx.Test(textString) then
		textString = regEx.Replace(textString, newPath)
		regEx.Pattern = "c:"
		if regEx.Test(textString) then
			textString = regEx.Replace(textString, newDrive)
			replaced = true
			outFile.Write textString
		else
			replaced = false
		end if
	else
		replaced = false
	end if
	outFile.Close
	if replaced = true then
		' delete original file and rename temporary file
		gFso.DeleteFile(gXamppPath & configFile)
		gFso.MoveFile gXamppPath & configTempFile, gXamppPath & configFile
	else
		gFso.DeleteFile(gXamppPath & configTempFile)
		call MsgBox("params.php not modified" & vbCrLf & vbCrLf & _
			"Possibly not a problem if re-installing Antlog", _
			vbExclamation + vbOKOnly, "Antlog3 Installation")
	end if
end sub

sub createDatabase(path, sqlFile)
	' create the antlog database by running mysql command
	dim retVal
	set retVal = gWshShell.exec(cmd & path & mysqlCmd & path & sqlFile)
	do while retVal.Status = 0
		WScript.Sleep 100
	loop
	if retVal.ExitCode = 0 then
		' display the output from mysql
		call msgBox(retVal.StdOut.ReadAll & vbCrLf & vbCrLf & retVal.StdErr.ReadAll, _
			vbInformation + vbOKOnly, "Antlog3 Installation")
	else
		call msgBox("Error running mySQL" & vbCrLf & vbCrLf & _
			"Ensure that Xampp is correctly installed.", vbExclamation + vbOKOnly, "Antlog3 Installation")
	end if
end sub