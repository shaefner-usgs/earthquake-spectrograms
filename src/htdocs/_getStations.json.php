<?php

include_once '../conf/config.inc.php'; // app config
include_once '../lib/_functions.inc.php'; // app functions
include_once '../lib/classes/Db.class.php'; // db connector, queries

// Don't cache
$now = date(DATE_RFC2822);
header('Cache-control: no-cache, must-revalidate');
header("Expires: $now");

$db = new Db;

/* This script is called via js (as an ajax request) or php (using
 * importJsonToArray(), which is declared in _functions.inc.php).
 *
 * - js mode: $timespan is set via querystring
 * - php mode: $timespan is set before including this script
 */
if (!isset($timespan)) {
  $timespan = safeParam('timespan', '24hr');
}

$currentHour = date('H');
$rsStations = $db->queryStations($timespan);
$today = date('Ymd');

if ($timespan === '2hr') {
  $hour = $currentHour; // start with current hour for bi-hourly plots
  $set = 'nca2';
} else { // 24hr
  $hour = '00'; // all daily plots use '00' for hour column in file name
  $set = 'nca';
}

// Initialize array template for json feed
$qs = '';
if ($_SERVER['QUERY_STRING']) {
  $qs = '?' . $_SERVER['QUERY_STRING'];
}
$output = [
  'type' => 'FeatureCollection',
  'metadata' => [
    'generated' => $now,
    'count' => $rsStations->rowCount(),
    'title' => 'Earthquake Science Center Spectrograms - ' . $timespan,
    'url' => 'https://earthquake.usgs.gov' . $_SERVER['PHP_SELF'] . $qs
  ],
  'features' => []
];

while ($row = $rsStations->fetch(PDO::FETCH_ASSOC)) {
  $img = sprintf('tn-nc.%s_%s_%s_%s_00.%s%s.gif',
    $row['site'],
    $row['type'],
    $row['network'],
    $row['code'],
    $today,
    $hour
  );
  $link = $today;
  // Must use global var b/c this script is called via a function in 'php mode'
  $path = "{$GLOBALS['CONFIG']['DATA_DIR']}/$set";

  // For bi-hourly, check each valid hour and use most recent plot
  if ($timespan === '2hr') {
    $plotHours = [
      '22', '20', '18', '16', '14', '12', '10', '08', '06', '04', '02', '00'
    ];
    foreach ($plotHours as $plotHour) {
      if ($plotHour <= $currentHour) {
        $img = preg_replace('/\d{2}\.gif$/', "$plotHour.gif", $img);
        if (file_exists("$path/$img")) {
          $link .= "/$plotHour";
          break;
        }
      }
    }
  }

  // Set img / link to empty strings if plot not available
  if (!file_exists("$path/$img")) {
    $img = '';
    $link = '';
  }

  $feature = [
    'type' => 'Feature',
    'id' => intval($row['id']),
    'geometry' => [
      'coordinates' => [
        floatval($row['lon']),
        floatval($row['lat'])
      ],
      'type' => 'Point'
    ],
    'properties' => [
      'code' => trim($row['code']),
      'img' => $img,
      'link' => $link,
      'name' => trim($row['name']),
      'network' => trim($row['network']),
      'site' => trim($row['site']),
      'type' => trim($row['type'])
    ]
  ];

  array_push ($output['features'], $feature);
}

// Send json stream to browser
showJson($output, $callback);
