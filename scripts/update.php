<?php
/**
 * 
 * Builds the PSL data array from the parts of a text line.
 * 
 * @param array &$data The PSL data array.
 * 
 * @param array $parts The parts of a PSL text line.
 * 
 * @return void
 * 
 */
function build(array &$data, array $parts)
{
    $part = array_pop($parts);
    $is_domain = true;

    if (strpos($part, '!') === 0) {
        $part = substr($part, 1);
        $is_domain = false;
    }

    if (! array_key_exists($part, $data)) {
        if ($is_domain) {
            $data[$part] = array();
        } else {
            $data[$part] = array('!' => '');
        }
    }

    if ($is_domain && count($parts) > 0) {
        build($data[$part], $parts);
    }
}

/**
 * 
 * Update the text and data files.
 * 
 */

// get the origin text file and save it locally
$text = file_get_contents('http://mxr.mozilla.org/mozilla-central/source/netwerk/dns/effective_tld_names.dat?raw=1');
$text_file = dirname(__DIR__) . DIRECTORY_SEPARATOR
           . 'data' . DIRECTORY_SEPARATOR
           . 'public-suffix-list.txt';
file_put_contents($text_file, $text);

// convert the origin text lines to a data array
$data = [];
$lines = file($text_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    // skip comment lines
    if (substr($line, 0, 2) == '//') {
        continue;
    }
    // get the line parts and build into the psl data
    $parts = explode('.', $line);
    build($data, $parts);
}

// write the data aray to a PHP file
$code = '<?php' . PHP_EOL . 'return ' . var_export($data, true) . ';';
$code_file = dirname(__DIR__) . DIRECTORY_SEPARATOR
           . 'data' . DIRECTORY_SEPARATOR
           . 'public-suffix-list.php';
file_put_contents($code_file, $code);

// done!
exit(0);
