<?php

namespace CsnTranslation;

return array(
    'controllers' => array(
        'invokables' => array(
            'CsnTranslation\Controller\Translation' => 'CsnTranslation\Controller\TranslationController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin-translation' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/admin/translation[/:action][/:id][/:filter]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                       '__NAMESPACE__' => 'CsnTranslation\Controller',
                        'controller' => 'Translation',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
            ),
            'translation-search-paginator' => array(
                'type' => 'Segment',
                    'options' => array(
                        'route' => '/admin/translation/search[/page-:page][/:filter]',
                        'constraints' => array(
                                'page' => '[0-9]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'CsnTranslation\Controller',
                        'controller' => 'Translation',
                        'action' => 'search',
                        'page' => 1,
                    ),
                ),
            ),
            'csntranslation-paginator' => array(
                'type' => 'Segment',
                    'options' => array(
                        'route' => '/admin/translation[/:page]',
                        'constraints' => array(
                                'page' => '[0-9]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'CsnTranslation\Controller',
                        'controller' => 'Translation',
                        'action' => 'index',
                        'page' => 1,
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'csn-translation' => __DIR__ . '/../view'
        ),
        'template_map' => array(
            __NAMESPACE__.'_paginator' => __DIR__ . '/../view/csn-translation/partial/paginator.phtml',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'csnTranslationFormBuilder' => 'CsnTranslation\Service\Factory\TranslationFormFactory',
        ),
    ),
    'translator' => array(
        'remote_translation' => array(
            array(
                'type' => 'DoctrineTranslationLoader', 
                'text_domain' => 'hotel'
            ),
        ),
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
                'text_domain' => 'csntranslation',
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                )
            )
        )
    ),
    'csn_translation_options' => array(
        'default_locale' => 'bg_BG',
    ),
);
