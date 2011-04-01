#!/usr/bin/php
<?php
/*
 * Modified from Nooku, removed need for command line console
 * Dependencies:
 *
 */

$source = $_SERVER['argv'][1];
$target = $_SERVER['argv'][2];

// Make symlinks
if(file_exists($source))
{
	$it = new KSymlinker($source, $target);
	while($it->valid()) {
		$it->next();
	}
}

class KSymlinker extends RecursiveIteratorIterator
{
	protected $_srcdir;
	protected $_tgtdir;

	public function __construct($srcdir, $tgtdir)
	{
		$this->_srcdir = $srcdir;
		$this->_tgtdir = $tgtdir;

		parent::__construct(new RecursiveDirectoryIterator($this->_srcdir));
	}

	public function callHasChildren()
	{
		$filename = $this->getFilename();
		if($filename[0] == '.') {
			return false;
		}

		$src = $this->key();

		$tgt = str_replace($this->_srcdir, '', $src);
		$tgt = str_replace('/site', '', $tgt);
  		$tgt = $this->_tgtdir.$tgt;

  		if(is_link($tgt)) {
        	unlink($tgt);
        }

  		if(!is_dir($tgt)) {
  			$this->createLink($src, $tgt);
  			return false;
  	  	}

		return parent::callHasChildren();
	}

	public function createLink($src, $tgt)
	{
        if(!file_exists($tgt))
		{
          	exec("ln -sf $src $tgt");
			echo $src.PHP_EOL."\t--> $tgt".PHP_EOL;
		}
	}
}
