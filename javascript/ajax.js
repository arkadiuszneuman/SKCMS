//sendGet("news", 0); //zaladowanie pierwszej strony na poczatku

function include(filename) //do includowania w javascripcie
{
    document.write('<' + 'script');
    document.write(' language="javascript"');
    document.write(' type="text/javascript"');
    document.write(' src="' + filename + '">');
    document.write('</' + 'script' + '>');
}

function getXMLHttpRequest() //przygotowanie ajaxa do roznych przegladarek
{
  var request = false;

  try
  {
    request = new XMLHttpRequest(); //ff opera chrome
  }
  catch(err1)
  {
    try
    {
      request = new ActiveXObject('Msxml2.XMLHTTP'); //ie 6
    }
    catch(err2)
    {
      try
      {
        request = new ActiveXObject('Microsoft.XMLHTTP'); //ie 5.5
      }
      catch(err3)
      {
        request = false;
      }
    }
  }
  return request;
}

function Close()
{
    document.getElementById('windowLogin').style.display = 'none';
}

function RefreshSite()
{
    var r = getXMLHttpRequest();
    r.open('GET', './blocks/login.php?from=outer', true);

    r.onreadystatechange = function()
    {
        if (r.readyState == 4)
        {
            document.getElementById('loginBlock').innerHTML = r.responseText;
        }
    }

    r.send(null);
}

function Login(task)
{
    var r = getXMLHttpRequest();
    if (task == "check") //pobranie danych z formularza
    {
        //task += "&login="
        //task += document.getElementsByName("login")[0].value;
        //task += "&pass="
        //task += document.getElementsByName("pass")[0].value;
		r.open('POST', './user.php?task=' + task);
		r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		r.send('&login=' + escape(document.getElementsByName('login')[0].value) + '&pass=' + escape(document.getElementsByName("pass")[0].value));
    }
    else if (task == "registation") //rejestracja po wpisaniu danych
    {
        task += "&login="
        task += document.getElementsByName("login")[0].value;
        task += "&pass="
        task += document.getElementsByName("pass")[0].value;
        task += "&mail="
        task += document.getElementsByName("mail")[0].value;

        r.send(null);
    }
	else
    {
		r.open('GET', './user.php?task='+task, true);
        r.send(null);
    }

    r.onreadystatechange = function()
    {
        if (r.readyState == (1 || 0))
        {
            document.getElementById('windowLogin').innerHTML = "Ładowanie...";
            document.getElementById('windowLogin').style.display = 'block';
        }
        else if (r.readyState == 4)
        {
           // document.getElementById('windowLogin').innerHTML = r.responseText;
            //document.getElementById('windowLogin').style.display = 'block';
			RefreshSite();
        }
        else
        {
            document.getElementById('windowLogin').innerHTML = "Błąd";
        }
    }
}


function sendGet(what, page) //site - gdzie przekierowac, what - funkcja, ktora wywola ajax po podaniu danych
{
    var r = getXMLHttpRequest();

    if (what == "news")
    {
        r.open('GET', './ajaxPHP/ajaxnews.php?page='+page, true);

        r.onreadystatechange = function()
        {
            if (r.readyState == (1 || 0))
            {
                document.getElementById('news').innerHTML = "Ładowanie...";
            }
            else if (r.readyState == 4)
            {
                document.getElementById('news').innerHTML = r.responseText;
            }
            else
            {
                document.getElementById('news').innerHTML = "Błąd";
            }
        }
    }
    r.send(null);
}
