<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // function return all the available providerDataProvider(W/X/Y).
    public function Providers()
    {

        $jsonFilePaths = glob(public_path('*.json'));
        $combinedData = [];
        foreach ($jsonFilePaths as $jsonFilePath) {
            // Read the JSON file contents into a string
            $jsonString = file_get_contents($jsonFilePath);

            // Convert the JSON string to an array using the json_decode function
            $jsonData = json_decode($jsonString, true);

            // Merge the current JSON data into the combined data array
            $combinedData = array_merge($combinedData, $jsonData);
        }
        // dd($combinedData);


        return $combinedData;
    }
    // function display data in api with filters.
    public function allProviders(Request $request)
    {
        $data = $this->Providers();
        if ($request->query('provider')) {
            $data = $this->showProvider($request->query('provider'));
        }
        if ($request->query('statusCode')) {
            $data =  $this->statusProvider($request->query('statusCode'), $data);
        }
        if ($request->query('currency')) {

            $data = $this->currency($request->query('currency'), $data);
        }
        if ($request->query('amounteMin') && $request->query('amounteMin')) {
            $data =  $this->amountRange($request, $data);
        }
        if ($data) {
            return response()->json($data);
        } else {
            return response()->json('No data is available for this research');
        }
    }
    // return specific file data like(/api/v1/transactaions?provider=DataProviderX)
    public function showProvider($provider)
    {
        $jsonFiles = glob(public_path('*.json'));
        $dataArray = [];
        foreach ($jsonFiles as $jsonFile) {

            if (basename($jsonFile, '.json') === $provider) {
                $jsonData = file_get_contents($jsonFile);
                $dataArray = json_decode($jsonData, true);
                break; //
            }
        }
        if (!empty($dataArray)) {
            return $dataArray;
        } else {
            return 'data not found';
        }
    }
    // return all data with search statusCode in all json files like(/api/v1/transactaions?statusCode=paid)
    public function statusProvider($status, $data)
    {
        $search_results = [];
        $status_values = array();
        if ($status == 'paid') {
            $status_values = array(1, "done", 100);
        } elseif ($status == 'pending') {
            $status_values = array(2, "wait", 200);
        } elseif ($status == 'reject') {
            $status_values = array(3, "nope", 300);
        } else {
            return response()->json('No data is available for this research');
        }
        foreach ($data as $item) {

            if (array_key_exists('status', $item)  && in_array($item['status'], $status_values)) {

                $search_results[] = $item;
            }
            if (array_key_exists('transactionStatus', $item) && in_array($item['transactionStatus'], $status_values)) {

                $search_results[] = $item;
            }
        }
        return $search_results;
    }

    // return all data with search amountRange in all json files like(/api/v1/transactaions?amounteMin=10&amounteMax=100 iwith including 10 and 100.)
    public function amountRange(Request $request, $data)
    {

        $results = array_filter($data, function ($item) use ($request) {
            return (
                (array_key_exists('amount', $item) && $item['amount'] >= $request->amounteMin && $item['amount'] <= $request->amounteMax) ||


                (array_key_exists('transactionAmount', $item) && $item['transactionAmount'] >= $request->amounteMin && $item['transactionAmount'] <= $request->amounteMax)

            );
        });
        return $results;
    }


    // return all data with search amountRange in all json files like( /api/v1/transactaions?currency=EGP)
    public function currency($search, $data)
    {

        $results = array_filter($data, function ($item) use ($search) {
            return (array_key_exists('currency', $item) && $item['currency'] == $search

            );
        });
        return $results;
    }
}
