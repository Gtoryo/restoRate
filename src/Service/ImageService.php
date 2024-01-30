<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $image, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        // On donner un nouveau nom au fichier (Optionnel mais très pratique)

        $fichier = md5(uniqid(rand(), true)) .  '.webp';

        // On récupère les infos de l'image comme : ses dimensions

        $image_infos = getimagesize($image);

        if($image_infos === false) {

            throw new Exception('Foramt d\'image non pris en charge');

        }

        //On vérifie le format d'image

        switch ($image_infos['mime']) {
            case 'image/png':
                $image_source = imagecreatefrompng($image);
                break;
            case 'image/jpeg':
                $image_source = imagecreatefromjpeg($image);
                break;
            case 'image/webp':
                $image_source = imagecreatefromwebp($image);
                break;
            default:
                throw new Exception('Format d\'image non pris en charge: ' . $image_infos['mime']);
        } 
        
        // On recadre l'image

        // On récupère les dimensions de l'image

        $imageWidth = $image_infos[0];
        $imageHeight = $image_infos[1];

        // On vérifie l'orientation de l'image

        switch($imageWidth <=> 1){

            case -1: // On est en portrait

                $squareSise = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSise)/2;
                break;

            case 0: // c'est un carré

                $squareSise = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;

            case 1: // On est en paysage

            $squareSise = $imageWidth;
            $src_x = ($imageHeight - $squareSise)/2;
            $src_y = 0;
            break;
        }

        // On crée une nouvelle image vièrge dans laquelle on va venir coller notre image

        $resized_image = imagecreatetruecolor($width, $height);

        // On fait la copie

        imagecopyresampled($resized_image, $image_source, 0, 0, $src_x, $src_y, $width, $height, $squareSise, $squareSise);

        // On récupère le path de notre image

        $path = $this->params->get('image_directory') . $folder;

        // On créer le dossier de destination si il n'existe pas

        if(!file_exists($path . '/mini/')) {

            mkdir($path . '/mini/', 0755, true);
        }

        // On stock l'image recadrée

        imagewebp($resized_image, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        $image->move($path . '/', $fichier);

        return $fichier;
    } 


    public function delete(string $fichier, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        if ($fichier !== 'default.webp') {
            $success = false;
            $path = $this->params->get('image_directory') . $folder;

            // Suppression de la miniature
            $miniature = $path . '/mini/' . $width . 'x' . $height . '-' . $fichier;
            $this->deleteFile($miniature);

            // Suppression de l'originale
            $originale = $path . '/' . $fichier;
            $this->deleteFile($originale);

            return true;
        }

        return false;
    }

    private function deleteFile(string $filePath)
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}