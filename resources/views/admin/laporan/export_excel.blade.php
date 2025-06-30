<table>
    <thead>
        <tr>
            <th>Nama Driver</th>
            <th>Waktu Selesai</th>
            <th>Tarif</th>
            <th>Pendapatan Driver</th>
            <th>Pendapatan Admin</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ $order->driver->nama ?? '-' }}</td>
                <td>{{ $order->waktu_selesai }}</td>
                <td>{{ $order->tarif }}</td>
                <td>{{ $order->pendapatan_driver }}</td>
                <td>{{ $order->pendapatan_admin }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
