<?php


namespace App\EventListener;


use App\Entity\BasketLine;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class DoctrineListener
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $result = $args->getObject();
        if ($result instanceof BasketLine) {
            $this->em->flush();
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $result = $args->getObject();
        if ($result instanceof BasketLine) {
            $this->em->flush();
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $result = $args->getObject();
        if ($result instanceof Product || $result instanceof Category || $result instanceof SubCategory || $result instanceof BasketLine) {
            $this->em->flush();
        }
    }
}
