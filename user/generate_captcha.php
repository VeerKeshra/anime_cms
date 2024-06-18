<?php
session_start();

// Generate a random CAPTCHA code and store it in session
$captcha_code = substr(md5(rand()), 0, 6);
$_SESSION['captcha'] = $captcha_code;

// Create the CAPTCHA image
$width = 100;
$height = 40;
$image = imagecreate($width, $height);

$background_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
$line_color = imagecolorallocate($image, 64, 64, 64);

// Add random lines to the image for added security
for ($i = 0; $i < 10; $i++) {
    imageline($image, 0, rand() % $height, $width, rand() % $height, $line_color);
}

// Use a built-in font if TTF is not available
imagestring($image, 5, 10, 10, $captcha_code, $text_color);

// Output the image as PNG
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>
