<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Comuna</th>
            <th>Dirección</th>
            <th>Rol</th>
            <th>Email</th>
            <th>Fecha de registro</th>
            <th>Servicios</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ $user->location_name }}</td>
                <td>{{ $user->domicile }}</td>
                <td>
                    @if(App\Models\BackpackUser::find($user->id)->hasRole("Demandante"))
                        Demandante
                    @elseif(App\Models\BackpackUser::find($user->id)->hasRole("Ofertante"))
                        Ofertante
                    @elseif(App\Models\BackpackUser::find($user->id)->hasRole("Administrador"))
                        Administrador
                    @endif
                </td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->created_at->format('d-m-Y') }}</td>
                <td>
                    @php
                        $services = "";
                        foreach(App\ServicesUser::where('user_id', $user->id)->with('service')->get() as $service)
                            $services .= $service->service->name.", ";
                        
                    @endphp
                    {{ $services }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>