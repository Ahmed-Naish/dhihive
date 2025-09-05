<?php

namespace Modules\Saas\Http\Repositories;

use Modules\Saas\Entities\SubscriptionModule;
use Modules\Saas\Http\Interfaces\SubscriptionModuleInterface;

class SubscriptionModuleRepository extends SubscriptionBaseRepository  implements SubscriptionModuleInterface
{
    protected $model;

    /**
     * SubscriptionModuleRepository constructor.
     *
     * @param SubscriptionModule $model The model instance to be used.
     */
    public function __construct(SubscriptionModule $model)
    {
        $this->model = $model;
    }

    /**
     * Get paginated items.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated items.
     */
    public function getPaginatedItems()
    {
        return $this->model->paginate(5);
    }

    /**
     * Get all items.
     *
     * @return \Illuminate\Database\Eloquent\Collection All items.
     */
    public function getAllItems()
    {
        return $this->model->all();
    }

    /**
     * Get order by ID.
     *
     * @param int $orderId The ID of the order.
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|null Order model or null.
     */
  
}
