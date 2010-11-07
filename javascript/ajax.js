function getXMLHttpRequest() //przygotowanie ajaxa do roznych przegladarek
{
  var request = false;

  try
  {
    request = new XMLHttpRequest();
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

var i = 0;
function sendGet(site, what) //site - gdzie przekierowac, what - funkcja, ktora wywola ajax po podaniu danych
{
    var r = getXMLHttpRequest();
    r.open('GET', site+i, true);
    ++i;

    if (what == "news")
    {
        r.onreadystatechange = function()
        {
            if (r.readyState == (1 || 0))
            {
                document.getElementById('newses').innerHTML = "Ładowanie...";
            }
            else if (r.readyState == 4)
            {
                document.getElementById('newses').innerHTML = r.responseText;
            }
            else
            {
                document.getElementById('newses').innerHTML = "Błąd";
            }
        }
    }
    r.send(null);
}