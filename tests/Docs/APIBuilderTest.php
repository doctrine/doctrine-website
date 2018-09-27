<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\APIBuilder;
use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class APIBuilderTest extends TestCase
{
    /** @var ProcessFactory|MockObject */
    private $processFactory;

    /** @var string */
    private $projectsPath;

    /** @var string */
    private $sculpinSourcePath;

    /** @var APIBuilder|MockObject */
    private $apiBuilder;

    protected function setUp() : void
    {
        $this->processFactory    = $this->createMock(ProcessFactory::class);
        $this->projectsPath      = '/data/doctrine';
        $this->sculpinSourcePath = '/data/doctrine-website/source';

        $this->apiBuilder = $this->getMockBuilder(APIBuilder::class)
            ->setConstructorArgs([
                $this->processFactory,
                $this->projectsPath,
                $this->sculpinSourcePath,
            ])
            ->setMethods(['filePutContents', 'unlinkFile'])
            ->getMock();
    }

    public function testBuildAPIDocs() : void
    {
        $project = new Project([
            'slug' => 'orm',
            'repositoryName' => 'doctrine2',
            'codePath' => '/src',
        ]);
        $version = new ProjectVersion(['slug' => '2.0', 'branchName' => '2.0']);

        $configContent = <<<CONFIG
<?php

use Sami\RemoteRepository\GitHubRemoteRepository;

return new Sami\Sami('/data/doctrine/doctrine2/src', [
    'build_dir' => '/data/doctrine-website/source/api/orm/2.0',
    'cache_dir' => '/data/doctrine/doctrine2/cache',
    'remote_repository' => new GitHubRemoteRepository('doctrine/doctrine2', '/data/doctrine/doctrine2'),
    'versions' => '2.0',
]);
CONFIG;

        $this->apiBuilder->expects(self::once())
            ->method('filePutContents')
            ->with('/data/doctrine/doctrine2/sami.php', $configContent);

        $this->processFactory->expects(self::once())
            ->method('run')
            ->with('php /data/doctrine-website/source/../sami.phar update /data/doctrine/doctrine2/sami.php --verbose');

        $this->apiBuilder->expects(self::once())
            ->method('unlinkFile')
            ->with('/data/doctrine/doctrine2/sami.php');

        $this->apiBuilder->buildAPIDocs($project, $version);
    }
}
