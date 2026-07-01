<?php
/**
 * Generate a theme screenshot programmatically
 * Run: php screenshot.php
 */
$width = 1200;
$height = 900;
$img = imagecreatetruecolor($width, $height);

// Colors
$white = imagecolorallocate($img, 255, 255, 255);
$primary = imagecolorallocate($img, 37, 99, 235);
$primaryDark = imagecolorallocate($img, 29, 78, 216);
$accent = imagecolorallocate($img, 249, 115, 22);
$text = imagecolorallocate($img, 30, 41, 59);
$textLight = imagecolorallocate($img, 100, 116, 139);
$border = imagecolorallocate($img, 226, 232, 240);
$bgSection = imagecolorallocate($img, 241, 245, 249);
$dark = imagecolorallocate($img, 15, 23, 42);

// Background
imagefilledrectangle($img, 0, 0, $width, $height, $white);

// Header
imagefilledrectangle($img, 0, 0, $width, 70, $white);
imageline($img, 0, 70, $width, 70, $border);
imagestring($img, 5, 30, 25, 'ChatJovenes', $primary);
imagestring($img, 3, 500, 28, 'Amistad   Edades   Latino   Contactos   General', $text);

// Hero
imagefilledrectangle($img, 0, 71, $width, 280, $primary);
imagestring($img, 5, 380, 120, 'Bienvenido a ChatJovenes', $white);
imagestring($img, 3, 370, 160, 'Conecta con personas de todo el mundo hispano', $white);
imagefilledrectangle($img, 350, 200, 700, 240, $white);
imagefilledrectangle($img, 710, 200, 850, 240, $accent);
imagestring($img, 3, 740, 212, 'Conectar', $white);

// Room cards
$y = 310;
imagestring($img, 5, 30, $y, 'Salas Recomendadas', $text);
$y += 40;
for ($i = 0; $i < 3; $i++) {
    $x = 30 + ($i * 390);
    imagefilledrectangle($img, $x, $y, $x + 360, $y + 200, $bgSection);
    imagefilledrectangle($img, $x, $y, $x + 360, $y + 120, $primaryDark);
    imagestring($img, 4, $x + 15, $y + 135, 'Canal de TV ' . ($i + 1), $text);
    imagestring($img, 2, $x + 15, $y + 160, 'Descripcion de la sala...', $textLight);
    imagefilledrectangle($img, $x + 15, $y + 180, $x + 85, $y + 195, $primary);
    imagestring($img, 2, $x + 25, $y + 182, 'Entrar', $white);
}

// Categories
$y += 230;
imagefilledrectangle($img, 0, $y, $width, $y + 180, $bgSection);
imagestring($img, 5, 30, $y + 15, 'Nuestras Categorias', $text);
for ($i = 0; $i < 4; $i++) {
    $x = 30 + ($i * 290);
    imagefilledrectangle($img, $x, $y + 50, $x + 260, $y + 160, $white);
    imagefilledrectangle($img, $x, $y + 50, $x + 260, $y + 110, $primary);
    $labels = array('Amistad', 'Edades', 'Latino', 'General');
    imagestring($img, 3, $x + 15, $y + 120, $labels[$i], $text);
    imagestring($img, 2, $x + 15, $y + 140, '12 salas', $textLight);
}

// Footer
imagefilledrectangle($img, 0, $height - 100, $width, $height, $dark);
imagestring($img, 3, 30, $height - 60, 'ChatJovenes  |  Top Canales  |  Ultimos Canales  |  Legal', $white);
imagestring($img, 2, 30, $height - 30, '(c) 2026 ChatJovenes. Todos los derechos reservados.', $textLight);

imagepng($img, __DIR__ . '/screenshot.png');
imagedestroy($img);
echo "Screenshot created.\n";
