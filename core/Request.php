<?php

namespace core;

class Request
{
	const METOD_POST = 'POST';
	const METOD_GET = 'GET';

	private $get;
	private $post;
	private $server;
	private $cookie;
	private $files;
	private $session;

	public function __construct($get, $post, $server, $cookie, $files, $session)
	{
		$this->get = $get;
		$this->post = $post;
		$this->server = $server;
		$this->cookie = $cookie;
		$this->files = $files;
		$this->session = $session;
	}

	public function get($key = null)
	{
		return $this->getArr($this->get, $key);
	}

	public function post($key = null)
	{
		return $this->getArr($this->post, $key);
	}

	public function cookie($key = null)
	{
		return $this->getArr($this->cookie, $key);
	}

	public function session($key = null)
	{
		return $this->getArr($this->session, $key);
	}

	private function getArr(array $arr, $key = null)
	{
		if(!$key) {
			return $arr;
		}

		if(isset($arr[$key])) {
			return $arr[$key];
		}

		return null;
	}

	public function isPost()
	{
		return $this->server['REQUEST_METHOD'] === self::METOD_POST;
	}

	public function isGet()
	{
		return $this->server['REQUEST_METHOD'] === self::METOD_GET;
	}
}