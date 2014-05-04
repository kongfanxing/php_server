<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\protocol;
use lib\main\ProtocolConst;

class TFrameProtocol extends Protocol
{
	const FRAME_FINISH = 1;
	const FRAME_UNFINISH = 0;
	
	//上次数据接收的状态
	private $protocol_status = array();
	//上次差的数据长度
	private $length_lack = array();
	
	function __construct($fd)
	{
		parent::__construct($fd);
	}
	
	function bufferInit()
	{
		if (!isset($this->buffer[$this->fd]))
			$this->buffer[$this->fd] = '';
		
		if (!isset($this->protocol_status[$this->fd]))
			$this->protocol_status[$this->fd] = self::FRAME_FINISH;
		
		if (!isset($this->length_lack[$this->fd]))
			$this->length_lack[$this->fd] = 0;
	}
	
	function checkHeader()
	{
		$this->bufferInit();

		$this->buffer[$this->fd] .= $this->request;
		unset($this->request);
		
		//如果不是完成状态说明上一次frame没有接受完成
		if ($this->isFrameFinished())
		{
			if (strlen($this->buffer[$this->fd]) < 4)
			{
				$this->setFrameUnFinished();
				return false;
			}
		}
		return true;	
	}
	
	function checkContent()
	{
		$this->header = substr($this->getBuffer(), 0, 4); 
		$arr = unpack ( 'N', $this->header);
		$content_length = $arr[1];
		if ($content_length > 0x7fffffff) $content_length = 0 - (($content_length - 1) ^ 0xffffffff);
		
		$length_need = $content_length+4;
		$length_buffer = strlen($this->getBuffer());

		if ($length_buffer < $length_need)			//本次接收的content的长度不够
		{
			$this->setFrameUnFinished();
			return ProtocolConst::STATUS_WAIT;
		}
		elseif ($length_buffer == $length_need)
		{
			$this->real_request = $this->getBuffer();
			$this->setFrameFinished();
			$this->clearBuffer();
		}
		else 
		{
			$this->real_request = substr($this->getBuffer(), 0, $length_need);
			$this->buffer[$this->fd] = substr($this->getBuffer(), $length_need);
			$this->setFrameUnFinished();
		}
		
		return true;
	}
	
	private function isFrameFinished()
	{
		return $this->protocol_status[$this->fd] === self::FRAME_FINISH;
	}
	
	private function setFrameUnFinished()
	{
		$this->protocol_status[$this->fd] = self::FRAME_UNFINISH;
	}
	
	private function setFrameFinished()
	{
		$this->protocol_status[$this->fd] = self::FRAME_FINISH;
	}
	
	private function getBuffer()
	{
		return $this->buffer[$this->fd];
	}
	
	private function clearBuffer()
	{
		$this->buffer[$this->fd] = '';
	}
}



