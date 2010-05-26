<?php

class Gwilym_UriParser_Fixed extends Gwilym_UriParser
{
	protected $_base;
	protected $_docroot;
	protected $_uri;

	public function __construct ($base, $uri = null, $docroot = null)
	{
		$this->_base = $base;

		if ($uri === null)
		{
			$this->_uri = substr($_SERVER['REQUEST_URI'], strlen($this->_base));
		}
		else
		{
			$this->_uri = $uri;
		}

		$this->_docroot = $docroot;
	}

	public function base ()
	{
		return $this->_base;
	}

	public function docroot ()
	{
		return $this->_docroot;
	}

	public function uri ()
	{
		return $this->_uri;
	}
}
