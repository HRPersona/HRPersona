<?php

namespace Persona\Hris\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Persona\Hris\Component\Skill\Model\SkillGroupInterface;
use Persona\Hris\Util\StringUtil;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="skill_groups", indexes={@ORM\Index(name="skill_groups_idx", columns={"name"})})
 *
 * @ApiResource(
 *     attributes={
 *         "filters"={
 *             "name.search"
 *         },
 *         "normalization_context"={"groups"={"read"}},
 *         "denormalization_context"={"groups"={"write"}}
 *     }
 * )
 *
 * @UniqueEntity("name")
 *
 * @author Muhamad Surya Iksanudin <surya.iksanudin@hrpersona.id>
 */
class SkillGroup implements SkillGroupInterface
{
    /**
     * @Groups({"read", "write"})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     *
     * @var string
     */
    private $id;

    /**
     * @Groups({"write", "read"})
     * @ORM\ManyToOne(targetEntity="Persona\Hris\Entity\SkillGroup", fetch="EAGER")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @ApiSubresource()
     *
     * @var SkillGroupInterface
     */
    private $parent;

    /**
     * @Groups({"read", "write"})
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getId(): string
    {
        return (string) $this->id;
    }

    /**
     * @return SkillGroupInterface
     */
    public function getParent(): ? SkillGroupInterface
    {
        return $this->parent;
    }

    /**
     * @param SkillGroupInterface $parent
     */
    public function setParent(SkillGroupInterface $parent = null): void
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = StringUtil::uppercase($name);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if ($this->getParent()) {
            return sprintf('%s => %s', $this->getParent()->getName(), $this->getName());
        }

        return $this->getName();
    }
}
