<?php

$section = $CONFIG['MOUNT_PATH'];
$url = $_SERVER['REQUEST_URI'];

$matches_index = false;
if (preg_match("@^$section(/index.php)?/?(\d+)?/?(latest|\d+)?$@", $url)) {
  $matches_index = true;
}

$NAVIGATION =
  navGroup('Spectrogram Displays',
    navItem("$section", 'Spectrograms', $matches_index) .
    navItem("$section/about.php", 'About the Spectrograms') .
    navItem("$section/examples.php", 'Examples') .
    navItem("/monitoring/helicorders", 'Seismogram Displays')
  );

print $NAVIGATION;
