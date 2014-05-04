<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\protocol;
use lib\main\ProtocolConst;

abstract class Protocol 
{	
	protected $request;
	protected $header;
	protected $content;
	
	protected $real_request;
	protected $fd;
	protected $buffer = array();
	
	public function __construct($fd)
	{
		$this->fd = $fd;
	}
	
	public function setFileDescript($fd)
	{
		$this->fd = $fd;
	}
	
	public function getRequest($data)
	{
		$this->request = $data;
		
		if (!$this->checkHeader())
			return  ProtocolConst::STATUS_WAIT;
		
		$content_check = $this->checkContent();
		
		if ($content_check === ProtocolConst::STATUS_ERR || $content_check === ProtocolConst::STATUS_WAIT)
		{
			return $content_check;
		}  

		return $this->real_request;
	}
	
	public function getResponse($data)
	{		
		return $data;
	}
	
	abstract function checkHeader();
	
	abstract function checkContent();
}

?>