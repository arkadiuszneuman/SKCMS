<?php
function GenerateHash()
{
	return md5(time() * rand());
}

function showPaging($page, $howMany, $count)
{
	$toReturn = "";
	@$link = $_GET['link'];

	if ($page > 0) //wyswietlenie poprzednia strona
	{
		if ($link != null)
			$toReturn = $toReturn."<a href=\"./?link=".$link."&page=0\"><<</a>
				<a href=\"./index.php?link=".$link."&page=".($page-1)."\"><</a> ";
		else
			$toReturn = $toReturn."<a href=\"./?page=0\"><<</a>
				<a href=\"./?page=".($page-1)."\"><</a> ";

	}
	else
	{
		$toReturn = $toReturn."<< < ";
	}

	for ($i = $page-2; $i < $page+5; ++$i) //wyswietlenie numerow stron
	{
		if ($i > 0 && ($i-1)*$howMany < $count) //numery stron tylko w zakresie min max
		{
			if ($i != $page + 1) //link nie moze byc aktualna strona
			{
				if ($link != null)
					$toReturn = $toReturn."<a href=\"./?link=".$link."&page=".($i-1)."\">".$i."</a> ";
				else
					$toReturn = $toReturn."<a href=\"./?page=".($i-1)."\">".$i."</a> ";
			}
			else
			{
				$toReturn = $toReturn."".$i." ";
			}
		}
	}

	if (($page+1)*$howMany < $count) //wyswietlenie nastepna strona i ostatnia strona
	{
		if ($link != null)
			$toReturn = $toReturn."<a href=\"./?link=".$link."&page=".($page+1)."\">></a> ";
		else
			$toReturn = $toReturn."<a href=\"./?page=".($page+1)."\">></a> ";

		if ($count%$howMany != 0) //jesli ilosc newsow przez ilosc newsow na strone jest nierowna
		{
			if ($link != null)
				$toReturn = $toReturn."<a href=\"./?link=".$link."&page=".((int)($count/$howMany))."\">>></a>";
			else
				$toReturn = $toReturn."<a href=\"./?page=".((int)($count/$howMany))."\">>></a>";
		}
		else
		{
			if ($link != null)
				$toReturn = $toReturn."<a href=\"./?link=".$link."&page=".(($count/$howMany) - 1)."\">>></a>";
			else
				$toReturn = $toReturn."<a href=\"./?page=".(($count/$howMany) - 1)."\">>></a>";
		}
	}
	else
	{
		$toReturn = $toReturn."> >>";
	}

	return $data = array("content"=>$toReturn);
}
?>
