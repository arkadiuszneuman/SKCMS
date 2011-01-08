<?php

include_once('.\..\sql.php');

$login = $_GET['login'];

if (strlen($login) < 3)
{
    $png = "x.png";
}
else
{
    $sql = new Sql();
    if ($sql->IsExistUser($login))
    {
        $png = "x.png";
    }
    else
    {
        $png = "tick.png";
    }
}

?><img alt="<?php echo $png ?>" src="javascript\<?php echo $png ?>"/><?php

?>
