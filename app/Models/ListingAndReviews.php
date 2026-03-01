<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ListingAndReviews extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'listingsAndReviews';
}
