<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{
    /**
     * Build a success response
     * @param  string|array $data
     * @param  int $code
     * @return Illuminate\Http\JsonResponse
     */
    public function successResponse($data, $code = Response::HTTP_OK)
    {
        return response()->json($data, $code);
    }

    /**
     * Build error responses
     * @param  string $message
     * @param  int $code
     * @return Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    protected function showAll(Collection $collection, $code = 200)
	{

		if($collection->isEmpty())
		{
			return $this->successResponse(['data' => $collection], $code);
		}


        $collection = $this->filterData($collection);
        $collection = $this->sortData($collection);
		$collection = $this->paginate($collection);
		$collection = $this->cacheResponse($collection);

		return $this->successResponse($collection, $code);
    }

	protected function showMessage($message, $code = 200)
	{
		return $this->successResponse(['data' => $message], $code);
    }

	protected function sortData(Collection $collection)
	{
		if(app()->request->has('sort_by'))
		{
            $attribute  = app()->request->sort_by;
            $collection = $collection->sortBy($attribute);

		}

		return $collection;
	}

	protected function filterData(Collection $collection)
	{
		foreach (app()->request->query() as $query => $value)
		{
			if(isset($query, $value)){
				$collection = $collection->where($query, $value);
			}
		}
		return $collection;
	}

	protected function paginate(Collection $collection)
	{

		$reglas = [
			'per_page' => 'integer|min:2|max:15'
		];

		Validator::validate(app()->request->all(), $reglas);

		$page = LengthAwarePaginator::resolveCurrentPage();

		$perPage = 10;

		if(app()->request->has('per_page')){
			$perPage = (int) app()->request->per_page;
		}

		$results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

		$paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
			'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

		$paginated->appends(app()->request->all());

		return $paginated;
	}

	protected function cacheResponse($data)
	{
		$url = app()->request->url();
		$queryParams = app()->request->query();

		ksort($queryParams);

		$queryString = http_build_query($queryParams);

		$fullUrl = "{$url}?{$queryString}";

		return Cache::remember($fullUrl, 35/60, function() use ($data) {
			return $data;
		});
	}
}
