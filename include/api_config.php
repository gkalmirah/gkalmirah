<?php
/**
 * API Configuration Helper
 * Allows fetching dynamic API keys from the database.
 */

if (!function_exists('get_api_credentials')) {
    /**
     * Fetch active API credentials for a given service.
     * 
     * @param string $service_name The exact name of the service (e.g., 'Razorpay')
     * @param mysqli $con Active database connection
     * @return array|null Returns assoc array ['api_key' => '...', 'api_secret' => '...'] or null if missing/disabled
     */
    function get_api_credentials($service_name, $con) {
        $name_safe = mysqli_real_escape_string($con, $service_name);
        $query = "SELECT api_key, api_secret FROM api_configurations WHERE service_name = '$name_safe' AND is_active = 1 LIMIT 1";
        $result = mysqli_query($con, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        
        return null;
    }
}
?>
