<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Algolia\AlgoliaSearch\Api\SearchClient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Website\Application;
use Doctrine\Website\Controllers\AtomController;
use Doctrine\Website\Controllers\BlogController;
use Doctrine\Website\Controllers\DocumentationController;
use Doctrine\Website\Controllers\HomepageController;
use Doctrine\Website\Controllers\PartnersController;
use Doctrine\Website\Controllers\ProjectController;
use Doctrine\Website\Controllers\SitemapController;
use Doctrine\Website\DataSources\ArrayDataSource;
use Doctrine\Website\DataSources\BlogPosts;
use Doctrine\Website\DataSources\DbPrefill\Partners;
use Doctrine\Website\DataSources\DbPrefill\Projects;
use Doctrine\Website\DataSources\DbPrefill\SimpleSource;
use Doctrine\Website\DataSources\SitemapPages;
use Doctrine\Website\Docs\RST\Guides;
use Doctrine\Website\Docs\RST\GuidesParser;
use Doctrine\Website\Docs\RST\RSTBuilder;
use Doctrine\Website\Github\GithubClientProvider;
use Doctrine\Website\Guides\Compiler\GlobMenuFixerTransformer;
use Doctrine\Website\Guides\Compiler\SidebarTransformer;
use Doctrine\Website\Guides\ReferenceResolver\HtmlResolver;
use Doctrine\Website\Guides\StaticWebsiteGenerator\GuidesMarkdownConverter;
use Doctrine\Website\Guides\StaticWebsiteGenerator\GuidesRstConverter;
use Doctrine\Website\Model\BlogPost;
use Doctrine\Website\Model\DoctrineUser;
use Doctrine\Website\Model\Partner;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\SitemapPage;
use Doctrine\Website\Projects\ProjectDataRepository;
use Doctrine\Website\Repositories\BlogPostRepository;
use Doctrine\Website\Repositories\DoctrineUserRepository;
use Doctrine\Website\Repositories\PartnerRepository;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\SitemapPageRepository;
use Doctrine\Website\Requests\PartnerRequests;
use Doctrine\Website\Requests\ProjectRequests;
use Doctrine\Website\Requests\ProjectVersionRequests;
use Doctrine\Website\StaticGenerator\Controller\ControllerProvider;
use Doctrine\Website\StaticGenerator\Request\RequestCollectionProvider;
use Doctrine\Website\StaticGenerator\Site;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFileBuilder;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFileFilesystemReader;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFileRenderer;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFileRepository;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFileRouteReader;
use Doctrine\Website\StaticGenerator\Twig\RoutingExtension;
use Doctrine\Website\StaticGenerator\Twig\StringTwigRenderer;
use Doctrine\Website\StaticGenerator\Twig\TwigRenderer;
use Doctrine\Website\Twig\MainExtension;
use Doctrine\Website\Twig\ProjectExtension;
use Github\Api\Repo;
use Github\Client;
use Highlight\Highlighter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Parsedown;
use phpDocumentor\Guides\NodeRenderers\TemplateNodeRenderer;
use phpDocumentor\Guides\Nodes\DocumentNode;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;

