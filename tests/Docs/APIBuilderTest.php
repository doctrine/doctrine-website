<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\APIBuilder;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class APIBuilderTest extends TestCase
{
    /** @var ProcessFactory|MockObject */
    private $processFactory;

    /** @var string */
    private $projectsDir;

    /** @var string */
    private $sculpinSourcePath;

    /** @var APIBuilder|MockObject */
    private $apiBuilder;

    protected function setUp() : void
    {
        $this->processFactory    = $this->createMock(ProcessFactory::class);
        $this->projectsDir       = '/data/doctrine';
        $this->sculpinSourcePath = '/data/doctrine-website/source';

        $this->apiBuilder = $this->getMockBuilder(APIBuilder::class)
            ->setConstructorArgs([
                $this->processFactory,
                $this->projectsDir,
                $this->sculpinSourcePath,
            ])
            ->setMethods(['filePutContents', 'unlinkFile'])
            ->getMock();
    }

    public function testBuildAPIDocs() : void
    {
        $project = new Project([
            'slug' => 'orm',
            'repositoryName' => 'orm',
            'codePath' => '/src',
        ]);
        $version = new ProjectVersion(['slug' => '2.0', 'branchName' => '2.0']);

        $configContent = <<<CONFIG
<?php

use Sami\RemoteRepository\GitHubRemoteRepository;

return new Sami\Sami('/data/doctrine/orm/src', [
    'build_dir' => '/data/doctrine-website/source/api/orm/2.0',
    'cache_dir' => '/data/doctrine/orm/cache',
    'remote_repository' => new GitHubRemoteRepository('doctrine/orm', '/data/doctrine/orm'),
    'versions' => '2.0',
]);
CONFIG;

        $this->apiBuilder->expects(self::once())
            ->method('filePutContents')
            ->with('/data/doctrine/orm/sami.php', $configContent);

        $this->processFactory->expects(self::once())
            ->method('run')
            ->with('php /data/doctrine-website/source/../sami.phar update /data/doctrine/orm/sami.php --verbose');

        $this->apiBuilder->expects(self::once())
            ->method('unlinkFile')
            ->with('/data/doctrine/orm/sami.php');

        $this->apiBuilder->buildAPIDocs($project, $version);
    }
}
