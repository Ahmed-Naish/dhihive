<?php

namespace Modules\Saas\Repositories;

interface PricingPlansRepositoryInterface
{
    public function find($id);
    public function show($id);
    public function update($id, array $data);
    public function getAll();
    public function getActiveAll();
    public function planFeatures();
    public function fields();

    public function store(array $data);
    public function table($request);
    public function tableDetail($request, $id);
    public function dataTable($request, $id = null);
    public function destroy($model, $id);
    public function statusUpdate($request);
    public function destroyAll($request);
    public function createAttributes();
    public function editAttributes($model);
    public function newStore($request);
    public function newUpdate($request, $model);

}
