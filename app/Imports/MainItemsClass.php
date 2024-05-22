<?php

namespace App\Imports;

use App\Models\MainItem;
use Maatwebsite\Excel\Concerns\ToModel;

/**
 * Summary of MainItemsClass
 */
class MainItemsClass implements ToModel
{
    /**
     * Summary of model
     * @param array $row
     * @return MainItem
     */
    public function model(array $row)
    {
        return new MainItem([
            'name' => $row[0],
            'product_id' => $row[1],
            'des' => $row[2],
            'amount' => $row[3],


            // Map other columns as needed
        ]);
    }
}
