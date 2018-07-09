<?php //strict
namespace PriceMonitorPlentyIntegration\Api;
use Plenty\Plugin\Http\Response;
use Plenty\Modules\Account\Events\FrontendUpdateCustomerSettings;
use Plenty\Modules\Authentication\Events\AfterAccountAuthentication;
use Plenty\Modules\Authentication\Events\AfterAccountContactLogout;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemRemove;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemUpdate;
use Plenty\Modules\Basket\Events\BasketItem\BeforeBasketItemAdd;
use Plenty\Modules\Basket\Events\BasketItem\BeforeBasketItemRemove;
use Plenty\Modules\Basket\Events\BasketItem\BeforeBasketItemUpdate;
use Plenty\Modules\Frontend\Events\FrontendCurrencyChanged;
use Plenty\Modules\Frontend\Events\FrontendLanguageChanged;
use Plenty\Modules\Frontend\Events\FrontendUpdateDeliveryAddress;
use Plenty\Modules\Frontend\Events\FrontendUpdatePaymentSettings;
use Plenty\Modules\Frontend\Events\FrontendUpdateShippingSettings;
use Plenty\Modules\Frontend\Events\FrontendPaymentMethodChanged;
use Plenty\Modules\Frontend\Events\FrontendShippingProfileChanged;
use Plenty\Modules\Basket\Events\Basket\AfterBasketChanged;
use Plenty\Modules\Basket\Events\Basket\AfterBasketCreate;
/**
 * Class ApiResponse
 * @package IO\Api
 */
class ApiResponse
{
	/**
	 * @var Dispatcher
	 */
	private $dispatcher;
	/**
	 * @var array
	 */
	private $eventData = [];
	/**
	 * @var mixed
	 */
	private $data = null;
	/**
	 * @var array
	 */
	private $headers = [];
    
    /**
     * @var null|Response
     */
    private $response = null;
    
    /**
     * ApiResponse constructor.
     * @param Dispatcher $dispatcher
     * @param Response $response
     */
	public function __construct(Response $response)
	{
        $this->response = $response;   
	}
    /**
     * @deprecated
     *
     * @param int $code
     * @param null $message
     * @return ApiResponse
     */
	public function error(int $code, $message = null):ApiResponse
	{
		return $this;
	}
    /**
     * @deprecated
     *
     * @param int $code
     * @param null $message
     * @return ApiResponse
     */
	public function success(int $code, $message = null):ApiResponse
	{
		return $this;
	}
    /**
     * @deprecated
     *
     * @param int $code
     * @param null $message
     * @return ApiResponse
     */
	public function info(int $code, $message = null):ApiResponse
	{
		return $this;
	}
    /**
     * @param string $key
     * @param string $value
     * @return ApiResponse
     */
	public function header(string $key, string $value):ApiResponse
	{
		$this->headers[$key] = $value;
		return $this;
	}
	/**
	 * @param $data
	 * @param int $code
	 * @param array $headers
	 * @return Response
	 */
	public function create($data, int $code = ResponseCode::OK, array $headers = []):Response
	{
		foreach($headers as $key => $value)
		{
			$this->header($key, $value);
		}
	
		$responseData["data"]   = $data;
        return $this->response->make(json_encode($responseData), $code, $this->headers);
	}
}