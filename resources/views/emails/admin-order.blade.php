@component('mail::message')
# {{ __('New Order Created') }}

## {{ __('Order Details') }}

- **{{ __('Order ID') }}:** {{ $order->id }}
- **{{ __('Customer') }}:** {{ $order->user->name }} {{ $order->user->surname }}
- **{{ __('Customer Email') }}:** {{ $order->user->email }}
- **{{ __('Order Status') }}:** {{ $order->status->getLabel() }}
- **{{ __('Total Amount') }}:** €{{ number_format($order->purchase_cost / 100, 2) }}
- **{{ __('Payment Method') }}:** {{ $order->payment_method->value }}

## {{ __('Products') }}

| {{ __('Product') }} | {{ __('Quantity') }} | {{ __('Unit Price') }} | {{ __('Total') }} |
|---------|----------|---------|-------|
@foreach ($products as $product)
| {{ $product->orderable->name }} | {{ $product->quantity }} | €{{ number_format($product->unit_price / 100, 2) }} | €{{ number_format(($product->unit_price * $product->quantity) / 100, 2) }} |
@endforeach

## {{ __('Shipping Address') }}

{{ $order->shippingAddress->name }} {{ $order->shippingAddress->surname }}
{{ $order->shippingAddress->address }}
{{ $order->shippingAddress->zip_code }} {{ $order->shippingAddress->city }}
{{ $order->shippingAddress->state }}, {{ $order->shippingAddress->country }}

@if ($order->billingAddress && $order->billingAddress->id !== $order->shippingAddress->id)
## {{ __('Billing Address') }}

{{ $order->billingAddress->name }} {{ $order->billingAddress->surname }}
{{ $order->billingAddress->address }}
{{ $order->billingAddress->zip_code }} {{ $order->billingAddress->city }}
{{ $order->billingAddress->state }}, {{ $order->billingAddress->country }}
@endif

---

{{ __('Best regards') }},
{{ config('app.name') }}
@endcomponent
