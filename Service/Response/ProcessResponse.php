<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Response;

use Magento\Framework\Serialize\SerializerInterface;

class ProcessResponse
{
    public function __construct(private readonly SerializerInterface $serializer) {}

    /**
     * @param $response
     * @return array|bool|float|int|string|null
     */
    public function execute($response)
    {
        $responseData = [];
        $status = $response->getStatusCode();
        if ($status >= 200 && $status < 300) {
            $responseData = $this->serializer->unserialize($response->getBody()->getContents());
        }

        return $responseData;
    }
}
