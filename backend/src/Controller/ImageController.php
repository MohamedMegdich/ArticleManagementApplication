<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ImageController extends AbstractController
{

    public function addImage(Request $request):JsonResponse
    {
        $photoFile = $request->files->get('photo');
        $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $originalFilename.'.'.$photoFile->guessExtension();
        $photoFile->move(
            $this->getParameter('articles_directory'),
            $newFilename
        );

        return $this->json('photo has been saved', 200, [],[]);
    }

    public function deleteImage(Request $request):JsonResponse
    {
        $photoname = $request->query->get('photoname');
        $filesystem = new Filesystem();
        $filesystem->remove($this->getParameter('articles_directory').'/'.$photoname);
        return $this->json('photo was sucessfuly deleted', 200, [],[]);
    }
    public function testImage(Request $request):JsonResponse
    {
        $projectDir = $request->getSchemeAndHttpHost();
        return $this->json($projectDir, 200, [],[]);
    }
}
