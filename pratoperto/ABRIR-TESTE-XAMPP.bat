@echo off
setlocal
title PratoPerto - teste XAMPP
for %%I in ("%~dp0..\..") do set "PRATOPERTO_XAMPP=%%~fI"
if not exist "%PRATOPERTO_XAMPP%\xampp-control.exe" (
  echo Extraia a pasta pratoperto em C:\xampp\htdocs\pratoperto
  echo Depois execute este arquivo novamente.
  echo Consulte README.md para outras pastas ou portas.
  pause
  exit /b 1
)
start "" "%PRATOPERTO_XAMPP%\xampp-control.exe"
echo Inicie Apache no painel do XAMPP.
echo O MySQL nao e necessario. Veja README.md se faltar pdo_sqlite.
start "" "http://localhost/pratoperto/"
pause
endlocal
