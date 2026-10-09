<?php

test('returns a successful response', function () {
    $response = $this->get(route('website.home'));

    $response->assertOk();
});
