<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use function sprintf;

final class EventSpeaker
{
    /** @var string */
    private $name;

    /** @var string */
    private $avatarUrl;

    /** @var string */
    private $topic;

    /** @var string */
    private $topicSlug;

    /** @var string */
    private $description;

    /** @var string */
    private $youTubeVideoId;

    public function __construct(
        string $name,
        string $avatarUrl,
        string $topic,
        string $topicSlug,
        string $description,
        string $youTubeVideoId
    ) {
        $this->name           = $name;
        $this->avatarUrl      = $avatarUrl;
        $this->topic          = $topic;
        $this->topicSlug      = $topicSlug;
        $this->description    = $description;
        $this->youTubeVideoId = $youTubeVideoId;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getAvatarUrl() : string
    {
        return $this->avatarUrl;
    }

    public function getTopic() : string
    {
        return $this->topic;
    }

    public function getTopicSlug() : string
    {
        return $this->topicSlug;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function hasYouTubeVideo() : bool
    {
        return $this->youTubeVideoId !== '';
    }

    public function getYouTubeVideoId() : string
    {
        return $this->youTubeVideoId;
    }

    public function getYouTubeUrl() : string
    {
        return sprintf('https://www.youtube.com/watch?v=%s', $this->youTubeVideoId);
    }
}
