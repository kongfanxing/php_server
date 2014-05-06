<?php
namespace lib\server\servers\thrift;

class ThriftTransport extends \Thrift\Transport\TTransport
{
	public $inBuf;
	public function __construct()
	{
		$this->inBuf = new \Thrift\Transport\TMemoryBuffer();
	}

	public function isOpen()
	{
		return true;
	}
	
	/**
	 * Open the transport for reading/writing
	 *
	 * @throws TTransportException if cannot open
	 */
	public function open()
	{
		
	}
	
	/**
	 * Close the transport.
	 */
	public function close(){}
	
	/**
	 * Read some data into the array.
	 *
	 * @param int    $len How much to read
	 * @return string The data that has been read
	 * @throws TTransportException if cannot read any more data
	 */
	public function read($len)
	{
		return $this->inBuf->read($len);
	}
	
	
	
	/**
	 * Writes the given data out.
	 *
	 * @param string $buf  The data to write
	 * @throws TTransportException if writing fails
	 */
	public function write($buf)
	{
		return $this->inBuf->write($buf);
	}
	
	/**
	 * Flushes any pending data out of a buffer
	 *
	 * @throws TTransportException if a writing error occurs
	 */
	public function flush() 
	{
		$this->inBuf->flush();
	}
	
	public function getBuffer()
	{
		return $this->inBuf;
	}
}

?>