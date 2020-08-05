@php
$user=\App\Models\BackpackUser::find(Auth::user()->id);
//dd($user->permissions);
@endphp
<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
  <div class="sidebar-inner slimscrollleft">
    <!--- Divider -->
    <div id="sidebar-menu">
      <ul>
        <!-- <li class="menu-title">Inicio</li> -->
        <li>
          <a href="{{url('/')}}" class="waves-effect waves-primary">
            <i class="ti-home"></i><span> Inicio </span>
          </a>
        </li>
        <!-- Si es Administrador  -->
        @if($user->hasRole('Administrador'))
        <li>
          <a href="{{url('/admin')}}" class="waves-effect waves-primary">
            <i class="ti-user"></i><span>Gestión de usuarios </span>
          </a>
          <a href="{{url('/invoices')}}" class="waves-effect waves-primary">
            <i class="fa fa-send"></i><span>Gestión de Facturas </span>
          </a>
        </li>
        <!--OPCIONES DEL OPERADOR  -->
        <li class=" has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="ti-list"></i><span> Operador </span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            @if($user->hasPermissionTo('Permisos Contrataciones'))
            <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect waves-primary">
                <i class="ti-rocket"></i><span> Contrataciones </span>
                <span class="menu-arrow"></span>
              </a>
              <ul class="list-unstyled">
                <li><a href="{{url('PersonalInformation')}}"><span>Registrar Datos Basicos del prestador</span></a></li>
                <li><a href="{{url('LenderList')}}"><span>Listado de Prestadores con datos asociados</span></a></li>
                <li class="has_sub">
                  <a href="javascript:void(0);" class="waves-effect">
                    <span>Configuración de modulos</span>
                    <span class="menu-arrow"></span>
                  </a>
                  <ul>
                    <li class="has_sub">
                      <li><a href="{{url('TravelPerModule')}}">Registrar Módulo</a></li>
                      <li><a href="{{url('EditTravelPerModule')}}"><span>Editar  Módulo</span></a></li>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
            @endif

            @if($user->hasPermissionTo('Permisos Operaciones'))
            <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect waves-primary">
                <i class="ti-rocket"></i><span> Operaciones </span>
                <span class="menu-arrow"></span>
              </a>
              <ul class="list-unstyled">
                <li class="has_sub">
                  <a href="javascript:void(0);" class="waves-effect">
                    <span>Traslados</span>
                    <span class="menu-arrow"></span>
                  </a>
                  <ul>
                    <li><a href="{{url('transfers')}}"><span>De Momento</span></a></li>
                    <li><a href="{{url('importTransfer')}}"><span>Programado</span></a></li>
                    <li><a href="{{ url('operator_transfers')}}"><i class="ti-list"></i><span>Traslados por asignar prestador</span></a></li>
                    <li><a href="{{ url('list_transfers')}}"><i class="ti-list"></i><span>Listado de traslados</span></a></li>
                    <li><a href="{{url('TemporaryTransfers')}}"><span>Traslados con errores</span></a></li>
                  </ul>
                </li>
              </ul>
            </li>
            @endif
            <!-- <a href="javascript:void(0);" class="waves-effect waves-primary">
              <i class="ti-list"></i><span> Contrataciones </span>
              <span class="menu-arrow"></span>
            </a>
            <ul class="list-unstyled">
              <li><a href="{{url('PersonalInformation')}}"><span>Registrar Datos Basicos del prestador</span></a></li>
              <li><a href="{{url('LenderList')}}"><span>Listado de Prestadores con datos asociados</span></a></li>
              <li class="has_sub">
                <a href="javascript:void(0);" class="waves-effect">
                  <span>Configuración de modulos</span>
                  <span class="menu-arrow"></span>
                </a>
                <ul>
                  <li class="has_sub">
                    <li><a href="{{url('TravelPerModule')}}">Registrar Módulo</a></li>
                    <li><a href="{{url('EditTravelPerModule')}}"><span>Editar  Módulo</span></a></li>
                  </li>
                </ul>
              </li>
            </ul> -->
            <!-- <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect waves-primary">
                <i class="ti-rocket"></i><span> Operaciones </span>
                <span class="menu-arrow"></span>
              </a>
              <ul class="list-unstyled">
                <li class="has_sub">
                  <a href="javascript:void(0);" class="waves-effect">
                    <span>Traslados</span>
                    <span class="menu-arrow"></span>
                  </a>
                  <ul>
                    <li><a href="{{url('transfers')}}"><span>De Momento</span></a></li>
                    <li><a href="{{url('importTransfer')}}"><span>Programado</span></a></li>
                    <li><a href="{{ url('operator_transfers')}}"><i class="ti-list"></i><span>Traslados por asignar prestador</span></a></li>
                    <li><a href="{{ url('list_transfers')}}"><i class="ti-list"></i><span>Listado de traslados</span></a></li>
                    <li><a href="{{url('TemporaryTransfers')}}"><span>Traslados con errores</span></a></li>

                  </ul>
                </li>
              </ul>
            </li> -->
          </ul>
        </li>

        <!--OPCIONES DEL PRESTADOR  -->
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="fa fa-car"></i><span>Prestador</span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            <a href="javascript:void(0);" class="waves-effect waves-primary">
              <i class="ti-settings"></i><span> Configuración Básica </span>
              <span class="menu-arrow"></span>
            </a>
            <ul class="list-unstyled">
              <li><a href="{{url('DriverRegistration')}}">Registrar Choferes</a></li>
            </ul>
            <li>
              <a href="{{ url('lender_transfers')}}">
                <i class="ti-car"></i>
                <span>Asignar Chofer</span>
              </a>
            </li>
            @if($user->lender)
            <li>
              <a href="{{ asset('assets/voucher.pdf') }}" download="Voucher">
                <i class="fa fa-file-text"></i>
                <span>Generar Voucher</span>
              </a>
            </li>
            @endif



          </ul>

        </li>

        <!--OPCIONES DEL CHOFER  -->
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="ti-settings"></i><span> Chofer</span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            <a href="{{url('ListTransfers')}}">
              <i class="ti-list"></i>
              <span>Lista de traslados asignados</span>
            </a>
          </ul>
        </li>

        @endif
        <!--OPCIONES DEL PRESTADOR  -->
        @if($user->hasRole('Prestador') && !$user->hasRole('Administrador'))
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="ti-settings"></i><span> Configuración Básica </span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            <li><a href="{{url('DriverRegistration')}}">Registrar Choferes</a></li>
          </ul>
          <li>
            <a href="{{ url('lender_transfers')}}">
              <i class="ti-list"></i>
              <span>Asignar Chofer</span>
            </a>
          </li>
          @if($user->lender)
          <li>
            <a href="{{ asset('assets/voucher.pdf') }}" download="Voucher">
              <i class="ti-list"></i>
              <span>Generar Voucher</span>
            </a>
          </li>
          @endif
        </li>
        @if($user->hasPermissionTo('Permisos Operaciones'))
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="ti-rocket"></i><span> Operaciones </span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect">
                <span>Traslados</span>
                <span class="menu-arrow"></span>
              </a>
              <ul>
                <li><a href="{{url('transfers')}}"><span>De Momento</span></a></li>
                <li><a href="{{url('importTransfer')}}"><span>Programado</span></a></li>
                <li><a href="{{ url('operator_transfers')}}"><i class="ti-list"></i><span>Traslados por asignar prestador</span></a></li>
                <li><a href="{{ url('list_transfers')}}"><i class="ti-list"></i><span>Listado de traslados</span></a></li>
                <li><a href="{{url('TemporaryTransfers')}}"><span>Traslados con errores</span></a></li>

              </ul>
            </li>
          </ul>
        </li>
        @endif
        @if($user->hasPermissionTo('Permisos Contrataciones'))
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="ti-rocket"></i><span> Contrataciones </span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            <li><a href="{{url('PersonalInformation')}}"><span>Registrar Datos Basicos del prestador</span></a></li>
            <li><a href="{{url('LenderList')}}"><span>Listado de Prestadores con datos asociados</span></a></li>
            <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect">
                <span>Configuración de modulos</span>
                <span class="menu-arrow"></span>
              </a>
              <ul>
                <li class="has_sub">
                  <li><a href="{{url('TravelPerModule')}}">Registrar Módulo</a></li>
                  <li><a href="{{url('EditTravelPerModule')}}"><span>Editar  Módulo</span></a></li>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        @endif
        @endif
        @if($user->hasRole('Operador') && !$user->hasRole('Administrador'))
        @if($user->hasPermissionTo('Permisos Contrataciones'))
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="ti-rocket"></i><span> Contrataciones </span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            <li><a href="{{url('PersonalInformation')}}"><span>Registrar Datos Basicos del prestador</span></a></li>
            <li><a href="{{url('LenderList')}}"><span>Listado de Prestadores con datos asociados</span></a></li>
            <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect">
                <span>Configuración de modulos</span>
                <span class="menu-arrow"></span>
              </a>
              <ul>
                <li class="has_sub">
                  <li><a href="{{url('TravelPerModule')}}">Registrar Módulo</a></li>
                  <li><a href="{{url('EditTravelPerModule')}}"><span>Editar  Módulo</span></a></li>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        @endif
        @if($user->hasPermissionTo('Permisos Operaciones'))
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="ti-rocket"></i><span> Operaciones </span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect">
                <span>Traslados</span>
                <span class="menu-arrow"></span>
              </a>
              <ul>
                <li><a href="{{url('transfers')}}"><span>De Momento</span></a></li>
                <li><a href="{{url('importTransfer')}}"><span>Programado</span></a></li>
                <li><a href="{{ url('operator_transfers')}}"><i class="ti-list"></i><span>Traslados por asignar prestador</span></a></li>
                <li><a href="{{ url('list_transfers')}}"><i class="ti-list"></i><span>Listado de traslados</span></a></li>
                <li><a href="{{url('TemporaryTransfers')}}"><span>Traslados con errores</span></a></li>

              </ul>
            </li>
          </ul>
        </li>
        @endif

        @endif
        @if($user->hasRole('Chofer') && !$user->hasRole('Administrador')  )
        @if($user->hasPermissionTo('Permisos Contrataciones'))
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="ti-rocket"></i><span> Contrataciones </span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            <li><a href="{{url('PersonalInformation')}}"><span>Registrar Datos Basicos del prestador</span></a></li>
            <li><a href="{{url('LenderList')}}"><span>Listado de Prestadores con datos asociados</span></a></li>
            <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect">
                <span>Configuración de modulos</span>
                <span class="menu-arrow"></span>
              </a>
              <ul>
                <li class="has_sub">
                  <li><a href="{{url('TravelPerModule')}}">Registrar Módulo</a></li>
                  <li><a href="{{url('EditTravelPerModule')}}"><span>Editar  Módulo</span></a></li>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        @endif
        @if($user->hasPermissionTo('Permisos Operaciones'))
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="ti-rocket"></i><span> Operaciones </span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect">
                <span>Traslados</span>
                <span class="menu-arrow"></span>
              </a>
              <ul>
                <li><a href="{{url('transfers')}}"><span>De Momento</span></a></li>
                <li><a href="{{url('importTransfer')}}"><span>Programado</span></a></li>
                <li><a href="{{ url('operator_transfers')}}"><i class="ti-list"></i><span>Traslados por asignar prestador</span></a></li>
                <li><a href="{{ url('list_transfers')}}"><i class="ti-list"></i><span>Listado de traslados</span></a></li>
                <li><a href="{{url('TemporaryTransfers')}}"><span>Traslados con errores</span></a></li>

              </ul>
            </li>
          </ul>
        </li>
        @endif
        <li>
          <a href="{{url('ListTransfers')}}">
            <i class="ti-list"></i>
            <span>Lista de traslados asignados</span>
          </a>
        </li>
        @endif
        <!-- @if($user->hasPermissionTo('Permisos Contrataciones'))
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect waves-primary">
            <i class="ti-rocket"></i><span> Opcion con permiso de contratacion </span>
            <span class="menu-arrow"></span>
          </a>
          <ul class="list-unstyled">
            <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect">
                <span>Traslados</span>
                <span class="menu-arrow"></span>
              </a>
              <ul>
                <li><a href="{{url('transfers')}}"><span>De Momento</span></a></li>
                <li><a href="{{url('importTransfer')}}"><span>Programado</span></a></li>
                <li><a href="{{ url('operator_transfers')}}"><i class="ti-list"></i><span>Traslados por asignar prestador</span></a></li>
                <li><a href="{{ url('list_transfers')}}"><i class="ti-list"></i><span>Listado de traslados</span></a></li>
                <li><a href="{{url('TemporaryTransfers')}}"><span>Traslados con errores</span></a></li>

              </ul>
            </li>
          </ul>
        </li>
        @endif -->
      </ul>
      <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>
<!-- Left Sidebar End -->
