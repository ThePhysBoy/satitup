<?php
require_once 'includes/video_functions.php';

// Test cases
$test_ids = [
    'dQw4w9WgXcQ',  // Direct ID
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',  // Full URL
    'https://youtu.be/dQw4w9WgXcQ',  // Short URL
    '',  // Empty
    'invalid'  // Invalid
];

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Test YouTube Thumbnails</title>
    <style>
        .test-container {
            max-width: 800px;
            margin: 20px auto;
            font-family: Arial, sans-serif;
        }
        .test-item {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .test-item img {
            max-width: 320px;
            display: block;
            margin: 10px 0;
            border: 1px solid #ccc;
        }
        .code {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>YouTube Thumbnail Test</h1>
        
        <?php foreach ($test_ids as $test): ?>
            <div class="test-item">
                <h3>Test Input:</h3>
                <div class="code"><?php echo htmlspecialchars($test ?: '(empty)'); ?></div>
                
                <h3>Video ID:</h3>
                <div class="code">
                    <?php 
                    $id = getYoutubeVideoId($test);
                    echo $id ? htmlspecialchars($id) : '(none)';
                    ?>
                </div>
                
                <h3>Thumbnail URL:</h3>
                <div class="code">
                    <?php 
                    $thumb = getYoutubeThumbnail($test);
                    echo htmlspecialchars($thumb);
                    ?>
                </div>
                
                <h3>Result:</h3>
                <img src="<?php echo $thumb; ?>" alt="Thumbnail">
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
