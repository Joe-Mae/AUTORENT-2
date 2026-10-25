<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

/**
 * Assainissement des images uploadées (spécification §8) :
 * ré-encodage (les métadonnées EXIF/GPS disparaissent), limitation de
 * la largeur et compression. Les PDF sont stockés tels quels.
 */
class UploadSanitizer
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public function __construct(private readonly ImageManager $manager = new ImageManager(new Driver)) {}

    /**
     * Stocke un fichier proprement : image ré-encodée sans métadonnées,
     * PDF inchangé. Retourne le chemin sur le disque demandé.
     */
    public function store(UploadedFile $file, string $directory, string $disk): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, self::IMAGE_EXTENSIONS)) {
            return $file->store($directory, $disk);
        }

        $image = $this->manager->decodePath($file->getRealPath());
        $image->scaleDown(width: 1600);

        // Ré-encodage JPEG : les EXIF (dont géolocalisation) sont perdues.
        $bytes = (string) $image->encode(new JpegEncoder(quality: 80));

        $path = trim($directory, '/').'/'.Str::random(40).'.jpg';
        Storage::disk($disk)->put($path, $bytes);

        return $path;
    }
}
