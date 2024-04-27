<?php
session_start();

// Generate a random captcha string
$captcha = generateCaptcha();

// Save the captcha value in the session for verification later
$_SESSION['captcha'] = $captcha;

// Create the captcha image
$width = 100;
$height = 30;
$image = imagecreatetruecolor($width, $height);
$bgColor = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0);

// Fill the background color
imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

// Add random lines
$lineColor = imagecolorallocate($image, 200, 200, 200);
for ($i = 0; $i < 5; $i++) {
    imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
}

// Add random dots
$dotColor = imagecolorallocate($image, 150, 150, 150);
for ($i = 0; $i < 50; $i++) {
    imagesetpixel($image, mt_rand(0, $width), mt_rand(0, $height), $dotColor);
}

// Add the captcha text
$font = 'C:\xampp\htdocs\Sim\poppins.ttf'; // Replace with the path to your font file
$fontSize = 16;
$x = $width / 2 - $fontSize * strlen($captcha) / 2;
$y = $height / 2 + $fontSize / 2;
imagettftext($image, $fontSize, 0, $x, $y, $textColor, $font, $captcha);

// Set the image header and output the image
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);

// Function to generate a random captcha string
function generateCaptcha($length = 5)
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $captcha = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $captcha .= $characters[mt_rand(0, $max)];
    }
    return $captcha;
}
?>