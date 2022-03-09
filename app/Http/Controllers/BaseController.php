<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    protected $success_code = 200;
    protected $validation_error_code = 400;
    protected $not_found_code = 404;
    protected $delete_code = 204;
    protected $unauthorized_code = 401;
    protected $many_request_code = 429;
    protected $internal_Server_Error = 500;
    protected $service_Unavailable = 503;
    protected $forbidden_error= 403;

    public function sendResponse($status_code,$result,$error)
    {
        $error_result=null;
        if(count(collect($error))){
            foreach(collect($error) as $key=>$value){
                $error_result['message'] = $value[0];
                break;
            }
            $error_type = '';
            $error_reason = '';
            switch($status_code) {
            case 200:
                $error_type = 'ok';
                $error_reason = 'success';
                break;
            case 400:
                $error_type = 'validation error';
                $error_reason = 'bad request';
                break;
            case 404:
                $error_type = 'not found error';
                $error_reason = 'page not found';
                break;
            case 204:
                $error_type = 'delete';
                $error_reason = 'record deleted';
                break;
            case 401:
                $error_type = 'unauthorized error';
                $error_reason = 'Authentication credentials were missing or incorrect';
                break;
            case 429:
                $error_type = 'many request error';
                $error_reason = 'The request cannot be served due to the rate limit having been exhausted for the resource';
                break;
            case 500:
                $error_type = 'internal server error';
                $error_reason = 'something is broken';
                break;
            case 503:
                $error_type = 'service unavailable error';
                $error_reason = 'The server is up, but overloaded with requests. Try again later!';
                break;
            case 403:
                $error_type = 'forbidden error';
                $error_reason = 'The request is understood, but unauthorized token';
                break;
            
            default:
                $error_type = 'service unavailable error';
                $error_reason = 'something is broken';
            }
            $error_result['error_reason'] = $error_reason;
            $error_result['error_type'] = $error_type;
        }
    	$response = [
            'status_code' => $status_code,
            'data'    => $result,
            'error'   => $error_result
        ];


        return response()->json($response, $status_code);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        else{
            $response['data'] = [];
        }


        return response()->json($response, $code);
    }

    public  function get_lat_long($address){
        $mapApiKey = "AIzaSyCIFwJ32kRhVlc7vhsfiB-69NJCgQRJDLY";
        $address = str_replace(" ", "+", $address);
        // https://maps.googleapis.com/maps/api/js?key=AIzaSyCIFwJ32kRhVlc7vhsfiB-69NJCgQRJDLY&libraries=places
        $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?key=$mapApiKey&address=$address&sensor=false");
        $json = json_decode($json);
        $data['lat'] = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
        $data['long'] = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        return $data;
    }
}