<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Off;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Log;

class OffController extends Controller
{
	public function uploadRecord(Request $request)
	{
		$request->validate([
			'first_name' => 'required|string|max:100',
			'last_name' => 'required|string|max:100',
			'em_id' => 'required|integer|unique:off,em_id',
			'day' => 'required|integer|between:1,7',
		]);

		try {
			$off = new Off();
			$off->first_name = $request->first_name;
			$off->last_name = $request->last_name;
			$off->em_id = $request->em_id;
			$off->day = $request->day;
			$off->save();

			return response()->json(['message' => 'Off record uploaded successfully'], 200);
		} catch (\Exception $e) {
			Log::error('Failed to upload Off record: ' . $e->getMessage());
			return response()->json(['error' => 'Failed to upload record', 'details' => $e->getMessage()], 500);
		}
	}

	
	public function getOffDayByEmId($em_id)
	{
		try {
			$off = Off::where('em_id', $em_id)->first();

			if (!$off) {
				Toastr::warning('No off day found for this user ID.', 'Not Found');
				return redirect()->back();
			}

			return response()->json([
				'em_id' => $em_id,
				'off_day' => $off->day
			]);
		} catch (\Exception $e) {
			Log::error('Failed to fetch Off day: ' . $e->getMessage());
			Toastr::error('Error fetching off day: ' . $e->getMessage(), 'Error');
			return redirect()->back();
		}
	}

}
