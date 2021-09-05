@echo off


@REM REM Comment obtenir le répertoire de fichiers
@REM for %%F in (%0) do set dirname=%%~dpF
@REM echo %dirname%

set phpfile=%~dp0\linecode.php
set phppath=php

%phppath% "%phpfile%" %*