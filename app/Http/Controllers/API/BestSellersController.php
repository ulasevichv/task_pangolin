<?php
namespace App\Http\Controllers\API;

use App\ExternalServices\NewYorkTimes\BooksAPI;
use App\Http\Middleware\CheckJson;
use App\Models\API\BestSellers\BestSeller;
use Illuminate\Http\Request;

class BestSellersController extends BaseAPIController
{
    private const BASE_ROUTE_PREFIX = 'best-sellers.';

    public const ROUTE_SEARCH = self::BASE_ROUTE_PREFIX . 'search';

    public function __construct()
    {
        $this->middleware(CheckJson::class, ['except' => []]);
    }

    public function search(BooksAPI $booksAPI, Request $request): object
    {
        return (new BestSeller($booksAPI))->search($request);
    }
}