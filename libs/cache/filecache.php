<?php

if (defined('LIBS_FILECACHE_PHP'))
{
	return;
}
else
{
	define('LIBS_FILECACHE_PHP', 1);
}

include_once 'cache.php';

class FileCache extends Cache
{
	protected $content = array();
	protected $filename;
	
	public function __construct()
	{
		parent::__construct();
		$content = array();
	}
	
	public function __destruct()
	{
		$this->flush();
	}
	

	public function init()
	{
		$filename = $this->dsn['filename'];
		if(!$filename)
		{
			ethrow("invalid cache filename");
		}
		
		touch($filename);
		$fp= fopen($filename, 'r');
		if(!$fp)
		{
			ethrow("create cache file {$filename} failed");
		}
		
		$this->filename = $filename;
		
		$size = filesize($filename);
		
		if($size > 0)
		{
			$data = fread($fp, $size);
			$this->content = @unserialize($data);
		}
		else
		{
			$this->content = array();
		}

		fclose($fp);
	}
	

	public function get($key, $id)
	{
		if(!$this->content)
            $this->init();
	    if(!isset($this->content[$key])
		   || !$this->content[$key])
		{
			return false;
		}
		
		$time = TIMESTAMP;
		if($this->content[$key]['expires'] > 0
		   && $this->content[$key]['expires'] <= $time)
		{
			$this->delete($key);
			return false;
		}
		
		return $this->content[$key]['data'];
	}
	

	public function set($key, $data, $lifeTime = 0)
	{
		$time = TIMESTAMP;
		if($lifeTime > 0)
		{
			$this->content[$key]['expires'] = $time + $lifeTime;
		}
		else
		{
			$this->content[$key]['expires'] = 0;
		}
		
		$this->content[$key]['data'] = $data;
		$this->flush();
	}
	

	public function delete($key)
	{
		if(isset($this->content[$key]))
		{
			unset($this->content[$key]);
		}
	}


	public function flush()
	{
		$this->gc();
		$data = serialize($this->content);
		
		$fp = fopen($this->filename, 'w');
		if(!$fp)
		{
			return;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);	
	}
	

	public function gc()
	{
		$time = TIMESTAMP;
		foreach($this->content as $key=>$value)
		{
			if($value['expires'] > 0 &&
			   $time >= $value['expires'])
			{
				$this->delete($key);
			}
		}
	}
	
}



// end of script