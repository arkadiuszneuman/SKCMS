<?php 

include_once("CPanel.php");

class CBlocks extends CPanel
{
	public function __construct()
    {
        parent::__construct();
    }
    
    public function Blocks()
    {
    	echo "tutaj sa bloki";
    	
    	$dir = opendir('./');
		while(false !== ($file = readdir($dir)))
		{
		  	if($file != '.' && $file != '..')
		  	{
		  		$extension = explode(".", $file);
		  		if (count($extension) == 1) //wyrzucenie folderow
		  			continue;
		  		$extension = $extension[count($extension)-1]; //zlapanie rozszerzenia
		  		
		  		if ($extension == "php")
		    		echo $file . '<br />';
		  	}
		}
    }
}

?>