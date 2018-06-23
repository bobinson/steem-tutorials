Eine Datei in die STEEM Blockchain laden
======

Es kam die Frage auf:

> Ist es möglich Dateien in die STEEM Blockchain zu bekommen?

Mit ein paar kleinen Tricks und Kniffen bekommt man sozusagen alles in die STEEM Blockchain.


Was wird benötigt:
------

- Ein STEEM Account
- Eine Datei
- Etwas Know How im programmieren
    - Unsere Beispiel Skripte sind in PHP geschrieben

Es wurde versucht das Tutorial so einfach wie möglich zu gestallten. 
Jedoch sind einige Voraussetzungen nötig, daher stufen wir dies für Erfahrene ein.


Was lerne ich:
------
    
- Wie splitte ich Dateien mit PHP
- Wie kann ich Dateien in die STEEM Blockchain hochladen
- Wie kann ich gesplittete Dateien mit PHP wieder zusammenfügen


**Die folgenden Skripte sind zum Testzweck und nicht für einen produktiven Einsatz gedacht**


Generelle Vorgehensweise
------

- Wir splitten unsere Datei in kleine Stücke
    - Dies hat den Grund, da STEEM eine maximale Länge eines Posts hat
- Wir wandeln jedes Teilstück in Text um
    - Dies hat den Grund da wir Binär Daten nur schlecht in einen Post bekommen
- Wir schreiben für jedes Teilstück ein Post / Kommentar 
    - Der Inhalt eines Posts ist der Inhalt einer Datei
- Wir lese jeden einzelnen Post und fügen alle Inhalte wieder zusammen
    

Tutorial
------

- Alle Beispiele sind in PHP geschrieben, das Vorgehen ist aber in jeder erdenklichen Sprache umsetzbar. 
- Als Beispiel nehmen wir das Whitepaper von Steemit.


### Splitten der Datei

Das Splitten der Datei geht rechts einfach. 

**Vorgehen**

- Wir schauen uns die Größe der Datei an,
- splitten die Datei in viele kleine Teilstücke mit `fread($file_handle, $buffer)`,
- fertig

**Teilausschnitt Beispiel**

```php
// ...

// how many parts would be existed
$parts = $file_size / $buffer;

// ...

for ($i = 0; $i < $parts; $i++) {
    // read buffer sized amount of the file
    $file_part = fread($file_handle, $buffer);

    // the filename of the part
    $file_part_path = $store_path.$file_name.".part$i";

    // create and write the part
    $file_new = fopen($file_part_path, 'w+');
    fwrite($file_new, $file_part);
    fclose($file_new);
}

// ...

```

Das komplette Beispiel findest du in [split.php](https://github.com/pcsg/steem-tutorials/blob/master/upload-a-file/split.php)



### Upload der Datei

Hier ist der interessante Teil. 
Wenn wir nun unsere kleinen Teilstücke haben, müssen wir jedes einzelne Stück in Text umwandeln welches wir als Post hochladen können.

**Vorgehen**

- Wir nehmen ein Teilstück,
- lesen den Inhalte der Datei ein mit `file_get_contents()`,
- wandeln den binären Inhalt in Text um. Zum Beispiel mit `bin2hex()`,
- schreiben mit diesem Inhalt ein neuen Post

**Beispiel**

```php
// ...

$fileData = bin2hex(file_get_contents('PATH_TO_FILE/steem-whitepaper.pdf.part0'));

// ...
```

Das komplette Beispiel findest du in [upload.php](https://github.com/pcsg/steem-tutorials/blob/master/upload-a-file/upload.php)


### Download der Datei

Damit das ganze Sinn hat, müssen wir die Datei auch wieder irgendwie aus der Blockchain rausbekommen.

Mit dem LightRPC Client (https://github.com/hernandev/light-rpc) ist es ein einfaches per PHP einzelne Posts auszulesen.
Unser Beispiel Skript nutzt diesen, falls du nicht weist wie man diesen installiert, 
schau einfach mal auf https://github.com/hernandev/light-rpc vorbei. Hier wird das wunderbar erklärt. 

Falls gewünscht können wir hier auch ein kleines Tutorial für schreiben.


**Vorgehen**

- Wir lesen unsere summary Datei ein,
- hohlen uns den Inhalt von jedem Post,
- wandeln diesen Inhalt zurück zu Binärdaten um,
- packen alle Inhalte zu einer Datei zusammen.

In unserer Beispieldatei haben wir den Download Teil in eine Funktion ausgelagert, damit das ganze etwas übersichtlicher ist.

```php

/**
 * This function fetch a steemit post
 *
 * @param string $username
 * @param string $permlink
 * @return mixed
 */
function getContent($username, $permlink)
{
    $Client = new Client('https://api.steemit.com');

    $Request  = new Request('content_api', 'get_content', [$username, $permlink]);
    $Response = $Client->send($Request);

    if ($Response->isError()) {
        var_dump($Response->error());
        exit;
    }

    $response = $Response->toArray();
    $result   = $response['result'];

    return $result['body'];
}
```

Nun zum Teil wie wir alles zusammen bekommen.
In unserer `$summary` haben wir alle unsere Dateien, diese gehen wir nacheinander durch und packen unser Whitepaper wieder zu einer Datei.

```php

// now we go through all the files and get the content from the steem blockchain
foreach ($summary as $part => $permlink) {
    echo $part.PHP_EOL;

    // get the content from the steem blockchain with our helper function
    $content = getContent('pscg.test', $permlink);

    // the content is in hex text, but we need binary.
    // if you still remember we converted it for the steem blockchain
    // now we convert it back to binary data
    $content = hex2bin($content);

    // this binary data is now attached to our file
    file_put_contents($filename, $content, \FILE_APPEND);
}
```

Das komplette Beispiel findest du in [updownloadload.php](https://github.com/pcsg/steem-tutorials/blob/master/upload-a-file/download.php)

Wir hoffen das Tutorial hat Spass gemacht und dir auch ein wenig etwas beigebracht.

Wir wünschen dir weiterhin viel Spass
Hen, vom PCSG Team
