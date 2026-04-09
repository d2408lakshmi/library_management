<?php
// This line includes the Composer autoloader, which makes all installed libraries available.
require_once 'vendor/autoload.php';

// Bring the barcode generator class into the current namespace.
use Picqer\Barcode\BarcodeGeneratorPNG;

// Check if a 'code' parameter was provided in the URL (e.g., generate_barcode.php?code=KIT1004VNS)
if (isset($_GET['code']) && !empty($_GET['code'])) {

    // Get the barcode text from the URL.
    $code = $_GET['code'];

    // Create an instance of the barcode generator. We'll generate a PNG image.
    $generator = new BarcodeGeneratorPNG();

    try {
        // Generate the barcode image data.
        // Parameters: text, barcode type, width factor, height, color
        // TYPE_CODE_128 is the same as BCGcode128.
        $barcode_image = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 50);

        // Set the HTTP header to tell the browser it's receiving a PNG image.
        header('Content-Type: image/png');

        // Output the raw image data.
        echo $barcode_image;

    } catch (Exception $e) {
        // If the barcode generation fails, you can output an error message.
        // For a real application, you might log this error instead.
        header('Content-Type: text/plain');
        echo 'Error generating barcode: ' . $e->getMessage();
    }

} else {
    // If no code is provided, send a bad request response.
    header("HTTP/1.0 400 Bad Request");
    echo "Barcode text not provided.";
}