!include "MUI.nsh"
Name "LxCars Client 1.4.1"
!define INSTALLATIONNAME "LxCarsClient"


Function LaunchLink
  ExecShell "" "$INSTDIR\Tutorial.pdf"
FunctionEnd

!define MUI_FINISHPAGE_RUN
!define MUI_FINISHPAGE_RUN_TEXT "Zeige mir das Tutorial an."
!define MUI_FINISHPAGE_RUN_FUNCTION "LaunchLink"



OutFile "LxCarsClientInstall.exe"
InstallDir $PROGRAMFILES\LxCarsClient

VIProductVersion                 "1.4.1.0"
VIAddVersionKey ProductName      "LxCars"
VIAddVersionKey Comments         "Client for Windows"
VIAddVersionKey CompanyName      "Inter-Data"
VIAddVersionKey LegalCopyright   "Inter-Data"
VIAddVersionKey FileDescription  "Setup for Windows"
VIAddVersionKey FileVersion      1
VIAddVersionKey ProductVersion   1
VIAddVersionKey InternalName     "Inter-Data"
VIAddVersionKey LegalTrademarks  "Inter-Data"
VIAddVersionKey OriginalFilename "LxCarsSetup.exe"


!insertmacro MUI_PAGE_DIRECTORY
!insertmacro MUI_PAGE_INSTFILES
!insertmacro MUI_PAGE_FINISH

!insertmacro MUI_UNPAGE_CONFIRM
!insertmacro MUI_UNPAGE_INSTFILES

!insertmacro MUI_LANGUAGE "German"




Section ""
  SetOutPath $INSTDIR
  File "Z:\lxcars\clientInterface\LxCarsClient.exe" 
  File "Z:\lxcars\clientInterface\Tutorial.pdf"
  WriteUninstaller $INSTDIR\uninstall.exe
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${INSTALLATIONNAME}" "DisplayName" "LxCarsClient"
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${INSTALLATIONNAME}" "UninstallString" '"$INSTDIR\uninstall.exe"'
  WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${INSTALLATIONNAME}" "NoModify" 1
  WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${INSTALLATIONNAME}" "NoRepair" 1

  WriteRegStr HKCR "lxcars" "" "URL:lxcars Protocol"
  WriteRegStr HKCR "lxcars" "URL Protocol" '""'
  WriteRegStr HKCR "lxcars\shell\open\command" "" '"$PROGRAMFILES\LxCarsClient\LxCarsClient.exe" "%1"'
SectionEnd


Section "Uninstall"
  DeleteRegKey HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${INSTALLATIONNAME}"
  DeleteRegKey HKCR "lxcars"
  Delete $INSTDIR\uninstall.exe
  Delete $INSTDIR\LxCarsClient.exe
  Delete $INSTDIR\readme.pdf

  RMDir $INSTDIR
SectionEnd
