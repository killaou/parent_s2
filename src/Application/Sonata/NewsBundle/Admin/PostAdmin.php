<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\NewsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Sonata\CoreBundle\Model\ManagerInterface;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Permalink\PermalinkInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class PostAdmin extends Admin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var Pool
     */
    protected $formatterPool;

    /**
     * @var PermalinkInterface
     */
    protected $permalinkGenerator;

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('author')
            ->add('enabled')
            ->add('title')
            ->add('abstract')
            ->add('content', null, array('safe' => true))
            ->add('tags')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array(
                    'class' => 'col-md-8'
                ))
                ->add('author', 'sonata_type_model_list')
                ->add('title')
                ->add('abstract', null, array('attr' => array('rows' => 5)))
                ->add('content', 'sonata_formatter_type', array(
                    'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
                    'format_field'   => 'contentFormatter',
                    'source_field'   => 'rawContent',
                    'source_field_options'      => array(
                        'horizontal_input_wrapper_class' => $this->getConfigurationPool()->getOption('form_type') == 'horizontal' ? 'col-lg-12': '',
                        'attr' => array('class' => $this->getConfigurationPool()->getOption('form_type') == 'horizontal' ? 'span10 col-sm-10 col-md-10': '', 'rows' => 20)
                    ),
                    'ckeditor_context'     => 'news',
                    'target_field'   => 'content',
                    'listener'       => true,
                ))
            ->end()
                ->with('Options', array(
                        'class' => 'col-md-4'
                    ))
                    ->add('enabled', null, array('required' => false))
                    ->add('image', 'sonata_type_model_list', array('required' => false), array(
                        'link_parameters' => array(
                            'context' => 'news'
                        )
                    ))

                    ->add('publicationDateStart', 'sonata_type_datetime_picker', array('dp_side_by_side' => true))
                    ->add('commentsCloseAt', 'sonata_type_datetime_picker', array('dp_side_by_side' => true))
                    ->add('commentsEnabled', null, array('required' => false))
                    ->add('commentsDefaultStatus', 'sonata_news_comment_status', array('expanded' => true))
                ->end()

            ->with('Tags', array(
                'class' => 'col-md-4'
                ))
                ->add('tags', 'sonata_type_model', array(
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ))
                ->add('collection', 'sonata_type_model_list', array('required' => false))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('custom', 'string', array('template' => 'SonataNewsBundle:Admin:list_post_custom.html.twig', 'label' => 'Post'))
            ->add('onHomePage', null, array('editable' => true, 'label' => 'Afficher sur la page d\'accueil'))
            ->add('publicationDateStart')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $that = $this;

        $datagridMapper
            ->add('title')
            ->add('enabled')
##            ->add('tags', null, array('field_options' => array('expanded' => true, 'multiple' => true)))
            ->add('author')

        ;
    }

    /**
     * {@inheritdoc}
     *
    public function getWithOpenCommentFilter($queryBuilder, $alias, $field, $value)
    {
        if (!is_array($data) || !$data['value']) {
            return;
        }

        $queryBuilder->leftJoin(sprintf('%s.comments', $alias), 'c');
        $queryBuilder->andWhere('c.status = :status');
        $queryBuilder->setParameter('status', CommentInterface::STATUS_MODERATE);
    }*/

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_edit_post'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_view_comments'),
            array('uri' => $admin->generateUrl('sonata.news.admin.comment.list', array('id' => $id)))
        );

        if ($this->hasSubject() && $this->getSubject()->getId() !== null) {
            $menu->addChild(
                $this->trans('sidemenu.link_view_post'),
                array('uri' => $admin->getRouteGenerator()->generate('sonata_news_view', array('permalink' => $this->permalinkGenerator->generate($this->getSubject()))))
            );
        }
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager($userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param \Sonata\FormatterBundle\Formatter\Pool $formatterPool
     *
     * @return void
     */
    public function setPoolFormatter(FormatterPool $formatterPool)
    {
        $this->formatterPool = $formatterPool;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($post)
    {
        $post->setContent($this->formatterPool->transform($post->getContentFormatter(), $post->getRawContent()));
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($post)
    {
        $post->setContent($this->formatterPool->transform($post->getContentFormatter(), $post->getRawContent()));
    }

    /**
     * @param PermalinkInterface $permalinkGenerator
     */
    public function setPermalinkGenerator(PermalinkInterface $permalinkGenerator)
    {
        $this->permalinkGenerator = $permalinkGenerator;
    }
}
