<?php

namespace Modules\Saas\Repositories;

interface EmailTemplateRepositoryInterface
{
    public function find($id);
    public function show($id);
    public function update($request, $id);
    public function fields();

    public function store(array $data);
    public function table($request);
    public function tableDetail($request, $id);
    public function dataTable($request, $id = null);
    public function destroy($model, $id);
}
