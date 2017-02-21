<?php

namespace Lch\MediaBundle\Controller;

use AppBundle\Entity\Image;
use Lch\MediaBundle\Form\ImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    public function listAction(Request $request) {
        // TODO handle differents media types
        // TODO add events for listing filtering
        // TODO add pagination, infinite scroll
        $images = $this->getDoctrine()->getRepository('AppBundle:Image')->findAll();

        return $this->render('@LchMedia/Image/list.html.twig', [
            'images' => $images
        ]);
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Image();
        $form = $this->createForm(ImageType::class, $entity, [
            'action' => $this->generateUrl('lch_media_image_add'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            if ($form->isValid()) {

                if (null !== $entity->getFile()) {

                    $fileName = $this->get('lch.media_bundle.image_manager')->upload($entity);
                    $entity->setFile($fileName);
                }

                if (null === $entity->getId()) {
                    $em->persist($entity);
                }

                $em->flush();

                $response = new JsonResponse();
                $response->setData(array(
                    'id' => $entity->getId(),
                    'name' => $entity->getName(),
                    'url' => $entity->getFile(),
                ));

                return $response;
            } else {
                $response = new Response(
                    'Content',
                    Response::HTTP_BAD_REQUEST,
                    array('content-type' => 'text/html')
                );
            }
        }

        return $this->render('@LchMedia/Image/add.html.twig', [
                'form' => $form->createView(),
            ],
            (isset($response)) ? $response : null
        );
    }
}
