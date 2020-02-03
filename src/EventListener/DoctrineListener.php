<?php


namespace App\EventListener;


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

    public function postPersist(LifecycleEventArgs $args)
    {
        $result = $args->getObject();
        if ($result instanceof Product || $result instanceof Category || $result instanceof SubCategory) {
            $this->em->flush();
        }
    }
}
