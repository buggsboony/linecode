@echo off

echo append directory to PATH ENV variable
REM echo %~dp0   #With Trailing Slash (backslash)
REM Without Traling slash
SET currentPath=%cd%
echo %currentPath%
systemPropertiesAdvanced
pause