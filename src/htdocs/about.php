<?php

include_once '../conf/config.inc.php'; // app config

if (!isset($TEMPLATE)) {
  $TITLE = 'About the Spectrograms';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="css/about.css" />';
  $FOOT = '';

  include 'template.inc.php';
}

?>

<h2>What is a Spectrogram?</h2>

<p>A spectrogram is a means for viewing the frequency content of a seismogram
  as it changes with time. Once each minute, we calculate the frequency spectrum
  of the seismogram between 0 and 10 Hz. The spectral amplitude values are
  converted to color with deep blues representing low values, ranging through
  greens and yellows to deep red for the high values. Each minute is thus
  displayed as a horizontal colored line representing by its changing color the
  differences in shaking intensity at different frequencies from 0 to 10 Hz. By
  plotting these horizontal lines adjacent to one another as they are calculated
  we can see a time sequence of the frequency spectrum.</p>

<h2>How to Read the Display</h2>

<p>The spectrograms displayed are from a few of the seismograph stations
  routinely recorded by the Northern California Seismograph Network. The
  spectrograms show a record of the frequency content of ground motion at a
  particular seismograph station in Northern California during a 24-hour period.
  The spectrogram is "read" from top to bottom (this is the direction that time
  increases). Each horizontal line represents in color the amount of ground
  motion at frequencies ranging from 0 to 10 Hz. Each horizontal line represents
  the frequency spectrum for 1 minute of data.</p>

<p>The corresponding data trace is plotted along the right-hand axis.</p>

<p>The vertical lines are not part of the spectrogram but are present to
  indicate equal intervals of frequency. Time is indicated at the left side of
  the plot in local Pacific time and at the right side in Universal (or
  Greenwich) time.</p>

<h2>Interpretation</h2>

<p>When an earthquake occurs the spectrogram will show ground motions that
  typically last from several tens of seconds to many minutes depending on the
  size of the earthquake and the sensitivity of the seismograph.</p>

<p>On these spectrograms you may see local earthquakes in Northern California
  and earthquakes elsewhere in the world. Almost any earthquake in the world
  having a magnitude greater than 5.5 will be seen on these spectrograms.</p>

<p><a href="examples.php">Illustrative Examples</a> &raquo;</p>

<h2>How the Data Channels are Named</h2>

<p>Each data channel has a three part name such as MSL VHZ NC. The first part
  identifies the station. The middle part describes the data. The last part
  identifies the seismic network. The station name and network uniquely
  identify the location where the data are being recorded. The data descriptor
  tells a) what is being measured (velocity, displacement, acceleration), b) what
  sort of instrument is doing the recording (digital, hi-gain analog, etc.), and
  c) the orientation of the sensor (vertical, horizontal-north-south or
  horizontal-east-west). For example, VHZ is a <strong>high-gain</strong>
  (sensitive) analog <strong>velocity</strong> sensor, sensing
  <strong>vertical</strong> movement.</p>

<ul id="naming" class="no-style">
  <li><h4>Station Name (Left)</h4>
    <ul class="no-style">
      <li>Examples: MSL, HSF, JSF, etc.</li>
    </ul>
  </li>
  <li><h4>Data Type (Middle)</h4>
    <ul class="no-style">
      <li>V = Velocity<br />D = Displacement<br />A = Acceleration</li>
      <li>H = High Gain Analog<br />D = Digital</li>
      <li>Z = Vertical<br />N = North-South<br />E = East-West</li>
    </ul>
  </li>
  <li><h4>Seismic Network (Right)</h4>
    <ul class="no-style">
      <li>NC = Northern California Seismic Network</li>
    </ul>
  </li>
</ul>
