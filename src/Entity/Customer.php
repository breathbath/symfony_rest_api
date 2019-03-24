<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Type;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity
 * @ORM\Table(name="customer")
 * @UniqueEntity(fields={"email"}, groups={"registration"})
 */
class Customer
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(groups={"registration"})
     *
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(groups={"registration"})
     *
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Date()
     * @Type("DateTime<'Y-m-d'>")
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @SWG\Property(description="The unique identifier of the customer.")
     */
    private $uid;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday(?\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string|null
     */
    public function getUid():?string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     */
    public function setUid(string $uid): void
    {
        $this->uid = $uid;
    }
}