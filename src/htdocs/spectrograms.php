<?php

date_default_timezone_set('America/Los_Angeles');

include_once '../conf/config.inc.php'; // app config
include_once '../lib/_functions.inc.php'; // app functions
include_once '../lib/classes/Db.class.php'; // db connector, queries

$db = new Db;

$date = safeParam('date');
$id = safeParam('id');

// 'hardwire' for now
$set = 'nca';

if (!isset($TEMPLATE)) {
  $TITLE = 'Real-time Spectrogram Displays';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="css/spectrograms.css" />';
  $FOOT = '';

  function getImg ($date, $id, $instrument) {
    $imgDateStr = $date;
    $file = sprintf('%s/tn-nc.%s_00.%s00.gif',
      $GLOBALS['set'],
      str_replace(' ', '_', $instrument),
      $imgDateStr
    );

    // If no image exists, display 'no data' msg
    if (file_exists($GLOBALS['CONFIG']['DATA_DIR'] . '/' . $file)) {
      $img = sprintf('<a href="%d/%s">
          <img src="data/%s" alt="spectrogram thumbnail" />
        </a>',
        $id,
        $date,
        $file
      );
    } else {
      $img = '<p class="nodata">No data available</p>';
    }

    return $img;
  }

  include 'template.inc.php';
}

$listHtml = '<ul class="stations no-style">';

if ($id) { // show plots for a given station
  // Query db to get station details
  $rsStation = $db->queryStation($id);

  // If station found, set subtitle; otherwise, show error
  $row = $rsStation->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $instrument = $row['site'] . ' ' . $row['type'] . ' ' . $row['network'] .
      ' ' . $row['code'];
    $subtitle = $instrument . ' (' . trim($row['name']) . ')';
  } else {
    print '<p class="alert info">Station not found</p>';
    return;
  }

  // Loop thru past 15 days
  for ($i = 0; $i < 15; $i ++) {
    $date = date('Ymd', strtotime('-' . $i . ' day'));
    $imgTitle = date('M j, Y', strtotime($date));
    $img = getImg($date, $id, $instrument);

    $listHtml .= "<li><h3>$imgTitle</h3>$img</li>";
  }
} else { // show plots for all stations on a given date
  $subtitle = 'All Stations';
  $header = getHeaderComponents($date);

  // Query db to get a list of stations
  $rsStations = $db->queryStations();

  while ($row = $rsStations->fetch(PDO::FETCH_ASSOC)) {
    $instrument = $row['site'] . ' ' . $row['type'] . ' ' . $row['network'] .
      ' ' . $row['code'];
    $img = getImg($date, $row['id'], $instrument);

    $listHtml .= "<li><h3>$instrument</h3>$img</li>";
  }
}

$listHtml .= '</ul>';

?>

<h2><?php print $subtitle; ?></h2>

<?php if ($header) { ?>
  <header>
    <h3><?php print $header['title']; ?></h3>
    <ul class="no-style">
      <li><?php print $header['prevLink']; ?></li>
      <li><?php print $header['nextLink']; ?></li>
    </ul>
  </header>
<?php } ?>

<?php print $listHtml; ?>

<p class="back">&laquo; <a href="../spectrograms">Back to station list / map</a></p>
