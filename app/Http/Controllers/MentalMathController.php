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
    /**
     * Redirect to the exam link.
     */
    public function examLink($level)
    {
		$link = [
            '1' => 'https://docs.google.com/forms/d/e/1FAIpQLSfcxjX6NtH5r3rN9klmVihgPEbgbHKkrpMS5iq9InKNrsfIcQ/viewform',
            '2' => 'https://docs.google.com/forms/d/e/1FAIpQLSe0CnyIZ5R-yl-lw0Hvaoq3rir6oMjFM3SHNWMqQECWQYZ6kQ/viewform',
            '3' => 'https://docs.google.com/forms/d/e/1FAIpQLSeUJAuKHM3Khnbu_Wsvy6f5K8kR1uTtL8gmCzI5VLroouGPrw/viewform',
            '4' => 'https://docs.google.com/forms/d/e/1FAIpQLSer0p6SGVA7CCQRYOKd3v4uPuzC4gebgSF00efOkfjLg9kiew/viewform',
            '6' => 'https://docs.google.com/forms/d/e/1FAIpQLScr8MOI95Hul44yNiwJcCo4CBnCr6s53VuVHZs6PlumFerl8g/viewform'
        ];

        return redirect($link[$level]);
    }	
}
