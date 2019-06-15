<?php


namespace App\Http\Controllers\Api\Admin\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Junity\Hashids\Facades\Hashids;
use Facades\{ App\Facades\Media as MediaManager };
use MediaUploader;


class BaseController extends Controller
{
    /**
    * success response method.
    *
    * @return \Illuminate\Http\Response
    */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }


    /**
    * return error response.
    *
    * @return \Illuminate\Http\Response
    */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function store_media($model, $media, $name, $surname){
        $file_preferences = config('file_preferences');
        if($media){
    
            $media_type = current(explode('/', $media->getMimeType()));
            $media_filename= pathinfo($media->getClientOriginalName(), PATHINFO_FILENAME);

            if($media_type == 'image'){
                foreach($file_preferences['sizes'] as $key => $dimension){
                    $resized_media = \Image::make($media)->resize($dimension[0], $dimension[1])->encode('jpg', $file_preferences['quality']);
                    $m_resized_media = MediaUploader::fromString($resized_media)->toDestination('public', $file_preferences['images_folder'])->useFilename($media_filename . '-' . $key)->upload();
                    $model->attachMedia($m_resized_media, [$key]);
                }
    
                foreach($file_preferences['sizes'] as $key => $dimension){
                    $resized_media = \Image::make($media);
                    
                    $width =  $resized_media->width();
                    $height = $resized_media->height();
    
                    $resized_media->text($name, $width - ($width / 3), $height - ($height / 4), function($font) {
                        $font->file(resource_path('fonts/OpenSans-Semibold.ttf'));
                        $font->size(50);
                        $font->align('center');
                        $font->valign('top');
                    });

                    $resized_media->text('        ' . $surname, $width - ($width / 3), $height - ($height / 6), function($font) {
                        $font->file(resource_path('fonts/OpenSans-Semibold.ttf'));
                        $font->size(50);
                        $font->align('center');
                        $font->valign('top');
                    })->resize($dimension[0], $dimension[1])->encode('jpg', $file_preferences['quality']);

                    $m_resized_media = MediaUploader::fromString($resized_media)->toDestination('public', $file_preferences['images_folder'])->useFilename($media_filename . '-' . $key . '-with-name')->upload();
                    $model->attachMedia($m_resized_media, [$key . '-with-name']);
                }
            }
        } else {
            $media_filename = 'default';

            foreach($file_preferences['sizes'] as $key => $dimension){
                $media = \Image::make(resource_path('img/default.png'));
                $resized_media = \Image::make($media)->resize($dimension[0], $dimension[1])->encode('jpg', $file_preferences['quality']);
                $m_resized_media = MediaUploader::fromString($resized_media)->toDestination('public', $file_preferences['images_folder'])->useFilename($media_filename . '-' . $key)->upload();
                $model->attachMedia($m_resized_media, [$key]);
            }

            foreach($file_preferences['sizes'] as $key => $dimension){
                $resized_media = \Image::make(resource_path('img/default.png'));
                
                $width =  $resized_media->width();
                $height = $resized_media->height();

                $resized_media->text($name, $width - ($width / 3), $height - ($height / 4), function($font) {
                    $font->file(resource_path('fonts/OpenSans-Semibold.ttf'));
                    $font->size(50);
                    $font->align('center');
                    $font->valign('top');
                });

                $resized_media->text('        ' . $surname, $width - ($width / 3), $height - ($height / 6), function($font) {
                    $font->file(resource_path('fonts/OpenSans-Semibold.ttf'));
                    $font->size(50);
                    $font->align('center');
                    $font->valign('top');
                })->resize($dimension[0], $dimension[1])->encode('jpg', $file_preferences['quality']);

                $m_resized_media = MediaUploader::fromString($resized_media)->toDestination('public', $file_preferences['images_folder'])->useFilename($media_filename . '-' . $key . '-with-name')->upload();
                $model->attachMedia($m_resized_media, [$key . '-with-name']);
            }
        }
    }
}
