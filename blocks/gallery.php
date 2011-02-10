<?php
$path = 'gallery/1';

if (($dirHandle = @dir($path)) == FALSE)
{
	$content = "Błąd otwierania katalogu ze zdjęciami";
}
else
{
	while (($file = $dirHandle->read()) != FALSE)
	{
		if ($file != '.' && $file != '..')
		{
			$images[] = $file;
		}
	}
	
	$imageNumber = rand(1, count($images));
	if ($imageNumber < 10)
	{
		$content = '<a href="'.$path.'/0'.$imageNumber.'.jpg" title="Zdjęcie" rel="lightbox"><img src="'.$path.'/0'.$imageNumber.'.jpg" style="width: 95%"/></a>';
	}
	else
	{
		$content = '<a href="'.$path.'/'.$imageNumber.'.jpg" title="Zdjęcie" rel="lightbox"><img src="'.$path.'/'.$imageNumber.'.jpg" /></a>';
	}
}

return $content;

?>
