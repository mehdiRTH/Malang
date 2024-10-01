<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class UsersImport implements ToCollection
{

    public function collection(Collection $rows)
    {
        // dd($rows[1]);

    }
}
