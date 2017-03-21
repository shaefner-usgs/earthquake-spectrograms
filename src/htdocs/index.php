<?php

include_once '../conf/config.inc.php'; // app config
include_once '../lib/_functions.inc.php'; // app functions

$timespan = safeParam('timespan', '24hr');

if (!isset($TEMPLATE)) {
  $set = 'nca';
  $today = date('Ymd');

  if ($timespan === '2hr') {
    $set = 'nca2';
  }

  $TITLE = 'Real-time Spectrogram Displays';
  $NAVIGATION = true;
  $HEAD = '
    <link rel="stylesheet" href="/lib/leaflet-0.7.7/leaflet.css" />
    <link rel="stylesheet" href="css/index.css" />
  ';
  $FOOT = '
    <script>
      var MOUNT_PATH = "' . $CONFIG['MOUNT_PATH'] . '",
          SET = "' . $set . '",
          TIMESPAN = "' . $timespan . '";
    </script>
    <script src="/lib/leaflet-0.7.7/leaflet.js"></script>
    <script src="js/index.js"></script>
  ';

  // importJsonToArray() sets headers -> needs to run before including template
  $stations = importJsonToArray(__DIR__ . '/_getStations.json.php', $timespan);

  print_r($stations);

  include 'template.inc.php';
}

$height = ceil($stations['metadata']['count'] / 4) * 32;
$stationsHtml = '<ul class="stations no-style" style="height: '. $height . 'px;">';

foreach ($stations['features'] as $feature) {
  $props = $feature['properties'];
  $name = $props['site'] . ' ' . $props['type'] . ' ' . $props['network'] .
    ' ' . $props['code'];
  $link = "$today/00"; // default
  if ($props['link'] !== '') {
    $link = $props['link'];
  }

  $stationsHtml .= sprintf('<li>
      <a href="%s/%s/%s" title="View station">%s</a>
    </li>',
    $timespan,
    $feature['id'],
    $link,
    $name
  );
}

$stationsHtml .= '</ul>';

?>

<p>These spectrogram displays depict the frequency content of a seismogram as
  it changes with time, updated once per minute. Each plot represents 24 hours
  of data from one station. <a href="about.php">Read more</a> &raquo;</p>

<div class="map"></div>

<h3 class="count">
  <?php print $stations['metadata']['count']; ?> stations on this map
</h3>

<?php print $stationsHtml; ?>

<p><a href="<?php print $timespan; ?>/<?php print $today; ?>">View spectrograms
  for all stations</a> &raquo;</p>
