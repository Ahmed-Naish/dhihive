<?php

namespace Modules\Services\Repositories;

interface MachineRepositoryInterface
{
    public function find($id);
    public function show($id);
    public function update($id, array $data);
    public function getAll();
    public function getActiveAll();
    public function fields();

    public function store(array $data);
    public function table($request);
    public function dataTable($request, $id = null);
    public function destroy($model);
    public function statusUpdate($request);
    public function destroyAll($request);
    public function createAttributes();
    public function editAttributes($model);
    public function newStore($request);
    public function newUpdate($request, $model);

}
