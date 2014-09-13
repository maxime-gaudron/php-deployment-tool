<?php

namespace QaSystem\CoreBundle\Command;

use QaSystem\CoreBundle\Entity\Deployment;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Filesystem\Filesystem;

class DeployCommand extends ContainerAwareCommand
{
    const NAME = 'deployment:tools:deploy';

    protected function configure()
    {
        $this
            ->setName(static::NAME)
            ->setDescription('Trigger a deployment')
            ->addArgument('deploymentId', InputArgument::REQUIRED);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return Filesystem
     */
    protected function getFileSystem()
    {
        return new Filesystem();
    }

    /**
     * @param int $deploymentId
     * @return string
     */
    public static function getPidfilePath($deploymentId)
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . "php-deployment-tool-deploy-$deploymentId.pid";
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deploymentId = $input->getArgument('deploymentId');
        $pidFile = static::getPidfilePath($deploymentId);
        $filesystem = $this->getFileSystem();

        $filesystem->dumpFile($pidFile, getmypid());

        /** @var Deployment $deployment */
        $deployment = $this->getEntityManager()
            ->getRepository('QaSystemCoreBundle:Deployment')
            ->findOneById($deploymentId);

        if (is_null($deployment)) {
            throw new \RuntimeException("Deployment $deploymentId not found");
        }

        if ($deployment->getStatus() !== Deployment::STATUS_PENDING) {
            throw new \RuntimeException("Deployment $deploymentId aborted, status is not pending");
        }

        $deploymentTool = $this->getContainer()->get('qa_system_core.deployment_tool');
        $deploymentTool->deploy($deployment);

        $filesystem->remove($pidFile);
    }
}
