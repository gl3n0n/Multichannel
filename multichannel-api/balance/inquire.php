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
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

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

    if ( (!empty($start_date) && empty($end_date) ) || (empty($start_date) && !empty($end_date)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

    $balance = new Balance($dbconn, $client_id);
    $total_points = $balance->inquire($brand_id, $campaign_id, $channel_id, $start_date, $end_date);
    if ($total_points)
    {
        $response['result_code'] = 200;
        $response['balance'] = $total_points;
    }
    else
    {
        $response['result_code'] = 404;
        $response['error_txt'] = 'Balance not found';
    }

    echo json_encode($response);

?>

