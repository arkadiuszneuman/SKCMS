<?php

class CUsers extends CPanel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Users()
    {
		if (Privileges::CheckPrivilege(Privileges::USERS, $this->privileges))
		{
			?><script type="text/javascript" src="./javascript/privileges.js"></script><?php
				@$id = $_GET['id'];
			if ($id == null)
				$id = 0;

			@$page = $_GET['page'];
			if ($page == null)
				$page = 0;

			?><div id="windowUser"></div><?php

				if (isset($_POST['save']))
				{
					@$priv = $_POST['priv']; //zlapanie checkboksow z uprawnieniami
					@$login = $_GET['login'];
					if (!$priv)
						$priv[] = 0;

					$privileges = 0;
					foreach($priv as $p)
						$privileges |= $p;

					if ($this->sql->SavePrivileges($id, $privileges))
						$this->SendInfo("Uprawnienia użytkownika <b>$login</b> zostały zmienione");
					else
						$this->SendInfo("Nie można zmienić uprawnień użytkownika $login");
				}

			$this->DrawInfo();

			$links = $this->sql->ReadUsers($page*$this->howMany, $this->howMany);
			$this->DrawTable($links, CPanel::USERS);
			$this->DrawPaging($this->sql->NumberOfUsers());
		}
		else
		{
			echo "Nie możesz tu być!";
		}
    }
}

?>
