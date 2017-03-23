<?php

include_once '../conf/config.inc.php'; // app config
include_once '../lib/_functions.inc.php'; // app functions
include_once '../lib/classes/Db.class.php'; // db connector, queries

$db = new Db;

$date = safeParam('date');
$id = safeParam('id');
$timespan = safeParam('timespan', '24hr');

if (!isset($TEMPLATE)) {
  $TITLE = 'Real-time Spectrogram Displays';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="../css/spectrograms.css" />';
  $FOOT = '';

  $hour = NULL;
  if ($timespan === '2hr') {
    $hour = '00'; // show first bi-hourly plot of the day
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
      $thumb = getThumb($date, $id, $instrument, $hour);
      $thumbTitle = date('M j, Y', strtotime($date));
      $listHtml .= "<li><h3>$thumbTitle</h3>$thumb</li>";
    }
  } else { // show plots for all stations on a given date
    $header = getHeaderComponents($date);
    $subtitle = 'All Stations';

    // Query db to get a list of stations
    $rsStations = $db->queryStations($timespan);

    while ($row = $rsStations->fetch(PDO::FETCH_ASSOC)) {
      $instrument = $row['site'] . ' ' . $row['type'] . ' ' . $row['network'] .
        ' ' . $row['code'];
      $thumb = getThumb($date, $row['id'], $instrument, $hour);
      $listHtml .= "<li><h3>$instrument</h3>$thumb</li>";
    }
  }

  $listHtml .= '</ul>';

  $TITLETAG = "Spectrograms | $subtitle";
  if ($header['title']) {
    $TITLETAG .= ' - ' . $header['title'];
  }

  include 'template.inc.php';
}

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

<p class="back">&laquo; <a href="../<?php print $timespan; ?>">Back to station
  list / map</a></p>
