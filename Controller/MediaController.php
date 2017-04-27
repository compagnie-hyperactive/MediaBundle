<?php

namespace Lch\MediaBundle\Controller;

use Lch\MediaBundle\DependencyInjection\Configuration;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Event\DownloadEvent;
use Lch\MediaBundle\Event\PostDeleteEvent;
use Lch\MediaBundle\Event\PostPersistEvent;
use Lch\MediaBundle\Event\PostStorageEvent;
use Lch\MediaBundle\Event\PreDeleteEvent;
use Lch\MediaBundle\Event\PrePersistEvent;
use Lch\MediaBundle\LchMediaEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
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
     * @param mixed $type
     * @param bool $libraryMode
     * @return Response
     * @throws \Exception
     */
    public function listAction(Request $request, $type = Media::ALL, $libraryMode = false) {

        $registeredMediaTypes = $this->getParameter('lch.media.types');

        if($request->request->has('search')) {
            $searchParameters = $request->request->get('search');
        } else {
            $searchParameters = [];
        }

        // If type not set, select all
        if(null === $type) {
            $type = Media::ALL;
        }

        // If all selected
        if($type == Media::ALL) {
            $authorizedMediaTypes = $registeredMediaTypes;
        }

        // One registered
        else if(isset($registeredMediaTypes[$type])) {
            // Array creation with only type allowed
            $authorizedMediaTypes = [$type => $registeredMediaTypes[$type]];
        }

        // An array is registered
        else if(is_array($type)) {
            foreach($type as $typeSelected) {
                $authorizedMediaTypes = [$typeSelected => $registeredMediaTypes[$typeSelected]];
            }
        }

        // Nothing usable. Exception
        else {
            // TODO specialize exception
            throw new \Exception();
        }
        // TODO add events for listing filtering, pass types found to event
        $medias = $this->get('lch.media.manager')->getFilteredMedias($authorizedMediaTypes, $searchParameters);

        // TODO add pagination, infinite scroll

        // TODO handle generalisation for view
        // Choose CKEditor template if params in query
//        if($request->query->has("CKEditor")) {
//            $template = '@LchMedia/Media/fragments/list.ckeditor.html.twig';
//        } else {
            $template = '@LchMedia/Media/fragments/list.html.twig';
//        }
        return $this->render('@LchMedia/Media/fragments/list.html.twig', [
            'medias' => $medias,
            'type' => $type,
            'libraryMode' => $libraryMode
        ]);
    }

    /**
     * @param Request $request
     * @param $type
     * @return JsonResponse|Response
     * @throws \Exception
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

            // Throw event to act after storage
            $postStorageEvent = new PostStorageEvent($postPersistEvent->getMedia());
            $this->get('event_dispatcher')->dispatch(LchMediaEvents::POST_STORAGE, $postStorageEvent);

            // Generate thumbnail
            // TODO find an organization to avoid circular reference if calling service directly
            $templateEvent = $this->get('lch.media.manager')->getListItem($postPersistEvent->getMedia());
            $listItem = $this->renderView($templateEvent->getTemplate(), [
                'listItemEvent' => $templateEvent,
                'attributes' => []
            ]);

            // Merge success tag + prepersist + postpersist (for ID)
            return new JsonResponse(
                array_merge(['success'   => true],
                    array_merge(
                        is_array($prePersistEvent->getData()) ? $prePersistEvent->getData() : [],
                        [
                            'id' => $postPersistEvent->getMedia()->getId(),
                            'thumbnail' => $listItem,
                            'type' => $type
                        ]
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

    public function removeAction(Request $request, int $id, string $type)
    {
        if(!isset($this->getParameter('lch.media.types')[$type])) {
            // TODO throw exception type not defined
        }


        $mediaClass = $this->getParameter('lch.media.types')[$type][Configuration::ENTITY];
        $media = $this->getDoctrine()->getRepository($mediaClass)->find($id);

        if(!$media instanceof Media) {
            // TODO throw exception media not found
        }

        try {
            $preDeleteEvent = new PreDeleteEvent($media);
            $this->get('event_dispatcher')->dispatch(
                LchMediaEvents::PRE_DELETE,
                $preDeleteEvent
            );

            $this->getDoctrine()->getManager()->remove($media);
            $this->getDoctrine()->getManager()->flush();

            $postDeleteEvent = new PostDeleteEvent($preDeleteEvent->getMedia());
            $this->get('event_dispatcher')->dispatch(
                LchMediaEvents::POST_DELETE,
                $postDeleteEvent
            );
        }
        catch(\Exception $exception) {
            return new JsonResponse([
                    'error' => true,
                    'error' => $exception->getCode(),
                    'message' => $exception->getMessage()
                ]
            );
        }

        return new JsonResponse(['success' => true]);
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

        $file = new File($media->getFile()->getRealPath());

        // Authorization check
        $this->denyAccessUnlessGranted(Media::DOWNLOAD, $media);

        // Distpatch download event
        $downloadEvent = new DownloadEvent($media, $file);
        $this->get('event_dispatcher')->dispatch(
            LchMediaEvents::DOWNLOAD,
            $downloadEvent
        );

        // prepare BinaryFileResponse
        $fileName = "{$downloadEvent->getMedia()->getName()}.{$downloadEvent->getFile()->getExtension()}";
        $response = new BinaryFileResponse($downloadEvent->getFile()->getRealPath());
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName,
            iconv('UTF-8', 'ASCII//TRANSLIT', $fileName)
        );

        // Serve file
        return $response;
    }

    public function searchAction(Request $request) {
        return $this->forward('LchMediaBundle:Media:list', [
            'request' => $request,
            'type' => $request->request->get('type'),
            'libraryMode' => $request->request->get('libraryMode'),
        ]);
    }
}
