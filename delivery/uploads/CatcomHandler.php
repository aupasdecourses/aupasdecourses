<?php
/*
 * jQuery File Upload Plugin PHP Class
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

class CatcomHandler extends UploadHandler
{

    //Return same name for all type of extensions
    protected function get_unique_filename($file_path, $name, $size, $type, $error,
            $index, $content_range)
    {
        if (strpos($name, '.') === false &&
                preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
            $name .= '.'.$matches[1];
        }
        if ($this->options['correct_image_extensions'] &&
                function_exists('exif_imagetype')) {
            switch (@exif_imagetype($file_path)){
                case IMAGETYPE_JPEG:
                    $extensions = array('jpg', 'jpeg');
                    break;
                case IMAGETYPE_PNG:
                    $extensions = array('png');
                    break;
                case IMAGETYPE_GIF:
                    $extensions = array('gif');
                    break;
            }
            // Adjust incorrect image file extensions:
            if (!empty($extensions)) {
                $parts = explode('.', $name);
                $extIndex = count($parts) - 1;
                $ext = strtolower(@$parts[$extIndex]);
                if (!in_array($ext, $extensions)) {
                    $parts[$extIndex] = $extensions[0];
                    $name = 'original.'.$parts[$extIndex];
                }
            }
        }

        return $name;
    }
}
