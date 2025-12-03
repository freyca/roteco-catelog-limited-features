@component('mail::message')
# {{ __('Order Confirmation') }}

{{ __('Hello') }} {{ $order->user->name }},

{{ __('Thank you for your order!') }}

## {{ __('Order Details') }}

- **{{ __('Order ID') }}:** {{ $order->id }}
- **{{ __('Order Status') }}:** {{ $order->status->getLabel() }}
- **{{ __('Total Amount') }}:** €{{ number_format($order->purchase_cost / 100, 2) }}
- **{{ __('Payment Method') }}:** {{ $order->payment_method->value }}

## {{ __('Products') }}

@foreach ($products as $product)
| {{ __('Product') }} | {{ __('Quantity') }} | {{ __('Price') }} |
|---------|----------|-------|
| {{ $product->orderable->name }} | {{ $product->quantity }} | €{{ number_format($product->unit_price / 100, 2) }} |
@endforeach

## {{ __('Shipping Address') }}

{{ $order->shippingAddress->name }} {{ $order->shippingAddress->surname }}
{{ $order->shippingAddress->address }}
{{ $order->shippingAddress->zip_code }} {{ $order->shippingAddress->city }}
{{ $order->shippingAddress->state }}, {{ $order->shippingAddress->country }}

{{ __('If you have any questions, please contact us.') }}

{{ __('Best regards') }},
{{ config('app.name') }}
@endcomponent