return static function (ContainerConfigurator $container): void {
    $services   = $container->services();
    $parameters = $container->parameters();
    $container->import('services/orm.php');

    $services->defaults()
        ->private()
        ->autowire()
        ->autoconfigure()
        ->bind('$rootDir', '%doctrine.website.root_dir%')
        ->bind('$env', '%doctrine.website.env%')
        ->bind('$projectsDir', '%doctrine.website.projects_dir%')
        ->bind('$docsDir', '%doctrine.website.docs_dir%')
        ->bind('$sourceDir', '%doctrine.website.source_dir%')
        ->bind('$projectsData', '%doctrine.website.projects_data%')
        ->bind('$webpackBuildDir', '%doctrine.website.webpack_build_dir%')
        ->bind('$projectIntegrationTypes', '%doctrine.website.project_integration.types%')
        ->bind('$packagistUrlFormat', 'https://packagist.org/packages/%s.json')
        ->bind('$dbPrefills', tagged_iterator('doctrine.website.db_prefill'))
        ->bind('$templatesDir', '%doctrine.website.templates_dir%')
        ->bind('$routes', '%doctrine.website.routes%');

    $services->load('Doctrine\\Website\\', '../lib/*');

    $services->set(SourceFileBuilder::class)
        ->args([
            service(SourceFileRenderer::class),
            service(Filesystem::class),
            [service(GuidesRstConverter::class), service(GuidesMarkdownConverter::class)],
            ['/\/api\//'],
        ]);

    $services->set(StringTwigRenderer::class)
        ->autowire(false)
        ->args([
            '%doctrine.website.templates_dir%',
            [service(MainExtension::class), service(ProjectExtension::class), service(RoutingExtension::class)],
        ]);

    $services->alias(TwigRenderer::class, StringTwigRenderer::class);

    $services->set(SourceFileRepository::class)
        ->args([[service(SourceFileFilesystemReader::class), service(SourceFileRouteReader::class)]]);

    $services->set(ControllerProvider::class)
        ->args([[service(AtomController::class), service(BlogController::class), service(DocumentationController::class), service(HomepageController::class), service(PartnersController::class), service(ProjectController::class), service(SitemapController::class)]]);

    $services->set(RequestCollectionProvider::class)
        ->args([[service(PartnerRequests::class), service(ProjectRequests::class), service(ProjectVersionRequests::class)]]);

    $services->alias(Site::class, \Doctrine\Website\Site::class);

    $services->set(Application::class)
        ->public()
        ->autowire();

    $services->set(ProjectDataRepository::class)
        ->autowire();

    $services->set(GithubClientProvider::class)
        ->args([
            inline_service(Client::class),
            service(CacheItemPoolInterface::class),
            '%doctrine.website.github.http_token%',
        ]);

    $services->set(Repo::class)
        ->factory([service(GithubClientProvider::class), 'repositories']);

    $services->set(Highlighter::class)
        ->autowire();

    $services->set(Parsedown::class, Parsedown::class)
        ->autowire();

    $services->set(\Symfony\Component\Console\Application::class)
        ->autowire();

    $services->set(Filesystem::class)
        ->autowire();

    $services->set(ArgumentResolver::class)
        ->autowire();

    $services->set(CacheItemPoolInterface::class, FilesystemAdapter::class)
        ->autowire(false)
        ->args([
            '',
            0,
            '%doctrine.website.cache_dir%/fscache',
        ]);

    $services->set('SendGrid', 'SendGrid')
        ->autowire()
        ->args(['%doctrine.website.send_grid.api_key%']);

    $services->set(SearchClient::class)
        ->autowire(false)
        ->args([
            '%doctrine.website.algolia.app_id%',
            '%doctrine.website.algolia.admin_api_key%',
        ])
        ->factory([null, 'create']);

    $services->set(\Doctrine\Website\Site::class)
        ->args([
            '%doctrine.website.title%',
            '%doctrine.website.subtitle%',
            '%doctrine.website.url%',
            '%doctrine.website.keywords%',
            '%doctrine.website.description%',
            '%doctrine.website.env%',
            '%doctrine.website.google_analytics_tracking_id%',
            '%doctrine.website.assets_url%',
        ]);

    $services->set(BlogPostRepository::class)
        ->public()
        ->autowire(false)
        ->args([BlogPost::class])
        ->factory([service(EntityManagerInterface::class), 'getRepository']);

    $services->set(DoctrineUserRepository::class)
        ->public()
        ->autowire(false)
        ->args([DoctrineUser::class])
        ->factory([service(EntityManagerInterface::class), 'getRepository']);

    $services->set(PartnerRepository::class)
        ->public()
        ->autowire(false)
        ->args([Partner::class])
        ->factory([service(EntityManagerInterface::class), 'getRepository']);

    $services->set(ProjectRepository::class)
        ->public()
        ->autowire(false)
        ->args([Project::class])
        ->factory([service(EntityManagerInterface::class), 'getRepository']);

    $services->set(SitemapPageRepository::class)
        ->autowire(false)
        ->args([SitemapPage::class])
        ->factory([service(EntityManagerInterface::class), 'getRepository']);

    $services->set('doctrine.website.data_sources.db_prefill.doctrine_user', SimpleSource::class)
        ->args([
            DoctrineUser::class,
            inline_service(ArrayDataSource::class)
                ->args(['%doctrine.website.doctrine_users%']),
            service(EntityManagerInterface::class),
        ])
        ->tag('doctrine.website.db_prefill');

    $services->set('doctrine.website.data_sources.db_prefill.sitemap_pages', SimpleSource::class)
        ->args([
            SitemapPage::class,
            service(SitemapPages::class),
            service(EntityManagerInterface::class),
        ])
        ->tag('doctrine.website.db_prefill');

    $services->set('doctrine.website.data_sources.db_prefill.blog_posts', SimpleSource::class)
        ->args([
            BlogPost::class,
            service(BlogPosts::class),
            service(EntityManagerInterface::class),
        ])
        ->tag('doctrine.website.db_prefill');

    $services->set('doctrine.website.data_sources.db_prefill.projects', Projects::class)
        ->args([
            service(\Doctrine\Website\DataSources\Projects::class),
            service(EntityManagerInterface::class),
        ])
        ->tag('doctrine.website.db_prefill');

    $services->set('doctrine.website.data_sources.db_prefill.partners', Partners::class)
        ->args([
            inline_service(ArrayDataSource::class)
                ->args(['%doctrine.website.partners%']),
            service(EntityManagerInterface::class),
        ])
        ->tag('doctrine.website.db_prefill');

    $services->set(MainExtension::class)
        ->tag('twig.extension');

    $services->set(Guides::class);

    $services->set(GuidesParser::class)
        ->public();

    $services->set(RSTBuilder::class)
        ->args(['$builder' => service(Guides::class)]);

    $services->set(EventDispatcherInterface::class, EventDispatcher::class);

    $services->set(HtmlResolver::class)
        ->tag('phpdoc.guides.reference_resolver');

    $services->set(GlobMenuFixerTransformer::class)
        ->tag('phpdoc.guides.compiler.nodeTransformers');

    $services->set(SidebarTransformer::class)
        ->tag('phpdoc.guides.compiler.nodeTransformers');

    $services->set(GuidesRstConverter::class)
        ->args(['$nodeRenderer' => service('page_renderer')]);

    $services->set(GuidesMarkdownConverter::class)
        ->args(['$nodeRenderer' => service('page_renderer')]);

    $services->set('page_renderer', TemplateNodeRenderer::class)
        ->args([
            '$template' => 'website-document.html.twig',
            '$nodeClass' => DocumentNode::class,
        ]);

    $services->alias(LoggerInterface::class, 'logger');

    $services->set('logger', Logger::class)
        ->args(['$name' => 'docs'])
        ->call('pushHandler', [service(StreamHandler::class)]);

    $services->set(StreamHandler::class)
        ->args(['php://stderr']);
};
