<?php
namespace App\Filament\Resources\TestUploadResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TestUploadResource;
use Illuminate\Support\Facades\Storage;
use App\Models\TestUpload;
use Intervention\Image\ImageManagerStatic as Image;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = TestUploadResource::class;
    public static bool $public = true;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        $model = new (static::getModel());

        $data = $request->all();

        if ($request->hasFile('upload_url')) {
            $file = $request->file('upload_url');
            $optimizedImage = Image::make($file)->encode('webp', 90);
            $filename = uniqid() . '.webp';
            $path = Storage::disk('public')->put($filename, $optimizedImage);
            $data['upload_url'] = str_replace('/storage/', '', Storage::url($filename));
        } elseif (!empty($data['remote_url'])) {
            $data['upload_url'] = str_replace('/storage/', '', TestUpload::saveImageFromUrl($data['remote_url']));
            unset($data['remote_url']);
        }

        $model->fill($data);

        $model->save();

        return static::sendSuccessResponse($model, "Berhasil Membuat Sumber Daya");
    }
}
