<?php
session_start();

// Function to generate a random string
function generateCaptchaText($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $captchaText = '';
    for ($i = 0; $i < $length; $i++) {
        $captchaText .= $characters[mt_rand(0, strlen($characters) - 1)];
    }
    return $captchaText;
}

// Function to create the CAPTCHA image
function createCaptchaImage($text) {
    $width = 250;  // Increased width for more complexity
    $height = 80;  // Increased height for more complexity
    $image = imagecreatetruecolor($width, $height);

    $backgroundColor = imagecolorallocate($image, 255, 255, 255); // White background
    $textColor = imagecolorallocate($image, 0, 0, 0); // Black text
    $lineColor = imagecolorallocate($image, 200, 200, 200); // Light gray lines
    $noiseColor = imagecolorallocate($image, 150, 150, 150); // Noise color

    imagefilledrectangle($image, 0, 0, $width, $height, $backgroundColor);

    // Add some random noise
    for ($i = 0; $i < 200; $i++) {
        imagesetpixel($image, mt_rand(0, $width), mt_rand(0, $height), $noiseColor);
    }

    // Draw random lines
    for ($i = 0; $i < 6; $i++) {
        imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
    }

    // Add the text with slight distortion
    $font = __DIR__ . '/arial.ttf'; // Ensure this path is correct or adjust as needed
    $fontSize = 30;
    $textBox = imagettfbbox($fontSize, 0, $font, $text);
    $textWidth = abs($textBox[2] - $textBox[0]);
    $textHeight = abs($textBox[5] - $textBox[1]);
    $x = ($width - $textWidth) / 2;
    $y = ($height + $textHeight) / 2;

    // Add text with distortion
    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        $angle = mt_rand(-30, 30); // Random angle for distortion
        $displacementY = mt_rand(-10, 10); // Random vertical displacement
        imagettftext($image, $fontSize, $angle, $x, $y + $displacementY, $textColor, $font, $char);
        $x += $textWidth / strlen($text);
    }

    // Output the image
    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
}

// Generate CAPTCHA text
$captchaText = generateCaptchaText();
$_SESSION['captcha_text'] = $captchaText;

// Create CAPTCHA image
createCaptchaImage($captchaText);
?>
