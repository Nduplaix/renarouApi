<?php


namespace App\Subscriber;


use App\Entity\Banner;
use App\Entity\Image;
use App\Service\UploadService;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PostImageSubscriber implements EventSubscriberInterface
{

    /** @var UploadService */
    private $uploadService;

    /** @var RequestStack */
    private $request;

    public function __construct(UploadService $uploadService, RequestStack $request)
    {
        $this->uploadService = $uploadService;
        $this->request = $request;
    }

    public static function getSubscribedEvents()
    {
        return array(
            EasyAdminEvents::PRE_PERSIST => array('postImage'),
            EasyAdminEvents::PRE_UPDATE => array('postImage'),
            EasyAdminEvents::PRE_DELETE  => array('deleteImage'),
        );
    }

    function postImage(GenericEvent $event) {
        $result = $event->getSubject();
        $method = $event->getArgument('request')->getMethod();

        if (! $result instanceof Image || $method !== Request::METHOD_POST) {
            $this->postBanner($event);
            return;
        }

        if ($result->getImage() === null && $result->getPath()) {
            $url = $this->uploadService->moveImage($result->getPath(), $result->getProduct()->getId());
            $result->setPath($url);
            $result->setLink($this->request->getCurrentRequest()->getUriForPath($url));
        }

        if ($result->getImage() instanceof UploadedFile) {
            $url = $this->uploadService->saveImage($result->getImage(), $result->getProduct()->getId());
            $this->uploadService->deleteImage($result->getPath());
            $result->setPath($url);
            $result->setLink($this->request->getCurrentRequest()->getUriForPath($url));
        }
    }

    function deleteImage(GenericEvent $event)
    {
        $result = $event->getArgument('request')->query->get('id');
        $method = $event->getArgument('request')->getMethod();
        $type = $event->getArgument('request')->query->get('entity');

        if ( $type !== 'Product' || $method !== Request::METHOD_DELETE) {
            $this->deleteBanner($event);
            return;
        }
        die;
        $this->uploadService->deleteProductImages($result);
    }

    function postBanner(GenericEvent $event)
    {
        $result = $event->getSubject();
        $method = $event->getArgument('request')->getMethod();

        if (! $result instanceof Banner || $method !== Request::METHOD_POST) {
            return;
        }

        if ($result->getImageUpload() === null && $result->getImage()) {
            $url = $this->uploadService->moveImage($result->getImage(), $result->getLabel());
            $result->setImage($url);
            $result->setLink($this->request->getCurrentRequest()->getUriForPath($url));
        }

        if ($result->getImageUpload() instanceof UploadedFile) {
            $url = $this->uploadService->saveImage($result->getImageUpload(), $result->getLabel());
            $this->uploadService->deleteImage($result->getImage());
            $result->setImage($url);
            $result->setLink($this->request->getCurrentRequest()->getUriForPath($url));
        }
    }

    function deleteBanner(GenericEvent $event)
    {
        $result = $event->getArgument('request')->query->get('label');
        $method = $event->getArgument('request')->getMethod();
        $type = $event->getArgument('request')->query->get('entity');

        if (! $type === 'Banner' || $method !== Request::METHOD_DELETE) {
            return;
        }

        $this->uploadService->deleteProductImages($result);
    }
}
