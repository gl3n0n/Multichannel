<?php
    require_once('../config/database.php');
    require_once('../includes/balance.php');

    //params
    //
    #TODO
    $client_id = $_POST['client_id'];


    $brand_id = $_POST['brand_id'];
    $campaign_id = $_POST['campaign_id'];
    $channel_id = $_POST['channel_id'];
    $points = $_POST['points'];
    $multiplier = $_POST['multiplier'];

    $response = array(
        'result_code' => '',
    );

    if (empty($client_id))
    {
        $response['result_code'] = 403;
        $response['error_txt'] = 'Forbidden';
        echo json_encode($response);
        return;
    }

    if (empty($multiplier))
    {
        $multiplier = 1;
    }

    if (empty($brand_id) || empty($campaign_id) || empty($channel_id))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

    if (empty($points))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

    $balance = new Balance($dbconn, $client_id);
    if ($balance->isAllowed($brand_id, $campaign_id, $channel_id))
    {
        $new_points = $balance->update($brand_id, $campaign_id, $channel_id, $points, $multiplier);
        if ($new_points)
        {
            $response['result_code'] = 200;
            $response['balance'] = $new_points * $multiplier;
        }
        else
        {
            $response['result_code'] = 404;
            $response['error_txt'] = 'Balance not found';
        }
    }
    else
    {
        $response['result_code'] = 403;
        $response['error_txt'] = 'Promo expired';
    }
    echo json_encode($response);
?>


