<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QueryFilterSearch
{
	public static function applyFilters(Builder $query, array $filters, array $rules): Builder
	{
		foreach ($filters as $key => $value) {
			if (isset($rules[$key]) && is_callable($rules[$key])) {
				$query = $rules[$key]($query, $value) ?: $query;
			}
		}

		return $query;
	}

	public static function applySearch($query, $search, array $columns)
	{
		return $query->where(function ($q) use ($search, $columns) {
			foreach ($columns as $column) {
				if (str_contains($column, '.')) {
					[$relation, $relColumn] = explode('.', $column);
					$q->orWhereHas($relation, function ($relQ) use ($relColumn, $search) {
						$relQ->where($relColumn, 'LIKE', "%{$search}%");
					});
				} else {
					$q->orWhere($column, 'LIKE', "%{$search}%");
				}
			}
		});
	}

	public static function applySortCreatedAt(
		Builder $query,
		?string $sortBy = 'created_at',
		?string $sortDir = 'desc',
		array $allowed = ['updated_at', 'created_at', 'deleted_at']
	): Builder {
		$table = $query->getModel()->getTable();

		// normalisasi arah sort
		$dir = strtolower((string) $sortDir) === 'asc' ? 'asc' : 'desc';

		// pastikan kolom valid; fallback ke created_at
		$sort = in_array($sortBy, $allowed, true) ? $sortBy : 'created_at';

		if ($sort === 'deleted_at') {
			// NON-deleted (NULL) lebih dulu, lalu urut berdasarkan deleted_at
			return $query
				->orderByRaw("{$table}.deleted_at IS NULL DESC")
				->orderBy("{$table}.deleted_at", $dir)
				->orderBy("{$table}.id", 'desc');
		}

		return $query
			->orderBy("{$table}.{$sort}", $dir)
			->orderBy("{$table}.id", 'desc');
	}

	public static function applyPagination(Builder $query, Request $request)
	{
		$limit = $request->input('limit');

		// Jika limit tidak diberikan atau null, maka ambil semua data
		if (is_null($limit)) {
			return $query->get(); // Kembalikan collection biasa, bukan pagination
		}

		return $query->paginate((int) $limit);
	}

	public static function formatPaginationCollection($result, $resourceClass)
	{
		$response = $resourceClass::collection($result)->response()->getData(true);

		return [
			'data' => $response['data'],
			'pagination' => [
				'links' => [
					'first' => $result->url(1),
					'last'  => $result->url($result->lastPage()),
					'prev'  => $result->previousPageUrl(),
					'next'  => $result->nextPageUrl(),
				],
				'meta' => [
					'current_page' => $result->currentPage(),
					'last_page'    => $result->lastPage(),
					'per_page'     => $result->perPage(),
					'total'        => $result->total(),
				]
			]
		];
	}
}
