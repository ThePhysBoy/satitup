<?php
// Generate placeholder images for faculty departments
$faculties = [
    'faculty-agri' => 'คณะเกษตรศาสตร์และทรัพยากรธรรมชาติ',
    'faculty-dent' => 'คณะทันตแพทยศาสตร์',
    'faculty-ict' => 'คณะเทคโนโลยีสารสนเทศและการสื่อสาร',
    'faculty-law' => 'คณะนิติศาสตร์',
    'faculty-nurse' => 'คณะพยาบาลศาสตร์',
    'faculty-med' => 'คณะแพทยศาสตร์',
    'faculty-pharm' => 'คณะเภสัชศาสตร์',
    'faculty-polsci' => 'คณะรัฐศาสตร์และสังคมศาสตร์',
    'faculty-science' => 'คณะวิทยาศาสตร์',
    'faculty-medsci' => 'คณะวิทยาศาสตร์การแพทย์',
    'faculty-eng' => 'คณะวิศวกรรมศาสตร์',
    'faculty-libarts' => 'คณะศิลปศาสตร์',
    'faculty-arch' => 'คณะสถาปัตยกรรมศาสตร์และศิลปกรรมศาสตร์',
    'faculty-ams' => 'คณะสหเวชศาสตร์',
    'faculty-ed' => 'วิทยาลัยการศึกษา',
    'faculty-scm' => 'วิทยาลัยการจัดการ',
    'faculty-demo' => 'โรงเรียนสาธิตมหาวิทยาลัยพะเยา'
];

// Create directory if it doesn't exist
$directory = 'images/faculties';
if (!file_exists($directory)) {
    mkdir($directory, 0777, true);
}

// Define colors for faculties (pastel colors)
$colors = [
    '#FFD1DC', '#FFB6C1', '#FFC0CB', '#FF69B4', 
    '#FFFFE0', '#FFFACD', '#FAFAD2', '#FFEFD5',
    '#E0FFFF', '#AFEEEE', '#B0E0E6', '#ADD8E6',
    '#D8BFD8', '#DDA0DD', '#EE82EE', '#DA70D6',
    '#98FB98', '#90EE90', '#8FBC8F', '#3CB371'
];

// Generate placeholder images
foreach ($faculties as $filename => $name) {
    // Create an image
    $width = 400;
    $height = 300;
    $image = imagecreatetruecolor($width, $height);
    
    // Random color from the array
    $color_index = array_rand($colors);
    $color_hex = $colors[$color_index];
    
    // Convert hex to RGB
    list($r, $g, $b) = sscanf($color_hex, "#%02x%02x%02x");
    $bg_color = imagecolorallocate($image, $r, $g, $b);
    
    // Fill background
    imagefill($image, 0, 0, $bg_color);
    
    // Text color
    $text_color = imagecolorallocate($image, 50, 50, 50);
    
    // Add faculty name
    $font_size = 5;
    $text = $name;
    
    // Get text dimensions
    $text_box = imagettfbbox($font_size, 0, 'arial', $text);
    if ($text_box === false) {
        // If TTF not available, use built-in font
        $font = 5; // Built-in font size
        $text_width = imagefontwidth($font) * strlen($text);
        $text_height = imagefontheight($font);
    } else {
        $text_width = $text_box[2] - $text_box[0];
        $text_height = $text_box[1] - $text_box[7];
    }
    
    // Calculate position to center text
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2 + $text_height;
    
    // Add text to image
    if ($text_box === false) {
        // Use built-in font
        imagestring($image, $font, $x, $y, $text, $text_color);
    } else {
        // Use TTF font
        imagettftext($image, $font_size, 0, $x, $y, $text_color, 'arial', $text);
    }
    
    // Add UP logo or text
    $logo_text = "มหาวิทยาลัยพะเยา";
    if ($text_box === false) {
        $logo_font = 3;
        $logo_width = imagefontwidth($logo_font) * strlen($logo_text);
        imagestring($image, $logo_font, ($width - $logo_width) / 2, $y + 30, $logo_text, $text_color);
    } else {
        $logo_size = 3;
        $logo_box = imagettfbbox($logo_size, 0, 'arial', $logo_text);
        $logo_width = $logo_box[2] - $logo_box[0];
        imagettftext($image, $logo_size, 0, ($width - $logo_width) / 2, $y + 30, $text_color, 'arial', $logo_text);
    }
    
    // Save the image
    $filepath = $directory . '/' . $filename . '.jpg';
    imagejpeg($image, $filepath, 90);
    imagedestroy($image);
    
    echo "Created: $filepath<br>";
}

echo "All placeholder images have been created successfully!";
?>
