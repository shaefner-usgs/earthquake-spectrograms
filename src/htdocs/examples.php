<?php

include_once '../conf/config.inc.php'; // app config

if (!isset($TEMPLATE)) {
  $TITLE = 'Examples';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="css/example.css" />';
  $FOOT = '';

  include 'template.inc.php';
}

?>

<h2>Teleseisms</h2>

<p>A teleseism is a record of an earthquake made by a seismograph at a great
  distance.</p>

<figure>
  <img src="img/teleseism.gif" alt="Teleseism" />
  <figcaption>This is the spectrogram from a magnitude 7.7 earthquake in
    Sumatera on June 4, 2000.</figcaption>
</figure>

<h2>Diurnal Cultural Noise</h2>

<figure class="right">
  <img src="img/diurnal.gif" alt="Diurnal noise" />
</figure>

<p>Diurnal Cultural Noise is noise from human activity repeated
  on a daily basis.</p>

<p>In addition to recording the movement of the ground caused by earthquakes,
  seismometers measure ground motion from many other sources. Human activity
  can be a significant source of ground motion.</p>

<p>This spectrogram is a 24 hour record from station JSF, located near
  Interstate 280 on the Stanford University campus in California. The record
  starts at the bottom at 5:00pm local time. Low frequency noise declines
  through the evening and remains at a minimum throughout the night. Between
  5:00am and 6:00am, there is an abrupt increase in low frequency noise which
  is sustained throughout the day. </p>

<h2 class="clear">Seismometer Calibration Pulse</h2>

<figure class="right">
  <img src="img/calib_seis.gif" alt="Seismometer calibration" />
</figure>

<p>Many of the seismometers in our network are of the magnet-coil-spring type.
  This type of instrument consists of a permanent magnet and a coil of wire.
  The coil, which is wound around a rather massive core, is suspended by a
  spring. When the ground moves, the coil tends to remain in place due to its
  mass, while the magnet, which is rigidly attached to the seismometer housing,
  moves relative to the coil. The relative motion produces a current in the
  coil, and it is this electrical signal that ends up being recorded as a
  seismogram.</p>

<p>The mechanical response of the magnet, springs and coil, as well as the
  electronics that amplifies the current, all affect the final signal. If the
  springs weaken or the electronics drifts, for example, the seismogram will
  not be accurate. Since these seismometers are located all over northern
  California, it is not practical to visit each one to check its performance.</p>

<p>Instead, the seismometer is programmed to check itself. Once a day, the
  electronics in the seismometer sends a controlled current through the coil.
  The response of the magnet-spring-coil system to this test signal is sent
  back as a calibration pulse. These pulses can be measured at the central
  recording site in Menlo Park, California, to assure that each seismometer is
  functioning properly.</p>

<figure style="max-width: 522px">
  <img src="img/calib_spct.gif" alt="Spectrogram calibration" />
  <figcaption>The calibration pulse above produces a spectral streak like this. Much of
    the energy in the calibration pulse is at frequencies greater than 10 Hz
    and is not seen in the spectrogram.</figcaption>
</figure>
