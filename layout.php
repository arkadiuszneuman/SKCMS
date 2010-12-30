<?php

class Layout
{
	public $render;

	public function RenderHeader($title)
	{
		$this->render = file_get_contents("templates/fancy/header.tpl"); // umożliwić zmianę templatki
		$this->render = str_replace("{title}", $title, $this->render);
		$this->render = preg_replace('({(.*?)})', "", $this->render);
		return $this->render;
	}

	public function RenderFooter($content)
	{
		$this->render = file_get_contents("templates/fancy/footer.tpl");
		$this->render = str_replace("{content}", $content, $this->render);
		$this->render = preg_replace('({(.*?)})', "", $this->render);
		return $this->render;
	}

	public function Render($file, $data)
	{
		if (($this->render=file_get_contents("templates/fancy/".$file.".tpl")) == FALSE) // umożliwić zmianę templatki
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
