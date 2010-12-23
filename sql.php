<?php
    include('database.php');

    class Sql
    {
        private $sql_conn = null;

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
        
        public function AddNews($title, $note) //dodanie newsa
        {
            $title = $this->ProtectString($title);
            $note = $this->ProtectString($note);

            $query = "INSERT INTO `news` (`id`, `title`, `date`, `note`) VALUES
            (NULL, '".trim($title)."', ' ".date("Y-m-d H:i:s")."', '".trim($note)."');";

            return mysql_query($query);
        }

        public function NumberOfNews()
        {
            $row = mysql_fetch_array(mysql_query("SELECT COUNT(*) as howmany FROM news"));
            return $row["howmany"];
        }

        public function ReadNews($isId, $from, $howMany) //odczytanie newsow, isId - czy zwracac tez id; from - od ktorej danej zwracac, to - ile notek
        {
            $from = $this->ProtectInt($from);
            $howMany = $this->ProtectInt($howMany);

            $query = "SELECT title, date, note, id_link";

            if ($isId)
                $query = $query.", id";

            $query = $query." FROM news WHERE NOT proporties='1' ORDER BY date DESC LIMIT $from, $howMany"; //proporties=1 - kosz - wyswietlenie newsow nie znajdujacych sie w koszu

            $reply = mysql_query($query);
            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $array[$i]['title'] = $line[0];
                $array[$i]['date'] = $line[1];
                $array[$i]['note'] = $line[2];
                $array[$i]['idLink'] = $line[3];
                if ($isId)
                    $array[$i]['id'] = $line[4];
            }

            if (@$array == null)
                return null;
            return $array;
        }

        public function ReadNewsFromBin($isId, $from, $howMany) //odczytanie newsow, isId - czy zwracac tez id; from - od ktorej danej zwracac, to - ile notek
        {
            $from = $this->ProtectInt($from);
            $howMany = $this->ProtectInt($howMany);

            $query = "SELECT title, date, note";

            if ($isId)
                $query = $query.", id";

            $query = $query." FROM news WHERE proporties='1' ORDER BY date DESC LIMIT $from, $howMany"; //proporties=1 - kosz - wyswietlenie newsow nie znajdujacych sie w koszu

            $reply = mysql_query($query);
            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $array[$i]['title'] = $line[0];
                $array[$i]['date'] = $line[1];
                $array[$i]['note'] = $line[2];
                if ($isId)
                    $array[$i]['id'] = $line[3];
            }
            if (@$array == null)
                return null;
            return $array;
        }

        public function ReadSelectedNews($id) //odczytanie pojednynczego newsa
        {
            $id = $this->ProtectInt($id);

            $query = "SELECT title, note FROM news WHERE id='$id'";

            $reply = mysql_query($query);
            $line = mysql_fetch_row($reply);

            $array['title'] = $line[0];
            $array['note'] = $line[1];

            return $array;
        }

        public function EditNews($id, $title, $note) //zmiana konkretnego newsa
        {
            $id = $this->ProtectInt($id);

            $title = $this->ProtectString($title);
            $note = $this->ProtectString($note);

            $query = "UPDATE news SET title='$title', note='$note' WHERE id='$id'";

            return mysql_query($query);
        }

        //funkcja zabezpiecza id i tworzy z tablicy id odpowiednie zapytanie (potrzebne przy usuwaniu newsow)
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

        public function RemoveNews($id) //usuwanie newsa/newsow jesli przekazujemy tablice
        {
            $query = "DELETE FROM news WHERE ".$this->doIdQuery($id);
            return mysql_query($query);
        }

        public function RemoveNewsToBin($id) //usuwanie newsa/newsow jesli przekazujemy tablice
        {
            $query = "UPDATE news SET proporties='1' WHERE ".$this->doIdQuery($id);
            return mysql_query($query);
        }

        public function RecoverNewsFromBin($id) //usuwanie newsa/newsow jesli przekazujemy tablice
        {
            $query = "UPDATE news SET proporties='0' WHERE ".$this->doIdQuery($id);
            return mysql_query($query);
        }

        public function ReadLinks($id = null)
        {
            $query = "SELECT id, link FROM links";

            if ($id != null)
            {
                $this->ProtectInt($id);
                $query = $query." WHERE id='$id'";
            }

            $reply = mysql_query($query);
            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $array[$i]['id'] = $line[0];
                $array[$i]['link'] = $line[1];
            }

            if (@$array == null)
                return null;
            return $array;
        }

        public function AddLink($link) //dodanie newsa
        {
            $link = $this->ProtectString($link);

            $query = "INSERT INTO links (link) VALUES ('$link')";

            return mysql_query($query);
        }

        public function EditLink($id, $link) //dodanie newsa
        {
            $link = $this->ProtectString($link);
            $id = $this->ProtectInt($id);
            $query = "UPDATE links SET link='$link' WHERE id='$id'";

            return mysql_query($query);
        }

        public function AddAdmin($login, $pass, $name, $mail) //dodawanie admina
        {
            $login = $this->ProtectString($login);
            $pass = $this->ProtectString($pass);
            $name = $this->ProtectString($name);
            $mail = $this->ProtectString($mail);

            $query = "INSERT INTO `admins` (`id`, `login`, `pass`, `name`, `mail`) VALUES
            (NULL, '".trim($login)."', ' ".trim($pass)."', '".trim($name)."', '".trim($mail)."');";

            return mysql_query($query);
        }

        public function CheckAdmin($login, $pass) //sprawdzanie loginu i hasla
        {
            $login = $this->ProtectString($login);

            $query = "SELECT login, pass FROM admins WHERE login='$login'";
            $reply = mysql_query($query);

            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                if (trim($line[0]) === trim($login) && trim($line[1]) === trim($pass))
                    return true;
            }

            return false;
        }

        public function Close()
        {
            mysql_close($this->sql_conn);
        }
    }
?>
