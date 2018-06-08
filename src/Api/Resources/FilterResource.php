<?php //strict
namespace PriceMonitorPlentyIntegration\Api\Resources;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use PriceMonitorPlentyIntegration\Api\ApiResource;
use PriceMonitorPlentyIntegration\Api\ApiResponse;
use PriceMonitorPlentyIntegration\Api\ResponseCode;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;
use PriceMonitorPlentyIntegration\Models\ProductFilter;
use Plenty\Plugin\Log\Loggable;
// use IO\Services\OrderService;
// use IO\Services\CustomerService;
/**
 * Class FilterResource
 * @package IO\Api\Resources
 */
class FilterResource extends ApiResource
{
    /**
     * FilterResource constructor.
     * @param Request $request
     * @param ApiResponse $response
     */
	public function __construct(
		Request $request,
		ApiResponse $response)
	{
		parent::__construct($request, $response);
	}
    /**
     * List the orders of the customer
     * @return Response
     */
	public function index():Response
	{
		// $page  = (int)$this->request->get("page", 1);
		// $items = (int)$this->request->get("items", 10);
        // $data = pluginApp(CustomerService::class)->getOrders($page, $items);
        $data = [];
		 return $this->response->create($data, ResponseCode::OK);
	}
    /**
     * Create an order
     * @return Response
     */
	public function store():Response
	{
        // $priceMonitorId = $this->request->get('ContractId', 0);
        // $type = $this->request->get('type', '');
        // $filter = $this->request->get('filter', '');

        $this->getLogger("FilterResource_store")->debug('u store metodi', "");

          $filterOriginals = pluginApp(ProductFilter::class);
          $filterOriginals->contractId = "1";
          $filterOriginals->type = "string";
          $filterOriginals->serializedFilter = "";

          
          $database = pluginApp(DataBase::class);
          $database->save($filterOriginals); 
		return $this->response->create($filterOriginals, ResponseCode::OK);
	}
}