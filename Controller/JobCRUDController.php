<?php

namespace Nz\CronBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CRUDController.
 *
 * @author  nz
 */
class JobCRUDController extends Controller
{

    /**
     * default list action
     */
    public function listAction(Request $request = null)
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        /* return $this->render('NzCrawlerBundle:CRUD:list.html.twig', array( */
        return $this->render($this->admin->getTemplate('list'), array(
                'action' => 'list',
                'mode' => 'list',
                'form' => $formView,
                'datagrid' => $datagrid,
                'csrf_token' => $this->getCsrfToken('sonata.batch'),
                ), null, $request);
    }

    /**
     * Cron Scan
     */
    public function cronScanAction(Request $request)
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'nz:cron:scan'));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();

        $this->addFlash('sonata_flash_success', $content);

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * Run job
     */
    public function runJobAction(Request $request)
    {
        $job = $this->admin->getSubject();

        if (!$job) {
            throw new NotFoundHttpException(sprintf('Unable to find the job with id : %s', $job->getId()));
        }
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => $job->getCommand()));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();

        $this->addFlash('sonata_flash_success', $content);

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * Install cron
     */
    public function installCronAction(Request $request)
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'nz:cron:install'));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();

        $this->addFlash('sonata_flash_success', $content);

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
    /**
     * Remove cron
     */
    public function removeCronAction(Request $request)
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'nz:cron:remove'));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();

        $this->addFlash('sonata_flash_success', $content);

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
