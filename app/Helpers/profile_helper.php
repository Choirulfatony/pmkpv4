<?php

if (!function_exists('get_profile_picture')) {
    /**
     * Get the appropriate profile picture URL
     * Handles Google profile pictures by caching them locally to avoid rate limiting
     *
     * @param string $profilePic The profile picture URL from session
     * @param int $userId The user ID for filename generation
     * @param string $namaLengkap The user's full name for fallback filename
     * @return string The URL to use for the profile picture
     */
    function get_profile_picture($profilePic, $userId = 0, $namaLengkap = '')
    {
        // If no profile picture, return default
        if (!$profilePic) {
            return base_url('assets/adminlte/img/logorssmnew.png');
        }

        // Check if it's a Google profile picture
        if (strpos($profilePic, 'googleusercontent') !== false) {
            // Generate filename based on user ID and timestamp
            $filename = 'user_' . $userId . '_' . time() . '.jpg';
            
            // Define paths
            $webPath = '/uploads/profile_pics/' . $filename;
            $filePath = FCPATH . 'uploads/profile_pics/' . $filename;
            
            // Ensure directory exists
            $dir = FCPATH . 'uploads/profile_pics/';
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Check if we already have a recent copy (less than 24 hours old)
            $latestFile = null;
            $latestTime = 0;
            
            if (is_dir($dir)) {
                $files = glob($dir . 'user_' . $userId . '_*.jpg');
                foreach ($files as $file) {
                    $fileTime = filemtime($file);
                    // Extract timestamp from filename
                    if (preg_match('/user_' . $userId . '_(\d+)\.jpg$/', basename($file), $matches)) {
                        $fileTime = (int)$matches[1];
                    }
                    
                    if ($fileTime > $latestTime) {
                        $latestTime = $fileTime;
                        $latestFile = $file;
                    }
                }
            }
            
            // If we have a recent copy (less than 24 hours old), use it
            if ($latestFile && (time() - $latestTime) < 86400) { // 24 hours
                return base_url('uploads/profile_pics/' . basename($latestFile));
            }
            
            // Otherwise, try to download and cache the image
            try {
                // Initialize cURL
                $ch = curl_init($profilePic);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                
                $imageData = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                // If successful and we got image data
                if ($httpCode == 200 && $imageData !== false && strlen($imageData) > 0) {
                    // Save the image
                    if (file_put_contents($filePath, $imageData) !== false) {
                        return base_url($webPath);
                    }
                }
            } catch (Exception $e) {
                // Log error but continue to fallback
                log_message('error', 'Failed to cache Google profile picture: ' . $e->getMessage());
            }
            
            // If we couldn't download/cache, fall back to default
            return base_url('assets/adminlte/img/logorssmnew.png');
        }
        
        // If it's not a Google URL, treat it as a local path
        return base_url($profilePic);
    }
}