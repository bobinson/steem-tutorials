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

- Alle Beispiele sind in PHP geschrieben, das Vorgehen ist aber in jeder erdenlichen Sprache umsetzbar. 
- Als Beispiel nehmen wir das Whitepaper von Steemit.


### Splitten der Datei

Das Splitten der Datei geht rechts einfach. 

- Wir schauen uns die Größe der Datei an
- Splitten die Datei in viele kleine Teilstücke mit `fread($file_handle, $buffer)`
- Fertig

Teilausschnitt:

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

- Wir nehmen ein Teilstück
- Lesen den content der Datei ein `file_get_contents()`
- Wandeln den Binären Inhalt in text um. Zum Beispiel mit `bin2hex()`
- Schreiben mit diesem Inhalt ein neuen Post

Beispiel:

```php
// ...

$fileData = bin2hex(file_get_contents('PATH_TO_FILE/steem-whitepaper.pdf.part0'));

// ...
```

Das komplette Beispiel findest du in [upload.php](https://github.com/pcsg/steem-tutorials/blob/master/upload-a-file/upload.php)


### Download der Datei

