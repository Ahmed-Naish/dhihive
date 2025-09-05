<?php

namespace Modules\Break\Transformers;

use Carbon\Carbon;
use Modules\Break\Entities\UserBreak;
use Illuminate\Http\Resources\Json\JsonResource;

class BreakResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                 => $this->id,
            'break_type_id'      => $this->break_type_id,
            'break_type'         => @$this->breakType->name,
            'is_remark_required' => @$this->breakType->is_remark_required ? true : false,
            'date'               => $this->date,
            'start_time'         => Carbon::parse($this->start_time)->format('h:i A'),
            'end_time'           => $this->end_time ? Carbon::parse($this->end_time)->format('h:i A') : null,
            'duration'           => $this->end_time ? $this->duration : null,
            'reason'             => $this->reason,
            'remark'             => $this->remark,
        ];
    }
}
