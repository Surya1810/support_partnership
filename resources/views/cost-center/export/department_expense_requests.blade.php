<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Digunakan</th>
            <th>Judul</th>
            <th>Kode Transaksi</th>
            <th>User</th>
            <th>Limit</th>
            <th>Diajukan (Rp)</th>
            <th>Digunakan (Rp)</th>
            <th>Dikembalikan (Rp)</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requests as $index => $item)
            <tr>
                <td>{{ $index + 1 }}.</td>
                <td>{{ $item->use_date->format('Y-m-d') }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->code_ref_request }}</td>
                <td>{{ $item->user->name }}</td>
                <td>{{ $item->costCenter->amount_credit }}</td>
                <td>{{ $item->total_amount }}</td>
                <td>{{ $item->items->sum('actual_amount') }}</td>
                <td>
                    @if ($item->status == 'finish')
                        {{ $item->total_amount - $item->items->sum('actual_amount') }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
