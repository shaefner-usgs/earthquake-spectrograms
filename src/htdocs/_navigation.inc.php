<?php

$section = $CONFIG['MOUNT_PATH'];
$url = $_SERVER['REQUEST_URI'];

$matches2hr = false;
if (preg_match("@^$section(/index.php)?/2hr(.*)?$@", $url)) {
  $matches2hr = true;
}
$matches24hr = false;
if (preg_match("@^$section(/index.php)?/24hr(.*)?$@", $url)) {
  $matches24hr = true;
}

$NAVIGATION =
  navGroup('Spectrogram Displays',
    navItem("$section/24hr", 'Daily Spectrograms', $matches24hr) .
    navItem("$section/2hr", 'Bi-hourly Spectrograms', $matches2hr) .
    navItem("$section/about.php", 'About the Spectrograms') .
    navItem("$section/examples.php", 'Examples') .
    navItem("/monitoring/seismograms", 'Seismogram Displays')
  );

print $NAVIGATION;
