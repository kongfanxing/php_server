<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\protocol;
use lib\main\ProtocolConst;

/**
 * 协议处理的基类
 * 
 */
abstract class Protocol 
{	
	/**
	 * 每次的请求
	 * @var string
	 */
	protected $request;
	
	/**
	 * header
	 * @var string
	 */
	protected $header;
	
	/**
	 * body
	 * @var string
	 */
	protected $content;
	
	/**
	 * 完整的请求
	 * @var string
	 */
	protected $real_request;
	
	/**
	 * 文件描述符
	 * @var int
	 */
	protected $fd;
	
	/**
	 * 存储接受的数据缓存
	 * @var array
	 */
	protected $buffer = array();
	
	/**
	 * 上次数据接收的状态
	 * @var array
	 */
	protected $protocol_status = array();
	
	public function __construct($fd)
	{
		$this->fd = $fd;
	}
	
	/**
	 * 设置文件描述符
	 * @param int $fd
	 */
	public function setFileDescript($fd)
	{
		$this->fd = $fd;
	}
	
	/**
	 * 获取请求,如果是一个完整的请求则返回，否则返回一个状态码
	 * @var string
	 */
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
	
	/**
	 * 对响应数据的处理
	 * @param string $data
	 * @return string
	 */
	public function getResponse($data)
	{		
		return $data;
	}
	
	/**
	 * 检验header
	 * @return Boolean
	 */
	abstract function checkHeader();
	
	/**
	 * 检验body
	 * @return Boolean|int
	 */
	abstract function checkContent();
}

?>