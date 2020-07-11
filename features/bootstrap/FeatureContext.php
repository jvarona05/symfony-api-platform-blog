<?php

use App\DataFixtures\AppFixtures;
use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Coduo\PHPMatcher\Matcher;
use Doctrine\ORM\EntityManagerInterface;
use \Coduo\PHPMatcher\Factory\MatcherFactory;

class FeatureContext extends RestContext
{
    const USERS = [
        'admin' => 'Alosk@olsdjnkfg09sdgf'
    ];
    const AUTH_URL = '/api/login_check';
    const AUTH_JSON = '
        {
            "username": "%s",
            "password": "%s"
        }
    ';

    /**
     * @var AppFixtures
     */
    private $fixtures;

    /**
     * @var Matcher
     */
    private $matcher;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        Request $request,
        AppFixtures $fixtures,
        EntityManagerInterface $em
    ) {
        parent::__construct($request);
        $this->fixtures = $fixtures;
        $this->matcher =
            (new MatcherFactory())->createMatcher();
        $this->em = $em;
    }

    /**
     * @Given I am authenticated as :user
     * @param $user
     */
    public function iAmAuthenticatedAs($user)
    {
        $this->request->setHttpHeader('Content-Type', 'application/ld+json');
        $this->request->send(
            'POST',
            $this->locatePath(self::AUTH_URL),
            [],
            [],
            sprintf(self::AUTH_JSON, $user, self::USERS[$user])
        );

        $json = json_decode($this->request->getContent(), true);

        // Make sure the token was returned
        $this->assertTrue(isset($json['token']));

        $token = $json['token'];

        $this->request->setHttpHeader(
            'Authorization',
            'Bearer '.$token
        );
    }

    /**
     * @Then the JSON matches expected template:
     */
    public function theJsonMatchesExpectedTemplate(PyStringNode $json)
    {
        $actual = $this->request->getContent();
        var_dump($actual);
        $this->assertTrue(
            $this->matcher->match($actual, $json->getRaw())
        );
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema()
    {
        // Get entity metadata
        $classes = $this->em->getMetadataFactory()
            ->getAllMetadata();

        // Drop and create schema
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        // Load fixtures... and execute
        $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($this->em);
        $fixturesExecutor =
            new \Doctrine\Common\DataFixtures\Executor\ORMExecutor(
                $this->em,
                $purger
            );

        $fixturesExecutor->execute([
            $this->fixtures
        ]);
    }

    /**
     * @BeforeScenario @image
     */
    public function prepareImages()
    {
        copy(
            __DIR__ . '/../fixtures/Stewie.png',
            __DIR__ . '/../fixtures/files/Stewie.png'
        );
    }
}