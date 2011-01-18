<?php
    include('database.php');

    class Privileges
    {
        const USER = 1; //czy uzytkownik istnieje (jesli nie to zbanowany)
        const COMMENTS = 2; //dodawanie/edycja swoich komentarzy (nie w panelu)
        const ARTICLES = 4; // dodawanie/edycja artykulow
        const BIN = 8; //usuwanie artykulow z kosza
        const MENU = 16; //dodawanie/edycja linkow w menu
        const USERS = 32; //dodawanie/edycja/banowanie uzytkownikow
        const USERCOMMENTS = 64; //dodawanie/edycja/dopuszczanie komentarzy uzytkownikow
        const ALL = 127;

        //sprawdza czy w grupie uprawnien (privileges) istnieje uprawnienie (privilege)
        static function CheckPrivilege($privilege, $privileges)
        {
            if ($privilege & $privileges)
                return true;

            return false;
        }
        
        static function PrivilegeToString($privilege)
        {
            switch ($privilege)
            {
                case 0:
                    $s = "Brak uprawnień";
                    break;
                case Privileges::USER:
                    $s = "Użytkownik";
                    break;
                case Privileges::COMMENTS:
                    $s = "Możliwość komentowania";
                    break;
                case Privileges::ARTICLES:
                    $s = "Dodawanie/edycja/wyrzucanie do kosza artykułów";
                    break;
                case Privileges::BIN:
                    $s = "Bezpowrotne usuwanie artykułów z kosza";
                    break;
                case Privileges::MENU:
                    $s = "Dodawanie/edycja/usuwanie linków w menu";
                    break;
                case Privileges::USERS:
                    $s = "Edycja użytkowników";
                    break;
                case Privileges::USERCOMMENTS:
                    $s = "Usuwanie/edycja/dopuszczanie komentarzy użytkowników";
                    break;
            }

            return $s;
        }

        static function WhatPrivileges($number)
        {
            if (Privileges::CheckPrivilege(Privileges::USER, $number))
                $array[] = Privileges::USER;
            if (Privileges::CheckPrivilege(Privileges::COMMENTS, $number))
                $array[] = Privileges::COMMENTS;
            if (Privileges::CheckPrivilege(Privileges::ARTICLES, $number))
                $array[] = Privileges::ARTICLES;
            if (Privileges::CheckPrivilege(Privileges::BIN, $number))
                $array[] = Privileges::BIN;
            if (Privileges::CheckPrivilege(Privileges::MENU, $number))
                $array[] = Privileges::MENU;
            if (Privileges::CheckPrivilege(Privileges::USERS, $number))
                $array[] = Privileges::USERS;
            if (Privileges::CheckPrivilege(Privileges::USERCOMMENTS, $number))
                $array[] = Privileges::USERCOMMENTS;

            if (@$array == null)
                $array[] = 0;
            return $array;
        }
    }

    class Sql
    {
        private $sql_conn = null;

        //zmienne okreslajace proporties w zapytaniach
        const NOTHING = 0;
        const BIN = 1;

        public function Sql($server = null, $user = null, $pass = null, $database = null) //polaczenie z wybraniem bazy
        {
            global $SQLserver; //zlapanie zmiennych globalnych z pliku database.php
            global $SQLdatabase;
            global $SQLlogin;
            global $SQLpass;
            
            if ($server == null) //jesli nie podano jakiegos parametru to jest on brany z database.php
                $server = $SQLserver;
            if ($user == null)
                $user = $SQLlogin;
            if ($database == null)
                $database = $SQLdatabase;
            if ($pass == null)
                $pass = $SQLpass;

            $this->sql_conn = mysql_connect($server, $user, $pass);
            mysql_select_db($database);
        }

        private function ProtectString($string)
        {
            if (get_magic_quotes_gpc()) //usuniecie slashow jesli magic quotes wylaczone
                $string = stripslashes($string);

            $string = mysql_real_escape_string($string); //zabezpieczenie przed sql injection
            //$string = htmlspecialchars($string);

            return $string;
        }

        private function ProtectInt($int)
        {
            if (is_array($int))
            {
                for ($i=0; $i < count($int); ++$i)
                {
                    $int[$i] = trim($int[$i]);
                    settype($int[$i], "integer"); //zabezpieczenie przed sql injection
                }
            }
            else
            {
                $int = trim($int);
                settype($int, "integer"); //zabezpieczenie przed sql injection
            }
            return $int;
        }

        public function SelectDatabase($database) //zmiana bazy danych
        {
            mysql_select_db($database);
        }
        
        public function AddArticle($title, $note, $author, $link) //dodanie articles
        {
            $title = $this->ProtectString($title);
            $note = $this->ProtectString($note);
			$author = $this->ProtectString($author);
			$link = $this->ProtectInt($link);

            $query = "INSERT INTO articles (`id`, `title`, `date`, `note`, `author`, `id_link`) VALUES
            (NULL, '".trim($title)."', ' ".date("Y-m-d H:i:s")."', '".trim($note)."', '".$author."', '".$link."');";

            return mysql_query($query);
        }

        public function NumberOfArticles($proporties, $idLink = null) //proporties - czy w koszu czy nie
        {
            $query = "SELECT COUNT(*) as howmany FROM articles WHERE proporties='$proporties'";

            if ($idLink != null)
                $query = $query." AND id_link='$idLink'";

            $row = mysql_fetch_array(mysql_query($query));

            return $row["howmany"];
        }

        //odczytanie articles, from - od ktorej danej zwracac, to - ile notek, idlink - id liknku, z ktorego zwracac artykuly
        public function ReadArticles($proporties, $from, $howMany, $idLink = null)
        {
            $from = $this->ProtectInt($from);
            $howMany = $this->ProtectInt($howMany);

            $query = "SELECT title, date, note, id_link, id, author FROM articles WHERE proporties='$proporties'";
            
            if ($idLink != null)
                $query = $query." AND id_link='$idLink'";

            $query = $query."ORDER BY date DESC LIMIT $from, $howMany"; //proporties=1 - kosz - wyswietlenie articles nie znajdujacych sie w koszu

            $reply = mysql_query($query);
            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $array[$i]['title'] = $line[0];
                $array[$i]['date'] = $line[1];
                $array[$i]['note'] = $line[2];
                $array[$i]['idLink'] = $line[3];
                $array[$i]['id'] = $line[4];
				$array[$i]['author'] = $line[5];
            }

            if (@$array == null)
                return null;
            return $array;
        }

        public function ReadArticle($id) //odczytanie pojednynczego articles
        {
            $id = $this->ProtectInt($id);

            $query = "SELECT title, note, author, id_link, date FROM articles WHERE id='$id'";

            $reply = mysql_query($query);
            if (($line = mysql_fetch_row($reply)) == 0)
				return null;

            $array['title'] = $line[0];
            $array['note'] = $line[1];
			$array['author'] = $line[2];
			$array['link'] = $line[3];
			$array['date'] = $line[4];

            return $array;
        }

        public function EditArticle($id, $title, $note, $author, $link) //zmiana konkretnego articles
        {
            $id = $this->ProtectInt($id);

            $title = $this->ProtectString($title);
            $note = $this->ProtectString($note);
			$author = $this->ProtectString($author);
			$link = $this->ProtectInt($link);

            $query = "UPDATE articles SET title='$title', note='$note', author='$author', id_link='$link' WHERE id='$id'";

            return mysql_query($query);
        }

        public function UpdateArticleLink($visibleIn, $page, $proporties, &$isChanged) //przypisanie artukulu do konkretnego linku w menu, ischanged - czy jakis zostal zmieniony
        {
            $query = "SELECT id, id_link, note FROM articles WHERE proporties='".$proporties."' ORDER BY date DESC LIMIT ".($page*20).", 20"; //musi zwracac note, inaczej zle zwraca idki, durny ten sql

            $reply = mysql_query($query);
            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $array[$i]['id'] = $line[0];
                $array[$i]['idLink'] = $line[1];
            }


            foreach ($array as $n)
            {
                if ($n['idLink'] == null)
                    $n['idLink'] = 0;

                if (@$visibleIn[$n['id']] != $n['idLink']) //zmiana tylko zmienionych linkow
                {
                    $query = "UPDATE articles SET id_link='".@$visibleIn[$n['id']]."' WHERE id='".$n['id']."'";
                    if (!mysql_query($query))
                        return false;
                    else
                        $isChanged = true;
                }
            }

            return true;
        }

        //funkcja zabezpiecza id i tworzy z tablicy id odpowiednie zapytanie (potrzebne przy usuwaniu articles)
        private function doIdQuery($id)
        {
            $id = $this->ProtectInt($id);
            $idiesString = "";

            for ($i = 0; $i < count($id); ++$i)
            {
                if ($i == count($id)-1) //jesli ostatni element w tablicy
                    $idiesString = $idiesString."id='$id[$i]'";
                else
                    $idiesString = $idiesString."id='$id[$i]' OR ";
            }

            return $idiesString;
        }

        public function RemoveArticle($id) //usuwanie articles/article jesli przekazujemy tablice
        {
            $query = "DELETE FROM articles WHERE ".$this->doIdQuery($id);
            return mysql_query($query);
        }

        //przeniesienie do kosza
        public function ArticlesToBin($id) 
        {
            $query = "UPDATE articles SET proporties='1' WHERE ".$this->doIdQuery($id);
            return mysql_query($query);
        }

        //przeniesienie spowrotem do artykulow
        public function BinToArticles($id) //usuwanie articles/article jesli przekazujemy tablice
        {
            $query = "UPDATE articles SET proporties='0' WHERE ".$this->doIdQuery($id);
            return mysql_query($query);
        }

        public function ReadLinks($whichOne = null) //whichOne  - id lub nazwa konkretnego linku
        {
            $query = "SELECT * FROM links";

            if ($whichOne != null)
            {
                if (is_int($whichOne))
                {
                    $this->ProtectInt($whichOne);
                    $query = $query." WHERE id='$whichOne'";
                }
                else if (is_string($whichOne))
                {
                    $this->ProtectString($whichOne);
                    $query = $query." WHERE link='$whichOne'";
                }
            }
            $query = $query." ORDER BY links.order";
            $reply = mysql_query($query);
            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $array[$i]['id'] = $line[0];
                $array[$i]['link'] = $line[1];
                $array[$i]['order'] = $line[2];
            }

            if (@$array == null)
                return null;
            return $array;
        }

        public function AddLink($link)
        {
            $link = $this->ProtectString($link);

            $query = "INSERT INTO links (link) VALUES ('$link')";

            return mysql_query($query);
        }

        public function EditLink($id, $link)
        {
            $link = $this->ProtectString($link);
            $id = $this->ProtectInt($id);
            $query = "UPDATE links SET link='$link' WHERE id='$id'";

            return mysql_query($query);
        }

        public function RemoveLink($id) 
        {
            $query = "DELETE FROM links WHERE ".$this->doIdQuery($id);
            return mysql_query($query);
        }

        public function SaveOrder()
        {
            $query = "SELECT id, links.order FROM links";
            $reply = mysql_query($query);
            $isOnce = false; //czy przynajmniej jadna rzecz bedzie zmieniona
            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $id = $line[0];
                $order = $line[1];
                if ($_POST[$id] != $order)
                {
                    $query = "UPDATE links SET links.order='$_POST[$id]' WHERE id='$id'";
                    $isOnce = true;
                    if (!mysql_query($query))
                        $isOnce = false;
                }
            }

            return $isOnce;
        }

        public function AddUser($login, $pass, $mail) //dodawanie uzytkownika
        {
            $login = $this->ProtectString($login);
            $pass = $this->ProtectString($pass);
            $mail = $this->ProtectString($mail);

            $query = "INSERT INTO users (login, pass, mail, privileges) VALUES
            ('".trim($login)."', '".trim($pass)."', '".trim($mail)."', '".Privileges::COMMENTS."');";

            return mysql_query($query);
        }

        public function CheckUser($login, $pass) //sprawdzanie loginu i hasla
        {
            $login = $this->ProtectString($login);

            $query = "SELECT login, pass FROM users WHERE login='$login'";
            $reply = mysql_query($query);

            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                if (trim($line[0]) === trim($login) && trim($line[1]) === trim($pass))
                    return true;
            }

            return false;
        }

        public function IsExistUser($login)
        {
            $login = $this->ProtectString($login);

            $query = "SELECT login FROM users WHERE login='$login'";
            $reply = mysql_query($query);

            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                if (trim($line[0]) === trim($login))
                    return true;
            }

            return false;
        }

        public function ReadUsers($from, $howMany)
        {
            $from = $this->ProtectInt($from);
            $howMany = $this->ProtectInt($howMany);

            $query = "SELECT id, login, privileges FROM users ORDER BY login LIMIT $from, $howMany";

            $reply = mysql_query($query);
            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $array[$i]['id'] = $line[0];
                $array[$i]['login'] = $line[1];
                $array[$i]['privileges'] = $line[2];
            }

            if (@$array == null)
                return null;
            return $array;
        }

        //zwraca ilosc uzytkownikow
        public function NumberOfUsers()
        {
            $query = "SELECT COUNT(*) as howmany FROM users";

            $row = mysql_fetch_array(mysql_query($query));

            return $row["howmany"];
        }

		public function ReturnUserName($id)
		{
			$query = "SELECT login FROM users WHERE id='$id'";
			$result = mysql_query($query);

			if(mysql_num_rows($result) == 0)
				return null;
			else
				return mysql_result($result, 0);
		}

		public function ReturnUserID($login)
		{
			$login = $this->ProtectString($login);
			$query = "SELECT id FROM users WHERE login='$login'";
			$result = mysql_query($query);

			if (mysql_num_rows($result) == 0)
				return null;
			else
				return mysql_result($result, 0);
		}

        public function CheckPrivileges($login)
        {
            $login = $this->ProtectString($login);

            $query = "SELECT privileges FROM users WHERE login='$login'";
            $reply = mysql_query($query);

            $line = mysql_fetch_row($reply);

            return $line[0];
        }

        public function SavePrivileges($id, $privileges)
        {
            $id = $this->ProtectInt($id);

            $query = "UPDATE users SET privileges='$privileges' WHERE id='$id'";
            
            return mysql_query($query);
        }

        public function SavePreferences($howMany)
        {
            //$userID = $this->ProtectInt($userID);
            $userName = $this->ProtectString($_SESSION['name']);
            $howMany = $this->ProtectInt($howMany);

            $query = "SELECT id FROM users WHERE login='$userName'"; //pobranie IDka usera
            $reply = mysql_query($query);
            $line = mysql_fetch_row($reply);

            $query = "SELECT id_user FROM preferences WHERE id_user='$line[0]'";
            $reply = mysql_query($query);
            if (mysql_fetch_row($reply)) //sprawdzenie czy rekord istnieje
                $query = "UPDATE preferences SET howMany='$howMany'";
            else
                $query = "INSERT INTO preferences (id_user, howMany) VALUES ('$line[0]', '$howMany')";

            return mysql_query($query);
        }

        //zwrocenie ustawien
        public function LoadPreferences()
        {
            $userName = $this->ProtectString($_SESSION['name']);
            $query = "SELECT howMany FROM preferences WHERE id_user=(SELECT id FROM users WHERE login='$userName')";

            $reply = mysql_query($query);
            $line = mysql_fetch_row($reply);
            $array['howMany'] = $line[0];

            if (@$array == null)
                return null;
            return $array;
        }

		public function ReadComments($id)
		{
            $query = "SELECT user_id, name, note, date, id FROM comments WHERE article_id = '$id' ORDER BY date";
			$reply = mysql_query($query);
			
			for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
			{
				if ($line[0] != 0)
				{
					$array[$i]['user'] = $this->ReturnUserName($line[0]);
				}
				else
				{
					$array[$i]['user'] = $line[1];
				}
				$array[$i]['note'] = $line[2];
				$array[$i]['date'] = $line[3];
				$array[$i]['id'] = $line[4];
			}

			if ($array == null)
				return null;

			return $array;
		}

		public function AddComment($article_id, $author = null, $user_id = null,  $note)
		{
            $article_id = $this->ProtectInt($article_id);
            $note = $this->ProtectString($note);
			if ($author != null)
				$author = $this->ProtectString($author);
			if ($user_id != null)
				$user_id = $this->ProtectInt($user_id);

            $query = "INSERT INTO comments (`article_id`, `user_id`, `name`, `note`, `date`) VALUES
            ('".$article_id."', '".$user_id."', '".trim($author)."', '".trim($note)."', '".date("Y-m-d H:i:s")."');";

            return mysql_query($query);
		}

        public function Close()
        {
            mysql_close($this->sql_conn);
        }
    }
?>
