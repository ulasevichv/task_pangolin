<?php
namespace App\Helpers;

class FileHelper
{
    public static function getFileNames(string $targetDirPath, bool $recursive = false, bool $returnAbsolutePaths = false): array
    {
        if (!is_dir($targetDirPath)) {
            throw new \Exception('Invalid dir path: ' . $targetDirPath);
        }

        $ds = DIRECTORY_SEPARATOR;
        $fileNames = [];

        $dirHandle = opendir($targetDirPath);

        while (($fileOrDirName = readdir($dirHandle)) !== false) {
            if ($fileOrDirName == '.' || $fileOrDirName == '..') {
                continue;
            }

            $fileOrDirPath = $targetDirPath . $ds . $fileOrDirName;

            if (is_file($fileOrDirPath)) {
                $fileNames[] = $fileOrDirName;
            } elseif ($recursive && is_dir($fileOrDirPath)) {
                $subDirFileNames = static::getFileNames($fileOrDirPath, true);
                foreach ($subDirFileNames as $subDirFileName) {
                    $fileNames[] = $fileOrDirName . $ds . $subDirFileName;
                }
            }
        }

        closedir($dirHandle);

        if ($returnAbsolutePaths) {
            foreach ($fileNames as &$fileName) {
                $fileName = $targetDirPath . $ds . $fileName;
            }
        }

        return $fileNames;
    }
}