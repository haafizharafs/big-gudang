<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent my-0 py-0 px-0">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="{{ route('home') }}"><i
                    class="fa-solid fa-house"></i></a></li>
        @if (!request()->is('*dashboard*'))
            @foreach ($breadcrumbs as $breadcrumb)
                @if (!$loop->last)
                    <li class="breadcrumb-item text-sm text-white"><a class="opacity-5 text-white"
                            href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a></li>
                @else
                    <li class="breadcrumb-item text-sm text-white active" aria-current="page">{{ $breadcrumb['label'] }}
                    </li>
                @endif
            @endforeach
        @endif
    </ol>
</nav>
