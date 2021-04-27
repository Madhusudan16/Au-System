<?php
    if(!function_exists("encrypt")) {
        /**
         * Encrypt value
         * @param value
         * @return encrypted value
         */
        function encrypt($value)
        {
            return crypt()->encryptString($value);
        }
    }

    if(!function_exists("decrypt")) {
        /**
         * Decrypt value
         * @param value
         * @return decrypted value
         */
        function decrypt($value)
        {
            return crypt()->decryptString($value);
        }
    }

    if(!function_exists("respond")) {
        /**
         * api_response format
         * @param string message
         * @param  mix data,
         * @param  int status
         * @return response()
         */
        function respond($message, $data = [], $status = 200)
        {
            $response = [
                'message' => $message,
                'success' => ($status == 200) ? true :  false,
                'data' => $data,

            ];
            if(isset($data['data'])) {
                $response['data'] = $data['data'];
            }

            return response()->json($response, $status);
        }
    }
