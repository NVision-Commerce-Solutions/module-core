<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Request;

interface PostInterface
{
    /**
     * @param $method
     * @param array $postData
     * @return array
     */
    public function execute(string $method, array $postData = []): array;
}
