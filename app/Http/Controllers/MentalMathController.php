<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MentalMath;
use App\Http\Requests\StoreMentalMathRequest;
use App\Http\Requests\UpdateMentalMathRequest;

class MentalMathController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('MentalMath');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMentalMathRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MentalMath $mentalMath)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MentalMath $mentalMath)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMentalMathRequest $request, MentalMath $mentalMath)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MentalMath $mentalMath)
    {
        //
    }

}
