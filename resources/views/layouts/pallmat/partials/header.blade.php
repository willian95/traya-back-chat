
<!-- Top Bar Start -->
<div class="topbar">

  <!-- LOGO -->
  <div class="topbar-left">
    <div class="text-center">
      <a href="{{url('/')}}" class="logo"><i class="mdi mdi-radar"></i> <span>PallMat</span></a>
    </div>
  </div>
  
  <!-- Button mobile view to collapse sidebar menu -->
  <nav class="navbar-custom">
    <ul class="list-inline float-right mb-0">
      <li class="list-inline-item dropdown notification-list">
        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
        aria-haspopup="false" aria-expanded="false">
        <i class="fa fa-user-circle fa-3x pt-2" aria-hidden="true"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right profile-dropdown " aria-labelledby="Preview">
        <!-- item-->
        <div class="dropdown-item noti-title">
          <h4 class="text-overflow">
            <h5>{{Auth::user()->name}}</h5>
            <small class="font-weight-bold">
            </small>
          </h4>
        </div>

        <!-- item-->
        <a class="dropdown-item notify-item" href="{{ route('logout') }}"
        onclick="event.preventDefault();
        document.getElementById('logout-form').submit();">
        <i class="mdi mdi-logout"></i>    Cerrar sesión
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
      </form>

    </div>
  </li>

</ul>

<ul class="list-inline menu-left mb-0">
  <li class="float-left">
    <button class="button-menu-mobile open-left waves-light waves-effect">
      <i class="mdi mdi-menu"></i>
    </button>
  </li>
</ul>

</nav>

</div>
<!-- Top Bar End -->
