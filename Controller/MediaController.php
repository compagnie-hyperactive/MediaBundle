<?php

namespace Lch\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MediaController extends Controller implements MediaControllerInterface
{

    public function listAction(Request $request) {
        
        // TODO add events for listing filtering
        $mediaTypes = $this->get('lch.media_bundle.media_type_finder')->find();
        
        // TODO pass types found to event for filtering by type
        $medias = $this->get('lch.media_bundle.media_manager')->filter($mediaTypes);
//        $medias = $this->getDoctrine()->getRepository('')->findAll();

        // TODO add pagination, infinite scroll
        // Choose CKEditor template if params in query
        if($request->query->has("CKEditor")) {
            $template = '@LchMedia/Image/list.ckeditor.html.twig';
        } else {
            $template = '@LchMedia/Image/list.html.twig';
        }
        return $this->render($template, [
            'images' => $medias
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

    public function editAction()
    {
        // TODO: Implement editAction() method.
    }

    public function removeAction()
    {
        // TODO: Implement removeAction() method.
    }

}
