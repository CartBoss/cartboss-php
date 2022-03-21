## Requirements ##

To use CartBoss SDK, the following things are required:

+ Get yourself a free [CartBoss account](https://app.cartboss.io/account/register). No sign up costs.
+ Add new [website](https://app.cartboss.io/sites/) to get API key.
+ PHP >= 7.3
+ Up-to-date OpenSSL (or other SSL/TLS toolkit)

## Composer Installation ##

By far the easiest way to install the CartBoss SDK is to require it
with [Composer](http://getcomposer.org/doc/00-intro.md).

    $ composer require cartboss/cartboss-php:^2.0

    {
        "require": {
            "cartboss/cartboss-php": "^2.0"
        }
    }

## Before you begin ##

Please take a few minutes to learn about basic CartBoss concepts.

[//]: # (### CartBoss flow ###)

[//]: # (1. On your site, visitor adds an item to the cart)

[//]: # (2. On checkout page, visitor enters &#40;at least&#41; her phone number along with any other information)

[//]: # (3. An AddToCart event is sent to CartBoss and processed within 1 minute )

[//]: # (4. A sequence of abandoned cart text messages is generated for this visitor)

[//]: # (5. After initial delay, first SMS is sent to visitor - with a link to restore her cart &#40;provided by you&#41;)

[//]: # (6. Link that takes the visitor back to your page also includes additional CartBoss parameters &#40;eg. cb__att, )

[//]: # (   cb__discount,...&#41; and automatic utm_* values.)

[//]: # (7. CartBoss URL interceptors parse and validate cb__ prefixed query parameters &#40;see URL Interceptors&#41;)

[//]: # (8. Visitor's cart is restored in new browser session and visitor is redirected back to the checkout page)

[//]: # (9. Once visitor places her order, a Purchase event is sent back to CartBoss)

[//]: # (10. Existing abandoned cart sequence is destroyed along with new post-purchase sequence being created)

### 1. Events ###

CartBoss is an event based service. Our SDK includes everything you need to build, validate and send events to CartBoss
either in "real-time" or from your backend.

#### 1.1. AddToCart event ####

AddToCart event occurs when visitor leaves your site without placing an order. It is up to you to decide when this makes
sense for your case, but most common implementation is to send AddToCart event **every time** visitor updates one of the
checkout fields (eg. First name, Phone, Cart items, ...).

AddToCart event is then processed by CartBoss and a queue of abandoned cart messages for provided phone number is
created at our end. If there's no following Purchase event received, CartBoss will start sending messages accordingly to
your abandoned cart automation setup.

It's perfectly fine to send multiple AddToCart events for same order.

#### 1.2. Purchase event ####

Purchase event occurs when visitor places an order. Purchase event is very similar to AddToCart event, with an addition
of CartBoss Attribution token (explained below).

When CartBoss receives Purchase event for particular order, all remaining messages from abandoned cart sequence are
destroyed and never sent. If enabled, a new post-purchase sequence is created.

Because of this, it is very important to make sure you send Purchase event for all "completed" orders from your
backend (eg. cronjob). Basic implementation would include adding a new boolean field `send_cartboss_purchase` to your
order table with default value set as false, updated to true once order is placed.

### 2. URL Interceptors ###

Most text messages (SMS) sent by CartBoss to your contacts contain an urls that take them back to your site when
clicked.

CartBoss "communicates" with your site through a few special URL query parameters prefixed with "cb__". With the help of
CartBoss SDK you can easily access intercepted values through registered listeners.

#### 2.1. Attribution Interceptor ####

CartBoss's Attribution token (cb__att) is a 64char hash injected into **all urls** pointing to your site. It helps
CartBoss to distinct between organic and CartBoss-based purchases. Your main objective is to intercept, store and pass
attribution token to Purchase event once order is successfully placed.

Example urls with Attribution Token injected:

```
https://www.your-site.com?cb__att=7zd1n87e8712z87nzdqs87z1278dnz1
https://store.your-site.com/path/?foo=bar&?cb__att=7zd1n87e8712z87nzdqs87z1278dnz1
```

#### 2.2. Discount Interceptor ####

CartBoss's Discount token (cb__discount) is an encrypted and urlencoded array of three values (**type|code|value**). It
enables you to send dynamic offers to your visitors (eg. *Hi Mike, order now and get 20% OFF of your entire order. Click
here <url>*).

Example urls with Discount Token injected:

```
https://www.your-site.com?cb__discount=<encoded>
https://store.your-site.com/path/?foo=bar&?cb__discount=<encoded>
```

Discount interceptor validates, decodes and returns valid Coupon object if discount is provided through SMS message.

## Example implementation ##

Our code includes Example project which can be found at this link
[Example project](https://github.com/CartBoss/cartboss-php/tree/main/examples). In case you need additional help, please
contact us.

## Getting started ##

### 1. Initializing CartBoss SDK ###

Initialize CartBoss SDK library with your API key. It is recommended to initialize CartBoss SDK and register URL
interceptors on each request and as early as possible in your code.

```php
$cartboss = new \CartBoss\Api\CartBoss("l33t87zd1n87e8712z87nzdqs87z1278dnz187wzne");
``` 

### 2. Registering Attribution interceptor ###

Every time **Attribution token** is intercepted and pushed to your implementation of `onAttributionIntercepted()`
method, it's advised to store it into session/cart/order/database for later use, as you're going to attach it to
Purchase event later on.

```php
$cartboss->onAttributionIntercepted(function(Attribution $attribution) {
    print $attribution->isValid(); // bool
    print $attribution->getToken(); // n87e8712z87nzdqs87z12712z87nzdq78dnz187w
    
    
});
``` 

### 3. Registering Discount interceptor ###

Every time **Discount token** is intercepted and pushed to your implementation of `onCouponIntercepted()` method, you
should insert a new coupon (if it doesn't already exist) to your database and attach it to visitor's cart/order/session.

```php
$cartboss->onCouponIntercepted(function(Coupon $coupon) {
    print $attribution->isValid(); // bool
    print $attribution->getType(); // PERCENTAGE|FIXED_AMOUNT|FREE_SHIPPING|CUSTOM
    print $attribution->getValue(); // float
    print $attribution->getCode(); // str
    
    // helper methods
    print $attribution->isFreeShipping(); // bool
    print $attribution->isPercentage(); // bool
    print $attribution->isFixedAmount(); // bool
    print $attribution->isCustom(); // bool
});
``` 

### 4. Sending AddToCart event ###

AddToCart event is usually sent, whenever new checkout information is available. For example; when visitor enters a
phone number, sets first name, or updates delivery country...

**There are two very important aspects to keep in mind:**

+ order_id provided through `$order->setId("...")` is a glue between AddToCart and Purchase events. It tells CartBoss
  that order which has been previously "abandoned" is now "purchased".
+ checkout_url provided through `$order->setCheckoutUrl("...")` points to your script that should be able to restore
  previously abandoned session/order (Desktop) in a new browser (Phone). Keep in mind cookies are not shared between
  browsers and/or webviews (Facebook app, etc.).

Minimal AddToCart event example.
You can find full example at
[event_atc.php](https://github.com/CartBoss/cartboss-php/blob/main/examples/event_atc.php).

```php
// Create AddToCart event
$event = new AddToCartEvent();


// Create contact
$contact = new Contact();
$contact->setPhone("1234567");
$contact->setCountry("US");
$contact->setFirstName("Jake");
$contact->setAcceptsMarketing(true); // in case your site supports "consent checkout field"

// Add contact to event
$event->setContact($contact);

// Create order
$order = new Order();
$order->setId("1001"); // id that uniquely represents cart/order in your local database 
$order->setValue(19.99); // float 2 decimals 
$order->setCurrency('EUR'); // currency code

// Set publicly accessible url to the script that can restore visitor's order from provided order id  
// https://www.your-site.com/cartboss-restore-cart.php?order_id=1001
// https://www.your-site.com/cartboss-restore-cart.php?order_hash=9n12998as7as798d791dn988jdsa (if you don't want to expose db order_id)
$order->setCheckoutUrl("https://site.com/cartboss/cart_restore.php?order_id=1001"); 

// Add order to event
$event->setOrder($order);

// Send event to CartBoss
try {
    $cartboss->sendOrderEvent($event);

} catch (EventValidationException $e) {
    var_dump($e->getMessage());

} catch (ApiException $e) {
    var_dump($e->getMessage());
}
``` 

### 5. Restoring abandoned cart ###

Each abandoned cart text messages includes shortened url (checkout_url) back to your website. URL is provided through a
method `setCheckoutUrl()` with AddToCart event. This handler must be publicly accessible and able to restore visitors
old session within a new browser.

Implementation of this script is up to your business logic, but in abstract implementation, it should do the following:

1. parse url parameter for order_id
2. fetch order from your local database
3. check if order exists and is still in "abandoned" state
4. store order_id to visitors session
5. redirect to your checkout page (along with utm_ params)
6. otherwise, redirect visitor to your home page

You can find example cart restore script at
[cart_restore.php](https://github.com/CartBoss/cartboss-php/blob/main/examples/cart_restore.php).


### 6. Sending Purchase event ###
Purchase event is sent once the order has been placed on your store. It is highly suggested sending these events from 
your backend eg. 1min cronjob.  

Minimal Purchase event example. You can find full example at 
[event_purchase.php](https://github.com/CartBoss/cartboss-php/blob/main/examples/event_purchase.php).

```php
// Create AddToCart event
$event = new PurchaseEvent();

// Attach attribution token, you have received through Attribution URL Interceptor when visitor visited your site 
// through SMS link.  
$event->setAttributionToken("...");

// Create contact
$contact = new Contact();
$contact->setPhone("1234567");
$contact->setCountry("US");
$contact->setFirstName("Jake");
$contact->setAcceptsMarketing(true); // in case your site supports "consent checkout field"

// Add contact to event
$event->setContact($contact);

// Create order
$order = new Order();
$order->setId("1001"); // id that uniquely represents cart/order in your local database 
$order->setValue(19.99); // float 2 decimals 
$order->setCurrency('EUR'); // currency code

// Add order to event
$event->setOrder($order);

// Send event to CartBoss
try {
    $cartboss->sendOrderEvent($event);

} catch (EventValidationException $e) {
    var_dump($e->getMessage());

} catch (ApiException $e) {
    var_dump($e->getMessage());
}
``` 

### FAQ ###
Q: What happens when AddToCart event is sent to CartBoss?  
A: A queue of abandoned cart text messages is created for provided phone number - usually one to three texts.

Q: When does CartBoss stop sending abandoned cart texts?  
A: When a Purchase event with same $order->setId() as AddToCart event is received, or all texts from the queue have 
been sent out.

Q: What happens if I don't provide attribution token with Purchase event?  
A: Such event will not count towards CartBoss statistics, as it's considered as organic conversion.

Q: What is checkout_url I have to send with AddToCart event?  
A: An URL that is sent to your visitor to restore session/cart in a new mobile browser.

Q: Should I send a Purchase event for every purchase made on our site - organic and CartBoss based?  
A: Yes 

Q: What should I don if sending an event fails?  
A: You can simply ignore AddToCart errors, but should do your best to deliver all Purchase events.

### Helpers ###

We provide a few helper methods to ease your CartBoss SDK integration.

#### PHP ####

Please see [```CartBoss\Api\Utils```](https://github.com/CartBoss/cartboss-php/blob/main/src/Utils.php) for available
helper functions.

#### JS ####

Please see [CartBossJS](https://github.com/CartBoss/cartboss-php/blob/main/examples/js/cartboss-helper.js) for more
info. jQuery required.

