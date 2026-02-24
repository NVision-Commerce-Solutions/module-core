<?php

declare(strict_types=1);

namespace Commerce365\Core\Plugin;

use Commerce365\Core\Model\MainConfig;
use Commerce365\Core\Service\Product\GetParentProduct;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;

class ConfigurableImageShare
{
    public function __construct(
        private readonly GetParentProduct $getParentProduct,
        private readonly MainConfig $mainConfig
    ) {}

    /**
     * @param ImageFactory $subject
     * @param Product $product
     * @param string $imageId
     * @param array|null $attributes
     * @return array
     */
    public function beforeCreate(
        ImageFactory $subject,
        Product $product,
        string $imageId,
        ?array $attributes = null
    ): array {
        if (!$this->mainConfig->isConfigurableImageEnabled()) {
            return [$product, $imageId, $attributes];
        }

        if ($product->getTypeId() !== 'simple') {
            return [$product, $imageId, $attributes];
        }

        // Check if the simple product has an image (other than placeholder)
        $hasImage = $product->getImage() && $product->getImage() !== 'no_selection';

        // If the product has its own image and we're not replacing, use the original image
        if ($hasImage && !$this->mainConfig->isConfigurableImageReplaceExisting()) {
            return [$product, $imageId, $attributes];
        }

        // Get parent configurable product
        $parentProduct = $this->getParentProduct->execute($product);

        if (!$parentProduct) {
            return [$product, $imageId, $attributes];
        }

        return [$parentProduct, $imageId, $attributes];
    }
}
