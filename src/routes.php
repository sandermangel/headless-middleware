<?php
// Routes

$app->post('/transactions/csv', \Ffm\Apicall\Controller\GetdataController::class);

$app->get('/oauth/magento1/request', \Ffm\Apicall\Controller\Oauth\Magento1Controller::class . ':request');
$app->get('/oauth/magento1/callback', \Ffm\Apicall\Controller\Oauth\Magento1Controller::class . ':callback');
