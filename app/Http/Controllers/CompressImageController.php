<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JJArroyo\IloveImgLaravel\Facades\Iloveimg;
use JJArroyo\TinifyLaravel\Facades\Tinify;

use function PHPUnit\Framework\fileExists;

class CompressImageController extends Controller

{

    
    public function index(){
        $path = public_path('/storage/images/');
        if(!is_dir($path)){
            mkdir($path);
        }
        $scanned_directory = array_diff(scandir($path), array('..', '.')); 
        $files = [];
        foreach ($scanned_directory as $key => $image) {
            if(fileExists( $path.$image)){
                
                $name = basename( $path.$image);  
                $size = $this->filesizeFormatted($path.$image);

                $files[] = array("name" => $name,"size" => $size,"url" =>  '/storage/images/'.$image);
                Log::debug( $files);
            }
        }

        return view("welcome")->with("files",$files);
    }

    function filesizeFormatted($path){
        $size = filesize($path);
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    public function compressImg(Request $request){      
        
        $path = public_path('/storage/images/');
        if(!is_dir($path)){
            mkdir($path);
        }

        $scanned_directory = array_diff(scandir($path), array('..', '.'));        
        foreach ($scanned_directory as $key => $image) {
            unlink($path.$image); 
        }

        $file = $request->file('avatar');        
        $extension = $file->getClientOriginalExtension();

        $contents = file_get_contents($file);
        Storage::put('public/images/Original.'.$extension,  $contents, 'public');

        $result = Tinify::fromFile($file);
        $contents = $result->toBuffer();        
        
        if(Storage::put('public/images/Optimizada.'.$extension,  $contents, 'public')){
            $path = public_path('/storage/images/Optimizada.'.$extension);
            $myTaskResize = Iloveimg::newTask('resize');
            $myTaskResize->addFile($path);
            $myTaskResize->setResizeMode('percentage');
            $myTaskResize->setPercentage('75');                   
            // Execute the task
            $myTaskResize->execute();
            // Download the package files
            $myTaskResize->download( public_path('/storage/images/'));  
        }

        return redirect('/');
    }
}
