<?php

namespace App\Test\Controller;

use App\Entity\Bloc;
use App\Repository\BlocRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlocControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private BlocRepository $repository;
    private string $path = '/bloc/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Bloc::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Bloc index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'bloc[titre]' => 'Testing',
            'bloc[date_creation]' => 'Testing',
            'bloc[date_modification]' => 'Testing',
            'bloc[description]' => 'Testing',
            'bloc[lien]' => 'Testing',
            'bloc[texte]' => 'Testing',
            'bloc[image]' => 'Testing',
        ]);

        self::assertResponseRedirects('/bloc/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Bloc();
        $fixture->setTitre('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setDate_modification('My Title');
        $fixture->setDescription('My Title');
        $fixture->setLien('My Title');
        $fixture->setTexte('My Title');
        $fixture->setImage('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Bloc');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Bloc();
        $fixture->setTitre('My Title');
        $fixture->setDateCreation('My Title');
        $fixture->setDateModification('My Title');
        $fixture->setDescription('My Title');
        $fixture->setLien('My Title');
        $fixture->setTexte('My Title');
        $fixture->setImage('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'bloc[titre]' => 'Something New',
            'bloc[date_creation]' => 'Something New',
            'bloc[date_modification]' => 'Something New',
            'bloc[description]' => 'Something New',
            'bloc[lien]' => 'Something New',
            'bloc[texte]' => 'Something New',
            'bloc[image]' => 'Something New',
        ]);

        self::assertResponseRedirects('/bloc/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getDate_creation());
        self::assertSame('Something New', $fixture[0]->getDate_modification());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getLien());
        self::assertSame('Something New', $fixture[0]->getTexte());
        self::assertSame('Something New', $fixture[0]->getImage());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Bloc();
        $fixture->setTitre('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setDate_modification('My Title');
        $fixture->setDescription('My Title');
        $fixture->setLien('My Title');
        $fixture->setTexte('My Title');
        $fixture->setImage('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/bloc/');
    }
}
