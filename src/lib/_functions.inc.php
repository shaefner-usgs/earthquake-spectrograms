<?php

/**
 * Get components (title, navigation links) for header on full-size plots
 *
 * @param $date {Integer}
 *     Ymd date, e.g. 20170223
 *
 * @return {Array}
 */
function getHeaderComponents ($date, $timespan=NULL) {
  $cutoffDate = date('Ymd', strtotime('-14 days'));
  $time = strtotime($date);
  $today = date('Ymd');

  // Set header defaults first
  $nextHref = date('Ymd', strtotime('+1 day', $time));
  $nextLink = '';
  $prevHref = date('Ymd', strtotime('-1 day', $time));
  $prevLink = '';
  $title = date('D M j, Y', $time);

  if ($timespan === '2hr') {
    $nextHref = "../$nextHref/00";
    $prevHref = "../$prevHref/00";
  }

  if ($date === $cutoffDate) {
    $prevHref = '';
  } else if ($date === $today) {
    $nextHref = '';
  }
  if ($nextHref) {
    $nextLink = '<a href="' . $nextHref . '" class="next">Next<i
      class="material-icons">&#xE5CC;</i></a>';
  }
  if ($prevHref) {
    $prevLink = '<a href="' . $prevHref . '" class="prev"><i
      class="material-icons">&#xE5CB;</i> Prev</a>';
  }

  return [
    'title' => $title,
    'prevLink' => $prevLink,
    'nextLink' => $nextLink
  ];
}

/**
 * Get html for <img>, or return message (<p>) if img not found
 *
 * @param $date {Integer}
 * @param $id {Integer}
 * @param $instrument {String}
 *
 * @return $img {Html}
 */
function getThumb ($date, $id, $instrument, $hour=NULL) {
  if ($hour) {
    $hour = "/$hour"; // add leading slash for appending to href
  }
  $imgDateStr = $date;
  $file = sprintf('tn-nc.%s_00.%s00.gif',
    str_replace(' ', '_', $instrument),
    $imgDateStr
  );

  // If no image exists, display 'no data' msg
  if (file_exists($GLOBALS['CONFIG']['DATA_DIR'] . '/' . $GLOBALS['set'] . '/' . $file)) {
    $img = sprintf('<a href="%s/%s/%d/%s%s">
        <img src="%s/data/%s/%s" alt="spectrogram thumbnail" />
      </a>',
      $GLOBALS['CONFIG']['MOUNT_PATH'],
      $GLOBALS['timespan'],
      $id,
      $date,
      $hour,
      $GLOBALS['CONFIG']['MOUNT_PATH'],
      $GLOBALS['set'],
      $file
    );
  } else {
    $img = '<p class="nodata">No data available</p>';
  }

  return $img;
}

/**
 * Import dynamically generated json file and store it in an array
 *
 * @param $file {String}
 *     full path to json file to import (__DIR__ magic constant is useful)
 * @param $timespan {String} default is NULL
 *     type ('2hr' or '24hr') of spectrograms to display - filter for json results
 *
 * @return {Array} json file contents
 */
function importJsonToArray ($file, $timespan=NULL) {
  if (is_file($file)) {
    // Read file contents into output buffer
    ob_start();
    include $file;
    $contents = ob_get_contents();
    ob_end_clean();

    // Reset to html (gets set to JSON by included $file)
    header('Content-Type: text/html');

    return json_decode($contents, true);
  } else {
    trigger_error("importJsonToArray(): Failed opening $file for import",
      E_USER_WARNING);
  }
}

/**
 * Get a request parameter from $_GET or $_POST
 *
 * @param $name {String}
 *     The parameter name
 * @param $default {?} default is NULL
 *     Optional default value if the parameter was not provided.
 * @param $filter {PHP Sanitize filter} default is FILTER_SANITIZE_STRING
 *     Optional sanitizing filter to apply
 *
 * @return $value {String}
 */
function safeParam ($name, $default=NULL, $filter=FILTER_SANITIZE_STRING) {
  $value = NULL;

  if (isset($_POST[$name]) && $_POST[$name] !== '') {
    $value = filter_input(INPUT_POST, $name, $filter);
  } else if (isset($_GET[$name]) && $_GET[$name] !== '') {
    $value = filter_input(INPUT_GET, $name, $filter);
  } else {
    $value = $default;
  }

  return $value;
}

/**
 * Convert an array to a json feed and print it
 *
 * @param $array {Array}
 *     Data from db
 * @param $callback {String} default is NULL
 *     optional callback for jsonp requests
 */
function showJson ($array, $callback=NULL) {
  header('Content-Type: application/json');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: *');
  header('Access-Control-Allow-Headers: accept,origin,authorization,content-type');

  $json = json_encode($array);
  if ($callback) {
    $json = "$callback($json)";
  }
  print $json;
}
