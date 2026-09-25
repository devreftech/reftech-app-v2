<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\HrDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Main Executive Dashboard for HR Management Module
     */
    public function index()
    {
        $hrData = (new HrDashboardService())->getHrDashboardData();

        return view('pages.hr.dashboard', $hrData);
    }
}
