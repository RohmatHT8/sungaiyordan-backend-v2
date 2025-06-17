<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\BookingRoomRepository;
use App\Entities\BookingRoom;
use App\Validators\BookingRoomValidator;

/**
 * Class BookingRoomRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BookingRoomRepositoryEloquent extends BaseRepository implements BookingRoomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BookingRoom::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
