<?php

namespace CsnTranslation\Entity;

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine ORM implementation of Locale entity
 *
 * @ORM\Entity
 * @ORM\Table(name="`locale`")
 * @Annotation\Name("Locale")
 */
class Locale
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=15, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", nullable=false)
     * @Annotation\Options({
     *   "label":"Locale:",
     * })
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="plural_forms", type="string", nullable=false)
     * @Annotation\Options({
     *   "label":"Plural forms:",
     * })
     */
    protected $pluralForms;

    public function __toString() {
		return $this->name;
	}

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set name
     *
     * @param  string $name
     * @return Locale
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * Set locale
     *
     * @param  string $locale
     * @return Locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get pluralForms
     *
     * @return string
     */
    public function getPluralForms()
    {
        return $this->pluralForms;
    }
    
    /**
     * Set pluralForms
     *
     * @param  string $pluralForms
     * @return Locale
     */
    public function setPluralForms($pluralForms)
    {
        $this->pluralForms = $pluralForms;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
