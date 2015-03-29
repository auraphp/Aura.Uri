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
 * Update the public suffix list files.
 * 
 */

// get the origin text file
$text = file_get_contents('http://mxr.mozilla.org/mozilla-central/source/netwerk/dns/effective_tld_names.dat?raw=1');
$lines = explode("\n", $text);

// convert text lines to data
$data = [];
foreach ($lines as $line) {
    // massage the line
    $line = trim($line);
    // skip empty and comment lines
    if (! $line || substr($line, 0, 2) == '//') {
        continue;
    }
    // get the line parts and build into the psl data
    $parts = explode('.', $line);
    build($data, $parts);
}

// write the data to a PHP file
$code = '<?php' . PHP_EOL . 'return ' . var_export($data, true) . ';';
$file = dirname(__DIR__) . DIRECTORY_SEPARATOR
      . 'data' . DIRECTORY_SEPARATOR
      . 'public-suffix-list.php';
file_put_contents($file, $code);

// done!
exit(0);
