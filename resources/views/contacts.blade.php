<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Nombre de emisor</th>
            <th>Correo de emisor</th>
            <th>Dirección de emisor</th>
            <th>Teléfono de emisor</th>
            <th>Nombre receptor</th>
            <th>Correo receptor</th>
            <th>Dirección receptor</th>
            <th>Teléfono receptor</th>
            <th>Tipo</th>
            
            <th>Servicios</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contacts as $contact)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $contact->created_at->format('d-m-Y') }}</td>
                <td>{{ $contact->caller->name }}</td>
                <td>{{ $contact->caller->email }}</td>
                <td>{{ $contact->caller->profile->domicile }}</td>
                <td>{{ $contact->caller->profile->phone }}</td>
                <td>{{ $contact->receiver->name }}</td>
                <td>{{ $contact->receiver->email }}</td>
                <td>{{ $contact->receiver->profile->domicile }}</td>
                <td>{{ $contact->receiver->profile->phone }}</td>
                <td>{{ $contact->type }}</td>
                
            </tr>
        @endforeach
    </tbody>
</table>