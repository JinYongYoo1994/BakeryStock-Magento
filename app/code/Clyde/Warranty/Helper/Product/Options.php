<?php
namespace Clyde\Warranty\Helper\Product;

class Options extends \Magento\ConfigurableProduct\Helper\Data
{
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = array();
        $allowAttributes = $this->getAllowAttributes($currentProduct);
        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            $productSku = $product->getSku();
            foreach ($allowAttributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());

                $options[$productAttributeId][$attributeValue][] = $productId;
                $options['index'][$productId][$productAttributeId] = $attributeValue;
                $options['index'][$productId]['sku'] = $productSku;
            }
        }

        return $options;
    }
}