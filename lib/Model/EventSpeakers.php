<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\SkeletonMapper\ObjectManagerInterface;
use Doctrine\Website\Repositories\TeamMemberRepository;
use function assert;

final class EventSpeakers extends AbstractLazyCollection
{
    /** @var mixed[] */
    private $event;

    /** @var ObjectManagerInterface */
    private $objectManager;

    /**
     * @param mixed[] $event
     */
    public function __construct(array $event, ObjectManagerInterface $objectManager)
    {
        $this->event         = $event;
        $this->objectManager = $objectManager;
    }

    protected function doInitialize() : void
    {
        $teamMemberRepository = $this->objectManager->getRepository(TeamMember::class);
        assert($teamMemberRepository instanceof TeamMemberRepository);

        $speakers = [];

        foreach ($this->event['speakers'] ?? [] as $speaker) {
            $speakerName = (string) ($speaker['name'] ?? '');

            $teamMember = $speakerName !== ''
                ? $teamMemberRepository->findOneByGithub($speakerName)
                : null;

            $topicSlug = (string) ($speaker['topicSlug'] ?? '');

            $speakers[$topicSlug] = new EventSpeaker(
                $teamMember !== null ? $teamMember->getName() : $speakerName,
                $teamMember !== null ? $teamMember->getAvatarUrl() : (string) ($speaker['avatarUrl'] ?? ''),
                (string) ($speaker['topic'] ?? ''),
                $topicSlug,
                (string) ($speaker['description'] ?? ''),
                (string) ($speaker['youTubeVideoId'] ?? '')
            );
        }

        $this->collection = new ArrayCollection($speakers);
    }
}
