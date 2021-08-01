<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);
namespace BKozlic\ConfigurableOptions\Model;

class ValueModifierPool
{
    /**
     * @var array
     */
    protected $modifiers;

    /**
     * ValueModifierPool constructor.
     * @param array $modifiers
     */
    public function __construct(array $modifiers = [])
    {
        $this->modifiers = $this->sort($modifiers);
    }

    /**
     * Returns modifiers for product attribute values
     * @return array
     */
    public function getModifiers()
    {
        $modifiers = [];
        foreach ($this->modifiers as $modifier) {
            if (isset($modifier['class'])) {
                $modifiers[] = $modifier['class'];
            }
        }

        return $modifiers;
    }

    /**
     * Sorts modifiers array
     * @param array $modifiers
     * @return array
     */
    protected function sort(array $modifiers)
    {
        usort($modifiers, function ($a, $b) {
            return $this->getSortOrderNumber($a) <=> $this->getSortOrderNumber($b);
        });

        return $modifiers;
    }

    /**
     * Returns sort number for modifier
     * @param array $modifier
     * @return int
     */
    protected function getSortOrderNumber(array $modifier)
    {
        return isset($modifier['sortOrder']) ? $modifier['sortOrder'] : 0;
    }
}
