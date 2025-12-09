<?php

use App\Repositories\Shipping\ShippingRepositoryInterface;
use App\Services\Shipping;

beforeEach(function () {
    test()->repository = mock(ShippingRepositoryInterface::class);
    test()->shipping = new Shipping(test()->repository);
});

describe('Shipping Service', function () {
    it('gets track status', function () {
        test()->repository
            ->shouldReceive('getTrackStatus')
            ->once()
            ->andReturn('In Transit');

        $status = test()->shipping->getTrackStatus();

        expect($status)->toBe('In Transit');
    });

    it('gets track status as delivered', function () {
        test()->repository
            ->shouldReceive('getTrackStatus')
            ->once()
            ->andReturn('Delivered');

        $status = test()->shipping->getTrackStatus();

        expect($status)->toBe('Delivered');
    });

    it('checks if shipment is shipped', function () {
        test()->repository
            ->shouldReceive('isShipped')
            ->once()
            ->andReturn(true);

        $isShipped = test()->shipping->isShipped();

        expect($isShipped)->toBeTrue();
    });

    it('checks if shipment is not shipped', function () {
        test()->repository
            ->shouldReceive('isShipped')
            ->once()
            ->andReturn(false);

        $isShipped = test()->shipping->isShipped();

        expect($isShipped)->toBeFalse();
    });

    it('gets track information url', function () {
        $url = 'https://tracking.courier.com/track/ABC123';

        test()->repository
            ->shouldReceive('getTrackInformationUrl')
            ->once()
            ->andReturn($url);

        $trackUrl = test()->shipping->getTrackInformationUrl();

        expect($trackUrl)->toBe($url);
    });
});
