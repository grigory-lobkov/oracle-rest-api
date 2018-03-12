@echo off
rem
rem Export source of object from oracle, powershell used for right trim
rem
rem 22.02.2018 Copyright (c) 2018 Grigory Lobkov
rem THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
rem

setlocal
rem sqlpluscmd=C:\oracle\ora11\BIN\sqlplus.exe
set sqlpluscmd=sqlplus.exe
set tmpfile=%temp%\query.sql
rem chcp 65001
rem set NLS_LANG=.AL32UTF8


set pkg=API
set type=PACKAGE
set outfile=API.PKS
call :export

set pkg=API
set type=PACKAGE BODY
set outfile=API.PKB
call :export

echo:
echo:
echo Export completed!
pause
exit /b %errorlevel%


:export
(
echo SET HEAD OFF
echo SET FEED OFF
echo SET TERM OFF
echo SET ECHO OFF
echo SET LINE 999
echo SET NEWPAGE NONE
echo SET PAGESIZE 0
echo SPOOL %outfile%
echo PROMPT CREATE OR REPLACE
echo select text from user_source
echo where name='%pkg%' and type='%type%';
rem echo select dbms_metadata.get_ddl('PACKAGE','API',USER) from dual; >> %tmpfile%
echo SPOOL OFF;
echo /
echo EXIT;
) > %tmpfile%
%sqlpluscmd% W_SYS/h4b0kk5cd7@man @%tmpfile%
rem echo TmpFile:
rem type %tmpfile%
del %tmpfile%
rem echo outfile1:
rem type %outfile%
powershell -command "$l=(Get-Content %outfile%) | foreach{ $_.TrimEnd()}; write-output $l | out-file "%outfile%" -encoding utf8"
echo />> %outfile%
rem echo outfile2:
rem type %outfile%
exit /b 0
