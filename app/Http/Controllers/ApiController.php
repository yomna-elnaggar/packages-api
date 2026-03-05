<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public static function respondWithSuccess($message = null, $data = null)
    {
        if ($data != null) {
            return response()->json(['success' => true, 'message' => $message, 'data' => $data]);
        } else {
            return response()->json(['success' => true, 'message' => $message]);
        }
    }

    public static function respondWithError($message, $errors, $code)
    {
        if ($errors != null) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
                'status_Code' => $code
            ], $code); 
        } else {
            return response()->json([
                'success' => false,
                'message' => $message,
                'status_Code' => $code
            ], $code); 
        }
    }

    public static function respondWithValidationError($message, $errors, $code)
    {
        if ($errors != null) {
            if ($errors instanceof \Illuminate\Support\MessageBag) {
                $errors = $errors->all();
            }

            if (is_array($errors)) {
                $errorMessage = implode(" | ", $errors);
            } else {
                $errorMessage = $errors;
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'status_code' => $code
            ], $code);
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'status_code' => $code
        ], $code);
    }

    public static function respondWithNotFound()
    {
        return response()->json(['success' => false, 'errors' => __('messages.not_found'), 'status_Code' => 404]);
    }

    public static function respondWithServerError()
    {
        return response()->json(['success' => false, 'status_Code' => 500]);
    }
}
