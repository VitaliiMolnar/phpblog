<?php

use core\DBConnector;
use core\Templater;
use models\UserModel;
use models\PostModel;

function __autoload($classname)
{
	include_once __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php';
}


session_start();

$uri = $_SERVER['REQUEST_URI'];

$uriParts = explode('/',  $uri);

unset($uriParts[0]);
unset($uriParts[1]);
unset($uriParts[2]);
unset($uriParts[3]);
$uriParts = array_values($uriParts);

$controller = isset($uriParts[0]) && $uriParts[0] !== '' ? $uriParts[0] : 'post';

try{
switch ($controller) {
	case 'post':
		$controller = 'Post';
		break;
		case 'user':
		$controller = 'User';
		break;
	
	default:
		throw new core\Exception\ErrorNotFoundException();
		break;
}

$id = false;

if(isset($uriParts[1]) && is_numeric($uriParts[1])) {
	$id = $uriParts[1];
	$uriParts[1] = 'one';
}

$action = isset($uriParts[1]) && $uriParts[1] !== '' && is_string($uriParts[1]) ? $uriParts[1] : 'index';
$actionParts = explode('-', $action);
for ($i=1; $i < count($actionParts); $i++) {
	if (!isset($actionParts[$i])) {
		continue;
	}

	$actionParts[$i] = ucfirst($actionParts[$i]);
}

$action = implode('', $actionParts);

$action = sprintf('%sAction', $action);
if(!$id) {
	$id = isset($uriPost[2]) && is_numeric($uriParts[2]) ? $uriParts[2] : false;
}

if($id) {
	$_GET['id'] = $id;
}

$request = new core\Request($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES, $_SESSION);

$controller = sprintf('controller\%sController', $controller);
$controller = new $controller($request);
$controller->$action();

} catch (\Exception $e) {
	$controller = sprintf('controller\%sController', 'Base');
	$controller = new $controller($request);
	$controller->errorHendler($e->getMessage(), $e->getTraceAsString());
}

$controller->render();