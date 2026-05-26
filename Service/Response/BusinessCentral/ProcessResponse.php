<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Response\BusinessCentral;

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
        if (empty($response)) {
            return [];
        }

        $responseData = [];

        $status = $response->getStatusCode();
        if ($status >= 200 && $status < 300) {
            $responseData = $this->serializer->unserialize($response->getBody()->getContents());
            if (!isset($responseData['value'])) {
                return [];
            }

            // BC OData wraps the actual JSON payload in the "value" field.
            $responseData = $this->serializer->unserialize($responseData['value']);
        }

        return $responseData;
    }
}
