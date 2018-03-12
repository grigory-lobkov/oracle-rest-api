<?php
	/*
		Dependencies between objects

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/

	require_once('Config.php');					// load configuration (public static class Config)
	require_once('ValValidator.php');			// user values validator
	require_once('RequestParser.php');			// parser of user requests to database
	try {
		require_once('Model/' . Config::$protocol . 'View.php');						// user-output class
		require_once('Model/' . Config::$protocol . Config::$auth . 'Auth.php');		// authorization class
	} catch (Exception $e) {
		die('Protocol ' . Config::$protocol . ' vs ' . Config::$auth . ' auth is not supported yet. ' . $e->getMessage());
	}
	try {
		require_once('Model/' . Config::$dbType . 'DB.php');			// database connection, configure
		require_once('Model/' . Config::$dbType . 'Storage.php');		// database storage requests
	} catch (Exception $e) {
		die('Database ' . Config::$dbType . ' is not supported yet. ' . $e->getMessage());
	}

	return [
		'View' => DI\Object(Config::$protocol . 'View'),
		'Validator' => DI\Object('ValValidator'),
		'Authorize' => DI\Object(Config::$protocol . Config::$auth . 'Auth'),
		'DataBase' => DI\Object(Config::$dbType . 'DB')
			->constructor(Config::$dbServerAddress, Config::$dbServerPort,
				Config::$dbName, Config::$dbEncoding, Config::$dbConnectionType),
		'DataStorage' => DI\Object(Config::$dbType . 'Storage'),
		'Parser' => DI\Object('RequestParser'),
	];

?>