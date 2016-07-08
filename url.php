<?php
namespace shgysk8zer0\Gravatar;

final class URL
{
	const GRAVATAR     = 'https://www.gravatar.com/avatar/';
	const DEFAULT_SIZE = 80;
	const MAX_SIZE     = 2048;
	const PARAMS       = '_params';

	private $_email = '';
	private $_params = array();

	public function __construct($email, $size = self::DEFAULT_SIZE, array $params = array())
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->_email = $email;
		} else {
			throw new \InvalidArgumentException("{$email} is not a valid email address");
		}

		if (filter_var($size, FILTER_VALIDATE_INT, ['options' => [
			'default'   => false,
			'min_range' => 1,
			'max_range' => self::MAX_SIZE
		]])) {
			$this->s($size);
		} else {
			throw new \InvalidArgumentException('$size is required to be an integer less than ' . self::MAX_SIZE);
		}

		array_map([$this, '__set'], array_keys($params), array_values($params));
	}

	public function __toString()
	{
		return self::GRAVATAR . $this->_getHash() . $this->_getParams();
	}

	public function __isset($param)
	{
		return array_key_exists($param, $this->{self::PARAMS});
	}

	public function __unset($param)
	{
		unset($this->{self::PARAMS}[$param]);
	}

	public function __set($param, $value)
	{
		$this->{self::PARAMS}[$param] = $value;
	}

	public function __get($param)
	{
		return $this->__isset($param) ? $this->{self::PARAMS}[$param] : null;
	}

	public function __call($param, $value)
	{
		$this->__set($param, $value[0]);
	}

	private function _getHash()
	{
		return md5(strtolower($this->_email));
	}

	private function _getParams()
	{
		return '?' . http_build_query($this->{self::PARAMS});
	}
}
