
<html>
<head>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css"
          integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/cartboss-helper.js"></script>
</head>
<body>
<div class="container py-2">
    <div class="d-flex justify-content-between align-items-center">
        <h1>PurchaseEvent</h1>
        <div>
            <a href="/" class="btn btn-primary">Back to checkout &rarr;</a>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-lg-6">
            <div class="bg-danger text-white p-2">
                <h3>Error</h3>
                <?php print_r(CartBoss\Api\Storage\ContextStorage::get(TMPL_EVENT_ERROR)) ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="border  p-2">
                <h3>Event payload</h3>
                <pre><?php print_r(CartBoss\Api\Storage\ContextStorage::get(TMPL_EVENT_PAYLOAD)) ?></pre>
            </div>
        </div>
    </div>

</div>
</body>
</html>
