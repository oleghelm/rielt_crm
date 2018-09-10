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
    public function multipleUploadFromUrl($files)
    {
        $fileNames = array();
        foreach($files as $file){
            $tmp = explode('/',$file);
            list($name,$format) = explode('.',$tmp[count($tmp)-1]);
            $fileName = md5(uniqid()).'.'.$format;
            $data = $this->getSslPage($file);
            file_put_contents($this->getTargetDir().$this->getCurrentDir().'/'.$fileName, $data);
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


    private function getSslPage($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
        
    }
}