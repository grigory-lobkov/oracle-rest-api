<?php
	/*
		Prepare dependency injector

		Useful links for understanding PHP-DI
			https://www.webcodegeeks.com/php/php-dependency-injection-tutorial/#section_6
			https://stackoverflow.com/questions/30616399/php-di-annotation-not-working
			http://php-di.org/doc/annotations.html

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/

	require('vendor/autoload.php');			// prepare PHP-DI

	// Creating global object, capable creating any object, linked in Dependencies.php
	// Example of use inside classes:
	//		global $classes;
	//		$auth = $classes->get('Authorize');		// gets Authorize-linked class
	$builder = new DI\ContainerBuilder();
	$builder->useAnnotations(true);
	$builder->addDefinitions('Dependencies.php');
	$classes = $builder->build();

?>