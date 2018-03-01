@echo off
rem Example queries to show API-server in work
rem 01.03.2018 Grigory Lobkov
rem
rem THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
rem
rem To get worked, download curl from here https://curl.haxx.se/download.html and unpack to the same folder, where this CMD is.
rem

setlocal
set out=EXAMPLES.md
del %out%

set params="http://w_action1:r1gn5@192.168.11.31/action/26477680"
call :makecurl
set params="http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&fields=ID,TYPE"
call :makecurl
set params=--data "{DEV_ID:24,WEIGHT:0,TYPE:1}" http://w_action1:r1gn5@192.168.11.31/action
call :makecurl
set params="http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&limit=1&offset=2"
call :makecurl
set params="http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&order=ID%%20desc&fields=ID"
call :makecurl

pause
exit /b %errorlevel%

:makecurl
echo|set /p="### " >> %out%
echo curl %params% >> %out%
echo ```>> %out%
curl %params% >> %out%
echo:>> %out%
echo ```>> %out%
echo:>> %out%
exit /b 0