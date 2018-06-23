<?php

/**
 * This script splits our file into many small single pieces
 * We can then upload these parts
 */

$file   = 'steem-whitepaper.pdf';   // our main file
$buffer = 30 * 1024;                // steemit limit is 64kb, we use ~30

// open file to read and get all information we need
$file_handle = fopen($file, 'r');
$file_size   = filesize($file);
$file_name   = basename($file);

// how many parts would be existed
$parts = $file_size / $buffer;

// path to write our parts
$store_path = "splits/";

if (!is_dir($store_path)) {
    mkdir($store_path);
}

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

// close the main file handle
fclose($file_handle);

// we split our file in a lot of little parts