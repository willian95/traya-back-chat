<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<style>

    .text-center{
        text-align: center;
    }
    .bold{
        font-weight: bold;
    }

    td{
        border: 1pt solid black;
    }

</style>


<table style="width: 100%">
    <tr>
        <td colspan="3">
            <h1 class="text-center">{{ $location }}</h1>
        </td>
    </tr>
    <tr>
        <td class="text-center bold">Usuarios</td>
        <td class="text-center bold">Trabajadores</td>
        <td class="text-center bold">Total</td>    
    </tr>
    <tr>
        <td class="text-center">{{ $userRoles }}</td>
        <td class="text-center">{{ $workerRoles }}</td>
        <td class="text-center">{{ $userRoles + $workerRoles }}</td>  
    </tr>
    <tr>
        <td colspan="3">
            <h3 class="text-center">Nuevos Usuarios</h3>
        </td>
    </tr>
    <tr>
        <td class="text-center bold">Mes</td>
        <td class="text-center bold" colspan="2">Cantidad</td>
    </tr>
    @foreach($newUsers as $user)
        <tr>
            @if($user['month'] == 'Jan')
                <td class="text-center">Ene</td>
            @elseif($user['month'] == 'Feb')
                <td class="text-center">Feb</td>
            @elseif($user['month'] == 'Mar')
                <td class="text-center">Mar</td>
            @elseif($user['month'] == 'Apr')
                <td class="text-center">Abr</td>
            @elseif($user['month'] == 'May')
                <td class="text-center">May</td>
            @elseif($user['month'] == 'Jun')
                <td class="text-center">Jun</td>
            @elseif($user['month'] == 'Jul')
                <td class="text-center">Jul</td>
            @elseif($user['month'] == 'Aug')
                <td class="text-center">Ago</td>
            @elseif($user['month'] == 'Sep')
                <td class="text-center">Sep</td>
            @elseif($user['month'] == 'Oct')
                <td class="text-center">Oct</td>
            @elseif($user['month'] == 'Nov')
                <td class="text-center">Nov</td>
            @elseif($user['month'] == 'Dec')
                <td class="text-center">Dic</td>
            @endif
            <td class="text-center" colspan="2">{{ $user['count'] }}</td>
        </tr> 
    @endforeach
    <tr>
        <td colspan="3">
            <h3 class="text-center">Contrataciones</h3>
        </td>
    </tr>
    <tr>
        <td class="text-center bold">Mes</td>
        <td class="text-center bold" colspan="2">Cantidad</td>
    </tr>

    @foreach($contracts as $user)
        <tr>
            @if($user['month'] == 'Jan')
                <td class="text-center">Ene</td>
            @elseif($user['month'] == 'Feb')
                <td class="text-center">Feb</td>
            @elseif($user['month'] == 'Mar')
                <td class="text-center">Mar</td>
            @elseif($user['month'] == 'Apr')
                <td class="text-center">Abr</td>
            @elseif($user['month'] == 'May')
                <td class="text-center">May</td>
            @elseif($user['month'] == 'Jun')
                <td class="text-center">Jun</td>
            @elseif($user['month'] == 'Jul')
                <td class="text-center">Jul</td>
            @elseif($user['month'] == 'Aug')
                <td class="text-center">Ago</td>
            @elseif($user['month'] == 'Sep')
                <td class="text-center">Sep</td>
            @elseif($user['month'] == 'Oct')
                <td class="text-center">Oct</td>
            @elseif($user['month'] == 'Nov')
                <td class="text-center">Nov</td>
            @elseif($user['month'] == 'Dec')
                <td class="text-center">Dic</td>
            @endif
            <td class="text-center" colspan="2">{{ $user['count'] }}</td>
        </tr> 
    @endforeach
    <tr>
        <td colspan="3" class="text-center bold">Total: {{ $totalContracts }}</td>
    </tr>

    <tr>
        <td colspan="3">
            <h3 class="text-center">Contactos</h3>
        </td>
    </tr>
    <tr>
        <td class="text-center bold">Mes</td>
        <td class="text-center bold" colspan="2">Cantidad</td>
    </tr>

    @foreach($contacts as $user)
        <tr>
            @if($user['month'] == 'Jan')
                <td class="text-center">Ene</td>
            @elseif($user['month'] == 'Feb')
                <td class="text-center">Feb</td>
            @elseif($user['month'] == 'Mar')
                <td class="text-center">Mar</td>
            @elseif($user['month'] == 'Apr')
                <td class="text-center">Abr</td>
            @elseif($user['month'] == 'May')
                <td class="text-center">May</td>
            @elseif($user['month'] == 'Jun')
                <td class="text-center">Jun</td>
            @elseif($user['month'] == 'Jul')
                <td class="text-center">Jul</td>
            @elseif($user['month'] == 'Aug')
                <td class="text-center">Ago</td>
            @elseif($user['month'] == 'Sep')
                <td class="text-center">Sep</td>
            @elseif($user['month'] == 'Oct')
                <td class="text-center">Oct</td>
            @elseif($user['month'] == 'Nov')
                <td class="text-center">Nov</td>
            @elseif($user['month'] == 'Dec')
                <td class="text-center">Dic</td>
            @endif
            <td class="text-center" colspan="2">{{ $user['count'] }}</td>
        </tr> 
    @endforeach
    <tr>
        <td colspan="3" class="text-center bold">Total: {{ $totalContacts }}</td>
    </tr>

</table>