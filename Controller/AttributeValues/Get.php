<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace BKozlic\ConfigurableOptions\Controller\AttributeValues;

use BKozlic\ConfigurableOptions\Service\GetProductAttributeValuesService;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Get Controller
 */
class Get implements HttpGetActionInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var GetProductAttributeValuesService
     */
    protected $getProductAttributeValuesService;

    /**
     * Get constructor.
     * @param RequestInterface $request
     * @param ResultFactory $resultFactory
     * @param ProductRepositoryInterface $productRepository
     * @param GetProductAttributeValuesService $getProductAttributeValuesService
     */
    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ProductRepositoryInterface $productRepository,
        GetProductAttributeValuesService $getProductAttributeValuesService
    ) {
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->productRepository = $productRepository;
        $this->getProductAttributeValuesService = $getProductAttributeValuesService;
    }

    /**
     * Return attribute values specified in config based on product id
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $productId = $this->request->getParam('productId');
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if (!$productId) {
            return $resultJson->setData(['success' => false, 'data' => []]);
        }

        $resultJson->setData([
            'success' => true,
            'data' => $this->getProductAttributeValuesService->execute($productId, true)
        ]);

        return $resultJson;
    }
}
