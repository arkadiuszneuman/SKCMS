<?php

class Layout
{
	private $sql;
	private $style;
	public $render;

	function __construct($_style) 
	{
		$this->sql = new Sql();
		$this->style = $_style;
	}

	public function RenderHeader()
	{
		$data = array("title"=>$this->sql->GetSetting("homename"), "author"=>$this->sql->GetSetting("siteauthor"));

		if (($this->render=file_get_contents("templates/".$this->style."/header.tpl")) == FALSE) 
		{
			$this->render = "Błąd otwierania pliku z szablonem";
			return $this->render;
		}
		
		foreach($data as $t => $content)
		{ 
			$this->render=str_replace("{".$t."}", $content, $this->render); 
		} 
		
		$this->render=preg_replace('({(.*?)})', "", $this->render);

		return $this->render;
	}
	
	public function RenderFooter()
	{
		$data = array("content"=>$this->sql->GetSetting("homefooter"));

		if (($this->render=file_get_contents("templates/".$this->style."/footer.tpl")) == FALSE) 
		{
			$this->render = "Błąd otwierania pliku z szablonem";
			return $this->render;
		}
		
		foreach($data as $t => $content)
		{ 
			$this->render=str_replace("{".$t."}", $content, $this->render); 
		} 
		
		$this->render=preg_replace('({(.*?)})', "", $this->render);

		return $this->render;
	}

	public function Render($file, $data)
	{
		if (($this->render=file_get_contents("templates/".$this->style."/".$file.".tpl")) == FALSE) // umożliwić zmianę templatki
		{
			$this->render = "Błąd otwierania pliku z szablonem";
			return $this->render;
		}
		
		foreach($data as $t => $content)
		{ 
			$this->render=str_replace("{".$t."}", $content, $this->render); 
		} 
		
		$this->render=preg_replace('({(.*?)})', "", $this->render);

		return $this->render;
	}

	public function __destruct()
	{
		unset($this->render);
	}
}
?>
