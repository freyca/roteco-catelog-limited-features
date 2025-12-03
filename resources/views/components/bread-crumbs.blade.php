<nav class="flex mx-4 px-3 md:px-5 py-3 md:py-4 text-primary-700 border border-primary-200 rounded-full" aria-label="breadcrumb">
    <ol class="inline-flex items-center md:space-x-2 rtl:space-x-reverse truncate">
        @foreach ($breadcrumbs->getBreadCrumbs() as $breadcrumb => $url)
            @if($loop->first)
                <li class="inline-flex items-center">
                    <a href="{{ $url }}" class="inline-flex items-center text-sm font-medium text-primary-700 hover:text-primary-600">
                        @svg($breadcrumb, 'w-4 h-4')
                    </a>
                </li>
            @else
                <li @if($loop->last) {{ 'aria-current=page class=truncate' }} @endif>
                    <div class="flex items-center truncate">
                        @svg('heroicon-c-chevron-right', 'h-4 w-4 text-primary-800 text-semibold')
                        @if(! $loop->last)
                            <a href="{{$url}}" class="ms-1 text-sm font-medium text-primary-700 hover:text-primary-600 md:ms-2">{{ $breadcrumb }}</a>
                        @else
                            <span class="ms-1 text-sm font-medium text-primary-500 md:ms-2 truncate">{{ $breadcrumb }}</span>
                        @endif
                    </div>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
