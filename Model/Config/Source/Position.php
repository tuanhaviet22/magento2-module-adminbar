<?php
declare(strict_types=1);

namespace TH\Adminbar\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Position source model for admin bar
 */
class Position implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'top', 'label' => __('Top')],
            ['value' => 'bottom', 'label' => __('Bottom')]
        ];
    }
}
