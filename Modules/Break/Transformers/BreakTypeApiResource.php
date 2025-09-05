<?php

namespace Modules\Break\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class BreakTypeApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'will_ask_next_meal' => $this->will_ask_next_meal ? true : false,
            'is_remark_required' => $this->is_remark_required ? true : false,
            'icon' => uploaded_asset(@$this->icon_id),
        ];
    }
}
