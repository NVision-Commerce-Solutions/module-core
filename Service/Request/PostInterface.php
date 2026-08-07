<?php

namespace Commerce365\Core\Service\Request;

interface PostInterface
{
    /**
     * @param $method
     * @param array $postData
     * @param int|null $storeId Scope whose Business Central / hub configuration should be used.
     * @return array
     */
    public function execute($method, array $postData = [], ?int $storeId = null): array;
}
