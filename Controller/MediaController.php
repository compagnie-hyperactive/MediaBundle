<?php

namespace Lch\MediaBundle\Controller;

use Lch\MediaBundle\DependencyInjection\Configuration;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Event\DownloadEvent;
use Lch\MediaBundle\Event\PostPersistEvent;
use Lch\MediaBundle\Event\PrePersistEvent;
use Lch\MediaBundle\LchMediaEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class MediaController extends Controller // implements MediaControllerInterface
{

    /**
     * @param Request $request
     * @param string $type
     * @return Response
     * @throws \Exception
     */
    public function listAction(Request $request, $type = Media::ALL) {

        $registeredMediaTypes = $this->getParameter('lch.media.types');

        if($type == Media::ALL) {
            $authorizedMediaTypes = $registeredMediaTypes;
        } else if(isset($registeredMediaTypes[$type])) {
            // Array creation with only type allowed
            $authorizedMediaTypes = [$type => $registeredMediaTypes[$type]];
        } else {
            // TODO specialize exception
            throw new \Exception();
        }
        // TODO add events for listing filtering, pass types found to event
        $medias = $this->get('lch.media.manager')->getFilteredMedias($authorizedMediaTypes);

        // TODO add pagination, infinite scroll
        // TODO handle generalisation for view
        // Choose CKEditor template if params in query
        if($request->query->has("CKEditor")) {
            $template = '@LchMedia/Media/fragments/list.ckeditor.html.twig';
        } else {
            $template = '@LchMedia/Media/fragments/list.html.twig';
        }
        return $this->render($template, [
            'medias' => $medias,
            'choose' => true
        ]);
    }

    /**
     * @param Request $request
     * @param $type
     * @return JsonResponse|Response
     */
    public function addAction(Request $request, $type)
    {
        if(!isset($this->getParameter('lch.media.types')[$type])) {
            // TODO throw exception type not defined
        }

        $mediaClass = $this->getParameter('lch.media.types')[$type][Configuration::ENTITY];
        $mediaReflection = new \ReflectionClass($mediaClass);
        
        /**
         * @var Media
         */
        $mediaEntity = $mediaReflection->newInstance();

        if(!$mediaEntity instanceof Media) {
            // TODO specialise
            throw new \Exception();
        }

        $mediaForm = $this->createForm(
            $this->getParameter('lch.media.types')[$type][Configuration::FORM],
            $mediaEntity,
            [ 'action' => $this->generateUrl('lch_media_add', ['type' => $type])]
        );

        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            
            // Dispatch pre-persist event
            // - Allow different media types listener to correctly persist media according to its customisations
            $prePersistEvent = new PrePersistEvent($mediaEntity);
            $this->get('event_dispatcher')->dispatch(
                LchMediaEvents::PRE_PERSIST,
                $prePersistEvent
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($prePersistEvent->getMedia());
            $em->flush();

            // Dispatch post-persist event
            // - Once media correctly saved and stored, allow different media types listener to perform post-operations (thumbnail generations...) and retrieve ID for DataTransformer
            $postPersistEvent = new PostPersistEvent($prePersistEvent->getMedia());
            $this->get('event_dispatcher')->dispatch(
                LchMediaEvents::POST_PERSIST,
                $postPersistEvent
            );

            // Merge success tag + prepersist + postpersist (for ID)
            return new JsonResponse(
                array_merge(['success'   => true],
                    array_merge(
                        $prePersistEvent->getData(),
                        ['id' => $postPersistEvent->getMedia()->getId() ]
                    )
                )
            );
        }

        return $this->render($this->getParameter('lch.media.types')[$type][Configuration::ADD_VIEW], [
            'mediaForm' => $mediaForm->createView(),
            ]
        );
    }

//    /**
//     * @param Request $request
//     * @param int $id
//     * @return Response
//     * @throws \Exception
//     */
//    public function getThumbnailAction(Request $request, $id){
//        if($request->isXmlHttpRequest()) {
//            return $this->render("@LchMedia/Image/fragments/thumbnail.html.twig", [
//                    'media' => $media,
//                ]
//            );
//        } else {
//            // TODO SPecialize
//            throw new \Exception();
//        }
//    }

    public function editAction()
    {
        // TODO: Implement editAction() method.
    }

    public function removeAction()
    {
        // TODO: Implement removeAction() method.
    }

    /**
     * @param $id
     * @param $class
     * @return BinaryFileResponse
     * @throws \Exception
     */
    public function downloadAction($id, $class) {

        $media = $this->getDoctrine()->getRepository($class)->find($id);

        // check media is a media
        if(!$media instanceof Media) {
            // TODO Specialize
            throw new \Exception();
        }

        // Check storability
        if(!$this->get('lch.media.uploader')->checkStorable($media)) {
            // TODO Specialize
            throw new \Exception();
        }

        $basePath = $this->container->getParameter('kernel.root_dir') . '/../web';
        $filePath = $basePath . $media->getFile();

        $file = new File($filePath);

        // Authorization check
        $this->denyAccessUnlessGranted(Media::DOWNLOAD, $media);

        // Distpatch download event
        $downloadEvent = new DownloadEvent($media, $file);
        $this->get('event_dispatcher')->dispatch(
            LchMediaEvents::DOWNLOAD,
            $downloadEvent
        );

        // prepare BinaryFileResponse
        $response = new BinaryFileResponse($downloadEvent->getFile()->getRealPath());
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $downloadEvent->getFile()->getFilename(),
            iconv('UTF-8', 'ASCII//TRANSLIT', $downloadEvent->getFile()->getFilename())
        );

        // Serve file
        return $response;
    }

}
