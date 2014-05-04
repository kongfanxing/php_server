<?php
class MediaRetrieveProcesser extends \lib\server\servers\thrift\ServiceProcesser
{
	function execute()
	{
		$ret = new \MediaRetrieveResult();
		
		$ret->retCode = 200;
		$ret->retMsg = "OK, query is:'".$this->service_args->rs->fsql."'";
		$ret->total = 100;
		$ret->usedTime = 11;
		$ret->mediaIdList = array(123, 456, 789);
		
		return $ret;
	}
}