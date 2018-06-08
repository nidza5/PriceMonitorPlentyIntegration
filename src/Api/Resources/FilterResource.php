<?php //strict
namespace PriceMonitorPlentyIntegration\Api\Resources;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use PriceMonitorPlentyIntegration\Api\ApiResource;
use PriceMonitorPlentyIntegration\Api\ApiResponse;
use PriceMonitorPlentyIntegration\Api\ResponseCode;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
// use IO\Services\OrderService;
// use IO\Services\CustomerService;
/**
 * Class OrderResource
 * @package IO\Api\Resources
 */
class OrderResource extends ApiResource
{
    /**
     * OrderResource constructor.
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
		// return $this->response->create($data, ResponseCode::OK);
	}
    /**
     * Create an order
     * @return Response
     */
	public function store():Response
	{
		  $filterOriginals = pluginApp(ProductFilter::class);
          $this->dataBase->save($filterOriginals); 
		return $this->response->create($filterOriginals, ResponseCode::OK);
	}
}