@echo off
chcp 65001 >nul
setlocal
set "dreamlastXampp=%~dp0..\.."
if not exist "%dreamlastXampp%\xampp-control.exe" goto wrongfolder
if not exist "%dreamlastXampp%\php\php.exe" goto wrongfolder
"%dreamlastXampp%\php\php.exe" -r "exit(version_compare(PHP_VERSION,'8.1.0','>=') && extension_loaded('pdo_mysql') && extension_loaded('mbstring') ? 0 : 1);"
if errorlevel 1 goto missingphp
start "" "%dreamlastXampp%\xampp-control.exe"
echo.
echo TESTE LOCAL - DREAM LAST
echo.
echo No painel XAMPP, clique em Start ao lado de Apache.
echo Para testar PDV, ERP e CRM, inicie tambem MySQL
echo e instale os bancos conforme o README.md.
echo.
echo Este atalho considera Apache na porta padrao 80.
echo Pressione uma tecla quando o Apache estiver iniciado.
pause >nul
start "" "http://localhost/dreamlast_portfolio_completo/"
exit /b 0

:wrongfolder
echo.
echo Primeiro copie a pasta dreamlast_portfolio_completo
echo para a pasta htdocs do seu XAMPP.
echo Exemplo: C:\xampp\htdocs\dreamlast_portfolio_completo
echo Depois execute este arquivo novamente.
pause
exit /b 1

:missingphp
echo.
echo O teste das demos requer PHP 8.1 ou superior,
echo com pdo_mysql e mbstring habilitados no php.ini.
echo Verifique a instalacao PHP do seu XAMPP.
pause
exit /b 1
