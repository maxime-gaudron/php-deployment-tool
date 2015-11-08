<?php

namespace QaSystem\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Yaml\Yaml;

class DeploymentControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/deployment/');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Response is not successful');

        foreach (Yaml::parse(__DIR__.'/../Fixtures/tasks/echo.yml')['tasks'] as $task) {
            $this->assertCount(1, $crawler->filter(sprintf('html:contains("%s")', $task['name'])));
        }
    }
}
