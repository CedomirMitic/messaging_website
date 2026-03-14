<?php

namespace App\Traits;

trait CloudinaryUpload
{
    /**
     * @param $file - File which we are uploading
     * @param $folder - Target folder (expl. 'posts', 'avatars', 'banners')
     */
    public function uploadToCloudinary($file, $folder)
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $timestamp = time();

        // Signed paramethers
        $paramsToSign = "folder=$folder&timestamp=$timestamp";
        $signature = sha1($paramsToSign . $apiSecret);

        $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/image/upload");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new \CURLFile($file->getRealPath()),
            'timestamp' => $timestamp,
            'api_key' => $apiKey,
            'signature' => $signature,
            'folder' => $folder
        ]);

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response['secure_url'] ?? null;
    }

    /**
     * @param $imageUrl - Whole url from database
     * @param $folder - Folder in which photo is located
     */
    public function deleteFromCloudinary($imageUrl, $folder)
    {
        if (!$imageUrl) return;

        // Taking out name from url
        $path = parse_url($imageUrl, PHP_URL_PATH);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        
        // Public ID must contain folder so that cloudinary knows what to delete
        $publicId = $folder . '/' . $filename;

        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $timestamp = time();

        $paramsToSign = "public_id=$publicId&timestamp=$timestamp";
        $signature = sha1($paramsToSign . $apiSecret);

        $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/image/destroy");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key' => $apiKey,
            'signature' => $signature,
        ]);

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response;
    }
}