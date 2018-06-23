<?php

/**
 * This file shows how we get our uploaded parts from the posts and pack our file together
 *
 * We use the LightRPC Client from https://github.com/hernandev/light-rpc
 * Thank you hernandev for this work
 */

// this only works if LightRPC Client is available
use LightRPC\Client;
use LightRPC\Request;

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

//
// the main code
//

// first we read our summary to get all parts
$summary = json_decode(file_get_contents('summary.json'), true);

// thats the name of our final file
$filename = 'steem-whitepaper-bc.pdf';

if (file_exists($filename)) {
    unlink($filename);
}

echo 'Download ...'.PHP_EOL;

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

echo 'Done'.PHP_EOL;
