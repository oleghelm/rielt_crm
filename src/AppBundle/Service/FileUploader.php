<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDir;
    private $currentDir;


    public function __construct(string $targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getTargetDir().$this->getCurrentDir(), $fileName);

        return $fileName;
    }
    
    public function multipleUpload($files)
    {
        $fileNames = array();
        
        foreach($files as $file){
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            $file->move($this->getTargetDir().$this->getCurrentDir(), $fileName);
            
            $fileNames[] = $this->getCurrentDir().'/'.$fileName;
        }

        return $fileNames;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }
    
    public function setTargetDir(string $targetDir)
    {
        $this->targetDir = $targetDir;
    }
    function getCurrentDir() {
        return $this->currentDir;
    }

    function setCurrentDir($currentDir) {
        $this->currentDir = $currentDir;
    }


}