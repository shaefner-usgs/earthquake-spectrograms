<?php

include_once '../conf/config.inc.php'; // app config
include_once '../lib/_functions.inc.php'; // app functions
include_once '../lib/classes/Db.class.php'; // db connector, queries

$db = new Db;

$date = safeParam('date');
$hour = safeParam('hour', '00');
$id = safeParam('id');
$timespan = safeParam('timespan', '24hr');

if (!isset($TEMPLATE)) {
  $TITLE = 'Real-time Spectrogram Displays';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="' . $CONFIG['MOUNT_PATH'] .
    '/css/spectrogram.css" />';
  $FOOT = '';

  // Query db to get station details
  $rsStation = $db->queryStation($id);

  // If station found, create instrument name; otherwise, show error
  $row = $rsStation->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $instrument = $row['site'] . ' ' . $row['type'] . ' ' . $row['network'] .
      ' ' . $row['code'];
    $subtitle = $instrument . ' (' . trim($row['name']) . ')';
  } else {
    print '<p class="alert info">Station not found</p>';
    return;
  }

  $set = 'nca';
  if ($timespan === '2hr') {
    $set = 'nca2';
  }

  // spectrogram plot
  $imgDateStr = $date;
  $file = sprintf('nc.%s_00.%s%s.gif',
    str_replace(' ', '_', $instrument),
    $imgDateStr,
    $hour
  );

  // Create thumbnails if viewing 2hr plots
  if ($timespan === '2hr') {
    $thumbsList = '<ul class="thumbs no-style">';
    $plotHours = [
      '00', '02', '04', '06', '08', '10', '12', '14', '16', '18', '20', '22'
    ];
    foreach ($plotHours as $plotHour) {
      $li = sprintf('<li>
          <a href="%s">
            <img src="%s/data/%s/tn-%s" alt="spectrogram thumbnail for hour %s" />
          </a>
        </li>',
        $plotHour,
        $CONFIG['MOUNT_PATH'],
        $set,
        preg_replace('/\d{2}\.gif$/', "$plotHour.gif", $file),
        $plotHour
      );
      $thumbsList .= $li;
    }
    $thumbsList .= '</ul>';
  }

  $header = getHeaderComponents($date);

  // if no image, display 'no data' msg
  if (file_exists($CONFIG['DATA_DIR'] . '/' . $set . '/' . $file)) {
    $img = sprintf('<img src="%s/data/%s/%s" alt="spectrogram plot"
      class="timespan-%s spectrogram" />',
      $CONFIG['MOUNT_PATH'],
      $set,
      $file,
      $timespan
    );
  } else {
    $img = '<p class="alert info">No data available</p>';
  }

  $backLink = sprintf('<a href="%s/%s">Back to station %s</a>',
    $CONFIG['MOUNT_PATH'],
    $id,
    $instrument
  );
  $TITLETAG .= "Spectrograms | $subtitle - " . $header['title'];

  include 'template.inc.php';
}

?>

<h2><?php print $subtitle; ?></h2>

<header>
  <h3><?php print $header['title']; ?></h3>
  <ul class="no-style">
    <li><?php print $header['prevLink']; ?></li>
    <li><?php print $header['nextLink']; ?></li>
  </ul>
</header>

<?php print $img; ?>
<?php print $thumbsList; ?>

<p class="allstations"><a href="../<?php print $date; ?>">View spectrograms for
  all stations</a> &raquo;</p>

<p class="back">&laquo; <?php print $backLink;?></p>
