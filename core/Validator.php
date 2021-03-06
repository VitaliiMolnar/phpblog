<?php

namespace core;

use core\Exception\ValidatorException;

class Validator
{
	public $clean = [];
	public $errors = [];
	public $success = false;
	private $rules;

	public function execute(array $fields)
	{
		if (!$this->rules) {
			throw new ValidatorException('Rules for validation not found');
		}

		foreach ($this->rules as $name => $rules) {
			if (!isset($fields[$name]) && isset($rules['require'])) {
				$this->errors[$name][] = sprintf('Field "%s" is require', $name);
			}

			if (!isset($fields[$name]) && !isset($rules['require']) || !$rules['require']) {
				continue;
			}

			if (isset($rules['not_blank']) && $this->isBlank($fields[$name])) {
				$this->errors[$name][] = sprintf('Field "%s" is not be a blank', $name);
			}

			if (isset($rules['type']) && !$this->isTypeMatching($fields[$name], $rules['type'])) {
				$this->errors[$name][] = sprintf('Field "%s" is not be a %s type', $name, $rules['type']);
			}
			
			if (isset($rules['length']) && !$this->isLengthMatch($fields[$name], $rules['length'])) {
				$this->errors[$name][] = sprintf('Field "%s" has an incorrect length.', $name);
			}		

			if(empty($this->errors[$name])) {
				if (isset($rules['type']) && $rules['type'] === 'string') {
					$this->clean[$name] = htmlspecialchars(trim($fields[$name]));
				} elseif (isset($rules['type']) && $rules['type'] === 'integer') {
					$this->clean[$name] = (int)$fields[$name]; 
				} else {
					$this->clean[$name] = $fields[$name];
				}
			}
		}

		if (empty($this->errors)) {
			$this->success = true;
		}
	}

	public function setRules(array $rules)
	{
		$this->rules = $rules;
	}

	public function isLengthMatch($field, $length)
	{
		if ($isArray = is_array($length)) {
			$max = isset($length[1]) ? $length[1] : false;
			$min = isset($length[0]) ? $length[0] : false;
		} else {
			$max = $length;
			$min = false;
		}

		if ($isArray && (!$max || !$min)) {
			throw new ValidatorException('Incorrect data gvaen to method isLenghtMatch');
		}

		if (!$isArray && !$max) {
			throw new ValidatorException('Incorrect data gvaen to method isLenghtMatch');
		}

		$maxIsMatch = $max ? $this->isLengthMaxMatch($field, $max) : false;
		$minIsMatch = $min ? $this->isLengthMinMatch($field, $min) : false;

		return $isArray ? $maxIsMatch && $minIsMatch : $maxIsMatch;
	}

	public function isLengthMaxMatch($field, $length)
	{
		return mb_strlen($field) > $length === false; 
	}

	public function isLengthMinMatch($field, $length)
	{
		return mb_strlen($field) < $length === false;
	}

	public function isTypeMatching($field, $type)
	{
		switch ($type) {
			case 'string':
				return is_string($field);
				break;
			case 'int':
			case 'integer':
				return gettype($field) === 'integer' || ctype_digit($field);
				break;
			default:
				throw new ValidatorException('Incorrect data gvaen to method isTypeMatch');
				break;
		}
	}

	public function isBlank($filed)
	{
		$field = trim($filed);

		return $field === null || $filed === '';
	}
}