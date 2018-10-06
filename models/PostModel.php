<?php

namespace models;

use core\DBDriver;
use core\Validator;

class PostModel extends BaseModel
{
	protected $schema = [
		'id' => [
			'primary' => true,
		],

		'title' => [
			'type' => 'string',
			'length' => [50, 150],
			'not_blank' => true,
			'require' => true,
		],

		'preview' => [
			'type' => 'string',
			'length' => 250
		],

		'text' => [
			'type' => 'string',
			'not_blank' => true,
			'require' => true,
		]
	];


	public function __construct(DBDriver $db, Validator $validator)
	{
		parent::__construct($db, $validator, 'posts');
		$this->validator->setRules($this->schema);
	}

	public function getPostByID($id)
	{
		$res = $this->getById($id);

		if (!$res) {
			throw new Exception("Error Processing Request", 1);
		}

		return $res;
	}
}