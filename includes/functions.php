<?php

	function GenerateHash()
	{
		return md5(time() * rand());
	}

?>
