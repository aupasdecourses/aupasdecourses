<?php
class Apdc_Neighborhood_Helper_Media extends Mage_Core_Helper_Abstract
{
    protected $mediaDir = 'apdc/apdc_neighborhood';

    /**
     * uploadMedia
     *
     * @param string $fileIndex fileIndex
     *
     * @return array
     */
    public function uploadMedia($fileIndex)
    {
        if (!empty($_FILES[$fileIndex]) && $_FILES[$fileIndex]['error'] === 0) {
            $result = array();
            $saveTo =  Mage::getBaseDir('media') . DS . $this->mediaDir . DS;
            try{
                $uploader = new Varien_File_Uploader($fileIndex);

                //this sets the allowed extension Types, in this case we’re uploading images and videos.
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png', 'mp4'));

                //this allows the uploader to rename files if it has to. Essentially, if you’re uploading an image,
                //where you could run into a conflict, this should be true. But if you’re uploading a pdf for
                // instance, you’ll want to replace that pdf next time it’s uploaded.
                $uploader->setAllowRenameFiles(true);

                //this allows the uploader place this is a dispersed folder structure. I.e. an image like
                // “prod-5.jpg” would be put in p/r/prod-5.jpg. Turning this off will insure that it only
                // gets put in the base directory.
                $uploader->setFilesDispersion(false);
                $result = $uploader->save($saveTo);
                if ($result == false) {
                    Mage::throwException(Mage::helper('apdc_neighborhood')->__("Media Upload Failed: Make sure PHP can write to: ". $saveTo));
                }
                $result['file'] = $this->mediaDir . DS . $result['file'];
                $result['media_type'] = $this->getMediaType($result['file']);

                return $result;
            }
            catch(Exception $e){
                throw new Exception(Mage::helper('apdc_neighborhood')->__("Media Upload Failed: Make sure PHP can write to: ". $saveTo), 0, $e);
            }
        }

        return false;
    }

    /**
     * deleteMedia
     * delete media and all associated resized images
     *
     * @param string $file file
     *
     * @return bool
     */
    public function deleteMedia($file)
    {
        // delete the file
        try {
            $filePath = Mage::getBaseDir('media') . DS . $file;
            @unlink($filePath);

            $mediaName = $this->getMediaName($file);

            // Delete all resized images :
            $imgResizedDir = Mage::getBaseDir('media') . DS . $this->mediaDir . DS . 'resized';

            //check folder exist
            if(is_dir($imgResizedDir)){
                // Create recursive dir iterator which skips dot folders
                $dir = new RecursiveDirectoryIterator(
                    $imgResizedDir,
                    FilesystemIterator::SKIP_DOTS
                );

                // Flatten the recursive iterator, folders come before their files
                $it  = new RecursiveIteratorIterator(
                    $dir,
                    RecursiveIteratorIterator::SELF_FIRST
                );

                // Maximum depth is 1 level deeper than the base folder
                $it->setMaxDepth(1);

                // Basic loop displaying different messages based on file or folder
                foreach ($it as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        if ($fileinfo->getFilename() === $mediaName) {
                            unlink($it->getPath() . DS . $fileinfo->getFilename());
                        }
                    }
                }
            }
        } catch(Exception $e) {
            Mage::throwException(Mage::helper('apdc_neighborhood')->__('Impossible de supprimer les medias'));
        }

        return true;
    }

    /**
     * getMediaDirName
     *
     * @return string
     */
    public function getMediaDirName()
    {
        return $this->mediaDir;
    }

    /**
     * getMediaDir
     *
     * @param string $file file
     *
     * @return string
     */
    public function getMediaDir($file)
    {
        return Mage::getBaseDir('media') . DS . $file;
    }

    /**
     * getMediaName
     *
     * @param string $file file
     *
     * @return string
     */
    public function getMediaName($file)
    {
        return substr(strrchr($file, "/"), 1);
    }
    /**
     * getMediaUrl
     *
     * @param string $file   : filename
     * @param array  $resize : array('width' => (int)100, 'height' => (int)100);
     *
     * @return string
     */
    public function getMediaUrl($file, $resize = array())
    {
        $mediaType = $this->getMediaType($file);
        if (empty($resize) || $mediaType != 'image') {
            return Mage::getBaseUrl('media') . $file;
        }

        $width = (isset($resize['width']) && $resize['width'] > 0 ? $resize['width'] : null);
        $height = (isset($resize['height']) && $resize['height'] > 0 ? $resize['height'] : null);

        // get subfolder
        $subfolder = '';
        if ($width) {
            $subfolder .= 'w' . $width;
        }
        if ($height) {
            $subfolder .= 'h' . $height;
        }

        $destDir = Mage::getBaseDir('media') . DS . $this->mediaDir . DS . 'resized';

        if (!file_exists($destDir)) {
            mkdir($destDir, 0777);
        }

        $destDir .= DS . $subfolder;
        // create folder
        if (!file_exists($destDir)) {
            mkdir($destDir, 0777);
        }

        // get image name
        $imageName = $this->getMediaName($file);

        // resized image path
        $imageResized = $destDir . DS . $imageName;

        // changing image url into direct path
        $dirImg = $this->getMediaDir($file);

        // if resized image doesn't exist, save the resized image to the resized directory
        if (!file_exists($imageResized) && file_exists($dirImg)) {
            $imageObj = new Varien_Image($dirImg);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->resize($width, $height);
            $imageObj->save($imageResized);
        }

        return Mage::getBaseUrl('media') . $this->mediaDir . DS . 'resized' . DS . $subfolder . DS . $imageName;
    }

    /**
     * CheckMediaUrl
     *
     * @param string $filename filename
     *
     * @return void
     */
    public function checkMediaUrl($filename) {
        if (!empty($filename)) {
            $file = Mage::getBaseDir('media') . DS . $filename;
            if (file_exists($file) ) {
                return true;
            }
        }

        return false;
    }
    /**
     * getMediaType
     *
     * @param string $filename filename
     *
     * @return void
     */
    public function getMediaType($filename)
    {
        $imageExt = array('jpg','jpeg','gif','png');
        $videoExt = array('mp4');

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array($extension, $imageExt)) {
            return 'image';
        } else if (in_array($extension, $videoExt)) {
            return 'video';
        } else {
            return 'file';
        }
    }
}
