<?php

class CategAndMerchHandler extends UploadHandler
{
	//Return same name for all type of extensions
    protected function get_unique_filename($file_path, $name, $size, $type, $error,
            $index, $content_range)
    {
        $parts = explode('.', $name);
        $extIndex = count($parts) - 1;
        $ext = strtolower(@$parts[$extIndex]);
        $name='original.'.$ext;

        return $name;
    }
}
