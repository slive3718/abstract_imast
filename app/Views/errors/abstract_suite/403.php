<?php
use GuzzleHttp\Exception\GuzzleException;
/** @var GuzzleException $exception */
?>

<div class="d-flex align-items-center justify-content-center vh-100">
    <div class="text-center">
        <h1 class="display-1 fw-bold"><?=$exception->getCode()?></h1>
        <p class="fs-3"><?=ucfirst(($exception->getResponse())->getReasonPhrase())?>.</p>
        <p class="lead">
            The resource you're looking for doesn't exist.
        </p>
        <a href="<?=base_url()?>" class="btn btn-lg btn-primary">Go Home</a>
    </div>
</div>
