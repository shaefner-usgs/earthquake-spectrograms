<?php

include_once '../conf/config.inc.php'; // app config
include_once '../lib/_functions.inc.php'; // app functions
include_once '../lib/classes/Db.class.php'; // db connector, queries

$db = new Db;

// when this script is called via importJsonToArray(), which is declared in
// _functions.inc.php, $timespan is passed in as a function param
if (!isset($timespan)) {
  $timespan = safeParam('timespan', '24hr');
}

$now = date(DATE_RFC2822);
$today = date('Ymd');

$rsStations = $db->queryStations($timespan);

// Initialize array template for json feed
$output = [
  'generated' => $now,
  'count' => $rsStations->rowCount(),
  'type' => 'FeatureCollection',
  'features' => []
];

$currentHour = date('H');
if ($timespan === '24hr') {
  $hour = '00'; // all plots designated as '00' for hour column in file name
  $set_dir = 'nca';
} else {
  $hour = $currentHour; // start with current hour for bi-hourly plots
  $set_dir = 'nca2';
}
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
  $path = "{$CONFIG['DATA_DIR']}/$set_dir";

  if (!file_exists("$path/$img")) {
    if ($timespan === '2hr') { // check each valid hour; use most recent plot
      $plotHours = [
        '22', '20', '18', '16', '14', '12', '10', '08', '06', '04', '02', '00'
      ];
      foreach ($plotHours as $plotHour) {
        if ($plotHour <= $currentHour) {
          $img = preg_replace('/\d{2}\.gif$/', "$plotHour.gif", $img);
	  if (file_exists("$path/$img")) {
	    $link .= "/$plotHour";
            break 3;
          }
        }
      }
    }

  }

  $feature = [
    'geometry' => [
      'coordinates' => [
        floatval($row['lon']),
        floatval($row['lat'])
      ],
      'type' => 'Point'
    ],
    'id' => intval($row['id']),
    'properties' => [
      'code' => $row['code'],
      'img' => $img,
      'link' => $link,
      'name' => $row['name'],
      'network' => $row['network'],
      'site' => $row['site'],
      'type' => $row['type']
    ],
    'type' => 'Feature'
  ];


  array_push ($output['features'], $feature);
}

// Send json stream to browser
showJson($output, $callback);
