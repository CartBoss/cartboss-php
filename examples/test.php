<?php

use CartBoss\Api\Interceptors\AttributionUrlInterceptor;
use CartBoss\Api\Interceptors\ContactUrlInterceptor;
use CartBoss\Api\Interceptors\CouponUrlInterceptor;
use CartBoss\Api\Utils;

require_once __DIR__ . '/../cartboss-php.php';

// http://localhost:8888/cartboss_php/examples/test.php?cb__att=foo123&cb__contact=eyJjaXBoZXJ0ZXh0IjogInZsbFROYTRENGZiNHJjd0JqcFFhL25FQXRZTWE0U1YyWkd1ZzRxUjBXWEltV05wSE1OZU9sQUhVM0RsYWhXRlhXdXFiOEo1LzFWVTlDQnNCd1pPYUVFNWVyaGhQUWk2K3NLa3NHMFQ5TXZtM3RDZUR0Uy9oaHZ4Y2NWamZGVkx4aCs4ak9DN1loWktDdUppUURJYXNReDlqQjF1ejdwRUFNNWtTK00xWGMzZ2JVbEg5Nm52ZE0wZE5HWFZPWVVlcVhOaWNycFFSV1dRa1d3M3YyZDh1bTBia21UNlg0cDlKbHdpWWtSb2UxRktNbkpLQWhhcXJiY2pmdkxGTzBlT0lRWnZnRHNPbjBoQ1V6bWRvOStUYmk4SWNJU2VPRloyaWIyZFFBdzZzMGc4PSIsICJpdiI6ICJYNHNyU2NVcXprOW5iYm5DNGwrWWpnPT0ifQ%3D%3D&cb__discount=eyJjaXBoZXJ0ZXh0IjogIlZqeXhZVkt1bXZWalh2dUlkaDd6V0lTSk1xUk5IK2RsSWVqTzF6eHByZktvUWo0b0xIRUlXSHFmU1hTOW1ScndXcm5QNlBDRFhQSHB5bDJ4RWowdmx3PT0iLCAiaXYiOiAid2xWL1RYRlZvRWJNUThMRVlQeENoUT09In0

echo "A ";

$a = new AttributionUrlInterceptor();
echo $a->getToken();

$b = new ContactUrlInterceptor("GrpYQV3GGgUYMk4JIhJ2TPoC6GEHP7Tk6ApwiyGYtGdj76UnnfQiHYtzSqUM9kk4");
echo $b->getContact();

$c = new CouponUrlInterceptor("GrpYQV3GGgUYMk4JIhJ2TPoC6GEHP7Tk6ApwiyGYtGdj76UnnfQiHYtzSqUM9kk4", $a->getToken());
echo $c->getCoupon();


echo " B";
