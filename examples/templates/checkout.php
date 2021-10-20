<html>
<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="js/cartboss-helper.js"></script>
</head>
<body>
<div class="container py-2">
    <h1>Checkout sample</h1>

    <form method="post" action="event_purchase.php">
        <div class="mb-1">
            <label for="billing_phone" class="form-label">Phone number *</label>
            <input autocomplete="off" class="required form-control" id="billing_phone" name="billing_phone" required="required" type="tel" value="">
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="accepts_marketing" name="accepts_marketing">
            <label class="form-check-label" for="accepts_marketing">
                I want to receive special offers via SMS
            </label>
        </div>

        <div class="mb-1">
            <label for="billing_first_name" class="form-label">First name</label>
            <input autocomplete="off" class="required form-control" id="billing_first_name" name="billing_first_name" type="text" value="">
        </div>

        <div class="mb-1">
            <label for="billing_last_name" class="form-label">Last name</label>
            <input autocomplete="off" class="required form-control" id="billing_last_name" name="billing_last_name" type="text" value="">
        </div>

        <div class="mb-1">
            <label for="billing_company" class="form-label">Company</label>
            <input autocomplete="off" class="required form-control" id="billing_company" name="billing_company" type="text" value="">
        </div>

        <div class="mb-1">
            <label for="billing_address_1" class="form-label">Address 1</label>
            <input autocomplete="off" class="required form-control" id="billing_address_1" name="billing_address_1" type="text" value="">
        </div>

        <div class="mb-1">
            <label for="billing_address_2" class="form-label">Address 2</label>
            <input autocomplete="off" class="required form-control" id="billing_address_2" name="billing_address_2" type="text" value="">
        </div>

        <div class="mb-1">
            <label for="billing_city" class="form-label">City</label>
            <input autocomplete="off" class="required form-control" id="billing_city" name="billing_city" type="text" value="">
        </div>

        <div class="mb-1">
            <label for="billing_zip" class="form-label">ZIP</label>
            <input autocomplete="off" class="required form-control" id="billing_zip" name="billing_zip" type="text" value="">
        </div>

        <div class="mb-1">
            <label for="billing_state" class="form-label">State</label>
            <input autocomplete="off" class="required form-control" id="billing_state" name="billing_state" type="text" value="">
        </div>

        <div class="mb-1">
            <label for="billing_country" class="form-label">Country</label>
            <select class="form-select" id="billing_country" name="billing_country">
                <option value="SI" selected>Slovenia</option>
                <option value="DE">Germany</option>
            </select>
        </div>

        <div class="mb-1 mt-3">
            <button type="submit" class="btn btn-lg btn-primary">Place order &rarr;</button>
        </div>
    </form>
</div>
</body>
</html>
