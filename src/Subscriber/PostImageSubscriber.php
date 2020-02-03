<?php


namespace App\Subscriber;


use App\Entity\Image;
use App\Entity\Product;
use App\Service\UploadService;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PostImageSubscriber implements EventSubscriberInterface
{

    /** @var UploadService */
    private $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public static function getSubscribedEvents()
    {
        return array(
            EasyAdminEvents::PRE_PERSIST => array('postImage'),
            EasyAdminEvents::PRE_DELETE  => array('deleteImage'),
        );
    }

    function postImage(GenericEvent $event) {
        $result = $event->getSubject();
        $method = $event->getArgument('request')->getMethod();

        if (! $result instanceof Image || $method !== Request::METHOD_POST) {
            return;
        }

        if ($result->getImage() instanceof UploadedFile) {
            $url = $this->uploadService->saveImage($result->getImage(), $result->getProduct()->getId());
            $result->setPath($url);
        }
    }

    function deleteImage(GenericEvent $event)
    {
        $result = $event->getArgument('request')->query->get('id');
        $method = $event->getArgument('request')->getMethod();
        $type = $event->getArgument('request')->query->get('entity');

        if (! $type === 'Product' || $method !== Request::METHOD_DELETE) {
            return;
        }

        $this->uploadService->deleteImage($result);
    }
}
