<?php
namespace CsnTranslation\I18n\Translator\Loader;

use Doctrine\ORM\AbstractQuery;
use Zend\I18n\Translator\Loader\RemoteLoaderInterface;
use Zend\I18n\Translator\Plural\Rule as PluralRule;
use Zend\I18n\Translator\TextDomain;

class DoctrineTranslationLoader implements RemoteLoaderInterface
{
    protected $entityManager;

   public function __construct($em)
   {
       $this->entityManager = $em;
   }

    /**
     * @param string $locale
     * @param string $domain
     * @return TextDomain
     */
    public function load($locale, $domain)
    {
        $textDomain   = new TextDomain();
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query        = $queryBuilder->select('l')
                                     ->from('CsnTranslation\Entity\Locale', 'l')
                                     ->where('l.locale = :locale')
                                     ->setParameters(array(':locale' => $locale))
                                     ->getQuery();

        try {
            $localeInformation = $query->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (\Exception $e) {
            throw new \Exception(
                sprintf("Duplicate locale entry detected ('%s').", $locale)
            );
        }

        if (!count($localeInformation)) {
            return $textDomain;
        }

        if (strlen($localeInformation['pluralForms'])) {
            try {
                $textDomain->setPluralRule(
                    PluralRule::fromString($localeInformation['pluralForms'])
                );
            } catch (\Exception $e) {
                throw new \Exception(
                    sprintf("Incorrect plural rule detected for locale '%s'.", $locale)
                );
            }
        }
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query = $queryBuilder->select('t')
            ->from('CsnTranslation\Entity\Translation', 't')
            ->join('t.locale', 'l')
            ->where('t.domain = :domain AND l.id = :locale')
            ->setParameters(array('domain' => $domain, 'locale' => $localeInformation['id']))
            ->getQuery();

        $translations = $query->getResult(AbstractQuery::HYDRATE_ARRAY);

        foreach ($translations as $message) {
            if (is_int($message['pluralIndex'])) {
                if (!isset($textDomain[$message['token']])) {
                    $textDomain[$message['token']] = array();
                }
                if (isset($textDomain[$message['token']]) && !is_array($textDomain[$message['token']])) {
                    throw new Exception\InvalidArgumentException(
                        'Plural entries must be have unique keys from singular forms.'
                    );
                }
                if (isset($textDomain[$message['token']][$message['pluralIndex']])) {
                    throw new \Exception(
                        "Duplicate plural entry detected."
                    );
                }

                $textDomain[$message['token']][$message['pluralIndex']] = $message['translation'];
            } else {
                if (isset($textDomain[$message['token']])) {
                    throw new \Exception(
                        sprintf(
                            "Singular entries must be have unique keys from both singular and plural forms " .
                            "(locale '%s', '%s', '%s')",
                            $locale,
                            $domain,
                            $message['token']
                        )
                    );
                }

                $textDomain[$message['token']] = $message['translation'];
            }
        }

        return $textDomain;
    }
}