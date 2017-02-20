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
    public function saveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Image();

//        $id = $request->request->get('id');
//        if (null === $id || empty($id) || '' === $id) {
//            $entity = new Image();
//        } else {
//            /** @var Image $entity */
//            $entity = $em->getRepository('AtlasBundle:Image')->findOneBy(['id'=>$id]);
//            $entity->setFile(
//                new File($this->get('kernel')->getRootDir().'/../web'.$entity->getFile())
//            );
//        }

        $form = $this->createForm(ImageType::class, $entity, [
            'action' => $this->generateUrl('lch_media_image_save'),
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

        return $this->render('@App/Image/form.html.twig', [
                'form' => $form->createView(),
            ],
            (isset($response)) ? $response : null
        );
    }
}
