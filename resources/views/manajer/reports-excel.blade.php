<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Laporan Penjualan - PesanAja</title>
</head>
<body style="font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #1e293b;">
    <table border="0" cellspacing="0" cellpadding="6" style="border-collapse: collapse; width: 100%;">
        <colgroup>
            <col width="45">
            <col width="140">
            <col width="140">
            <col width="180">
            <col width="140">
            <col width="140">
        </colgroup>

        {{-- Kop Laporan --}}
        <tr>
            <td colspan="6" style="font-size: 16pt; font-weight: bold; color: #1E2A47; padding: 8px 6px 2px 6px;">
                PesanAja — Laporan Penjualan Restoran
            </td>
        </tr>
        <tr>
            <td colspan="6" style="font-size: 10pt; color: #475569; padding: 2px 6px;">
                Sistem Pemesanan &amp; Kasir Restoran Digital | {{ config('app.url') }}
            </td>
        </tr>
        <tr>
            <td colspan="6" style="font-size: 10pt; color: #334155; padding: 2px 6px;">
                <strong>Periode:</strong> {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong>Tanggal Cetak:</strong> {{ now()->format('d M Y, H:i') }} WIB
            </td>
        </tr>
        <tr><td colspan="6" style="height: 12px;"></td></tr>

        {{-- Ringkasan Penjualan --}}
        <tr>
            <td colspan="6" style="background-color: #1E2A47; color: #ffffff; font-weight: bold; border: 1px solid #1E2A47; padding: 7px 8px;">
                RINGKASAN PENJUALAN
            </td>
        </tr>
        <tr>
            <td colspan="4" style="background-color: #f8fafc; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">Total Omzet</td>
            <td colspan="2" align="right" style="font-weight: bold; color: #1E2A47; border: 1px solid #cbd5e1; padding: 6px 8px; mso-number-format:'\0022Rp\0022#,##0';">{{ $totalOmzet }}</td>
        </tr>
        <tr>
            <td colspan="4" style="background-color: #f8fafc; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">Pembayaran Online (Midtrans)</td>
            <td colspan="2" align="right" style="border: 1px solid #cbd5e1; padding: 6px 8px; mso-number-format:'\0022Rp\0022#,##0';">{{ $byMethod->get('midtrans', 0) }}</td>
        </tr>
        <tr>
            <td colspan="4" style="background-color: #f8fafc; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">Pembayaran Tunai (Kasir)</td>
            <td colspan="2" align="right" style="border: 1px solid #cbd5e1; padding: 6px 8px; mso-number-format:'\0022Rp\0022#,##0';">{{ $byMethod->get('cash', 0) }}</td>
        </tr>
        <tr><td colspan="6" style="height: 12px;"></td></tr>

        {{-- 5 Menu Terlaris --}}
        <tr>
            <td colspan="6" style="background-color: #1E2A47; color: #ffffff; font-weight: bold; border: 1px solid #1E2A47; padding: 7px 8px;">
                5 MENU TERLARIS
            </td>
        </tr>
        <tr>
            <th align="center" style="background-color: #e2e8f0; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">No</th>
            <th colspan="4" align="left" style="background-color: #e2e8f0; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">Nama Menu</th>
            <th align="right" style="background-color: #e2e8f0; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">Terjual</th>
        </tr>
        @forelse ($topMenus as $idx => $item)
            <tr>
                <td align="center" style="border: 1px solid #cbd5e1; padding: 6px 8px;">{{ $idx + 1 }}</td>
                <td colspan="4" style="border: 1px solid #cbd5e1; padding: 6px 8px;">{{ $item->menu_name }}</td>
                <td align="right" style="border: 1px solid #cbd5e1; padding: 6px 8px; mso-number-format:'#,##0';">{{ $item->total_qty }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" align="center" style="border: 1px solid #cbd5e1; padding: 6px 8px; color: #64748b;">Belum ada data penjualan.</td>
            </tr>
        @endforelse
        <tr><td colspan="6" style="height: 12px;"></td></tr>

        {{-- Daftar Transaksi --}}
        <tr>
            <td colspan="6" style="background-color: #1E2A47; color: #ffffff; font-weight: bold; border: 1px solid #1E2A47; padding: 7px 8px;">
                DAFTAR TRANSAKSI — SLIDE {{ $currentSlide }} DARI {{ $totalSlides }} ({{ $totalOrders }} TOTAL)
            </td>
        </tr>
        <tr>
            <th align="center" style="background-color: #e2e8f0; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">No</th>
            <th align="left" style="background-color: #e2e8f0; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">Kode Order</th>
            <th align="left" style="background-color: #e2e8f0; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">Tanggal</th>
            <th align="left" style="background-color: #e2e8f0; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">Pelanggan</th>
            <th align="left" style="background-color: #e2e8f0; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">Metode</th>
            <th align="right" style="background-color: #e2e8f0; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 8px;">Total</th>
        </tr>
        @forelse ($pagedOrders as $idx => $order)
            <tr>
                <td align="center" style="border: 1px solid #cbd5e1; padding: 6px 8px;">{{ (($currentSlide - 1) * $perSlide) + $idx + 1 }}</td>
                <td style="border: 1px solid #cbd5e1; padding: 6px 8px; mso-number-format:'\@';">{{ $order->order_code ?? ('#' . $order->id) }}</td>
                <td style="border: 1px solid #cbd5e1; padding: 6px 8px; mso-number-format:'\@';">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td style="border: 1px solid #cbd5e1; padding: 6px 8px;">{{ $order->user?->name ?? 'Tamu' }}</td>
                <td style="border: 1px solid #cbd5e1; padding: 6px 8px;">{{ $order->payment_method === 'cash' ? 'Tunai' : 'Online' }}</td>
                <td align="right" style="border: 1px solid #cbd5e1; padding: 6px 8px; mso-number-format:'\0022Rp\0022#,##0';">{{ $order->total }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" align="center" style="border: 1px solid #cbd5e1; padding: 6px 8px; color: #64748b;">Belum ada transaksi pada periode ini.</td>
            </tr>
        @endforelse
    </table>
</body>
</html>
