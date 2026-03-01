<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\Decimal128;

const MONGO_COLLECTION = 'listingsAndReviews';

function listingsCollection()
{
    return DB::connection('mongodb')
        ->getMongoDB()
        ->selectCollection(MONGO_COLLECTION);
}

/**
 * If id looks like ObjectId (24 hex), use ObjectId, else use string.
 */
function idFilter(string $id): array
{
    if (preg_match('/^[a-f\d]{24}$/i', $id)) {
        return ['_id' => new ObjectId($id)];
    }
    return ['_id' => $id]; // e.g. "10009999"
}

/**
 * Convert Mongo BSON types to JSON-friendly values
 * - _id: ObjectId -> string
 * - Decimal128 -> string
 * - UTCDateTime -> ISO 8601 string
 */
function normalizeMongo($value)
{
    if ($value instanceof \MongoDB\Model\BSONDocument || $value instanceof \MongoDB\Model\BSONArray) {
        $value = $value->getArrayCopy();
    }

    if (is_array($value)) {
        foreach ($value as $k => $v) {
            if ($k === '_id' && $v instanceof ObjectId) {
                $value[$k] = (string) $v;
                continue;
            }

            if ($v instanceof Decimal128) {
                $value[$k] = (string) $v;
                continue;
            }

            if ($v instanceof UTCDateTime) {
                $value[$k] = $v->toDateTime()->format(DATE_ATOM);
                continue;
            }

            $value[$k] = normalizeMongo($v);
        }
    }

    return $value;
}

/**
 * GET /api/listings?limit=10
 */
Route::get('/listings', function (Request $request) {
    $limit = (int) $request->query('limit', 10);
    $limit = max(1, min($limit, 50));

    $docs = listingsCollection()
        ->find([], ['limit' => $limit])
        ->toArray();

    return response()->json(array_map('normalizeMongo', $docs));
});

/**
 * GET /api/listings/{id}
 */
Route::get('/listings/{id}', function (string $id) {
    $doc = listingsCollection()->findOne(idFilter($id));

    if (!$doc) {
        return response()->json(['error' => 'Not found'], 404);
    }

    return response()->json(normalizeMongo($doc));
});

/**
 * POST /api/listings
 * Returns full inserted document
 */
Route::post('/listings', function (Request $request) {
    $payload = $request->all();
    unset($payload['_id']);

    $collection = listingsCollection();
    $result = $collection->insertOne($payload);

    $insertedId = $result->getInsertedId();
    $newDoc = $collection->findOne(['_id' => $insertedId]);

    return response()->json(normalizeMongo($newDoc), 201);
});

/**
 * PATCH /api/listings/{id}
 */
Route::patch('/listings/{id}', function (Request $request, string $id) {
    $payload = $request->all();
    unset($payload['_id']);

    if (empty($payload)) {
        return response()->json(['error' => 'No fields to update'], 400);
    }

    $result = listingsCollection()->updateOne(
        idFilter($id),
        ['$set' => $payload]
    );

    if ($result->getMatchedCount() === 0) {
        return response()->json(['error' => 'Not found'], 404);
    }

    // optional: return updated document
    $doc = listingsCollection()->findOne(idFilter($id));
    return response()->json(normalizeMongo($doc));
});

/**
 * DELETE /api/listings/{id}
 */
Route::delete('/listings/{id}', function (string $id) {
    $result = listingsCollection()->deleteOne(idFilter($id));

    return response()->json([
        'deleted' => $result->getDeletedCount() > 0,
        'count' => $result->getDeletedCount(),
    ]);
});

/**
 * GET /api/listings-count
 */
Route::get('/listings-count', function () {
    return response()->json(['count' => listingsCollection()->countDocuments()]);
});
