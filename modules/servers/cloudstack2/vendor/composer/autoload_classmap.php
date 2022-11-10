<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Attribute' => $vendorDir . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
    'Burgomaster' => $vendorDir . '/guzzlehttp/guzzle/build/Burgomaster.php',
    'GuzzleHttp\\Client' => $vendorDir . '/guzzlehttp/guzzle/src/Client.php',
    'GuzzleHttp\\ClientInterface' => $vendorDir . '/guzzlehttp/guzzle/src/ClientInterface.php',
    'GuzzleHttp\\Cookie\\CookieJar' => $vendorDir . '/guzzlehttp/guzzle/src/Cookie/CookieJar.php',
    'GuzzleHttp\\Cookie\\CookieJarInterface' => $vendorDir . '/guzzlehttp/guzzle/src/Cookie/CookieJarInterface.php',
    'GuzzleHttp\\Cookie\\FileCookieJar' => $vendorDir . '/guzzlehttp/guzzle/src/Cookie/FileCookieJar.php',
    'GuzzleHttp\\Cookie\\SessionCookieJar' => $vendorDir . '/guzzlehttp/guzzle/src/Cookie/SessionCookieJar.php',
    'GuzzleHttp\\Cookie\\SetCookie' => $vendorDir . '/guzzlehttp/guzzle/src/Cookie/SetCookie.php',
    'GuzzleHttp\\Exception\\BadResponseException' => $vendorDir . '/guzzlehttp/guzzle/src/Exception/BadResponseException.php',
    'GuzzleHttp\\Exception\\ClientException' => $vendorDir . '/guzzlehttp/guzzle/src/Exception/ClientException.php',
    'GuzzleHttp\\Exception\\ConnectException' => $vendorDir . '/guzzlehttp/guzzle/src/Exception/ConnectException.php',
    'GuzzleHttp\\Exception\\GuzzleException' => $vendorDir . '/guzzlehttp/guzzle/src/Exception/GuzzleException.php',
    'GuzzleHttp\\Exception\\InvalidArgumentException' => $vendorDir . '/guzzlehttp/guzzle/src/Exception/InvalidArgumentException.php',
    'GuzzleHttp\\Exception\\RequestException' => $vendorDir . '/guzzlehttp/guzzle/src/Exception/RequestException.php',
    'GuzzleHttp\\Exception\\SeekException' => $vendorDir . '/guzzlehttp/guzzle/src/Exception/SeekException.php',
    'GuzzleHttp\\Exception\\ServerException' => $vendorDir . '/guzzlehttp/guzzle/src/Exception/ServerException.php',
    'GuzzleHttp\\Exception\\TooManyRedirectsException' => $vendorDir . '/guzzlehttp/guzzle/src/Exception/TooManyRedirectsException.php',
    'GuzzleHttp\\Exception\\TransferException' => $vendorDir . '/guzzlehttp/guzzle/src/Exception/TransferException.php',
    'GuzzleHttp\\HandlerStack' => $vendorDir . '/guzzlehttp/guzzle/src/HandlerStack.php',
    'GuzzleHttp\\Handler\\CurlFactory' => $vendorDir . '/guzzlehttp/guzzle/src/Handler/CurlFactory.php',
    'GuzzleHttp\\Handler\\CurlFactoryInterface' => $vendorDir . '/guzzlehttp/guzzle/src/Handler/CurlFactoryInterface.php',
    'GuzzleHttp\\Handler\\CurlHandler' => $vendorDir . '/guzzlehttp/guzzle/src/Handler/CurlHandler.php',
    'GuzzleHttp\\Handler\\CurlMultiHandler' => $vendorDir . '/guzzlehttp/guzzle/src/Handler/CurlMultiHandler.php',
    'GuzzleHttp\\Handler\\EasyHandle' => $vendorDir . '/guzzlehttp/guzzle/src/Handler/EasyHandle.php',
    'GuzzleHttp\\Handler\\MockHandler' => $vendorDir . '/guzzlehttp/guzzle/src/Handler/MockHandler.php',
    'GuzzleHttp\\Handler\\Proxy' => $vendorDir . '/guzzlehttp/guzzle/src/Handler/Proxy.php',
    'GuzzleHttp\\Handler\\StreamHandler' => $vendorDir . '/guzzlehttp/guzzle/src/Handler/StreamHandler.php',
    'GuzzleHttp\\MessageFormatter' => $vendorDir . '/guzzlehttp/guzzle/src/MessageFormatter.php',
    'GuzzleHttp\\Middleware' => $vendorDir . '/guzzlehttp/guzzle/src/Middleware.php',
    'GuzzleHttp\\Pool' => $vendorDir . '/guzzlehttp/guzzle/src/Pool.php',
    'GuzzleHttp\\PrepareBodyMiddleware' => $vendorDir . '/guzzlehttp/guzzle/src/PrepareBodyMiddleware.php',
    'GuzzleHttp\\Promise\\AggregateException' => $vendorDir . '/guzzlehttp/promises/src/AggregateException.php',
    'GuzzleHttp\\Promise\\CancellationException' => $vendorDir . '/guzzlehttp/promises/src/CancellationException.php',
    'GuzzleHttp\\Promise\\Coroutine' => $vendorDir . '/guzzlehttp/promises/src/Coroutine.php',
    'GuzzleHttp\\Promise\\Create' => $vendorDir . '/guzzlehttp/promises/src/Create.php',
    'GuzzleHttp\\Promise\\Each' => $vendorDir . '/guzzlehttp/promises/src/Each.php',
    'GuzzleHttp\\Promise\\EachPromise' => $vendorDir . '/guzzlehttp/promises/src/EachPromise.php',
    'GuzzleHttp\\Promise\\FulfilledPromise' => $vendorDir . '/guzzlehttp/promises/src/FulfilledPromise.php',
    'GuzzleHttp\\Promise\\Is' => $vendorDir . '/guzzlehttp/promises/src/Is.php',
    'GuzzleHttp\\Promise\\Promise' => $vendorDir . '/guzzlehttp/promises/src/Promise.php',
    'GuzzleHttp\\Promise\\PromiseInterface' => $vendorDir . '/guzzlehttp/promises/src/PromiseInterface.php',
    'GuzzleHttp\\Promise\\PromisorInterface' => $vendorDir . '/guzzlehttp/promises/src/PromisorInterface.php',
    'GuzzleHttp\\Promise\\RejectedPromise' => $vendorDir . '/guzzlehttp/promises/src/RejectedPromise.php',
    'GuzzleHttp\\Promise\\RejectionException' => $vendorDir . '/guzzlehttp/promises/src/RejectionException.php',
    'GuzzleHttp\\Promise\\TaskQueue' => $vendorDir . '/guzzlehttp/promises/src/TaskQueue.php',
    'GuzzleHttp\\Promise\\TaskQueueInterface' => $vendorDir . '/guzzlehttp/promises/src/TaskQueueInterface.php',
    'GuzzleHttp\\Promise\\Tests\\AggregateExceptionTest' => $vendorDir . '/guzzlehttp/promises/tests/AggregateExceptionTest.php',
    'GuzzleHttp\\Promise\\Tests\\CoroutineTest' => $vendorDir . '/guzzlehttp/promises/tests/CoroutineTest.php',
    'GuzzleHttp\\Promise\\Tests\\CreateTest' => $vendorDir . '/guzzlehttp/promises/tests/CreateTest.php',
    'GuzzleHttp\\Promise\\Tests\\EachPromiseTest' => $vendorDir . '/guzzlehttp/promises/tests/EachPromiseTest.php',
    'GuzzleHttp\\Promise\\Tests\\EachTest' => $vendorDir . '/guzzlehttp/promises/tests/EachTest.php',
    'GuzzleHttp\\Promise\\Tests\\FulfilledPromiseTest' => $vendorDir . '/guzzlehttp/promises/tests/FulfilledPromiseTest.php',
    'GuzzleHttp\\Promise\\Tests\\IsTest' => $vendorDir . '/guzzlehttp/promises/tests/IsTest.php',
    'GuzzleHttp\\Promise\\Tests\\NotPromiseInstance' => $vendorDir . '/guzzlehttp/promises/tests/NotPromiseInstance.php',
    'GuzzleHttp\\Promise\\Tests\\PromiseTest' => $vendorDir . '/guzzlehttp/promises/tests/PromiseTest.php',
    'GuzzleHttp\\Promise\\Tests\\PropertyHelper' => $vendorDir . '/guzzlehttp/promises/tests/PropertyHelper.php',
    'GuzzleHttp\\Promise\\Tests\\RejectedPromiseTest' => $vendorDir . '/guzzlehttp/promises/tests/RejectedPromiseTest.php',
    'GuzzleHttp\\Promise\\Tests\\RejectionExceptionTest' => $vendorDir . '/guzzlehttp/promises/tests/RejectionExceptionTest.php',
    'GuzzleHttp\\Promise\\Tests\\TaskQueueTest' => $vendorDir . '/guzzlehttp/promises/tests/TaskQueueTest.php',
    'GuzzleHttp\\Promise\\Tests\\Thennable' => $vendorDir . '/guzzlehttp/promises/tests/Thennable.php',
    'GuzzleHttp\\Promise\\Tests\\Thing1' => $vendorDir . '/guzzlehttp/promises/tests/Thing1.php',
    'GuzzleHttp\\Promise\\Tests\\Thing2' => $vendorDir . '/guzzlehttp/promises/tests/Thing2.php',
    'GuzzleHttp\\Promise\\Tests\\UtilsTest' => $vendorDir . '/guzzlehttp/promises/tests/UtilsTest.php',
    'GuzzleHttp\\Promise\\Utils' => $vendorDir . '/guzzlehttp/promises/src/Utils.php',
    'GuzzleHttp\\Psr7\\AppendStream' => $vendorDir . '/guzzlehttp/psr7/src/AppendStream.php',
    'GuzzleHttp\\Psr7\\BufferStream' => $vendorDir . '/guzzlehttp/psr7/src/BufferStream.php',
    'GuzzleHttp\\Psr7\\CachingStream' => $vendorDir . '/guzzlehttp/psr7/src/CachingStream.php',
    'GuzzleHttp\\Psr7\\DroppingStream' => $vendorDir . '/guzzlehttp/psr7/src/DroppingStream.php',
    'GuzzleHttp\\Psr7\\FnStream' => $vendorDir . '/guzzlehttp/psr7/src/FnStream.php',
    'GuzzleHttp\\Psr7\\Header' => $vendorDir . '/guzzlehttp/psr7/src/Header.php',
    'GuzzleHttp\\Psr7\\InflateStream' => $vendorDir . '/guzzlehttp/psr7/src/InflateStream.php',
    'GuzzleHttp\\Psr7\\LazyOpenStream' => $vendorDir . '/guzzlehttp/psr7/src/LazyOpenStream.php',
    'GuzzleHttp\\Psr7\\LimitStream' => $vendorDir . '/guzzlehttp/psr7/src/LimitStream.php',
    'GuzzleHttp\\Psr7\\Message' => $vendorDir . '/guzzlehttp/psr7/src/Message.php',
    'GuzzleHttp\\Psr7\\MessageTrait' => $vendorDir . '/guzzlehttp/psr7/src/MessageTrait.php',
    'GuzzleHttp\\Psr7\\MimeType' => $vendorDir . '/guzzlehttp/psr7/src/MimeType.php',
    'GuzzleHttp\\Psr7\\MultipartStream' => $vendorDir . '/guzzlehttp/psr7/src/MultipartStream.php',
    'GuzzleHttp\\Psr7\\NoSeekStream' => $vendorDir . '/guzzlehttp/psr7/src/NoSeekStream.php',
    'GuzzleHttp\\Psr7\\PumpStream' => $vendorDir . '/guzzlehttp/psr7/src/PumpStream.php',
    'GuzzleHttp\\Psr7\\Query' => $vendorDir . '/guzzlehttp/psr7/src/Query.php',
    'GuzzleHttp\\Psr7\\Request' => $vendorDir . '/guzzlehttp/psr7/src/Request.php',
    'GuzzleHttp\\Psr7\\Response' => $vendorDir . '/guzzlehttp/psr7/src/Response.php',
    'GuzzleHttp\\Psr7\\Rfc7230' => $vendorDir . '/guzzlehttp/psr7/src/Rfc7230.php',
    'GuzzleHttp\\Psr7\\ServerRequest' => $vendorDir . '/guzzlehttp/psr7/src/ServerRequest.php',
    'GuzzleHttp\\Psr7\\Stream' => $vendorDir . '/guzzlehttp/psr7/src/Stream.php',
    'GuzzleHttp\\Psr7\\StreamDecoratorTrait' => $vendorDir . '/guzzlehttp/psr7/src/StreamDecoratorTrait.php',
    'GuzzleHttp\\Psr7\\StreamWrapper' => $vendorDir . '/guzzlehttp/psr7/src/StreamWrapper.php',
    'GuzzleHttp\\Psr7\\UploadedFile' => $vendorDir . '/guzzlehttp/psr7/src/UploadedFile.php',
    'GuzzleHttp\\Psr7\\Uri' => $vendorDir . '/guzzlehttp/psr7/src/Uri.php',
    'GuzzleHttp\\Psr7\\UriComparator' => $vendorDir . '/guzzlehttp/psr7/src/UriComparator.php',
    'GuzzleHttp\\Psr7\\UriNormalizer' => $vendorDir . '/guzzlehttp/psr7/src/UriNormalizer.php',
    'GuzzleHttp\\Psr7\\UriResolver' => $vendorDir . '/guzzlehttp/psr7/src/UriResolver.php',
    'GuzzleHttp\\Psr7\\Utils' => $vendorDir . '/guzzlehttp/psr7/src/Utils.php',
    'GuzzleHttp\\RedirectMiddleware' => $vendorDir . '/guzzlehttp/guzzle/src/RedirectMiddleware.php',
    'GuzzleHttp\\RequestOptions' => $vendorDir . '/guzzlehttp/guzzle/src/RequestOptions.php',
    'GuzzleHttp\\RetryMiddleware' => $vendorDir . '/guzzlehttp/guzzle/src/RetryMiddleware.php',
    'GuzzleHttp\\Test\\FunctionsTest' => $vendorDir . '/guzzlehttp/guzzle/tests/functionsTest.php',
    'GuzzleHttp\\Test\\Handler\\CurlFactoryTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Handler/CurlFactoryTest.php',
    'GuzzleHttp\\Test\\Handler\\CurlHandlerTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Handler/CurlHandlerTest.php',
    'GuzzleHttp\\Test\\Handler\\EasyHandleTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Handler/EasyHandleTest.php',
    'GuzzleHttp\\Test\\Handler\\MockHandlerTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Handler/MockHandlerTest.php',
    'GuzzleHttp\\Test\\Handler\\ProxyTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Handler/ProxyTest.php',
    'GuzzleHttp\\Test\\Handler\\StreamHandlerTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Handler/StreamHandlerTest.php',
    'GuzzleHttp\\Test\\InternalUtilsTest' => $vendorDir . '/guzzlehttp/guzzle/tests/InternalUtilsTest.php',
    'GuzzleHttp\\Test\\StrClass' => $vendorDir . '/guzzlehttp/guzzle/tests/functionsTest.php',
    'GuzzleHttp\\Tests\\ClientTest' => $vendorDir . '/guzzlehttp/guzzle/tests/ClientTest.php',
    'GuzzleHttp\\Tests\\CookieJar\\CookieJarTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Cookie/CookieJarTest.php',
    'GuzzleHttp\\Tests\\CookieJar\\FileCookieJarTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Cookie/FileCookieJarTest.php',
    'GuzzleHttp\\Tests\\CookieJar\\SessionCookieJarTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Cookie/SessionCookieJarTest.php',
    'GuzzleHttp\\Tests\\CookieJar\\SetCookieTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Cookie/SetCookieTest.php',
    'GuzzleHttp\\Tests\\Exception\\ConnectExceptionTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Exception/ConnectExceptionTest.php',
    'GuzzleHttp\\Tests\\Exception\\ReadSeekOnlyStream' => $vendorDir . '/guzzlehttp/guzzle/tests/Exception/RequestExceptionTest.php',
    'GuzzleHttp\\Tests\\Exception\\RequestExceptionTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Exception/RequestExceptionTest.php',
    'GuzzleHttp\\Tests\\Exception\\SeekExceptionTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Exception/SeekExceptionTest.php',
    'GuzzleHttp\\Tests\\HandlerStackTest' => $vendorDir . '/guzzlehttp/guzzle/tests/HandlerStackTest.php',
    'GuzzleHttp\\Tests\\Handler\\CurlMultiHandlerTest' => $vendorDir . '/guzzlehttp/guzzle/tests/Handler/CurlMultiHandlerTest.php',
    'GuzzleHttp\\Tests\\MessageFormatterTest' => $vendorDir . '/guzzlehttp/guzzle/tests/MessageFormatterTest.php',
    'GuzzleHttp\\Tests\\MiddlewareTest' => $vendorDir . '/guzzlehttp/guzzle/tests/MiddlewareTest.php',
    'GuzzleHttp\\Tests\\PoolTest' => $vendorDir . '/guzzlehttp/guzzle/tests/PoolTest.php',
    'GuzzleHttp\\Tests\\PrepareBodyMiddlewareTest' => $vendorDir . '/guzzlehttp/guzzle/tests/PrepareBodyMiddlewareTest.php',
    'GuzzleHttp\\Tests\\Psr7\\AppendStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/AppendStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\BadStream' => $vendorDir . '/guzzlehttp/psr7/tests/StreamDecoratorTraitTest.php',
    'GuzzleHttp\\Tests\\Psr7\\BaseTest' => $vendorDir . '/guzzlehttp/psr7/tests/BaseTest.php',
    'GuzzleHttp\\Tests\\Psr7\\BufferStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/BufferStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\CachingStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/CachingStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\DroppingStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/DroppingStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\ExtendedUriTest' => $vendorDir . '/guzzlehttp/psr7/tests/UriTest.php',
    'GuzzleHttp\\Tests\\Psr7\\FnStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/FnStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\HasToString' => $vendorDir . '/guzzlehttp/psr7/tests/HasToString.php',
    'GuzzleHttp\\Tests\\Psr7\\HeaderTest' => $vendorDir . '/guzzlehttp/psr7/tests/HeaderTest.php',
    'GuzzleHttp\\Tests\\Psr7\\Helpers' => $vendorDir . '/guzzlehttp/psr7/tests/Helpers.php',
    'GuzzleHttp\\Tests\\Psr7\\InflateStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/InflateStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\Integration\\ServerRequestFromGlobalsTest' => $vendorDir . '/guzzlehttp/psr7/tests/Integration/ServerRequestFromGlobalsTest.php',
    'GuzzleHttp\\Tests\\Psr7\\LazyOpenStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/LazyOpenStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\LimitStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/LimitStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\MessageTest' => $vendorDir . '/guzzlehttp/psr7/tests/MessageTest.php',
    'GuzzleHttp\\Tests\\Psr7\\MimeTypeTest' => $vendorDir . '/guzzlehttp/psr7/tests/MimeTypeTest.php',
    'GuzzleHttp\\Tests\\Psr7\\MultipartStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/MultipartStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\NoSeekStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/NoSeekStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\PumpStreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/PumpStreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\QueryTest' => $vendorDir . '/guzzlehttp/psr7/tests/QueryTest.php',
    'GuzzleHttp\\Tests\\Psr7\\ReadSeekOnlyStream' => $vendorDir . '/guzzlehttp/psr7/tests/ReadSeekOnlyStream.php',
    'GuzzleHttp\\Tests\\Psr7\\RequestTest' => $vendorDir . '/guzzlehttp/psr7/tests/RequestTest.php',
    'GuzzleHttp\\Tests\\Psr7\\ResponseTest' => $vendorDir . '/guzzlehttp/psr7/tests/ResponseTest.php',
    'GuzzleHttp\\Tests\\Psr7\\ServerRequestTest' => $vendorDir . '/guzzlehttp/psr7/tests/ServerRequestTest.php',
    'GuzzleHttp\\Tests\\Psr7\\Str' => $vendorDir . '/guzzlehttp/psr7/tests/StreamDecoratorTraitTest.php',
    'GuzzleHttp\\Tests\\Psr7\\StreamDecoratorTraitTest' => $vendorDir . '/guzzlehttp/psr7/tests/StreamDecoratorTraitTest.php',
    'GuzzleHttp\\Tests\\Psr7\\StreamTest' => $vendorDir . '/guzzlehttp/psr7/tests/StreamTest.php',
    'GuzzleHttp\\Tests\\Psr7\\StreamWrapperTest' => $vendorDir . '/guzzlehttp/psr7/tests/StreamWrapperTest.php',
    'GuzzleHttp\\Tests\\Psr7\\UploadedFileTest' => $vendorDir . '/guzzlehttp/psr7/tests/UploadedFileTest.php',
    'GuzzleHttp\\Tests\\Psr7\\UriComparatorTest' => $vendorDir . '/guzzlehttp/psr7/tests/UriComparatorTest.php',
    'GuzzleHttp\\Tests\\Psr7\\UriNormalizerTest' => $vendorDir . '/guzzlehttp/psr7/tests/UriNormalizerTest.php',
    'GuzzleHttp\\Tests\\Psr7\\UriResolverTest' => $vendorDir . '/guzzlehttp/psr7/tests/UriResolverTest.php',
    'GuzzleHttp\\Tests\\Psr7\\UriTest' => $vendorDir . '/guzzlehttp/psr7/tests/UriTest.php',
    'GuzzleHttp\\Tests\\Psr7\\UtilsTest' => $vendorDir . '/guzzlehttp/psr7/tests/UtilsTest.php',
    'GuzzleHttp\\Tests\\RedirectMiddlewareTest' => $vendorDir . '/guzzlehttp/guzzle/tests/RedirectMiddlewareTest.php',
    'GuzzleHttp\\Tests\\RetryMiddlewareTest' => $vendorDir . '/guzzlehttp/guzzle/tests/RetryMiddlewareTest.php',
    'GuzzleHttp\\Tests\\Server' => $vendorDir . '/guzzlehttp/guzzle/tests/Server.php',
    'GuzzleHttp\\Tests\\TransferStatsTest' => $vendorDir . '/guzzlehttp/guzzle/tests/TransferStatsTest.php',
    'GuzzleHttp\\Tests\\UriTemplateTest' => $vendorDir . '/guzzlehttp/guzzle/tests/UriTemplateTest.php',
    'GuzzleHttp\\TransferStats' => $vendorDir . '/guzzlehttp/guzzle/src/TransferStats.php',
    'GuzzleHttp\\UriTemplate' => $vendorDir . '/guzzlehttp/guzzle/src/UriTemplate.php',
    'GuzzleHttp\\Utils' => $vendorDir . '/guzzlehttp/guzzle/src/Utils.php',
    'JsonException' => $vendorDir . '/symfony/polyfill-php73/Resources/stubs/JsonException.php',
    'Normalizer' => $vendorDir . '/symfony/polyfill-intl-normalizer/Resources/stubs/Normalizer.php',
    'PhpToken' => $vendorDir . '/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
    'Stringable' => $vendorDir . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
    'UnhandledMatchError' => $vendorDir . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
    'ValueError' => $vendorDir . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
);