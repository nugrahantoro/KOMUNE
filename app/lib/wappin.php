<?php
class Wappin extends Controller
{
	function isWA($number) {
		// Remove any non-numeric characters from the input
		$number = preg_replace('/[^0-9]/', '', $number);

		// Check if the number starts with "62" or "08" and is at least 9 digits long
		if (preg_match('/^(62|08)[0-9]{9,}$/', $number)) {
			return true;
		} else {
			return false;
		}
	}
	
	 function token(){
        $base_url = "https://api.chat.wappin.app/";

        $url_login  = $base_url.'v1/users/login';

        $username = 'invoice-sekolah';
        $password = 'bPrCxruWra6$';

        $session = curl_init ( $url_login );
        curl_setopt ( $session, CURLOPT_URL, $url_login );
        curl_setopt ( $session, CURLOPT_POST, true );
        curl_setopt ( $session, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt ( $session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt ( $session, CURLOPT_USERPWD, "$username:$password");
        curl_setopt ( $session, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec ( $session );
        $httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);

        curl_close($session);

        $token = "";
        if ($httpCode === 200) {
            $respon = json_decode($result,true);
            $token = $respon['users'][0]['token'];
            return $token;
        } else {
            echo "Error: HTTP status code: $httpCode";
        }
    }
	
    function send_message($request){
		
		  $base_url = "https://api.chat.wappin.app/";

            $token = $this->token();
            $tok = "Authorization: Bearer ".$token;

            $url = $base_url."v1/messages";
            $request = json_encode($request);
            
            $session = curl_init ( $url );
            curl_setopt ( $session, CURLOPT_URL, $url );
            curl_setopt ( $session, CURLOPT_HTTPHEADER, array($tok,'Content-Type: application/json') );
            curl_setopt ( $session, CURLOPT_VERBOSE, true );
            curl_setopt ( $session, CURLOPT_POST, true );
            curl_setopt ( $session, CURLOPT_POSTFIELDS, $request );
            curl_setopt ( $session, CURLOPT_RETURNTRANSFER, TRUE );
            curl_setopt ( $session, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec ( $session );
            $httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);

            curl_close($session);

            if ($httpCode === 200) {
                $response = $result;
            } 
            else{
                $response = json_encode(array(
                    'status'  => $httpCode,
                    'message' => "Error: HTTP status code"
                ));
            }
            return $response;
        }
}
?>