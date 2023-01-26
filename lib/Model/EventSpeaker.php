<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use function sprintf;

final class EventSpeaker
{
    public function __construct(
        private string $name,
        private string $avatarUrl,
        private string $topic,
        private string $topicSlug,
        private string $description,
        private string $youTubeVideoId,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function getTopicSlug(): string
    {
        return $this->topicSlug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function hasYouTubeVideo(): bool
    {
        return $this->youTubeVideoId !== '';
    }

    public function getYouTubeVideoId(): string
    {
        return $this->youTubeVideoId;
    }

    public function getYouTubeUrl(): string
    {
        return sprintf('https://www.youtube.com/watch?v=%s', $this->youTubeVideoId);
    }
}
