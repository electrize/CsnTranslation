<?php

namespace CsnTranslation\Entity;

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine ORM implementation of Translation entity
 *
 * @ORM\Entity
 * @ORM\Table(name="`translation`")
 * @Annotation\Name("Translation")
 */
class Translation
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
    * @var HotelRome\Entity\Location
    *
    * @ORM\ManyToOne(targetEntity="CsnTranslation\Entity\Locale")
    * @ORM\JoinColumn(name="locale_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
    * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
    * @Annotation\Filter({"name":"StripTags"})
    * @Annotation\Filter({"name":"StringTrim"})
    * @Annotation\Options({
    *   "label":"Language:",
    *   "empty_option": "",
    *   "target_class":"CsnTranslation\Entity\Locale",
    *   "property": "name"
    * })
    */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", nullable=false)
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Options({
     *   "label":"Domain:",
     * })
     */
    protected $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="text", nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Options({
     *   "label":"Original text:",
     * })
     */
    protected $token;

    /**
     * @var string
     *
     * @ORM\Column(name="translation", type="text", nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Options({
     *   "label":"Translation:",
     * })
     */
    protected $translation;

    /**
     * @var string
     *
     * @ORM\Column(name="plural_index", type="string", nullable=true)
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Options({
     *   "label":"Plural Index:",
     * })
     */
    protected $pluralIndex;

    public function __construct() {
        $this->translate('Language:', 'csntranslation');
        $this->translate('Original text:', 'csntranslation');
        $this->translate('Translation:', 'csntranslation');
    }

    private function translate() {}

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
     * @return Translation
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
    
    /**
     * Set domain
     *
     * @param  TYPE $domain
     * @return Translation
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Set token
     *
     * @param  string $token
     * @return Translation
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get translation
     *
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }
    
    /**
     * Set translation
     *
     * @param  string $translation
     * @return Translation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;
        return $this;
    }

    /**
     * Get pluralIndex
     *
     * @return string
     */
    public function getPluralIndex()
    {
        return $this->pluralIndex;
    }
    
    /**
     * Set pluralIndex
     *
     * @param  string $pluralIndex
     * @return Translation
     */
    public function setPluralIndex($pluralIndex)
    {
        $this->pluralIndex = $pluralIndex;
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
