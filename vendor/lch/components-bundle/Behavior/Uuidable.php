<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 24/02/19
 * Time: 17:11
 */

namespace Lch\ComponentsBundle\Behavior;


use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

trait Uuidable
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     *
     * @Groups({"uuid"})
     */
    protected $id;

    /**
     * @return UuidInterface
     */
    public function getId(): ?UuidInterface
    {
        return $this->id;
    }
}