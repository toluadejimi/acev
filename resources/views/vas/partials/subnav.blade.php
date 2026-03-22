<nav class="vb-subnav" aria-label="Bill payment services">
    <a href="{{ route('vas.airtime') }}" class="vb-subnav__a{{ request()->routeIs('vas.airtime') ? ' vb-subnav__a--on' : '' }}">Airtime</a>
    <a href="{{ route('vas.data') }}" class="vb-subnav__a{{ request()->routeIs('vas.data') ? ' vb-subnav__a--on' : '' }}">Data</a>
    <a href="{{ route('vas.cable') }}" class="vb-subnav__a{{ request()->routeIs('vas.cable') ? ' vb-subnav__a--on' : '' }}">Cable TV</a>
    <a href="{{ route('vas.electricity') }}" class="vb-subnav__a{{ request()->routeIs('vas.electricity') ? ' vb-subnav__a--on' : '' }}">Electricity</a>
</nav>
