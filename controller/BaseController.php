<?php

namespace controller;

use core\Request;
use core\Exception\ErrorNotFoundException;

class BaseController
{
	protected $title;
	protected $content;
	protected $request;

	public function __construct(Request $request = null)
	{
		$this->title = 'Php2';
		$this->content = '';
		$this->request = $request;
	}

	public function __call($name, $params)
	{
		throw new ErrorNotFoundException();
	}

	public function render()
	{
		echo $this->build(
			__DIR__ . '/../views/main.html.php', [
			'title' => $this->title,
			'content' => $this->content
		]);
	}

	public function errorHandler($message, $trace)
	{
		$this->content = $message;
	}

	protected function redirect($uri)
	{
		header(sprintf('Location: %s', $uri));
		die();
	}

	protected function build($template, array $params = [])
	{
		ob_start();
		extract($params);
		include_once $template;

		return ob_get_clean();
	}
}