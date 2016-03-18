<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nz\CronBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class JobAdmin extends Admin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        // on top
        $collection->add('cron-scan', 'cron-scan');
        $collection->add('run-job', 'run-job');
        $collection->add('install-cron', 'install-cron');
        $collection->add('remove-cron', 'remove-cron');
        // on list
        /* $collection->add('crawl-link', $this->getRouterIdParameter() . '/crawl'); */
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('command')
            ->add('description')
            ->add('interval')
            ->add('nextRun')
            ->add('enabled')
            ->add('results')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Option', array(
                'class' => 'col-md-8',
            ))
            ->add('command')
            ->add('description')
            ->add('interval')
            ->add('nextRun')
            ->add('enabled')
            ->end()

        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            /* ->add('custom', 'string', array('template' => 'SonataNewsBundle:Admin:list_post_custom.html.twig', 'label' => 'Post')) */
            ->addIdentifier('command')
            ->add('description')
            ->add('enabled', null, array('editable' => true))
            /*       custom actions     */
            ->add('_action', 'run', array(
                'actions' => array(
                    'Run' => array(
                        'template' => 'NzCronBundle:CRUD:list__action_job.html.twig'
                    )
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {

        $datagridMapper
            ->add('command')
            ->add('enabled')
            ->add('description')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        /*
          if (!$childAdmin && !in_array($action, array('edit'))) {
          return;
          }
          d('here');
         */
        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_cron_scan'), array('uri' => $admin->generateUrl('cron-scan'))
        );
        $menu->addChild(
            $this->trans('sidemenu.link_cron_install'), array('uri' => $admin->generateUrl('install-cron'))
        );
        $menu->addChild(
            $this->trans('sidemenu.link_cron_remove'), array('uri' => $admin->generateUrl('remove-cron'))
        );
        /*

          $menu->addChild(
          $this->trans('sidemenu.link_view_comments'), array('uri' => $admin->generateUrl('sonata.news.admin.comment.list', array('id' => $id)))
          );

          if ($this->hasSubject() && $this->getSubject()->getId() !== null) {
          $menu->addChild(
          $this->trans('sidemenu.link_view_post'), array('uri' => $admin->getRouteGenerator()->generate('sonata_news_view', array('permalink' => $this->permalinkGenerator->generate($this->getSubject()))))
          );
          }
         */
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($option)
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($option)
    {
        
    }
}
