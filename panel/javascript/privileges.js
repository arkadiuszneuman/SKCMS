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

function OpenWindow(id, login, privileges)
{
    var r = getXMLHttpRequest();
    r.open('GET', './ajax/windowUser.php?id='+id+'&login='+login+'&privileges='+privileges, true);

    r.onreadystatechange = function()
    {
        if (r.readyState == (1 || 0))
        {
            document.getElementById('windowUser').innerHTML = "Ładowanie...";
            document.getElementById('windowUser').style.display = 'block';
        }
        else if (r.readyState == 4)
        {
            document.getElementById('windowUser').innerHTML = r.responseText;
            document.getElementById('windowUser').style.display = 'block';
            Checkboxes();
        }
        else
        {
            document.getElementById('windowUser').innerHTML = "Błąd";
        }
    };

    r.send(null);
}

function Checkboxes() //obsluga wylaczania i wlaczania checkboxow przy nacisnieciu checkboksa user
{
    user = document.getElementById('user');
    user.onclick = function()
    {
        checkboxes = document.getElementsByName('priv[]');
        for(var i in checkboxes)
        {
            checkboxes[i].disabled = !checkboxes[i].disabled;
        }

        user.disabled = false;
    };
}

function Close()
{
    document.getElementById('windowUser').style.display = 'none';
}