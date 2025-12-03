<x-layouts.app :seotags="$seotags">

    <div class="mx-10 flex flex-col items-center justify-center ">
        <div class="my-6 mt-20">
            @if($order->status === \App\Enums\OrderStatus::PaymentFailed)
                @svg('heroicon-c-x-circle', 'w-40 h-40 text-red-500')
            @else
                @svg('heroicon-o-check-circle', 'w-40 h-40 text-green-600')
            @endif
        </div>

        @if($order->payment_method === \App\Enums\PaymentMethod::BankTransfer)
        <div class="my-6">
            <p>En breves recibirás un correo electrónico con la información necesaria para realizar el pago</p>
        </div>
        @endif

        <div class="my-6 text-center font-bold text-xl">
        @if($order->status === \App\Enums\OrderStatus::PaymentFailed)
            <p>Algo ha fallado con tu pedido</p>
            <p>Nos pondremos en contacto contigo para solucionarlo</p>
        @else
            <p>Tu pedido se ha completado</p>
            <p>En breves recibirás un email de confirmación</p>
        @endif
        </div>
    </div>

    <x-buttons.whats-app-button />

</x-layouts.app>
