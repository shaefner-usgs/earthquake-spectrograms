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

  $set = 'nca'; // default

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

  // Spectrogram plot
  $imgDateStr = $date;
  $file = sprintf('nc.%s_00.%s%s.gif',
    str_replace(' ', '_', $instrument),
    $imgDateStr,
    $hour
  );

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

  // Create thumbnails if viewing 2hr plots
  if ($timespan === '2hr') {
    $plotHours = [
      '00', '02', '04', '06', '08', '10', '12', '14', '16', '18', '20', '22'
    ];
    $set = 'nca2';
    $thumbsList = '<ul class="thumbs no-style">';

    foreach ($plotHours as $plotHour) {
      $li = sprintf('<li>
          <h4>%s:00</h4>
          <a href="%s">
            <img src="%s/data/%s/tn-%s" alt="spectrogram thumbnail for hour %s" />
          </a>
        </li>',
        $plotHour,
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

  $header = getHeaderComponents($date, $timespan);

  $allLink = sprintf('<a href="%s/%s/%s">View spectrograms for all stations</a>',
    $CONFIG['MOUNT_PATH'],
    $timespan,
    $date
  );
  $backLink = sprintf('<a href="%s/%s/%s">Back to station %s</a>',
    $CONFIG['MOUNT_PATH'],
    $timespan,
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

<p class="allstations"><?php print $allLink; ?> &raquo;</p>
<p class="back">&laquo; <?php print $backLink;?></p>
