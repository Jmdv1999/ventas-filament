<div>
    <h3>Detalles de la Venta #{{ $venta->id }}</h3>
    <table @style(['width: 100%', 'margin-top: 1em'])>
        <thead @style([ 'background-color: #f59e0b' , 'color: #ffff', 'text-align: left', 'width: 100%', 'padding: 1em'])>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @if($venta->detalle)
            @foreach($venta->detalle as $detallado)
            <tr>

                <td>{{ $detallado->producto->descripcion}}</td>
                <td>{{ $detallado->cantidad }}</td>
                <td>{{ $detallado->subtotal }}</td>
            </tr>
            @endforeach
            @else
            <tfooter>No hay detalles disponibles para esta venta.</tfooter>
            @endif
        </tbody>
    </table>
</div>