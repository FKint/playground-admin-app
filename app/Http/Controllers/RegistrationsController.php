<?php

namespace App\Http\Controllers;

use App\Child;
use App\Day;
use Illuminate\Http\Request;

class RegistrationsController extends Controller
{
    public function show()
    {
        return view('registrations.index');
    }

    public function showDate(Request $request, $date)
    {
        if(date_parse($date) === FALSE)
            return $this->show();
        $day = Day::query()->whereDate('date', $date);

    }
}
