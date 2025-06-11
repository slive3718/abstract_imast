<?php

namespace App\Libraries;

class Upload
{

    public function doUpload($file, $filePath, $savePath, $fileName) {
        // Check if files array is not empty
        if (!empty($file)) {
                // Check if the file is valid and not moved
                if ($file->isValid() && !$file->hasMoved()) {
                    // Generate a new name for the file
                    $newName = $file->getRandomName();

                    // Check if the upload directory exists. If it does, move the uploaded file to that directory
                    if (is_dir($savePath)) {
                        if ($file->move($savePath, $newName)) {
                            $uploadResults = [
                                'new_name' => $newName,
                                'file_name' => $fileName,
                                'savePath' => $savePath,
                                'filePath' => $filePath
                            ];
                        } else {
                            return 'error';
                        }
                    } else {
                        // If the directory does not exist, create the directory and move the uploaded file to it
                        if (mkdir($savePath, 0777, true)) {
                            if ($file->move($savePath, $newName)) {
                                $uploadResults = [
                                    'new_name' => $newName,
                                    'file_name' => $fileName,
                                    'savePath' => $savePath,
                                    'filePath' => $filePath
                                ];
                            } else {
                                return 'error';
                            }
                        } else {
                            return 'error';
                        }
                    }
                } else {
                    // If the uploaded file is not valid, return an error message
                    return 'invalid';
                }
            return $uploadResults;
        }
        return 'Missing File';
    }

}