<?php
// Local SVG tile for the CCTV map when external map services are unavailable.
$z = max(0, min(22, (int)($_GET['z'] ?? 13)));
$x = max(0, (int)($_GET['x'] ?? 0));
$y = max(0, (int)($_GET['y'] ?? 0));

$red = 40 + ($x % 20);
$green = 60 + ($y % 20);
$blue = 90 + (($x + $y) % 20);
$background = sprintf('#%02x%02x%02x', $red, $green, $blue);
$label = htmlspecialchars("{$z} / {$x} / {$y}", ENT_QUOTES | ENT_XML1, 'UTF-8');

header('Content-Type: image/svg+xml; charset=UTF-8');
header('Cache-Control: public, max-age=86400');
echo '<svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256">'
    . '<rect width="256" height="256" fill="' . $background . '"/>'
    . '<path d="M32 0v256M64 0v256M96 0v256M128 0v256M160 0v256M192 0v256M224 0v256'
    . 'M0 32h256M0 64h256M0 96h256M0 128h256M0 160h256M0 192h256M0 224h256"'
    . ' fill="none" stroke="#50648c" stroke-width="1"/>'
    . '<text x="8" y="20" fill="#c8dcff" font-family="sans-serif" font-size="12">'
    . $label . '</text></svg>';
