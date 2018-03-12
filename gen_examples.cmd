@echo off
rem
rem Example queries to show API-server in work
rem To get worked, download curl from here https://curl.haxx.se/download.html and unpack to the same folder, where this CMD is.
rem
rem 01.03.2018 Copyright (c) 2018 Grigory Lobkov
rem THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
rem

setlocal
set out=EXAMPLES.md
del %out%

echo ## Deleting >> %out%
call:curl -X DELETE http://w_all:ghjlern@192.168.11.31/action?DEV_ID=24
echo # Inserting >> %out%
call:curl --data "{DEV_ID:24,TYPE:1}" http://w_action1:r1gn5@192.168.11.31/action/
call:curl --data "{data:{DEV_ID:24,TYPE:2}}" http://w_action1:r1gn5@192.168.11.31/action
call:curl --data "{data:[{DEV_ID:24,TYPE:3},{DEV_ID:24,TYPE:3}]}" http://w_action1:r1gn5@192.168.11.31/action
call:curl --data "{names:[DEV_ID,"TYPE"],data:[[24,4],[24,5],[24,6]]}" http://w_action1:r1gn5@192.168.11.31/action
call:curl --data "{data:[]}" http://w_action1:r1gn5@192.168.11.31/action
call:curl --data "{data:{}}" http://w_action1:r1gn5@192.168.11.31/action
call:curl --data "{}" http://w_action1:r1gn5@192.168.11.31/action
echo # Selecting >> %out%
call:curl http://w_action1:r1gn5@192.168.11.31/action/20000
call:curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&fields=ID,TYPE"
call:curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&limit=1&offset=2"
call:curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&order=ID%%%%20desc&fields=ID"
echo ## Updating >> %out%
call:curl -X PUT --data "{TYPE:1}" "http://w_all:ghjlern@192.168.11.31/action?DEV_ID=24&TYPE=3"
echo ## Set field >> %out%
call:curl -X PATH --data "1200" "http://w_all:ghjlern@192.168.11.31/action?DEV_ID=24&TYPE=2&fields=WEIGHT"
echo ## Get field >> %out%
call:curl -X PATH "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&TYPE=2&fields=WEIGHT"
echo ## Result >> %out%
call:curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&fields=ID,TYPE"


pause
exit /b %errorlevel%

:curl
echo ```>> %out%
echo # curl %*>> %out%
curl %*>> %out%
echo:>> %out%
echo ```>> %out%
echo:>> %out%
exit /b 0