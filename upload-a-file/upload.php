<?php

/**
 * This file is an example of how to upload a file into the STEEM blockchain.
 * This example is not functionally out of the box!!
 *
 * This has the reason that a lot of abuse can be done with this.
 */

$author = 'pscg.test';

//
// This part are QUIQQER module stuff, this part will not work directly
//

define('QUIQQER_SYSTEM', true);

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/header.php';

use SteemConnect\Operations\Comment;

$Factory      = PCSG\Steemit\Collectibles\Factory::getInstance();
$Steem        = PCSG\Steemit\Collectibles\Steem::getInstance();
$SteemConnect = PCSG\Steemit\Collectibles\SteemConnect::getInstance();

// test client
$Client = $Steem->getClient([
    'login',
    'vote',
    'comment'
]);

try {
    $Client->setToken(
        $SteemConnect->getTokenFromUser($author)
    );
} catch (SteemConnect\Exceptions\TokenException $Exception) {
    $authorizationURL = $Client->auth()->getAuthorizationUrl();

    echo $authorizationURL.PHP_EOL;
    exit;
}

//
// This part is the upload part
//

$dir             = 'splits/';
$summaryFileName = 'summary.json';

if (!file_exists($summaryFileName)) {
    file_put_contents($summaryFileName, '');
}

// we are looking at our summary
// this file is used to put all parts back together again later, if we needed it
$summary = json_decode(file_get_contents($summaryFileName), true);

if (!is_array($summary)) {
    $summary = [];
}

// we read in all file parts
$files = scandir($dir);

// we upload every part now
foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    if (!is_file($dir.$file)) {
        continue;
    }

    // if we have already uploaded this part we don't need to upload it anymore
    if (isset($summary[$file])) {
        continue;
    }

    // this is the important part
    // we will now convert the binary data of the part to hex characters
    // we can write these hex characters as post in the steem blockchain
    $fileData = bin2hex(file_get_contents($dir.$file));

    // now we create our post
    $Post    = new Comment();
    $newHash = $Factory->getNewId();

    $Post->author($author);
    $Post->title($newHash);

    $Post->body($fileData);
    $Post->category('test');
    $Post->tags(['test']);

    echo "Upload ".$file.PHP_EOL;
    $Response = $Client->broadcast($Post);

    // write the hash to the summary
    $summary[$file] = $newHash;

    file_put_contents($summary, json_encode($summary));

    echo "Part uploaded; please execute in 5 min again if it breaks up".PHP_EOL;

    // steemit limit is 5min to write a post -> better wait 6 min ;-)
    sleep(60 * 6);
}

echo 'File is completly uplaoded';
