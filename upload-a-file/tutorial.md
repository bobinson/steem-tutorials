Loading a file into the STEEM blockchain
======

The question arose:

> Is it possible to get files into the STEEM blockchain?

With a few little tricks you can get everything into the STEEM blockchain.

What is needed:
------

- A STEEM account
- One file
- Some know-how in programming
    - Our example scripts are written in PHP

We tried to make the tutorial as simple as possible. 
However, some prerequisites are necessary, so we classify this for experienced users.


What am I learning:
------
    
- How to split files with PHP
- How can I upload files to the STEEM blockchain
- How can I reassemble split files with PHP


*The following scripts are for testing purposes and not for productive use*

General procedure
------

- We split our file into small pieces
    - This is because STEEM has a maximum length of a post
- We convert each section into text
    - This is because we get binary data only badly in a post
- We write a post / comment for each section 
    - The content of a post is the content of a file
- We read every single post and put all content back together again
    

Tutorial
------

- All examples are written in PHP, but the procedure can be implemented in any language imaginable. 
- Let us take Steemit's white paper as an example.


### Split the file

Splitting the file is easy on the right. 

**Procedure**

- We're looking at the size of the file,
- split the file into many small sections with `fread($file_handle, $buffer)`,
- knackered

**Partial section Example**


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

You can find the complete example in [split.php](https://github.com/pcsg/steem-tutorials/blob/master/upload-a-file/split.php)


### Upload the file

Here's the interesting part. 
If we now have our small pieces, we have to convert every single piece into text that we can upload as mail.

**Procedure**

- We'll take a section,
- read the contents of the file with `file_get_contents()`,
- convert the binary content to text. For example with `bin2hex()`,
- write a new post with this content

**Example**

```php
// ...

$fileData = bin2hex(file_get_contents('PATH_TO_FILE/steem-whitepaper.pdf.part0'));

// ...
```

You can find the complete example in [upload.php](https://github.com/pcsg/steem-tutorials/blob/master/upload-a-file/upload.php)


### Download the file

For the whole thing to make sense, we have to get the file out of the blockchain.

With the LightRPC Client (https://github.com/hernandev/light-rpc) it is an easy to read single posts via PHP.
Our example script uses it. 
If you don't know how to install it, just have a look at https://github.com/hernandev/light-rpc Here's a wonderful explanation. 

*If requested, we can also write a small tutorial for you here.*


**Procedure**

- We're reading our summary file,
- get the contents of every post,
- convert this content back to binary data,
- pack all contents into one file.

*In our example file we have moved the download part into a function, so that the whole thing is a bit clearer.*


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


Now, to the "how we get it all together" part.

In our `$summary` we have all our files, we go through them one by one and put our whitepaper 
back into one file.


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

You can find the complete example in [download.php](https://github.com/pcsg/steem-tutorials/blob/master/upload-a-file/download.php)


We hope the tutorial was fun and taught you a little bit.

We wish you a lot of fun in the future
Hen, from the PCSG team
