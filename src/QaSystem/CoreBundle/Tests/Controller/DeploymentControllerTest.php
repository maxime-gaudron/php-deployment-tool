<?php

namespace QaSystem\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeploymentControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/deployment/');

        foreach ($client->getContainer()->getParameter('tasks') as $task) {
            $this->assertCount(1, $crawler->filter(sprintf('html:contains("%s")', $task['name'])));
        }
    }
}